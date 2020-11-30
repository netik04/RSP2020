<?php
    session_start();
    $role = "administrator";
    if(!isset($_SESSION[session_id()]) && $_SESSION["role"] != $role){ header("Location: ../../index.php"); exit(); }

    $id = htmlentities($_POST["id"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $verze = htmlentities($_POST["verze"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $stavAutor = $_POST["stavAutor"];
    $stavRedaktor = $_POST["stavRedaktor"];
    $datum = $_POST["datum"];

    if(!require '../../db.php') die("Chyba v DB");

    try
    {
        $query = $pdo->prepare("UPDATE verze SET stav_autor = ?, stav_redaktor = ?, datum = ? WHERE id_clanku = ? AND verze = ?");
        $params = array($stavAutor, $stavRedaktor, $datum, $id, $verze);
        $query -> execute($params);
    }
    catch(PDOException $ex)
    {
        die("Došlo k chybě zkukste to prosím za chvíli");
    }
?>