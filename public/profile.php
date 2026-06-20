<?php
/**
 * User Profile Page
 */
require_once '../config.php';
requireLogin();

$user_id = $auth->getCurrentUserId();
$user = $auth->getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? sanitize($_POST['action']) : '';

    if ($action === 'update_profile') {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');

        $result = $auth->updateProfile($user_id, $full_name, $phone, $address);
        if ($result['success']) {
            $success = $result['message'];
            $user = $auth->getCurrentUser();
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'change_password') {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $result = $auth->changePassword($user_id, $old_password, $new_password, $confirm_password);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

// Get user's purchases
$purchases = $db->fetchAll(
    'SELECT o.*, d.domain_name, p.status as payment_status FROM orders o
     JOIN domains d ON o.domain_id = d.id
     LEFT JOIN payments p ON o.id = p.order_id
     WHERE o.user_id = ? ORDER BY o.created_at DESC',
    array($user_id)
);

// Get user's offers
$offers = $db->fetchAll(
    'SELECT o.*, d.domain_name FROM offers o
     JOIN domains d ON o.domain_id = d.id
     WHERE o.user_id = ? ORDER BY o.created_at DESC',
    array($user_id)
);

// Get user's payment history
$payments = $db->fetchAll(
    'SELECT p.*, d.domain_name FROM payments p
     JOIN orders o ON p.order_id = o.id
     JOIN domains d ON o.domain_id = d.id
     WHERE p.user_id = ? ORDER BY p.created_at DESC',
    array($user_id)
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - OryZenX</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">⚡ OryZenX</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="profile.php">Profile</a></li>
                <?php if ($auth->isAdmin()): ?>
                    <li><a href="../admin/index.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <div class="auth-buttons">
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container" style="padding: 40px 0;">
        <div class="page-header">
            <h1>My Profile</h1>
            <p>Manage your account and view your activity</p>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="grid grid-2">
            <!-- Profile Information -->
            <div class="card">
                <h2 style="color: #00d4ff; margin-bottom: 20px;">Account Information</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="opacity: 0.6;">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="card">
                <h2 style="color: #00d4ff; margin-bottom: 20px;">Change Password</h2>
                <form method="POST" data-validate="true">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="old_password" required>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                        <small style="color: #999;">Minimum 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Change Password</button>
                </form>
            </div>
        </div>

        <!-- My Purchases -->
        <div class="card">
            <h2 style="color: #00d4ff; margin-bottom: 20px;">My Purchases</h2>
            <?php if (empty($purchases)): ?>
            <p style="color: #999;">No purchases yet.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $purchase): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($purchase['domain_name']); ?></td>
                            <td><?php echo formatPrice($purchase['amount']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['currency']); ?></td>
                            <td><?php echo getPaymentStatusBadge($purchase['payment_status'] ?? 'pending'); ?></td>
                            <td><?php echo formatDate($purchase['created_at'], 'M d, Y'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- My Offers -->
        <div class="card">
            <h2 style="color: #00d4ff; margin-bottom: 20px;">My Offers</h2>
            <?php if (empty($offers)): ?>
            <p style="color: #999;">No offers yet.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Offer Price</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $offer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($offer['domain_name']); ?></td>
                            <td><?php echo formatPrice($offer['offer_price']); ?></td>
                            <td><?php echo getStatusBadge($offer['status']); ?></td>
                            <td><?php echo formatDate($offer['created_at'], 'M d, Y'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Payment History -->
        <div class="card">
            <h2 style="color: #00d4ff; margin-bottom: 20px;">Payment History</h2>
            <?php if (empty($payments)): ?>
            <p style="color: #999;">No payments yet.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>TXN ID</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['domain_name']); ?></td>
                            <td><?php echo formatPrice($payment['amount']); ?></td>
                            <td><?php echo htmlspecialchars($payment['currency']); ?></td>
                            <td><?php echo htmlspecialchars(substr($payment['transaction_id'], 0, 12) . '...'); ?></td>
                            <td><?php echo getPaymentStatusBadge($payment['status']); ?></td>
                            <td><?php echo formatDate($payment['created_at'], 'M d, Y'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
