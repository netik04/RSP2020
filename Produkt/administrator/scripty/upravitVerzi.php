<?php

    $id = $_POST["id"];
    $verze = $_POST["verze"];
    $stavAutor = $_POST["stavAutor"];
    $stavRedaktor = $_POST["stavRedaktor"];
    $datum = $_POST["datum"];

    if(!require '../../db.php') die("Chyba v DB");

    try
    {
        $query = $pdo->prepare("UPDATE verze SET stav_autor = ?, stav_redaktor = ?, datum = ? WHERE id_clanku = ? AND verze = ?");
        $params = array($stavAutor, $stavRedaktor, $datum, $id, $verze);
        $query -> execute($params);
    }
    catch(PDOException $ex)
    {
        die("Došlo k chybě zkukste to prosím za chvíli");
    }
?>