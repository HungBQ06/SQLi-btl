<?php
session_start();
require_once 'config/db.php';

$message = '';
$error = '';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user']);
    session_destroy();
    header('Location: /my-account.php');
    exit;
}

// Handle Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        // Simple authentication query
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password';";
        $result = @mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $_SESSION['user'] = $user_data;
            $message = "Logged in successfully!";
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Please enter both Username and Password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - SWAG SHOP</title>
    <link rel="stylesheet" href="/assets/css/portswigger.css">
</head>
<body>

    <div class="store-container">
        <header class="store-header">
            <a href="/lab1-union-sqli/" class="store-brand">SWAG SHOP</a>
            <nav class="store-nav">
                <a href="/lab1-union-sqli/">Home</a>
                <a href="/my-account.php" style="color: #ff6600; font-weight: bold;">My account</a>
            </nav>
        </header>

        <?php if (isset($_SESSION['user'])): ?>
            <!-- User Dashboard when logged in -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px;">
                    <div>
                        <h1 style="color: #0f172a; margin: 0;">My Account Dashboard</h1>
                        <p style="color: #64748b; margin-top: 4px;">Welcome back, <strong style="color: #ff6600;"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong>!</p>
                    </div>
                    <a href="/my-account.php?action=logout" style="background: #ef4444; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px;">Logout</a>
                </div>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="color: #334155; margin-bottom: 12px;">👤 Account Details</h3>
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; font-weight: bold; color: #64748b; width: 140px;">Username:</td>
                            <td style="padding: 10px; color: #0f172a; font-family: monospace; font-size: 15px;"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; font-weight: bold; color: #64748b;">Email:</td>
                            <td style="padding: 10px; color: #0f172a;"><?php echo htmlspecialchars(isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : 'admin@sqli-lab.local'); ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; font-weight: bold; color: #64748b;">Role:</td>
                            <td style="padding: 10px;"><span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;"><?php echo htmlspecialchars(isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'user'); ?></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold; color: #64748b;">Secret API Key:</td>
                            <td style="padding: 10px; color: #22c55e; font-family: monospace; font-weight: bold;"><?php echo md5($_SESSION['user']['username'] . '_secret_key'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <!-- Login Form when not logged in -->
            <div style="max-width: 450px; margin: 0 auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <h2 style="color: #0f172a; margin-bottom: 6px;">Login to Your Account</h2>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Please enter your account credentials below.</p>

                <?php if (!empty($error)): ?>
                    <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px; border-radius: 4px; font-size: 14px; margin-bottom: 20px;">
                        ❌ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: bold; color: #475569; font-size: 14px; margin-bottom: 6px;">Username:</label>
                        <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 15px;">
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: bold; color: #475569; font-size: 14px; margin-bottom: 6px;">Password:</label>
                        <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 15px;">
                    </div>
                    <button type="submit" class="btn-view" style="width: 100%; border: none; padding: 12px; font-size: 16px; cursor: pointer;">Log in</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
