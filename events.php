<?php 
require_once './includes/auth_check.php';
require_once './config/db_config.php';
include './templates/header.php';

//// extragem toate evenimentele din baza de date

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt -> fetchAll();
?>

<h2>Evenimente Disponibile</h2>

<div class="events-grid">
    <?php foreach ($events as $event): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
            <p><strong>Data:</strong> <?php echo $event['event_date']; ?></p>
            <p><strong>Locație:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
            <a href="event_detail.php?id=<?php echo $event['id']; ?>" class="btn btn-detail">Vezi detalii</a>
            
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <hr>
                <a href="participants.php?id=<?php echo $event['id']; ?>" class="admin-link participants-link">👥 Vezi Participanți</a>
                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="admin-link edit-link">Editează</a>
                <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="admin-link delete-link">Șterge</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include './templates/footer.php'; ?>