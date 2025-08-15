<?php
require_once '../config.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$msg = [];

// Handle add new menu item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $name = trim($_POST['item_name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['item_price']);
    $cat = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // Handle image upload
    $image = $_FILES['item_image']['name'] ?? '';
    $image_tmp = $_FILES['item_image']['tmp_name'] ?? '';
    $image_folder = '../images/' . $image;

    if (empty($name) || empty($desc) || empty($price) || empty($image)) {
        $msg[] = 'Please fill out all fields.';
    } else {
        if (move_uploaded_file($image_tmp, $image_folder)) {
            $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, category_id, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $desc, $price, $cat, $image, $is_available]);
            $msg[] = 'New item added successfully!';
        } else {
            $msg[] = 'Image upload failed.';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$delete_id]);
    $msg[] = 'Item has been deleted.';
}

// Handle edit
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_item = $stmt->fetch();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $id = intval($_POST['update_id']);
    $name = trim($_POST['update_name']);
    $desc = trim($_POST['update_description']);
    $price = floatval($_POST['update_price']);
    $cat = isset($_POST['update_category_id']) ? intval($_POST['update_category_id']) : null;
    $is_available = isset($_POST['update_is_available']) ? 1 : 0;

    // Handle image upload
    $image = $_FILES['update_image']['name'] ?? '';
    $image_tmp = $_FILES['update_image']['tmp_name'] ?? '';
    $image_folder = '../images/' . $image;

    if (!empty($image)) {
        move_uploaded_file($image_tmp, $image_folder);
        $stmt = $pdo->prepare("UPDATE menu_items SET name=?, description=?, price=?, category_id=?, image_url=?, is_available=? WHERE id=?");
        $stmt->execute([$name, $desc, $price, $cat, $image, $is_available, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE menu_items SET name=?, description=?, price=?, category_id=?, is_available=? WHERE id=?");
        $stmt->execute([$name, $desc, $price, $cat, $is_available, $id]);
    }
    $msg[] = 'Item updated successfully!';
    $edit_item = null;
}

// Fetch categories
$cats = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
// Fetch menu items
$items = $pdo->query("SELECT * FROM menu_items")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Menu Management</title>
    <link rel="icon" type="image/x-icon" href="../favicon.png">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FFFBDE !important;
        }
        .admin-product-form-container { max-width: 500px; margin: 0 auto 2rem auto; }
        .item-display-table img { max-width: 100px; }
        .edit-form-container { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 0 10px #ccc; max-width: 500px; margin: 2rem auto; }
    </style>
</head>
<body>
    <div class="d-flex align-items-center mb-4 container" style="margin-top: 2rem;">
        <h2 class="mb-0">Menu Management</h2>
        <div class="ms-auto d-flex gap-2">
            <a href="admin_orders.php" class="btn btn-outline-secondary">Order Management</a>
            <a href="../index.php" class="btn btn-outline-primary">Go to Home</a>
        </div>
    </div>

    <div class="container">
        <?php foreach($msg as $m): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($m); ?></div>
        <?php endforeach; ?>

        <!-- Add Item Form -->
        <div class="admin-product-form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <h4>Add a New Item</h4>
                <input type="text" placeholder="Enter item name" name="item_name" class="form-control mb-2" required>
                <input type="text" placeholder="Enter description" name="description" class="form-control mb-2" required>
                <input type="number" step="0.01" placeholder="Enter item price" name="item_price" class="form-control mb-2" required>
                <select name="category_id" class="form-select mb-2" required>
                    <option value="">Select Category</option>
                    <?php foreach ($cats as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="file" accept="image/png, image/jpeg, image/jpg" name="item_image" class="form-control mb-2" required>
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" name="is_available" id="is_available" checked>
                    <label class="form-check-label" for="is_available">Available</label>
                </div>
                <button type="submit" class="btn btn-success w-100" name="add_item">Add Item</button>
            </form>
        </div>

        <!-- Edit Item Form -->
        <?php if ($edit_item): ?>
        <div class="edit-form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <h4>Edit Item</h4>
                <img src="../images/<?php echo htmlspecialchars($edit_item['image_url']); ?>" height="100" alt="">
                <input type="hidden" name="update_id" value="<?php echo $edit_item['id']; ?>">
                <input type="text" name="update_name" class="form-control mb-2" value="<?php echo htmlspecialchars($edit_item['name']); ?>" required>
                <input type="text" name="update_description" class="form-control mb-2" value="<?php echo htmlspecialchars($edit_item['description']); ?>" required>
                <input type="number" step="0.01" name="update_price" class="form-control mb-2" value="<?php echo $edit_item['price']; ?>" required>
                <select name="update_category_id" class="form-select mb-2" required>
                    <option value="">Select Category</option>
                    <?php foreach ($cats as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $edit_item['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="update_image" class="form-control mb-2" accept="image/png, image/jpeg, image/jpg">
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" name="update_is_available" id="update_is_available" <?php if ($edit_item['is_available']) echo 'checked'; ?>>
                    <label class="form-check-label" for="update_is_available">Available</label>
                </div>
                <button type="submit" class="btn btn-primary" name="update_item">Update Item</button>
                <a href="admin_menu.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
        <?php endif; ?>

        <!-- Items Table -->
        <div class="item-display mt-4">
            <table class="table table-bordered item-display-table">
                <thead>
                    <tr>
                        <th>Item Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td>
                            <?php if ($row['image_url']): ?>
                                <img src="../images/<?php echo htmlspecialchars($row['image_url']); ?>" height="60" alt="">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>RM <?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <?php
                                foreach ($cats as $cat) {
                                    if ($cat['id'] == $row['category_id']) {
                                        echo htmlspecialchars($cat['name']);
                                        break;
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <?php if ($row['is_available']): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="admin_menu.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="admin_menu.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
