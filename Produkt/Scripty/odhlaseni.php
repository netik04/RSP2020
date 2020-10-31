<?php
session_start();
unset($_SESSION[session_id()]);
header("Location: ../index.php");
die();
?>