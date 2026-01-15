<?php
require_once './includes/auth_check.php';
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: events.php");
    exit;
};

require_once './config/db_config.php';
include './templates/header.php';

$id = $_GET['id'];

try{
    $stmt = $pdo -> prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();

    if (!$event) {
        echo "<div class='alert alert-danger'>Evenimentul nu a fost găsit!</div>";
        include './templates/footer.php';
        exit;
    }
}catch (PDOException $e){
    die('Eroare: ' . $e->getMessage());
}
?>

<div class="event-details-wrapper">
    <a href="events.php" class="back-link">&larr; Înapoi la listă</a>
    
    <h1><?php echo htmlspecialchars($event['title']); ?></h1>
    
    <div class="event-info-box">
        <p><strong>📍 Locație:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
        <p><strong>📅 Data:</strong> <?php echo date('d.m.Y H:i', strtotime($event['event_date'])); ?></p>
        <p><strong>👥 Capacitate:</strong> <?php echo $event['max_capacity']; ?> locuri</p>
    </div>

    <h3>Descriere Completă:</h3>
    <div class="event-full-description">
        <?php echo htmlspecialchars($event['description']); ?>
    </div>

    <?php 
        $checkQuery = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND event_id = ?");
        $checkQuery->execute([$_SESSION['user_id'], $id]);
        $is_reserved = $checkQuery->fetch();

        $countQuery = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE event_id = ?");
        $countQuery->execute([$id]);
        $reservationsNumber = $countQuery->fetchColumn();
    ?>

    <div class="event-actions-area">
        <?php if ($is_reserved): ?>
            <p class="status-success">✅ Ești deja înscris la acest eveniment!</p>
            <a href="cancel_reservation.php?id=<?php echo $id; ?>" class="btn-cancel">Anulează participarea</a>
        <?php elseif ($reservationsNumber >= $event['max_capacity']): ?>
            <p class="status-error">🚫 Ne pare rău, nu mai sunt locuri disponibile.</p>
        <?php else: ?>
            <form action="./book_event.php" method="POST">
                <input type="hidden" name="event_id" value="<?php echo $id; ?>">
                <button type="submit" class="btn-participate">Participă la Eveniment</button>
            </form>
            <p><small>Locuri disponibile: <?php echo $event['max_capacity'] - $reservationsNumber; ?></small></p>
        <?php endif; ?>
    </div>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="admin-actions-section">
            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn-edit-event">Editează Eveniment</a>
        </div>
    <?php endif; ?>
</div>

<?php include './templates/footer.php'; ?>