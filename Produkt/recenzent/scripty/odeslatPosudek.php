<?php

    $role = "recenzent";
    session_start();

    if(!isset($_SESSION[session_id()]) && $_SESSION["role"] != $role){ header("Location: ../../index.php"); exit(); }

$base_path = "../../";

if(!require($base_path . "db.php")){die("Nastala chyba. Zkuste to prosím později");}

$login = htmlentities($_POST["login"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$id = htmlentities($_POST["id"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$verze = htmlentities($_POST["verze"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$rec01 = htmlentities($_POST["rec01"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$rec02 = htmlentities($_POST["rec02"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$rec03 = htmlentities($_POST["rec03"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$rec04 = htmlentities($_POST["rec04"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$recOdpoved = htmlentities($_POST["recOdpoved"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$recKontrola = $_POST["recKontrola"];
$datum = date("Y-m-d H:i:s");

try
{
    $query = $pdo->prepare("UPDATE posudek SET akt_zaj_prin = ?, jazyk_styl_prinos = ?, originalita = ?, odbor_uroven = ?, otevrena_odpoved = ?, datum_vytvoreni = ?, osobni_revize = ? WHERE id_clanku = ? AND verze = ? AND login_recenzenta = ?");
    $params = array($rec01, $rec02, $rec03, $rec04, $recOdpoved, $datum, $recKontrola, $id, $verze, $login);
    $query -> execute($params);
}
catch(PDOException $ex)
{
    die("Nastala chyba. Zkuste to prosím znovu.1");
}

try
{
    $queryVerze = $pdo->prepare("SELECT stav_redaktor FROM verze WHERE id_clanku = ? AND verze = ?");
    $params = array($id, $verze);
    $queryVerze -> execute($params);
}
catch(PDOException $ex)
{
    die("Nastala chyba. Zkuste to prosím později.2");
}

$stav = $queryVerze->fetchColumn(0);

if($stav == "Probíhá recenzní řízení")
{
    $tmp = "1. posudek doručen redakci";
}
else
{
    $tmp = "2. posudek doručen redakci";
}

try
{
    $queryUpdate = $pdo->prepare("UPDATE verze SET stav_redaktor = ? WHERE id_clanku = ? AND verze = ?");
    $params = array($tmp, $id, $verze);
    $queryUpdate -> execute($params);
}
catch(PDOException $ex)
{
    die("Nastala chyba. Zkuste to prosím znovu.3");
}

?>