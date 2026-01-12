<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
};


// verificam daca clientul este logat si este  admin.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>