<?php
    session_start();
    // role, která může skript spouštět
    $role = "administrator";

    if(!isset($_SESSION[session_id()]) && $_SESSION["role"] != $role){ header("Location: ../../index.php"); exit(); }

    $id_clanku = htmlentities($_POST["id"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $base_path = "../../";

    if(!require($base_path . 'db.php')){die("Problém s připojením.");}

    try
    {
        $querySoubory = $pdo->prepare("SELECT cesta FROM verze WHERE id_clanku = ?");
        $params = array($id_clanku);
        $querySoubory -> execute($params);
    }
    catch(PDOException $ex)
    {
        die($ex -> getMessage());
    }

    while($radek = $querySoubory->fetch(PDO::FETCH_ASSOC))
    {
        $cesta = $radek["cesta"];
        if(!unlink($base_path . $cesta))
        {
            die("Chyba v mazání souboru...");
        }
    }

    try
    {
        $queryYEET = $pdo->prepare("DELETE FROM clanek WHERE id_clanku = ?");
        $params = array($id_clanku);
        $queryYEET -> execute($params);
    }
    catch(PDOException $ex)
    {
        die($ex -> getMessage());
    }
?>