<?php
$base_path = "../../";

$role = array("redaktor", "sefredaktor");
session_start();
if(!in_array($_SESSION['role'], $role)) die();
//session_start();

if (isset($_REQUEST["id"]) && isset($_REQUEST["verze"]) && require($base_path."db.php")) {
    $data = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"]
    ];

    $sql = "UPDATE verze SET sefredaktor = NOT sefredaktor WHERE id_clanku = :id AND verze = :verze";
    $stmt = $pdo->prepare($sql);

    try{
        $stmt->execute($data);
        if($stmt->rowCount() == 1)
            echo(1);
        else
            echo(0);
    }
    catch(PDOException $e){
        echo(0);
    }
    $pdo = null;
    die();
}
echo(0); //return false;
die();
