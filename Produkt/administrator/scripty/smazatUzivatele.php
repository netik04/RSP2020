<?php

    $login = htmlentities($_POST["login"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $role = htmlentities($_POST["role"], ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $base_path = "../../";

    require $base_path . "db.php";

    try
    {
        $queryRole = $pdo->prepare("SELECT role FROM uzivatel WHERE login = ?");
        $paramsRole = array($login);
        $queryRole -> execute($paramsRole);
    }
    catch(PDOException $ex)
    {
        die("Nastala chyba. Zkuste to prosím znovu.");
    }

    if($role != $queryRole->fetchColumn(0))
    {
        die("Tento uživatel nemá zadanou roli. Don't mess with me");
    }

    if($role == "autor")
    {        
        try
        {
            $queryPise = $pdo->prepare("SELECT * FROM pise WHERE login = ?");
            $params = array($login);
            $queryPise -> execute($params);
        }
        catch(PDOException $ex)
        {
            die("Nastala chyba. Zkuste to prosím znovu.");
        }

        while(($radek = $queryPise->fetch(PDO::FETCH_ASSOC)) != FALSE)
        {
            try
            {
                $id_clanku = $radek["id_clanku"];
                $queryCount = $pdo->prepare("SELECT COUNT(*) FROM pise WHERE id_clanku = ?");
                $paramsCount = array($id_clanku);
                $queryCount -> execute($paramsCount);
            }
            catch(PDOException $ex)
            {
                die("Nastala chyba. Zkuste to prosím znovu.");
            }
            $pocet = $queryCount -> fetchColumn(0);
            if($pocet == 1)
            {
                try
                {
                    $queryReplace = $pdo->prepare("UPDATE pise SET login = ? WHERE id_clanku = ?");
                    $paramsReplace = array("[deleted]", $id_clanku);
                    $queryReplace -> execute($paramsReplace);
                }
                catch(PDOException $ex)
                {
                    die("Nastala chyba. Zkuste to prosím znovu.");
                }
            }        
        }
    }
    else if($role == "recenzent")
    {
        try
        {
            $queryPosudek = $pdo->prepare("UPDATE posudek SET login_recenzenta = ? WHERE login_recenzenta = ?");
            $params = array("[deleted]", $login);
            $queryPosudek -> execute($params);
        }
        catch(PDOException $ex)
        {
            die("Nastala chyba. Zkuste to prosím znovu.");
        }
    }
    else if($role == "redaktor")
    {
        die("Redaktora nelze smazat!");
    }
    else
    {
        die("Neznámá chyba!");
    }

    try
    {
        $queryYEET = $pdo->prepare("DELETE FROM uzivatel WHERE login = ?");
        $params = array($login);
        $queryYEET -> execute($params);
    }
    catch(PDOException $ex)
    {
        die("Nastala chyba. Zkuste to prosím znovu.");
    }
?>