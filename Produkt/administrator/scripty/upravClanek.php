<?php
    session_start();
    $role = "administrator";
    if(!isset($_SESSION[session_id()]) && $_SESSION["role"] != $role){ header("Location: ../../index.php"); exit(); }

    $id_clanku = htmlentities($_POST["id_cl"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $nazev = htmlentities($_POST["nazev"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $id_casopisu = htmlentities($_POST["id_cas"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
 
    if(!require '../../db.php') die("Chyba v DB");

    try
    {
        $query = $pdo->prepare("UPDATE clanek SET nazev = ?, id_casopisu = ? WHERE id_clanku = ?");
        $params = array($nazev, $id_casopisu, $id_clanku);
        $query -> execute($params);
    }
    catch(PDOException $ex)
    {
        die("Došlo k chybě zkukste to prosím za chvíli");
    }
?>