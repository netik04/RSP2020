<?php

$base_path = "../../";

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
        $pdo = null;
        die();
    }

    //print_r($result);

    $posudky = 0;
    $new_state;

    $sql = "UPDATE verze SET stav_redaktor = ";

    if($stmt->rowCount() == 2){
        if(!empty($result[0]))
            $posudky++;
        if(!empty($result[1]))
            $posudky++;

        if($posudky == 0){
            $sql .= "'Probíhá recenzní řízení'";//2
            $new_state = 2;
        }
        else if($posudky == 1){
            $sql .= "'1. posudek doručen redakci'";//3
            $new_state = 3;
        }
        else{
            $sql .= "'2. posudek doručen redakci'";//4
            $new_state = 4;
        }
    }else{
        $sql .= "'Čeká na stanovení recenzentů'";//1
        $new_state = 1;
    }
    
    $sql .= " WHERE id_clanku = :id AND verze = :verze;";


    $stmt = $pdo->prepare($sql);

    try{
        $stmt->execute($data);
        if($stmt->rowCount() == 1)
            echo($new_state);
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
