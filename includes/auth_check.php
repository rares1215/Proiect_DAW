<?php 
if(session_status()===PHP_SESSION_NONE){
    session_start();
}

//verificam daca clientul este logat pentru a avea acces.
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
?>