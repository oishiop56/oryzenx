<?php
/**
 * Register Page
 */
require_once '../config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $result = $auth->register($full_name, $email, $phone, $address, $password, $confirm_password);

    if ($result['success']) {
        $success = $result['message'];
        redirect('login.php');
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OryZenX</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">⚡ OryZenX</a>
        </div>
    </nav>

    <div class="container" style="padding: 40px 0;">
        <div style="max-width: 500px; margin: 0 auto;">
            <div class="card">
                <h1 style="color: #00d4ff; margin-bottom: 10px;">Create Account</h1>
                <p style="color: #999; margin-bottom: 30px;">Join OryZenX and start exploring premium domains</p>

                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" data-validate="true">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone">
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                        <small style="color: #999;">Minimum 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Register</button>

                    <p style="text-align: center; margin-top: 20px; color: #999;">
                        Already have an account? <a href="login.php" style="color: #00d4ff; text-decoration: none;">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
