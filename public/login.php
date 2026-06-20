<?php
/**
 * Login Page
 */
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $auth->login($email, $password);

    if ($result['success']) {
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        redirect($redirect);
    } else {
        $error = $result['message'];
    }
}

require_once '../includes/google-oauth.php';
$google_oauth = new GoogleOAuth();
$google_auth_url = $google_oauth->getAuthUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OryZenX</title>
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
                <h1 style="color: #00d4ff; margin-bottom: 10px;">Login to Your Account</h1>
                <p style="color: #999; margin-bottom: 30px;">Access your profile and manage your domains</p>

                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Login</button>
                </form>

                <div style="margin: 20px 0; text-align: center; color: #666;">
                    <span>or</span>
                </div>

                <a href="<?php echo $google_auth_url; ?>" class="btn" style="width: 100%; padding: 12px; background: #fff; color: #333; text-align: center; font-weight: 600;">
                    🔵 Login with Google
                </a>

                <p style="text-align: center; margin-top: 20px; color: #999;">
                    Don't have an account? <a href="register.php" style="color: #00d4ff; text-decoration: none;">Register here</a>
                </p>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
