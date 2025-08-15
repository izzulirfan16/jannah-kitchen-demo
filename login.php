<?php
require_once 'config.php';

$error = '';
$success = '';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Redirect to appropriate page
            if ($user['role'] === 'admin') {
                redirect('admin/admin_orders.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jannah Kitchen</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="loader.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            background:rgb(16, 51, 87);
            padding: 2rem 0;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: #1D5D9B;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .btn-primary {
            background-color: #749BC2;
            border-color: #749BC2;
            padding: 12px;
        }
        .btn-primary:hover {
            background-color: #91C8E4;
            border-color: #91C8E4;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-card">
                        <div class="login-header">
                            <h2><i class="fas fa-utensils me-2"></i>Jannah Kitchen</h2>
                            <p class="mb-0">Welcome back! Please sign in to your account.</p>
                        </div>
                        <div class="p-4">
                            <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username or Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center">
                                <p class="mb-2">Don't have an account? <a href="signup.php" class="text-decoration-none">Sign up here</a></p>
                                <p><a href="index.php" class="text-decoration-none">← Back to Home</a></p>
                            </div>
                            
                            <hr class="my-4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="page-loader">
        <div class="loader"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
    window.addEventListener('load', function() {
        document.getElementById('page-loader').style.opacity = '0';
        setTimeout(function() {
            document.getElementById('page-loader').style.display = 'none';
        }, 400);
    });
    </script>
</body>
</html>