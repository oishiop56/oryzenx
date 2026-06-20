<?php
/**
 * Home Page - Domain Marketplace
 */
require_once '../config.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 999999;
$domain_type = isset($_GET['type']) ? sanitize($_GET['type']) : '';

// Build query
$query = 'SELECT * FROM domains WHERE status = "available"';
$params = array();

if ($search) {
    $query .= ' AND domain_name LIKE ?';
    $params[] = '%' . $search . '%';
}

if ($min_price > 0) {
    $query .= ' AND price >= ?';
    $params[] = $min_price;
}

if ($max_price < 999999) {
    $query .= ' AND price <= ?';
    $params[] = $max_price;
}

if ($domain_type) {
    $query .= ' AND domain_type = ?';
    $params[] = $domain_type;
}

$query .= ' ORDER BY domain_type DESC, created_at DESC';

// Count total
$count_result = $db->fetchAll($query, $params);
$total_items = count($count_result);
$total_pages = ceil($total_items / ITEMS_PER_PAGE);

// Pagination
$offset = ($page - 1) * ITEMS_PER_PAGE;
$query .= ' LIMIT ? OFFSET ?';
$params[] = ITEMS_PER_PAGE;
$params[] = $offset;

$domains = $db->fetchAll($query, $params);

// Get premium domains for slider
$premium_domains = $db->fetchAll(
    'SELECT * FROM domains WHERE status = "available" AND domain_type = "premium" ORDER BY created_at DESC LIMIT 6',
    array()
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OryZenX - Domain Marketplace</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">⚡ OryZenX</a>
            <button class="menu-toggle">☰</button>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#domains">Domains</a></li>
                <?php if ($auth->isLoggedIn()): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <?php if ($auth->isAdmin()): ?>
                        <li><a href="../admin/index.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
            <div class="auth-buttons">
                <?php if (!$auth->isLoggedIn()): ?>
                    <a href="login.php" class="btn btn-secondary">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php else: ?>
                    <a href="profile.php" class="btn btn-primary">My Account</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div style="background: linear-gradient(135deg, #0f3460 0%, #1a1a2e 100%); padding: 80px 0; border-bottom: 2px solid #00d4ff;">
        <div class="container">
            <div style="text-align: center;">
                <h1 style="font-size: 48px; color: #00d4ff; margin-bottom: 20px;">Find Your Perfect Domain</h1>
                <p style="font-size: 18px; color: #ccc; margin-bottom: 30px;">Premium domain names for your business, project, or investment</p>
                
                <!-- Search Form -->
                <form class="search-form" method="GET" style="max-width: 500px; margin: 0 auto;">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="search" placeholder="Search domain..." value="<?php echo $search; ?>" style="flex: 1;">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container" style="padding-top: 40px;">
        <!-- Premium Domains Slider -->
        <?php if (!empty($premium_domains)): ?>
        <div class="premium-section">
            <h2 class="premium-title">✨ Featured Domains</h2>
            <div class="slider-container">
                <div class="slider">
                    <?php foreach ($premium_domains as $domain): ?>
                    <div class="slider-item">
                        <div class="domain-card premium">
                            <div class="domain-name"><?php echo htmlspecialchars($domain['domain_name']); ?></div>
                            <div class="domain-price"><?php echo formatPrice($domain['price']); ?></div>
                            <div class="domain-status"><?php echo getStatusBadge($domain['status']); ?></div>
                            <p style="color: #999; font-size: 14px; margin: 15px 0;"><?php echo htmlspecialchars($domain['description'] ?? 'Premium domain name'); ?></p>
                            <div style="display: flex; gap: 10px;">
                                <?php if ($auth->isLoggedIn()): ?>
                                    <a href="offer.php?domain_id=<?php echo $domain['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;">Make Offer</a>
                                    <a href="payment.php?domain_id=<?php echo $domain['id']; ?>" class="btn btn-success" style="flex: 1; text-align: center;">Buy Now</a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary" style="flex: 1; text-align: center;">Login to Offer</a>
                                    <a href="login.php" class="btn btn-success" style="flex: 1; text-align: center;">Login to Buy</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="slider-controls">
                    <button class="slider-btn slider-btn-prev">← Previous</button>
                    <button class="slider-btn slider-btn-next">Next →</button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div style="background: var(--secondary); padding: 20px; border-radius: 8px; margin-bottom: 40px;">
            <form method="GET" class="filter-form">
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Min Price</label>
                        <input type="number" name="min_price" placeholder="0" value="<?php echo $min_price; ?>" min="0" step="0.01">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Max Price</label>
                        <input type="number" name="max_price" placeholder="999999" value="<?php echo $max_price; ?>" min="0" step="0.01">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Domain Type</label>
                        <select name="type">
                            <option value="">All Types</option>
                            <option value="premium" <?php echo $domain_type === 'premium' ? 'selected' : ''; ?>>Premium</option>
                            <option value="normal" <?php echo $domain_type === 'normal' ? 'selected' : ''; ?>>Normal</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Available Domains -->
        <div id="domains">
            <h2 style="color: #00d4ff; margin-bottom: 30px; font-size: 28px;">Available Domains</h2>
            
            <?php if (empty($domains)): ?>
            <div class="alert alert-info">
                No domains found matching your criteria. <a href="index.php" style="color: #17a2b8; text-decoration: underline;">Clear filters</a>
            </div>
            <?php else: ?>
            <div class="grid grid-4">
                <?php foreach ($domains as $domain): ?>
                <div class="domain-card <?php echo $domain['domain_type'] === 'premium' ? 'premium' : ''; ?>">
                    <div class="domain-name"><?php echo htmlspecialchars($domain['domain_name']); ?></div>
                    <div class="domain-price"><?php echo formatPrice($domain['price']); ?></div>
                    <div class="domain-status"><?php echo getStatusBadge($domain['status']); ?></div>
                    <p style="color: #999; font-size: 13px; margin: 15px 0; min-height: 40px;"><?php echo htmlspecialchars(substr($domain['description'] ?? '', 0, 80)); ?></p>
                    <div style="display: flex; gap: 10px; margin-top: auto;">
                        <?php if ($auth->isLoggedIn()): ?>
                            <a href="offer.php?domain_id=<?php echo $domain['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 8px;">Offer</a>
                            <a href="payment.php?domain_id=<?php echo $domain['id']; ?>" class="btn btn-success" style="flex: 1; text-align: center; padding: 8px;">Buy</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary" style="flex: 1; text-align: center; padding: 8px;">Offer</a>
                            <a href="login.php" class="btn btn-success" style="flex: 1; text-align: center; padding: 8px;">Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php echo getPaginationLinks($page, $total_pages, 'index.php'); ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About OryZenX</h3>
                    <p>Premium domain marketplace connecting buyers and sellers worldwide.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#domains">Browse Domains</a></li>
                        <?php if ($auth->isLoggedIn()): ?>
                            <li><a href="profile.php">My Profile</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <p>Supported by Namecheap | GoDaddy | Hostinger</p>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>Email: <?php echo SITE_EMAIL; ?></p>
                    <p><a href="#contact" style="color: #00d4ff;">Contact Form</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> OryZenX Domain Marketplace. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
