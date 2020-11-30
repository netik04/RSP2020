<?php

$base_path = "../../";

$role = "redaktor";
session_start();
if($role !== $_SESSION['role']) die();

//session_start();

if (isset($_REQUEST["id"]) && isset($_REQUEST["verze"]) && isset($_REQUEST["duvod"])
    && isset($_SESSION[session_id()]) && require($base_path."db.php")) {
    
    $data1 = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"],
        'duvod' => $_REQUEST["duvod"],
        'login' => $_SESSION[session_id()]
    ];

    $data2 = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"]
    ];

    $sql = "INSERT INTO zprava (id_clanku, verze, datum_cas, text_zpravy, login, interni, duvod) VALUES".
        "(:id, :verze, Now(), :duvod, :login, 0, 1);";

    $stmt = $pdo->prepare($sql);
    try{
        $stmt->execute($data1);
        if($stmt->rowCount() != 1){
            echo(0);
            $pdo = null;
            die();
        }
    }
    catch(PDOException $e){
        //echo($e);
        echo(0);
        $pdo = null;
        die();
    }

    $sql = "UPDATE verze SET stav_redaktor = 'Probíhá úprava textu autorem', stav_autor = 'Vráceno k úpravě' WHERE id_clanku = :id AND verze = :verze";
    $stmt = $pdo->prepare($sql);
    try{
        $stmt->execute($data2);
        if($stmt->rowCount() == 1)
            echo(1);
        else
            echo(0);
    }
    catch(PDOException $e){
        //echo($e);
        echo(0);
    }

    $pdo = null;
    die();
}
echo(0); //return false;
die();
