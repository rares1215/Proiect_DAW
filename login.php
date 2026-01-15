<?php
require_once 'config/db_config.php';
session_start(); // pornim session pentru a tine minte utilizatorul


include 'templates/header.php';


$errors = [];


if ($_SERVER['REQUEST_METHOD'] =='POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];


    if(empty($email) || empty($password)){
        $errors[] = 'Te rugam sa completezi toate campurile.';
    }else{
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt -> execute([$email]);
        $user = $stmt ->fetch();

        // verificam daca user ul exista si daca parola este corecta.
        if($user && password_verify($password,$user['password'])){

            // verificam statusul daca contul a fost verificat!
            if($user['is_active'] == 0){
                $errors[] = 'Contul tau nu a fost inca verificat!';
            }else{
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit;
            }
        } else {
            $errors[] = 'email sau parola incorecta!';
        }
    }
}
?>

<h2>Autentificare</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="login.php" method="POST">
    <div class="form-group">
        <label>Email:</label>
        <input autocomplete="off" type="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
    </div>
    <div class="form-group">
        <label>Parolă:</label>
        <input autocomplete="off" type="password" name="password" required>
    </div>
    <button type="submit">Intră în cont</button>
</form>

<p class="login-register-link">Nu ai cont? <a href="register.php">Înregistrează-te aici</a>.</p>

<?php include './templates/footer.php'; ?>