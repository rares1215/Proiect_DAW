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

    $errors = [];

    // verificam valoarea capacitatii
    if($cap <= 0){
        $errors[] = 'Capacitatea trebuie sa fie un numar mai mare de 0';
    }

    // verificam ca data sa nu fie in trecut
    $currentDate = date('Y-m-d H:i:s');
    if($date < $currentDate){
        $errors[] = 'Data la care va avea loc evenimentul nu poate fii in trecut';
    }


    // verificam ca titlul sa fie unic
    $checkTitle = $pdo->prepare("SELECT id FROM events WHERE title = ?");
    $checkTitle->execute([$title]);
    if($checkTitle->fetch()){
        $errors[]= 'Un eveniment cu acest nume exista deja.';
    }

    // verificam ca 2 eveniemnte sa nu aibe loc in aceasi locatie in acelasi timp
    $checkConflict = $pdo->prepare("SELECT id FROM events WHERE location = ? AND event_date = ?");
    $checkConflict->execute([$loc,$date]);

    if($checkConflict->fetch()){
        $errors[] = 'Locatia este deja ocupata la ora selectata';
    }

    // Salvam doar daca nu avem nici o eroare.
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (title, description, location, event_date, max_capacity) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $desc, $loc, $date, $cap]);
            $success = "Evenimentul a fost adăugat cu succes!";
        } catch (PDOException $e) {
            $errors[] = "Eroare la baza de date: " . $e->getMessage();
        }
    }
};
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="admin_dashboard.php" method="POST">
    <div class="form-group">
        <label>Titlu Eveniment:</label>
        <input type="text" name="title" required>
    </div>
    <div class="form-group">
        <label>Descriere:</label>
        <textarea name="description" rows="4" class="textarea-full"></textarea>
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