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


<div class="event-details" style="max-width: 800px; margin: auto;">
    <a href="events.php" style="display: inline-block; margin-bottom: 20px;">&larr; Înapoi la listă</a>
    
    <h1><?php echo htmlspecialchars($event['title']); ?></h1>
    
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <p><strong>📍 Locație:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
        <p><strong>📅 Data:</strong> <?php echo date('d.m.Y H:i', strtotime($event['event_date'])); ?></p>
        <p><strong>👥 Capacitate:</strong> <?php echo $event['max_capacity']; ?> locuri</p>
    </div>

    <h3>Descriere Completă:</h3>
    <div style="line-height: 1.8; white-space: pre-wrap;">
        <?php echo htmlspecialchars($event['description']); ?>
    </div>

    <?php 

        //verificam daca user-ul este deja participant la eveniment
        $checkQuery = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND event_id = ?");
        $checkQuery->execute([$_SESSION['user_id'], $id]);
        $is_reserved = $checkQuery->fetch();

        /// verificam inainte daca mai sunt locuri disponibile la eveniment
        $countQuery = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE event_id = ?");
        $countQuery->execute([$id]);
        $reservationsNumber = $countQuery->fetchColumn();
    ?>

    <div style="margin-top: 20px; padding: 15px; border: 1px dashed #ccc;">
        <?php if ($is_reserved): ?>
            <p style="color: green; font-weight: bold;">✅ Ești deja înscris la acest eveniment!</p>
            <a href="cancel_reservation.php?id=<?php echo $id; ?>" style="color: red; font-size: 0.8em;">Anulează participarea</a>
        <?php elseif ($reservationsNumber >= $event['max_capacity']): ?>
            <p style="color: red; font-weight: bold;">🚫 Ne pare rău, nu mai sunt locuri disponibile.</p>
        <?php else: ?>
            <form action="./book_event.php" method="POST">
                <input type="hidden" name="event_id" value="<?php echo $id; ?>">
                <button type="submit" class="button" style="background: #27ae60; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;">Participă la Eveniment</button>
            </form>
            <p><small>Locuri disponibile: <?php echo $event['max_capacity'] - $reservationsNumber; ?></small></p>
        <?php endif; ?>
    </div>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="button" style="background: #3498db; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">Editează Eveniment</a>
        </div>
    <?php endif; ?>
</div>

<?php include './templates/footer.php'; ?>