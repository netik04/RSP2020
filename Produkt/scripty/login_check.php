<?php
    require_once '../db.php';

    $login = $_GET["login"];

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

    if($query -> fetchColumn(0) == 0)
    {
        echo("false");
    }
    else
    {
        echo("true");
    }
?>