<?php

$base_path = "../../";

$role = "redaktor";
session_start();
if($role !== $_SESSION['role']) die();

//session_start();
if(require($base_path . "scripty/jePrihlasen.php")){
    $id = $_REQUEST["id"];
    $verze = $_REQUEST["verze"];
    $interni = $_REQUEST["interni"];
    $message = htmlentities($_REQUEST["message"], ENT_QUOTES | ENT_HTML5, "UTF-8");
    date_default_timezone_set("Europe/Prague");
    $datetime = date("Y-m-d H:i:s");

    if(require($base_path . "db.php")){
        try{
            $query = $pdo->prepare("INSERT INTO zprava VALUES(?,?,?,?,?,?,?);");
            $params = array($id, $verze, $datetime, $message, $interni, $_SESSION[session_id()], 0);
            $query->execute($params);
        }catch(Exception $e){
            echo("Došlo k chybě. Zkuste to prosím za chvíli.");
            die();
        }
    }else{
        echo("Došlo k chybě. Zkuste to prosím za chvíli.");
        die();
    }
}
?>