<?php

$base_path = "../../";

$role = "redaktor";
session_start();
if($role !== $_SESSION['role']) die();

if (isset($_REQUEST["id"]) && isset($_REQUEST["verze"]) && require($base_path."db.php")) {
    $data = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"]
    ];

    $sql = "SELECT datum_vytvoreni FROM posudek WHERE id_clanku = :id AND verze = :verze;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    try {
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo(0);
        //echo($e);
        $pdo = null;
        die();
    }

    $sql2 = "SELECT Count(duvod) FROM zprava WHERE id_clanku = :id AND verze = :verze AND duvod = 1;";

    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute($data);

    try {
        $isReturned = $stmt2->fetch(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo(0);
        //echo($e);
        $pdo = null;
        die();
    }

    //print_r($result);

    $posudky = 0;
    $new_state = 0;

    if($isReturned){
        $stav_redaktor = "Probíhá úprava textu autorem";//5
        $stav_autor = "Vráceno k úpravě";
        $new_state = 5;
    }
    else if($stmt->rowCount() == 2){
        if(!empty($result[0]))
            $posudky++;
        if(!empty($result[1]))
            $posudky++;

        if($posudky == 0){
            $stav_redaktor = "Probíhá recenzní řízení";//2
            $stav_autor = "Předáno recenzentům";
            $new_state = 2;
        }
        else if($posudky == 1){
            $stav_redaktor = "1. posudek doručen redakci";//3
            $stav_autor = "Předáno recenzentům";
            $new_state = 3;
        }
        else{
            $stav_redaktor = "Posudky odeslány autorovi";//4
            $stav_autor = "Posudky doručeny";
            $new_state = 4;
        }
    }else{
        $stav_redaktor = "Čeká na stanovení recenzentů";//1
        $stav_autor = "Přijato redakcí";
        $new_state = 1;
    }
    
    $sql = "UPDATE verze SET stav_redaktor = '".$stav_redaktor."', " .
        "stav_autor = '".$stav_autor."' " .
        "WHERE id_clanku = :id AND verze = :verze;";


    $stmt = $pdo->prepare($sql);

    try{
        $stmt->execute($data);
        if($stmt->rowCount() == 1)
            echo($new_state);
        else{
            echo(0);
            $pdo = null;
            die();
        }
    }
    catch(PDOException $e){
        echo(0);
        $pdo = null;
        die();
        //echo($e);
    }

    $sql = "DELETE FROM zprava ".
        "WHERE id_clanku = :id AND verze = :verze AND (duvod = 2 OR duvod = 4)";

    $stmt = $pdo->prepare($sql);

    try{
        $stmt->execute($data);
    }
    catch(PDOException $e){
        //echo(0);
        //echo($e);
    }
    $pdo = null;
    die();
}
echo(0); //return false;
die();
