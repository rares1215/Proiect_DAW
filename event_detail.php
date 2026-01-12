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

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="button" style="background: #3498db; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">Editează Eveniment</a>
        </div>
    <?php endif; ?>
</div>

<?php include './templates/footer.php'; ?>