<?php
$host = "localhost";
$username = "studaci";
$password = "Studaci100*";
$dbname = "studaci";

try {
    $pdo = new PDO("mysql:host=$host;dbname=" . $dbname, $username, $password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
    return true;
} catch (PDOException $e) {
    //echo "Connection failed: " . $e->getMessage();
    return false;
}
