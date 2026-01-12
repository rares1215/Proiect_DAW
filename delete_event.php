<?php
require_once './includes/admin_check.php';
require_once './config/db_config.php';

if(isset($_GET['id'])){
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header('Location: events.php');
exit;
?>