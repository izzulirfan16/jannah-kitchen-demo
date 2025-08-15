<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, mi.id as item_id, mi.name, mi.price, (c.quantity * mi.price) as subtotal
    FROM cart c 
    JOIN menu_items mi ON c.menu_item_id = mi.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['subtotal'];
}

// Get user info
$stmt = $pdo->prepare("SELECT full_name, phone, address FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_info = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];
    // Insert order
    $order_notes = $_POST['order_notes'] ?? ($_SESSION['order_notes'] ?? '');
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method, notes) VALUES (?, ?, 'pending', ?, ?)");
    $stmt->execute([$user_id, $total, $payment_method, $order_notes]);
    $order_id = $pdo->lastInsertId();

    // Insert order items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt->execute([$order_id, $item['item_id'], $item['quantity'], $item['price']]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $_SESSION['success'] = "Order placed successfully!";
    header("Location: order_status.php");
    exit;
}
// Handle order notes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_notes'])) {
    $_SESSION['order_notes'] = trim($_POST['order_notes']);
    header("Location: checkout.php");
    exit;
}
unset($_SESSION['order_notes']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Jannah Kitchen</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="loader.css">
</head>
<body>
    <div id="page-loader">
        <div class="loader"></div>
    </div>
    <style>
        body {
            background-color: #FFFBDE !important;
        }
    </style>
<?php include 'header.php'; ?>
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h2>
    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">Your cart is empty. <a href="menu.php">Browse menu</a></div>
    <?php else: ?>
    <div class="row">
        <div class="col-lg-7">
            <h5>Delivery Information</h5>
            <div class="mb-3">
                <strong><?php echo htmlspecialchars($user_info['full_name']); ?></strong><br>
                <?php echo htmlspecialchars($user_info['phone']); ?><br>
                <?php echo htmlspecialchars($user_info['address']); ?>
            </div>
            <h5 class="mt-4">Order Summary</h5>
            <ul class="list-group mb-3">
                <?php foreach ($cart_items as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($item['name']); ?> <span>x<?php echo $item['quantity']; ?></span>
                    <span><?php echo number_format($item['subtotal'], 2); ?></span>
                </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                    Total
                    <span><?php echo number_format($total, 2); ?></span>
                </li>
            </ul>
        </div>
        <div class="col-lg-5">
            <form method="post" class="p-4 border rounded bg-light">
                <h5>Payment Method</h5>
                <div class="mb-3 d-flex gap-2">
                    <input type="radio" class="btn-check" name="payment_method" id="cod" value="Cash on Delivery" required autocomplete="off">
                    <label class="btn btn-outline-primary" for="cod">
                        <i class="fas fa-money-bill-wave me-1"></i> Cash on Delivery
                    </label>

                    <input type="radio" class="btn-check" name="payment_method" id="online" value="Online Payment" required autocomplete="off">
                    <label class="btn btn-outline-success" for="online">
                        <i class="fas fa-credit-card me-1"></i> Online Payment
                    </label>
                </div>
                <input type="hidden" name="order_notes" value="<?php echo htmlspecialchars($_SESSION['order_notes'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary w-100">Place Order</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('load', function() {
    var loader = document.getElementById('page-loader');
    loader.style.opacity = '0';
    setTimeout(function() {
        loader.style.display = 'none';
    }, 300);
});
</script>
</body>
</html>