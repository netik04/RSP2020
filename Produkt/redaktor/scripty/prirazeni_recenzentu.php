<?php

$base_path = "../../";

if (isset($_REQUEST["id"]) && isset($_REQUEST["verze"]) && isset($_REQUEST["uzaverka"])
    && isset($_REQUEST["r1"]) && isset($_REQUEST["r2"]) && require($base_path."db.php")) {
    
    $data1 = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"],
        'uzaverka' => $_REQUEST["uzaverka"],
        'r1' => $_REQUEST["r1"],
        'r2' => $_REQUEST["r2"]
    ];

    $data2 = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"]
    ];

    $sql1 = "INSERT INTO posudek (id_clanku, verze, datum_uzaverky, login_recenzenta) VALUES".
        "(:id, :verze, :uzaverka, :r1),".
        "(:id, :verze, :uzaverka, :r2);";
    $sql2 = "UPDATE verze SET stav_redaktor = 'Probíhá recenzní řízení' WHERE id_clanku = :id AND verze = :verze";


    $success = 1;

    $stmt = $pdo->prepare($sql1);
    try{
        $stmt->execute($data1);
        if($stmt->rowCount() != 2)
            $success = 0;
    }
    catch(PDOException $e){
        $success = 0;
        echo($e);
    }

    if($success){
        $stmt = $pdo->prepare($sql2);
        try{
            $stmt->execute($data2);
            if($stmt->rowCount() != 1)
                $success = 0;
        }
        catch(PDOException $e){
            $success = 0;
            echo($e);
        }
    }
    echo($success);

    $pdo = null;
    die();
}
echo(0); //return false;
die();
