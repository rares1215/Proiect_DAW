<?php
require_once './includes/admin_check.php';
require_once './config/db_config.php';
include './templates/header.php';

if (!isset($_GET['id'])) {
    header("Location: evenimente.php");
    exit;
}

$id = $_GET['id'];
$error = null;

// 1. Preluăm datele curente ale evenimentului
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    die("Evenimentul nu există.");
}

// 2. Verificam daca s-a trimis form-l
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $loc = trim($_POST['location']);
    $date = $_POST['event_date'];
    $cap = $_POST['max_capacity'];

    if (!empty($title) && !empty($date)) {
        try {
            $sql = "UPDATE events SET title = ?, description = ?, location = ?, event_date = ?, max_capacity = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$title, $desc, $loc, $date, $cap, $id]);
            
            // Refecth pentru datele noi din form
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch();
            header("Location: events.php");
            exit;
        } catch (PDOException $e) {
            $error = "Eroare: " . $e->getMessage();
        }
    }
}
?>

<h2>Editează Eveniment: <?php echo htmlspecialchars($event['title']); ?></h2>

<?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form action="edit_event.php?id=<?php echo $id; ?>" method="POST">
    <div class="form-group">
        <label>Titlu:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
    </div>
    <div class="form-group">
        <label>Descriere:</label>
        <textarea name="description" rows="4" style="width:100%"><?php echo htmlspecialchars($event['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label>Locație:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>">
    </div>
    <div class="form-group">
        <label>Data:</label>
        <input type="datetime-local" name="event_date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required>
    </div>
    <div class="form-group">
        <label>Capacitate:</label>
        <input type="number" name="max_capacity" value="<?php echo $event['max_capacity']; ?>">
    </div>
    <button type="submit">Salvează Modificările</button>
    <a href="evenimente.php" style="margin-left: 10px;">Anulează</a>
</form>

<?php include './templates/footer.php'; ?>