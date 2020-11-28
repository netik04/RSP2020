<?php
    session_start();
    $role = "administrator";
    if(!isset($_SESSION[session_id()]) && $_SESSION["role"] != $role){ header("Location: ../../index.php"); exit(); }

    $base_path = '../../';

    if(!require($base_path . 'db.php')){ die("Nastala chyba. Zkuste to prosím později."); }
    $login = htmlentities($_POST["login"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $id = htmlentities($_POST["id"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $verze = htmlentities($_POST["verze"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $puvodniRecenzent = htmlentities($_POST["login_rec"], ENT_QUOTES | ENT_HTML5, 'UTF-8');

    try
    {
        $query = $pdo->prepare("SELECT MAX(verze) FROM verze WHERE id_clanku = ? AND verze = ?");
        $params = array($id, $verze);
        $query->execute($params);
    }
    catch(PDOException $ex)
    {
        die("Nastala chyba. Zkuste to prosím později..");
    }
    

    if($verze != $query->fetchColumn(0))
    {
        die("Lze upravit posudek pouze u poslední verze");
    }
    else
    {        
        try
        {
            $query = $pdo->prepare("SELECT stav_redaktor FROM verze WHERE id_clanku = ? AND verze = ?");
            $params = array($id, $verze);
            $query->execute($params);
        }
        catch(PDOException $ex)
        {
            die("Nastala chyba. Zkuste to prosím později...");
        }
        switch($query->fetchColumn(0))
        {
            case "Probíhá recenzní řízení":
            case "1. posudek doručen redakci":
                try
                {
                    $query = $pdo->prepare("UPDATE posudek SET login_recenzenta = ? WHERE login_recenzenta = ? AND id_clanku = ? AND verze = ?");
                    $params = array($login, $puvodniRecenzent, $id, $verze);
                    $query->execute($params);
                }
                catch(PDOException $ex)
                {
                    die("Nastala chyba. Zkuste to prosím později....");
                }
                break;
            case "2. posudek doručen redakci":
                die("Nelze upravit již odeslaný posudek.");
                break;
            default:
                die("Tomuto posudku nelze změnit recenzenta");
                break;
        }
    }

?>