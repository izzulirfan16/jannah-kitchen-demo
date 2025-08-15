<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT full_name, phone, address, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($full_name) || empty($phone) || empty($address)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, phone=?, address=? WHERE id=?");
        $stmt->execute([$full_name, $phone, $address, $user_id]);
        $success = "Profile updated successfully!";
        // Refresh user info
        $user['full_name'] = $full_name;
        $user['phone'] = $phone;
        $user['address'] = $address;
        $_SESSION['full_name'] = $full_name;
    }
}
?>
<div id="page-loader">
    <div class="loader"></div>
</div>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="loader.css">
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-user me-2"></i>Your Profile</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" class="col-md-6 col-lg-5 p-4 border rounded bg-light">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Delivery Address</label>
            <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Email (cannot change)</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
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