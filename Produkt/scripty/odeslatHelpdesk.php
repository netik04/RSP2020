<?php
    session_start(); // nastartovat session + vystupni bufferovani
    ob_start();

    // stahnu si potřebné údaje
    $zprava = $_REQUEST["text"];
    $login = $_SESSION[session_id()];
    // pokud byla odeslána odpověď
    if(isset($_REQUEST["id"]))
    {
        // stáhnu si id otázky, na kterou uživatel odpovídal
        $id = $_REQUEST["id"];
        // připravím si string dotazu
        $sql = "INSERT INTO helpdesk (zprava, login, id_otazky) VALUES (?, ?, ?)";
        // připravím parametry
        $params = array($zprava, $login, $id);
    }
    else // byla odeslána otázka
    {
        // připravím dotaz
        $sql = "INSERT INTO helpdesk (zprava, login) VALUES (?, ?)";
        // připravím parametry
        $params = array($zprava, $login);
    }

    // připojím se do DB
    require_once '../db.php';
    try
    {
        // připravím a spustím dotaz
        $query = $pdo -> prepare($sql);
        $query -> execute($params);
    }
    catch(PDOException $ex)
    {
        die($ex -> getMessage());
    }

    // vše prošlo - vrátím se na helpdesk
    header("Location: ../helpdesk.php");

    ob_end_flush();
    exit();
?>