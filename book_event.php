<?php
require_once './includes/auth_check.php';
require_once './config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];

    try {
        // Verificam daca clientul este inscris deja la eveniment
        $checkReserved = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND event_id = ?");
        $checkReserved->execute([$user_id, $event_id]);
        if ($checkReserved->fetch()) {
            header("Location: event_detail.php?id=$event_id");
            exit;
        }

        // 2.Verificam capacitatea evenimentului, daca mai sunt locuri disponibile
        $stmtEvent = $pdo->prepare("SELECT max_capacity FROM events WHERE id = ?");
        $stmtEvent->execute([$event_id]);
        $event = $stmtEvent->fetch();

        // luam numarul de rezervari la evenimentul dat
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE event_id = ?");
        $stmtCount->execute([$event_id]);
        $currentCount = $stmtCount->fetchColumn();

        if ($currentCount >= $event['max_capacity']) {
            header("Location: event_detail.php?id=$event_id");
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, event_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $event_id]);
        
        header("Location: event_detail.php?id=$event_id");
        exit;

    } catch (PDOException $e) {
        die("Eroare la procesare: " . $e->getMessage());
    }
}