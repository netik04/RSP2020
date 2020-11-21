<?php

    $id_clanku = $_POST["id_cl"];
    $nazev = $_POST["nazev"];
    $id_casopisu = $_POST["id_cas"];

    if(!require '../../db.php') die("Chyba v DB");

    try
    {
        $query = $pdo->prepare("UPDATE clanek SET nazev = ?, id_casopisu = ? WHERE id_clanku = ?");
        $params = array($nazev, $id_casopisu, $id_clanku);
        $query -> execute($params);
    }
    catch(PDOException $ex)
    {
        die("Došlo k chybě zkukste to prosím za chvíli");
    }
?>