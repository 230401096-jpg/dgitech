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
    <!-- Vendor (Bootstrap, FontAwesome) via CDN for modern UI -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeoYz1FZC2i2Nq6b7rZ9Wl5qk0h5i5YkXc1p5j5Y5f5Y5f5" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
