<?php
require_once './config/db_config.php';
require_once './includes/mailer.php';
include './templates/header.php';

$message = "";
$messageClass = "";
$email = isset($_GET['email']) ? $_GET['email'] : '';

if($email){
    $stmt = $pdo ->prepare("SELECT id FROM users WHERE email = ? AND is_active = 0");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user){
        $new_v_code = bin2hex(random_bytes(16));
        $now = date('Y-m-d H:i:s');

        $update = $pdo->prepare("UPDATE users SET verification_code = ?, v_code_at = ? WHERE id = ?");
        if ($update->execute([$new_v_code, $now, $user['id']])) {
            if (sendVerificationEmail($email, $new_v_code)) {
                $message = "Un cod nou a fost trimis pe adresa de email.";
                $messageClass = "alert-success";
            } else {
                $message = "Eroare la trimiterea email-ului.";
                $messageClass = "alert-danger";
            }
        }
    }else {
        $message = "Acest cont nu există sau este deja activat.";
        $messageClass = "alert-danger";
    }
}
?>

<div class="container">
    <h2>Retrimite Cod de Verificare</h2>
    <?php if ($message): ?>
        <div class="alert <?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <p>Verifică adresa de email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
    <a href="verify.php?email=<?php echo urlencode($email); ?>" class="button">Înapoi la verificare</a>
</div>

<?php include './templates/footer.php'; ?>