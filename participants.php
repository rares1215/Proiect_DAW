<?php
require_once './includes/admin_check.php';
require_once './config/db_config.php';
include './templates/header.php';

if (!isset($_GET['id'])) {
    header("Location: evenimente.php");
    exit;
}

$event_id = $_GET['id'];


$stmtEvent = $pdo->prepare("SELECT title FROM events WHERE id = ?");
$stmtEvent->execute([$event_id]);
$event = $stmtEvent->fetch();

$sql = "SELECT users.username, users.email, reservations.reservation_date
        FROM reservations 
        JOIN users ON reservations.user_id = users.id 
        WHERE reservations.event_id = ?
        ORDER BY reservations.reservation_date DESC";
$stmtPart = $pdo->prepare($sql);
$stmtPart->execute([$event_id]);
$participants = $stmtPart->fetchAll();
?>

<div class="container">
    <a href="events.php">&larr; Înapoi la evenimente</a>
    <h2>Participanți pentru: <?php echo htmlspecialchars($event['title']); ?></h2>
    
    <p>Total înscriși: <strong><?php echo count($participants); ?></strong></p>

    <?php if (count($participants) > 0): ?>
        <table class="participants-table">
            <thead>
                <tr class="table-header">
                    <th>Username</th>
                    <th>Email</th>
                    <th>Data Înscrierii</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['username']); ?></td>
                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($p['reservation_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>Momentan nu s-a înscris nimeni la acest eveniment.</p>
    <?php endif; ?>
</div>

<?php include './templates/footer.php'; ?>