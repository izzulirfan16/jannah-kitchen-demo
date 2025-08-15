<?php
require_once '../config.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$status, $order_id]);
    $msg = "Order status updated!";
}

// Handle clear all orders (hide from admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_orders'])) {
    $pdo->exec("UPDATE orders SET hide_order = 1 WHERE hide_order = 0");
    $msg = "All orders have been hidden";
}

// Fetch orders with user info (only those not hidden)
$orders = $pdo->query("SELECT o.*, u.full_name, u.phone, u.address 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.hide_order = 0 
    ORDER BY o.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all order items for all orders
$order_items_map = [];
if ($orders) {
    $order_ids = array_column($orders, 'id');
    $in  = str_repeat('?,', count($order_ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT oi.*, mi.name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id IN ($in)");
    $stmt->execute($order_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $oi) {
        $order_items_map[$oi['order_id']][] = $oi;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Update Order Status</title>
    <link rel="icon" type="image/x-icon" href="../favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
            background-color: #FFFBDE !important;
        }
</style>
<body class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <h2 class="mb-0">Update Order Status</h2>
        <div class="ms-auto d-flex gap-2">
            <a href="admin_menu.php" class="btn btn-outline-secondary">Edit Menu Items</a>
            <a href="../index.php" class="btn btn-outline-primary">Go to Home</a>
        </div>
    </div>
    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Created</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <form method="post">
                    <input type="hidden" name="update_order" value="1">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <td><?php echo $order['id']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($order['full_name']); ?>
                        <?php if (!empty($order['phone'])): ?>
                            <br><small class="text-muted">📞 <?php echo htmlspecialchars($order['phone']); ?></small>
                        <?php endif; ?>
                        <?php if (!empty($order['address'])): ?>
                            <br><small class="text-muted">🏠 <?php echo htmlspecialchars($order['address']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo number_format($order['total_amount'], 2); ?>
                        <?php if (!empty($order_items_map[$order['id']])): ?>
                            <ul class="mb-0 ps-3 small">
                                <?php foreach ($order_items_map[$order['id']] as $oi): ?>
                                    <li>
                                        <?php echo htmlspecialchars($oi['name']); ?> x<?php echo $oi['quantity']; ?>
                                        <span class="text-muted">(RM <?php echo number_format($oi['price'],2); ?>)</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </td>
                    <td>
                        <select name="status" class="form-select">
                            <?php
                            $statuses = ['pending', 'confirmed', 'preparing', 'on its way', 'delivered', 'cancelled'];
                            foreach ($statuses as $status) {
                                echo '<option value="'.$status.'"'.($order['status']==$status?' selected':'').'>'.ucfirst($status).'</option>';
                            }
                            ?>
                        </select>
                        <div class="small mt-1">
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                        </div>
                    </td>
                    <td>
                        <?php echo $order['created_at']; ?>
                        <?php if (!empty($order['notes'])): ?>
                            <br><span class="text-info small">📝 <?php echo htmlspecialchars($order['notes']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><button class="btn btn-success btn-sm" type="submit">Update</button></td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-end mt-4">
        <form method="post" onsubmit="return confirm('Are you sure you want to clear all orders?');" class="d-inline">
            <button type="submit" name="clear_orders" class="btn btn-danger">Clear All Orders</button>
        </form>
    </div>
</body>
</html>