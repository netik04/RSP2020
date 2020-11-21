<?php
    $id = $_POST["id"];
    $verze = $_POST["verze"];

    $base_path = "../../";

    if(!require($base_path . 'db.php')){die("Problém s připojením.");}

    try{
        $query = $pdo->prepare("SELECT max(verze) FROM verze where id_clanku = ?");
        $query->execute(array($id));
    }catch(PDOException $e){
        die($e->getMessage());
    }

    $maxVerze =  $query->fetch(PDO::FETCH_COLUMN);
    
    if($verze == 1 || $verze < $maxVerze){
        die("Lze smazat pouze posledni verzi. Pokud chcete smazat 1. verzi, smazte clanek");
    }

    try{
        $query = $pdo->prepare("SELECT cesta FROM verze WHERE id_clanku = ? AND verze = ?");
        $params = array($id, $verze);
        $query->execute($params);
    }catch(PDOException $e){
        die($e->getMessage());
    }

    $cesta = $query->fetch(PDO::FETCH_COLUMN);
    if(!unlink($base_path . $cesta)) {
        die("Chyba v mazání souboru...");
    }

    try{
        $query = $pdo->prepare("DELETE from verze WHERE id_clanku = ? AND verze = ?");
        $params = array($id, $verze);
        $query->execute($params);
    }catch(PDOException $e){
        die($e->getMessage());
    }

    try{
        $query = $pdo->prepare("UPDATE verze SET stav_autor='Vráceno k úpravě', stav_redaktor = 'Probíhá úprava textu autorem' WHERE id_clanku = ? AND verze = ?");
        $params = array($id, $verze-1);
        $query->execute($params);
    }catch(PDOException $e){
        die($e->getMessage());
    }

?>