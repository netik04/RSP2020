<?php

    require_once '../../db.php';

    $id = $_GET["id"];

    try
    {
        $query = $pdo->prepare("SELECT datum_uzaverky, kapacita FROM casopis WHERE id_casopisu = ?");
        $params = array($id);
        $query -> execute($params);
    }
    catch(PDOException $ex)
    {
        die($ex -> getMessage());
    }
    $radek = $query->fetch(PDO::FETCH_ASSOC);
    $datum = $radek["datum_uzaverky"];
    $kapacita = $radek["kapacita"];

    try
    {
        $query = $pdo->prepare("SELECT COUNT(*) FROM clanek WHERE id_casopisu = ?");
        $params = array($id);
        $query->execute($params);
    }
    catch(PDOException $ex)
    {
        die($ex -> getMessage());
    }

    $pocetPrispevku = $query->fetchColumn(0);

    $data = array($datum, $kapacita, $pocetPrispevku);

    echo(json_encode($data));

?>