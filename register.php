<?php
require_once './config/db_config.php';
require_once 'includes/mailer.php';
include './templates/header.php';

// un array unde punem toate errorile
$errors = [];

/// verificam daca form-ul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];



    // validarea inputurilor

    ///lungimea usernameului
    if (strlen($user) <=6){
        $errors[] = "Numele de utilizator trebuie sa contina minim 6 caractere";
    };
    // verificare format email
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresa de email nu este valida";
    };

    // lungime parola

    if(strlen($password)<=10){
        $errors[] = "Parola trebuie sa contina cel putin 10 caractere";
    };

    // Verificam ca username-ul si emailul sa fie unique
    $checkemail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkuser = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $checkemail->execute([$email]);
    if($checkemail->fetch()){
        $errors[] = "Acest email este deja folosit";
    };

    $checkuser->execute([$user]);
    if($checkuser->fetch()){
        $errors[] = "Acest username este deja folosit";
    };


    ///// Salvam formul doar daca nu aveam nici o eroare.

    if(empty($errors)){

        /// criptam parola inainte de a o pune in baza de date
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // generam cod de verificare prin email
        $v_code = bin2hex(random_bytes(16));
        $time_code_sended = date('Y-m-d H:i:s');

        /// Pregatim inserarea in SQL cu pdo (pentru prevenire sql injection)
        $sql = "INSERT INTO users (username, email, password, verification_code,v_code_at) VALUES (?, ?, ?, ?,?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$user, $email, $hashed_password, $v_code,$time_code_sended]);
            
            if (sendVerificationEmail($email, $v_code)) {
                header("Location: verify.php?email=" . urlencode($email));
                exit;
            } else {
                // Dacă mail-ul eșuează, informăm admin-ul/user-ul
                $errors[] = "Contul a fost creat, dar mail-ul de verificare nu a putut fi trimis. Contactați administratorul.";
            }
        } catch (PDOException $e) {
            $errors[] = "Eroare la baza de date: " . $e->getMessage();
        }
    }
}
?>

<h2>Înregistrare Utilizator</h2>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form action="register.php" method="POST">
    <div class="form-group">
        <label>Nume utilizator:</label>
        <input 
        type="text" 
        name="username" 
        required
        autocomplete="off"
        >
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input 
        type="email" 
        name="email" 
        required
        autocomplete="off"
        >
    </div>
    <div class="form-group">
        <label>Parolă:</label>
        <input 
        type="password" 
        name="password" 
        required
        autocomplete="off"
        >
    </div>
    <button type="submit">Creează cont</button>
</form>

<?php 
include './templates/footer.php'; 
?>