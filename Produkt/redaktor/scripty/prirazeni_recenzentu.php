<?php

$base_path = "../../";

$role = "redaktor";
session_start();
if($role !== $_SESSION['role']) die();

if (isset($_REQUEST["id"]) && isset($_REQUEST["verze"]) && isset($_REQUEST["uzaverka"])
    && (!empty($_REQUEST['r1']) || !empty($_REQUEST['r2'])) && require($base_path."db.php")) {

    $data1 = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"],
        'uzaverka' => $_REQUEST["uzaverka"]
    ];

    $data2 = [
        'id' => $_REQUEST["id"],
        'verze' => $_REQUEST["verze"]
    ];

    $sql1 = "INSERT INTO posudek (id_clanku, verze, datum_uzaverky, login_recenzenta) VALUES ";
    
    if(!empty($_REQUEST['r1'])){
        $sql1 .= "(:id, :verze, :uzaverka, :r1)";
        $data1 += ['r1' => htmlentities($_REQUEST["r1"], ENT_QUOTES | ENT_HTML5, "UTF-8")];
    }
    if(!empty($_REQUEST['r2'])){
        if(!empty($_REQUEST['r1']))
            $sql1 .=", ";
        $sql1 .= "(:id, :verze, :uzaverka, :r2)";
        $data1 += ['r2' => htmlentities($_REQUEST["r2"], ENT_QUOTES | ENT_HTML5, "UTF-8")];
    }

    $sql2 = "UPDATE verze SET stav_redaktor = 'Probíhá recenzní řízení', stav_autor = 'Předáno recenzentům' WHERE id_clanku = :id AND verze = :verze";


    $success = 1;

    $stmt = $pdo->prepare($sql1);
    try{
        $stmt->execute($data1);
        if($stmt->rowCount() < 1)
            $success = 0;
    }
    catch(PDOException $e){
        $success = 0;
        //echo($e);
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
            //echo($e);
        }
    }
    echo($success);

    $pdo = null;
    die();
}
echo(0); //return false;
die();
