<?php
require_once './config/db_config.php';
include './templates/header.php';

$message = "";
$messageClass = "";

// Preluăm email-ul din URL (trimis de register.php)
$email_from_url = isset($_GET['email']) ? $_GET['email'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $code = trim($_POST['verification_code']);

    // query in baza de date pentru a gasii utilizator cu emailul si codul de verificare corespunzator.
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND verification_code = ? AND is_active = 0");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if ($user) {
        // daca utilizatorul exista si codul este corect atunci "activam" contul si stergem codul
        $update = $pdo->prepare("UPDATE users SET is_active = 1, verification_code = NULL WHERE id = ?");
        $update->execute([$user['id']]);

        $message = "Contul a fost activat cu succes! Acum te poți loga.";
        $messageClass = "alert-success";
    } else {
        $message = "Codul de verificare este incorect sau contul este deja activat.";
        $messageClass = "alert-danger";
    }
}
?>

<h2>Verificare Cont</h2>

<?php if ($message): ?>
    <div class="alert <?php echo $messageClass; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<form action="verify.php" method="POST">
    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email_from_url); ?>" required>
    </div>
    <div class="form-group">
        <label>Cod Verificare (din email):</label>
        <input type="text" name="verification_code" required placeholder="Introdu codul primit pe email">
    </div>
    <button type="submit">Activează Contul</button>
</form>

<?php include './templates/footer.php'; ?>