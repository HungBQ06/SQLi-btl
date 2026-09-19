<?php
session_start();
require_once 'config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$product = null;
$query = "SELECT * FROM products WHERE id = $id;";
$result = @mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
}

// Calculate total cart items
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
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Product'; ?> - SWAG SHOP</title>
    <link rel="stylesheet" href="/assets/css/portswigger.css">
</head>
<body>

    <div class="store-container">
        <header class="store-header">
            <a href="/lab1-union-sqli/" class="store-brand">SWAG SHOP</a>
            <nav class="store-nav">
                <a href="/" style="color: #64748b; font-weight: bold;">&larr; Lab Portal</a>
                <a href="/lab1-union-sqli/">Home</a>
                <a href="/my-account.php">My account</a>
                <a href="/cart.php" style="color: #ff6600; font-weight: bold;">🛒 Cart (<?php echo $total_items; ?>)</a>
            </nav>
        </header>

        <?php if ($product): ?>
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 30px; display: flex; gap: 40px; align-items: flex-start; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <!-- Real Product Image -->
                <div style="width: 380px; flex-shrink: 0;">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 350px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>

                <!-- Product Info & Add to Cart -->
                <div style="flex: 1;">
                    <span style="background: #ff6600; color: white; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;">
                        <?php echo htmlspecialchars($product['category']); ?>
                    </span>
                    <h1 style="margin: 12px 0 8px 0; color: #0f172a; font-size: 26px;">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h1>
                    <div style="font-size: 28px; font-weight: bold; color: #ff6600; margin-bottom: 20px;">
                        $<?php echo number_format($product['price'], 2); ?>
                    </div>

                    <!-- Rich Product Description -->
                    <div style="background: #f8fafc; border-left: 4px solid #ff6600; padding: 16px; border-radius: 0 6px 6px 0; margin-bottom: 24px;">
                        <h4 style="color: #334155; margin-bottom: 8px;">📝 Product Description & Specifications:</h4>
                        <p style="color: #475569; font-size: 15px; line-height: 1.7; margin: 0;">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </p>
                    </div>

                    <!-- Add to Cart Form -->
                    <form method="POST" action="/cart.php?action=add&id=<?php echo $product['id']; ?>" style="display: flex; gap: 16px; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <label style="font-weight: bold; color: #475569;">Quantity:</label>
                            <input type="number" name="quantity" value="1" min="1" max="99" style="width: 70px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 16px; font-weight: bold; text-align: center;">
                        </div>
                        <button type="submit" class="btn-view" style="border: none; padding: 14px 28px; font-size: 16px; cursor: pointer;">🛒 Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 20px; border-radius: 8px;">
                <h2>❌ Product Not Found!</h2>
                <p style="margin-top: 8px;">The requested product ID #<?php echo $id; ?> does not exist.</p>
                <br>
                <a href="/lab1-union-sqli/" style="color: #ff6600; font-weight: bold;">&larr; Back to Shop</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
