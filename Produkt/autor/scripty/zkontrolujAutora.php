<?php
session_start();
    // kontrolní script, jestli zadaný login již v DB existuje
    
    // připojím se do DB
    require_once '../../db.php';

    // stáhnu si login
    $login = $_GET["login"];

    if($login == $_SESSION[session_id()])
    {
        echo("stejny_autor");
        exit();
    }

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
        echo("neexistuje");
    }
    else
    {
        try
        {
            $query = $pdo -> prepare("SELECT role FROM uzivatel WHERE login = ?");
            $params = array($login);
            $query -> execute($params);
        }
        catch(PDOException $ex)
        {
            die($ex -> getMessage());
        }

        if($query->fetchColumn(0) == "autor")
        {
            echo("true");
        }
        else
        {
            echo("neni_autor");
        }
    }
?>