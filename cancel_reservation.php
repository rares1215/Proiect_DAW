<?php
require './includes/auth_check.php';
require_once './config/db_config.php';

// Verificăm dacă avem ID-ul în URL
if(isset($_GET['id'])){
    $user_id = $_SESSION['user_id'];
    $event_id = $_GET['id'];

    try{
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
        
        header("Location: event_detail.php?id=$event_id");
        exit;
    } catch (PDOException $e){
        die("Eroare: " . $e->getMessage());
    }
} else {
    header("Location: events.php");
    exit;
}