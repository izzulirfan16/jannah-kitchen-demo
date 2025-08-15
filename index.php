<?php
require_once 'config.php';

// Get featured menu items
$stmt = $pdo->prepare("SELECT mi.*, c.name as category_name FROM menu_items mi 
                       LEFT JOIN categories c ON mi.category_id = c.id 
                       WHERE mi.is_available = 1 AND mi.category_id = 1
                       ORDER BY mi.created_at DESC LIMIT 6");
$stmt->execute();
$featured_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jannah Kitchen</title>
    <link href="https://fonts.googleapis.com/css2?family=WindSong:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="loader.css">
</head>
<body>
    <div id="page-loader">
    <div class="loader"></div>
    </div>
    <?php include 'header.php'; ?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-2 fw-bold mb-4 animate__animated animate__fadeInDown" style="font-family: WindSong, cursive;">
                Jannah Kitchen
            </h1>
            <p class="lead fs-3 mb-4 animate__animated animate__fadeInUp">Makanan budjet untuk anda semua</p>
            <a href="menu.php" class="btn btn-primary btn-lg px-3 py-2">
                <i class="fas fa-utensils me-2"></i>View Menu
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="background-color:rgb(221, 216, 185);">
        <div class="container">
            <h2 class="text-center mb-5">Kenapa Jannah Kitchen?</h2>
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-certificate feature-icon"></i>
                    <h4>100% Murah</h4>
                    <p>Boleh makan sedap walaupun dompet sudah kering</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-shipping-fast feature-icon"></i>
                    <h4>Delivery Laju</h4>
                    <p>Dari kolej hingga ke pintu rumah, semua tempat kita hantar</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-heart feature-icon"></i>
                    <h4>Made with Kasih sayang</h4>
                    <p>Makanan semua dibuat dengan ikhlas</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Items -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Apa yang ada?</h2>
            <div class="row">
                <?php foreach ($featured_items as $item): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="images/<?php echo htmlspecialchars($item['image_url'] ?: 'images/placeholder.jpg'); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($item['category_name']); ?></p>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="h5 text-primary mb-0"><?php echo formatPrice($item['price']); ?></span>
                                <?php if (isLoggedIn()): ?>
                                <form method="POST" action="add_to_cart.php" class="d-inline">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                                <?php else: ?>
                                <a href="login.php" class="btn btn-outline-primary btn-sm">Login to Order</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="menu.php" class="btn btn-outline-primary btn-lg">View Full Menu</a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-3 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-phone me-2"></i> +60 12-345 6789</p>
                    <p><i class="fas fa-envelope me-2"></i> info@jannahkitchen.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2025 Jannah Kitchen. All rights reserved.</p>
        </div>
    </footer>
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