<?php
    // kontrolní script, jestli zadaný login již v DB existuje
    
    // připojím se do DB
    require_once '../db.php';

    // stáhnu si login
    $login = $_GET["login"];

    // zjistím, kolik uživatelů existuje se zadaným loginem
    try
    {
        $query = $pdo -> prepare("SELECT COUNT(*) FROM uzivatel WHERE login = ?");
        $params = array($login);
        $query -> execute($params);
    }
    catch(PDOException $ex)
    {
        die($ex -> getMessage());
    }

    // pokud login neexistuje
    if($query -> fetchColumn(0) == 0)
    {
        // vrátím false
        echo("false");
    }
    else
    {
        // pokud existuje, vracím true
        echo("true");
    }
?>