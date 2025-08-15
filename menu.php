<?php
require_once 'config.php';

// Get categories
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Get selected category
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';

// Get menu items
$sql = "SELECT mi.*, c.name as category_name FROM menu_items mi 
        LEFT JOIN categories c ON mi.category_id = c.id 
        WHERE mi.is_available = 1";

if ($selected_category) {
    $sql .= " AND mi.category_id = :category_id";
}

$sql .= " ORDER BY c.name, mi.name";

$stmt = $pdo->prepare($sql);
if ($selected_category) {
    $stmt->bindParam(':category_id', $selected_category);
}
$stmt->execute();
$menu_items = $stmt->fetchAll();

// Group items by category
$items_by_category = [];
foreach ($menu_items as $item) {
    $items_by_category[$item['category_name']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Jannah Kitchen</title>
    <link rel="stylesheet" href="loader.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FFFBDE !important;
        }
        .navbar-brand {
            font-weight: bold;
            color: #749BC2 !important;
        }
        .btn-primary {
            background-color: #749BC2;
            border-color: #749BC2;
        }
        .btn-primary:hover {
            background-color: #91C8E4;
            border-color: #91C8E4;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .category-filter {
            background-color: #FFFBDE !important;
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .price-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #749BC2;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }
        .category-section {
            scroll-margin-top: 100px;
        }
    </style>
</head>
<body>
    <div id="page-loader">
        <div class="loader"></div>
    </div>
    <?php include 'header.php'; ?>

    <!-- Page Header -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">Menu</h1>
        <p class="text-center text-muted">Makanan Budget Untuk Anda Semua</p>
    </div>

    <!-- Category Filter -->
    <div class="category-filter">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="menu.php" class="btn <?php echo !$selected_category ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            All Categories
                        </a>
                        <?php foreach ($categories as $category): ?>
                        <a href="menu.php?category=<?php echo $category['id']; ?>" 
                           class="btn <?php echo $selected_category == $category['id'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items -->
    <div class="container my-5">
        <?php if (empty($items_by_category)): ?>
        <div class="text-center py-5">
            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
            <h3>No items found</h3>
            <p class="text-muted">No menu items available in this category.</p>
        </div>
        <?php else: ?>
            <?php foreach ($items_by_category as $category_name => $items): ?>
            <section class="category-section mb-5" id="category-<?php echo strtolower(str_replace(' ', '-', $category_name)); ?>">
                <h2 class="mb-4 pb-2 border-bottom"><?php echo htmlspecialchars($category_name); ?></h2>
                <div class="row">
                    <?php foreach ($items as $item): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm position-relative">
                            <div class="position-relative">
                                <img src="images/<?php echo htmlspecialchars($item['image_url'] ?: 'placeholder.jpg'); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <span class="price-badge"><?php echo formatPrice($item['price']); ?></span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars($item['description']); ?></p>
                                <div class="mt-auto">
                                    <?php if (isLoggedIn()): ?>
                                    <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <div class="input-group">
                                            <input type="number" name="quantity" value="1" min="1" max="10" 
                                                   class="form-control" style="max-width: 80px;">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-cart-plus"></i> Add to Cart
                                            </button>
                                        </div>
                                    </form>
                                    <?php else: ?>
                                    <div class="d-grid">
                                        <a href="login.php" class="btn btn-outline-primary">
                                            Login to Order
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

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