<?php
session_start();
session_unset(); // Șterge toate variabilele din sesiune
session_destroy(); // Distruge sesiunea complet

header("Location: login.php");
exit;