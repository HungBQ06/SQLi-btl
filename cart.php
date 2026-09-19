<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 1. Add to Cart
if ($action === 'add') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : (isset($_GET['qty']) ? intval($_GET['qty']) : 1);
    if ($qty < 1) $qty = 1;

    if ($id > 0) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
        $_SESSION['cart_msg'] = "Product added to cart successfully!";
    }
    header("Location: /cart.php");
    exit;
}

// 2. Remove Item
if ($action === 'remove') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: /cart.php");
    exit;
}

// 3. Update Quantities
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $id => $qty) {
            $id = intval($id);
            $qty = intval($qty);
            if ($qty > 0) {
                $_SESSION['cart'][$id] = $qty;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
    header("Location: /cart.php");
    exit;
}

// 4. Checkout
if ($action === 'checkout') {
    if (!empty($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        $order_id = 'ORD-' . rand(10000, 99999);
        $_SESSION['checkout_success'] = $order_id;
    }
    header("Location: /cart.php");
    exit;
}

// Fetch products in cart from database
$cart_products = [];
$total_price = 0;
$total_items = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $id_list = implode(',', array_map('intval', $ids));
    $query = "SELECT * FROM products WHERE id IN ($id_list);";
    $result = @mysqli_query($conn, $query);

    if ($result) {
        while ($p = mysqli_fetch_assoc($result)) {
            $p['qty'] = $_SESSION['cart'][$p['id']];
            $p['subtotal'] = $p['price'] * $p['qty'];
            $total_price += $p['subtotal'];
            $total_items += $p['qty'];
            $cart_products[] = $p;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - SWAG SHOP</title>
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

        <!-- Checkout Success Alert -->
        <?php if (isset($_SESSION['checkout_success'])): ?>
            <div style="background: #f0fdf4; border: 2px solid #22c55e; color: #15803d; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h2 style="margin: 0 0 8px 0;">🎉 Order Placed Successfully!</h2>
                <p>Your order tracking number is: <strong><?php echo htmlspecialchars($_SESSION['checkout_success']); ?></strong>. Your order is now being processed for shipping.</p>
            </div>
            <?php unset($_SESSION['checkout_success']); ?>
        <?php endif; ?>

        <h2>🛒 Your Shopping Cart (<?php echo $total_items; ?> <?php echo $total_items === 1 ? 'item' : 'items'; ?>)</h2>
        <br>

        <?php if (empty($cart_products)): ?>
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 40px; text-align: center;">
                <p style="font-size: 18px; color: #64748b; margin-bottom: 20px;">Your shopping cart is currently empty!</p>
                <a href="/lab1-union-sqli/" class="btn-view" style="padding: 12px 24px; font-size: 15px;">&larr; Continue Shopping</a>
            </div>
        <?php else: ?>
            <form method="POST" action="/cart.php?action=update">
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 14px;">
                                <th style="padding: 14px 20px;">Product</th>
                                <th style="padding: 14px;">Price</th>
                                <th style="padding: 14px;">Quantity</th>
                                <th style="padding: 14px;">Subtotal</th>
                                <th style="padding: 14px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_products as $item): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 16px 20px; display: flex; align-items: center; gap: 16px;">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                        <div>
                                            <a href="/product.php?id=<?php echo $item['id']; ?>" style="font-weight: bold; color: #0f172a; text-decoration: none;"><?php echo htmlspecialchars($item['name']); ?></a>
                                            <div style="font-size: 12px; color: #64748b;"><?php echo htmlspecialchars($item['category']); ?></div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px; font-weight: 500; color: #334155;">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td style="padding: 16px;">
                                        <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['qty']; ?>" min="1" max="99" style="width: 60px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; text-align: center; font-weight: bold;">
                                    </td>
                                    <td style="padding: 16px; font-weight: bold; color: #ff6600;">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td style="padding: 16px; text-align: center;">
                                        <a href="/cart.php?action=remove&id=<?php echo $item['id']; ?>" style="color: #ef4444; text-decoration: none; font-weight: bold; font-size: 14px;">❌ Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px;">
                    <button type="submit" style="background: #cbd5e1; color: #334155; border: none; padding: 12px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">🔄 Update Cart</button>
                    <div style="text-align: right;">
                        <div style="font-size: 20px; font-weight: bold; color: #0f172a; margin-bottom: 8px;">
                            Total: <span style="color: #ff6600;">$<?php echo number_format($total_price, 2); ?></span>
                        </div>
                        <a href="/cart.php?action=checkout" class="btn-view" style="padding: 12px 28px; font-size: 16px; font-weight: bold; display: inline-block;">💳 Proceed to Checkout</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
