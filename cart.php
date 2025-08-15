<?php
require_once 'config.php';

// Require login
requireLogin();

$user_id = $_SESSION['user_id'];

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $cart_id = (int)$_POST['cart_id'];
                $quantity = (int)$_POST['quantity'];
                
                if ($quantity > 0 && $quantity <= 10) {
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$quantity, $cart_id, $user_id]);
                    $_SESSION['success'] = 'Cart updated successfully!';
                } else {
                    $_SESSION['error'] = 'Invalid quantity.';
                }
                break;
                
            case 'remove':
                $cart_id = (int)$_POST['cart_id'];
                $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $stmt->execute([$cart_id, $user_id]);
                $_SESSION['success'] = 'Item removed from cart.';
                break;
                
            case 'clear':
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $_SESSION['success'] = 'Cart cleared successfully!';
                break;
        }
        redirect('cart.php');
    }
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, mi.id as item_id, mi.name, mi.description, 
           mi.price, mi.image_url, (c.quantity * mi.price) as subtotal
    FROM cart c 
    JOIN menu_items mi ON c.menu_item_id = mi.id 
    WHERE c.user_id = ? 
    ORDER BY c.added_at DESC
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate totals
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['subtotal'];
}

// Get user info for checkout
$stmt = $pdo->prepare("SELECT full_name, phone, address FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_info = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Jannah Kitchen</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="loader.css">
    <style>
        body {
            background-color: #FFFBDE !important;
        }
        .navbar-brand {
            font-weight: bold;
            color: #2c5530 !important;
        }
        .btn-primary {
            background-color: #2c5530;
            border-color: #2c5530;
        }
        .btn-primary:hover {
            background-color: #1e3a20;
            border-color: #1e3a20;
        }
        .cart-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
        }
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-control {
            max-width: 80px;
        }
        .total-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>
    <div id="page-loader">
        <div class="loader"></div>
    </div>
    <?php include 'header.php'; ?>

    <div class="container my-4">
        <h1 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Add some delicious items from our menu!</p>
            <a href="menu.php" class="btn btn-primary">
                <i class="fas fa-utensils me-2"></i>Browse Menu
            </a>
        </div>
        <?php else: ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Cart Items -->
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="images/<?php echo htmlspecialchars($item['image_url'] ?: 'images/placeholder.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($item['description']); ?></p>
                            <strong class="text-primary"><?php echo formatPrice($item['price']); ?> each</strong>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="cart.php" class="d-inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <div class="input-group">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="10" class="form-control quantity-control">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </form>
                            <form method="POST" action="cart.php" class="d-inline ms-2">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this item?');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="fw-bold"><?php echo formatPrice($item['subtotal']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <form method="POST" action="cart.php" class="mt-3">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Clear your cart?');">
                        <i class="fas fa-trash-alt me-2"></i>Clear Cart
                    </button>
                </form>
            </div>
            <div class="col-lg-4">
                <!-- Cart Total & Checkout -->
                <div class="total-section shadow-sm">
                    <h4 class="mb-3">Order Summary</h4>
                    <ul class="list-group mb-3">
                        <?php foreach ($cart_items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($item['name']); ?> <span>x<?php echo $item['quantity']; ?></span>
                            <span><?php echo formatPrice($item['subtotal']); ?></span>
                        </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                            Total
                            <span><?php echo formatPrice($total); ?></span>
                        </li>
                    </ul>
                    <h6 class="mb-2">Delivery Info</h6>
                    <div class="mb-2">
                        <strong><?php echo htmlspecialchars($user_info['full_name']); ?></strong><br>
                        <?php echo htmlspecialchars($user_info['phone']); ?><br>
                        <?php echo htmlspecialchars($user_info['address']); ?>
                    </div>
                    <form method="post" action="checkout.php">
                        <div class="mb-3">
                            <br><br>
                            <label for="order_notes" class="form-label">Order Notes (optional)</label>
                            <textarea name="order_notes" id="order_notes" class="form-control" rows="2" placeholder="Any special instructions?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </button>
                    </form>
                </div>
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