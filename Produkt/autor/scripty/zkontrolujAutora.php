<?php
    // script, sloužící pro kontrolu, zdali login autora existuje
    // tento script je volán z hlavního formuláře pomocí AJAXu a přes echo vrací několik možných variant
    session_start();
    
    // připojím se do DB
    require_once '../../db.php';

    // stáhnu si login
    $login = $_GET["login"];

    // pokud je zadaný login hlavním autorem článku
    if($login == $_SESSION[session_id()])
    {
        // oznámím, že jde o stejného autora a vrátím se
        echo("stejny_autor");
        exit();
    }

    // pokud to není hlavní autor
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
        // oznámím scriptu, že neexistuje
        echo("neexistuje");
    }
    // pokud login existuje
    else
    {
        // zjistím, jakou roli má daný uživatel
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
        // pokud je daný uživatel autor
        if($query->fetchColumn(0) == "autor")
        {
            // může být uveden u článku - vrátím true
            echo("true");
        }
        // pokud není autor
        else
        {
            // oznámím scriptu, že není autorem
            echo("neni_autor");
        }
    }
?>