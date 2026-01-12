<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Organizare Evenimente</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<nav>
    <div class="nav-content">
        <div class="logo"><a href="index.php" style="color:white; text-decoration:none;">Evenimente Pro</a></div>
        <div class="links">
            <a href="index.php">Acasă</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="./admin_dashboard.php" style="color: #f1c40f;">Panou Admin</a>
                <?php endif; ?>
                <a href="events.php">Events</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Înregistrare</a>
            <?php endif; ?>
            
        </div>
    </div>
</nav>
    <div class="container">