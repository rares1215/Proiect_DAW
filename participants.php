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
        <table border="1" style="width:100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #eee;">
                    <th style="padding: 10px;">Username</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Data Înscrierii</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $p): ?>
                    <tr>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($p['username']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($p['email']); ?></td>
                        <td style="padding: 10px;"><?php echo date('d.m.Y H:i', strtotime($p['reservation_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Momentan nu s-a înscris nimeni la acest eveniment.</p>
    <?php endif; ?>
</div>

<?php include './templates/footer.php'; ?>