<?php
// includes/header.php
// Memastikan session dijalankan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'DGITECH'; ?></title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <!-- Vendor (Bootstrap, FontAwesome) -->
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
</head>
<body>
    <!-- Header / Navbar -->
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="/index.php">DGITECH</a>
            </div>
            <nav class="navbar">
                <ul class="nav-list">
                    <li><a href="/index.php">Home</a></li>
                    <li><a href="/shop.php">Shop</a></li>
                    <li><a href="/services.php">Services</a></li>
                    <li><a href="/about.php">About</a></li>
                    <li><a href="/contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/dashboard-customer.php" class="btn btn-sm btn-primary">Dashboard</a>
                    <a href="/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
                <?php else: ?>
                    <a href="/login.php" class="btn btn-sm btn-primary">Login</a>
                    <a href="/register.php" class="btn btn-sm btn-outline-secondary">Register</a>
                <?php endif; ?>
                <a href="/cart.php" class="btn btn-sm btn-icon"><i class="fas fa-shopping-cart"></i></a>
            </div>
        </div>
    </header>
    <main class="site-content">
