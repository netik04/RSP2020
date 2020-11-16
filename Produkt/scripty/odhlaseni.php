<?php // script zarizujici odhlaseni uzivatele 
session_start(); // start session
session_destroy(); // zruseni vsech promennych v session
header("Location: ../index.php"); // presmerovani na index 
die();
?>