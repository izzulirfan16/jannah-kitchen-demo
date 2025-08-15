<?php
require_once 'config.php';

// Require login
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)($_POST['quantity'] ?? 1);
    $user_id = $_SESSION['user_id'];
    
    // Validate quantity
    if ($quantity < 1 || $quantity > 10) {
        $_SESSION['error'] = 'Invalid quantity. Please select between 1 and 10.';
        redirect($_SERVER['HTTP_REFERER'] ?? 'menu.php');
    }
    
    // Check if item exists and is available
    $stmt = $pdo->prepare("SELECT id, name, price FROM menu_items WHERE id = ? AND is_available = 1");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();
    
    if (!$item) {
        $_SESSION['error'] = 'Item not found or not available.';
        redirect($_SERVER['HTTP_REFERER'] ?? 'menu.php');
    }
    
    try {
        // Check if item already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND menu_item_id = ?");
        $stmt->execute([$user_id, $item_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update existing cart item
            $new_quantity = $existing['quantity'] + $quantity;
            if ($new_quantity > 10) {
                $new_quantity = 10;
            }
            
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, added_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$new_quantity, $existing['id']]);
            
            $_SESSION['success'] = 'Cart updated! ' . htmlspecialchars($item['name']) . ' quantity changed to ' . $new_quantity;
        } else {
            // Add new item to cart
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, menu_item_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $item_id, $quantity]);
            
            $_SESSION['success'] = 'Added to cart! ' . htmlspecialchars($item['name']) . ' (x' . $quantity . ')';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error adding item to cart. Please try again.';
    }
} else {
    $_SESSION['error'] = 'Invalid request.';
}

// Redirect back to previous page or menu
redirect($_SERVER['HTTP_REFERER'] ?? 'menu.php');
?>