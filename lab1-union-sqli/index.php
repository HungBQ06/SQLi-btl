<?php
// =========================================================================
// LAB 1: UNION-based SQL Injection
// Vulnerable Parameter: `category` (GET Parameter)
// Protected Parameter:  `TrackingId` (Cookie - Prepared Statement)
// =========================================================================

session_start();
require_once '../config/db.php';

// -------------------------------------------------------------------------
// 1. COOKIE HANDLING & SAFE TRACKING QUERY (Prepared Statement)
// -------------------------------------------------------------------------
if (!isset($_COOKIE['TrackingId'])) {
    $tracking_id = bin2hex(random_bytes(8));
    setcookie('TrackingId', $tracking_id, time() + (86400 * 30), '/');
} else {
    $tracking_id = $_COOKIE['TrackingId'];
}

// Safe Query: TrackingId parameter is protected by Prepared Statements in Lab 1
$tracking_stmt = mysqli_prepare($conn, "SELECT * FROM tracking_logs WHERE tracking_id = ?");
if ($tracking_stmt) {
    mysqli_stmt_bind_param($tracking_stmt, "s", $tracking_id);
    mysqli_stmt_execute($tracking_stmt);
    mysqli_stmt_close($tracking_stmt);
}

// -------------------------------------------------------------------------
// 2. CATEGORY FILTER & VULNERABLE PRODUCT QUERY (String Concatenation)
// -------------------------------------------------------------------------
$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$products = [];

if ($category === 'All' || empty($category)) {
    $executed_sql = "SELECT id, name, price, image_url FROM products WHERE released = 1;";
} else {
    // VULNERABLE QUERY: Direct string concatenation allows UNION-based SQL Injection
    $executed_sql = "SELECT id, name, price, image_url FROM products WHERE category = '$category' AND released = 1;";
}

$result = @mysqli_query($conn, $executed_sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
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
    <title>SWAG SHOP - Product Catalog (Lab 1)</title>
    <link rel="stylesheet" href="/assets/css/portswigger.css">
</head>
<body>

    <div class="store-container">
        <!-- Store Brand Bar -->
        <header class="store-header">
            <a href="/lab1-union-sqli/" class="store-brand">SWAG SHOP</a>
            <nav class="store-nav">
                <a href="/" style="color: #64748b; font-weight: bold;">&larr; Lab Portal</a>
                <a href="/lab1-union-sqli/">Shop</a>
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
                        // Col 1 (id) expects INT: If a non-numeric string (like 'users' or 'table_name') is injected in Col 1 -> Type Mismatch -> Skip
                        if (isset($p['id']) && $p['id'] !== null && $p['id'] !== '' && !is_numeric($p['id'])) continue;
                        
                        // Must have at least something to display
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
