<?php
    $login = htmlentities($_POST["login"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $jmeno = $_POST["jmeno"];
    $prijmeni = $_POST["prijmeni"];
    $role = $_POST["role"];
    $email = $_POST["email"];
    $telefon = $_POST["telefon"];

    require_once '../../db.php';

    try
    {
        $query = $pdo->prepare("UPDATE uzivatel SET jmeno = ?, prijmeni = ?, role = ?, email = ?, telefon = ? WHERE login = ?");
        $params = array($jmeno, $prijmeni, $role, $email, $telefon, $login);
        $query->execute($params);
    }
    catch(PDOException $ex)
    {
        echo("Nastala chyba. Zkuste to prosím později.");
        die();
    }
?>