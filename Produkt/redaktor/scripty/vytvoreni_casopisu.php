<?php
$base_path="../../";

$role = "redaktor";
session_start();
if($role !== $_SESSION['role']) die();

    if(require_once($base_path. "db.php")){
        $tema = htmlentities($_REQUEST["tema"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $uzaverka = $_REQUEST["uzaverka"];
        $kapacita = htmlentities($_REQUEST["kapacita"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        try {
            $query = $pdo->prepare("INSERT INTO casopis (datum_uzaverky, tema, kapacita) VALUES(?, ?, ?);");
            $params = array($uzaverka, $tema, $kapacita);
            $query->execute($params);
            if($query->rowCount() == 1)
                echo(1);
            else
                echo(0);
        }
        catch(PDOException $e){
            echo(0);
        }
        $pdo = null;
        die();
    }
    echo(0); //return false;
    die();
?>