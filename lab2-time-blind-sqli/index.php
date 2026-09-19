<?php
// =========================================================================
// LAB 2: Time-based Blind SQL Injection
// Vulnerable Parameter: `TrackingId` (Cookie - String Concatenation)
// Protected Parameter:  `category` (GET Parameter - Prepared Statement)
// =========================================================================

// Disable PHP MySQL exception reporting to enforce TRUE Blind SQLi
mysqli_report(MYSQLI_REPORT_OFF);
error_reporting(0);
ini_set('display_errors', '0');

session_start();
require_once '../config/db.php';

// -------------------------------------------------------------------------
// 1. COOKIE HANDLING & VULNERABLE TRACKING QUERY (String Concatenation)
// -------------------------------------------------------------------------
if (!isset($_COOKIE['TrackingId'])) {
    $tracking_id = bin2hex(random_bytes(8));
    setcookie('TrackingId', $tracking_id, time() + (86400 * 30), '/');
} else {
    $tracking_id = $_COOKIE['TrackingId'];
}

// VULNERABLE QUERY: TrackingId parameter allows Time-based Blind SQL Injection
$tracking_sql = "SELECT * FROM tracking_logs WHERE tracking_id = '$tracking_id';";
@mysqli_query($conn, $tracking_sql);

// -------------------------------------------------------------------------
// 2. CATEGORY FILTER & SAFE PRODUCT QUERY (Prepared Statement)
// -------------------------------------------------------------------------
$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$products = [];

if ($category === 'All' || empty($category)) {
    // Safe Query: Uses Prepared Statement
    $stmt = mysqli_prepare($conn, "SELECT id, name, price, image_url FROM products WHERE released = 1;");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Safe Query: Category parameter is protected by Prepared Statements in Lab 2
    $stmt = mysqli_prepare($conn, "SELECT id, name, price, image_url FROM products WHERE category = ? AND released = 1;");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $category);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// -------------------------------------------------------------------------
// 3. CART COUNT CALCULATION
// -------------------------------------------------------------------------
$total_items = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $total_items = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWAG SHOP - Product Catalog (Lab 2)</title>
    <link rel="stylesheet" href="/assets/css/portswigger.css">
</head>
<body>

    <div class="store-container">
        <!-- Store Brand Bar -->
        <header class="store-header">
            <a href="/lab2-time-blind-sqli/" class="store-brand">SWAG SHOP</a>
            <nav class="store-nav">
                <a href="/" style="color: #64748b; font-weight: bold;">&larr; Lab Portal</a>
                <a href="/lab2-time-blind-sqli/">Shop</a>
                <a href="/my-account.php">My account</a>
                <a href="/cart.php" style="color: #ff6600; font-weight: bold;">🛒 Cart (<?php echo $total_items; ?>)</a>
            </nav>
        </header>

        <!-- Categories Navigation Filter -->
        <div class="category-bar">
            <span>Filter:</span>
            <a href="?category=All" class="cat-btn <?php echo ($category === 'All' || empty($category)) ? 'active' : ''; ?>">All</a>
            <a href="?category=Gifts" class="cat-btn <?php echo $category === 'Gifts' ? 'active' : ''; ?>">Gifts</a>
            <a href="?category=Clothing" class="cat-btn <?php echo $category === 'Clothing' ? 'active' : ''; ?>">Clothing</a>
            <a href="?category=Tech" class="cat-btn <?php echo $category === 'Tech' ? 'active' : ''; ?>">Tech</a>
            <a href="?category=Footwear" class="cat-btn <?php echo $category === 'Footwear' ? 'active' : ''; ?>">Footwear</a>
        </div>

        <!-- Product Cards Grid -->
        <h2>Refined category: <?php echo htmlspecialchars($category); ?></h2>
        <br>

        <?php if (empty($products)): ?>
            <p style="color: #64748b;">No products found in category "<?php echo htmlspecialchars($category); ?>".</p>
        <?php else: ?>
            <div class="product-list">
                <?php foreach ($products as $p): ?>
                    <?php 
                        if (isset($p['id']) && $p['id'] !== null && $p['id'] !== '' && !is_numeric($p['id'])) continue;
                        if (empty($p['name']) && empty($p['id']) && empty($p['price']) && empty($p['image_url'])) continue;
                    ?>
                    <div class="product-card">
                        <?php if (!empty($p['image_url'])): ?>
                            <?php if (filter_var($p['image_url'], FILTER_VALIDATE_URL) || strpos($p['image_url'], '/') === 0 || strpos($p['image_url'], 'assets/') === 0): ?>
                                <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['name'] ?? ''); ?>" style="width: 100%; height: 180px; object-fit: cover; border-radius: 4px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                            <?php else: ?>
                                <div style="font-family: monospace; color: #475569; margin-bottom: 8px; font-weight: bold;"><?php echo htmlspecialchars($p['image_url']); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div>
                            <?php if (!empty($p['name'])): ?>
                                <div class="product-title"><?php echo htmlspecialchars($p['name']); ?></div>
                            <?php endif; ?>

                            <?php if (isset($p['price']) && $p['price'] !== '' && $p['price'] !== null): ?>
                                <div class="product-price">
                                    <?php echo is_numeric($p['price']) ? '$' . number_format((float)$p['price'], 2) : htmlspecialchars($p['price']); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($p['id']) && is_numeric($p['id']) && intval($p['id']) > 0): ?>
                            <div style="display: flex; gap: 8px; margin-top: 12px;">
                                <a href="/product.php?id=<?php echo intval($p['id']); ?>" class="btn-view" style="flex: 1;">View details</a>
                                <a href="/cart.php?action=add&id=<?php echo intval($p['id']); ?>" style="background: #22c55e; color: white; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px;">+ Cart</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
