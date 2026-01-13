<?php 
require_once './includes/auth_check.php';
require_once './config/db_config.php';
include './templates/header.php';

//// extragem toate evenimentele din baza de date

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt -> fetchAll();
?>

<h2>Evenimente Disponibile</h2>

<div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php foreach ($events as $event): ?>
        <div class="card" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
            <p><strong>Data:</strong> <?php echo $event['event_date']; ?></p>
            <p><strong>Locație:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
            <a href="event_detail.php?id=<?php echo $event['id']; ?>" class="button" style="background: #2c3e50; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.9em;">Vezi detalii</a>
            
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <hr>
                <a href="participants.php?id=<?php echo $event['id']; ?>" style="color: purple; font-weight: bold;">👥 Vezi Participanți</a> |
                <a href="edit_event.php?id=<?php echo $event['id']; ?>" style="color: blue;">Editează</a> | 
                <a href="delete_event.php?id=<?php echo $event['id']; ?>" style="color: red;" onclick="return confirm('Sigur vrei să ștergi?')">Șterge</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include './templates/footer.php'; ?>