<?php
require_once 'config.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Status - Jannah Kitchen</title>
    <link rel="stylesheet" href="loader.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="page-loader">
    <div class="loader"></div>
    </div>
    <?php include 'header.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Order Status</h1>
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">You have not placed any orders yet.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                        <td><?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <?php
                                $badge = 'secondary';
                                switch ($order['status']) {
                                    case 'pending': $badge = 'warning'; break;
                                    case 'confirmed': $badge = 'info'; break;
                                    case 'preparing': $badge = 'primary'; break;
                                    case 'on its way': $badge = 'success'; break;
                                    case 'delivered': $badge = 'success'; break;
                                    case 'cancelled': $badge = 'danger'; break;
                                }
                            ?>
                            <span class="badge bg-<?php echo $badge; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
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