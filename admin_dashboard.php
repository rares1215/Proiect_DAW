<?php

require_once './includes/admin_check.php';
require_once './config/db_config.php';
include './templates/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $loc = trim($_POST['location']);
    $date = $_POST['event_date'];
    $cap = $_POST['max_capacity'];
    if(!empty($title) && !empty($date)){
        try{
            $stmt = $pdo->prepare("INSERT INTO events (title, description, location, event_date, max_capacity) VALUES (?, ?, ?, ?, ?)");
            $stmt -> execute([$title,$desc,$loc,$date,$cap]);
            $success = "Evenimentul a fost adăugat cu succes!";
        }catch (PDOException $e){
            $error = "Eroare la salvare: " . $e->getMessage();
        }
    }else {
        $error = "Titlul si data sunt obligatorii!";
    }
};
?>

<?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form action="admin_dashboard.php" method="POST">
    <div class="form-group">
        <label>Titlu Eveniment:</label>
        <input type="text" name="title" required>
    </div>
    <div class="form-group">
        <label>Descriere:</label>
        <textarea name="description" rows="4" style="width:100%"></textarea>
    </div>
    <div class="form-group">
        <label>Locație:</label>
        <input type="text" name="location">
    </div>
    <div class="form-group">
        <label>Data și Ora:</label>
        <input type="datetime-local" name="event_date" required>
    </div>
    <div class="form-group">
        <label>Capacitate maximă:</label>
        <input type="number" name="max_capacity" value="100">
    </div>
    <button type="submit">Creează Eveniment</button>
</form>