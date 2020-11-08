<?php
session_start();
$base_path = "../../";
if(require($base_path . "scripty/jePrihlasen.php")){
    $id = $_REQUEST["id"];
    $verze = $_REQUEST["verze"];
    $interni = $_REQUEST["interni"];
    $message = $_REQUEST["message"];
    date_default_timezone_set("Europe/Prague");
    $datetime = date("Y-m-d H:i:s");

    if(require($base_path . "db.php")){
        echo "test";
        try{
            $query = $pdo->prepare("INSERT INTO zprava VALUES(?,?,?,?,?,?,?);");
            $params = array($id, $verze, $datetime, $message, $interni, $_SESSION[session_id()], false);
            $query->execute($params);
        }catch(Exception $e){
            $_SESSION["errorMessage"] = $e->getMessage();
            header("Location:" . $base_path. "redaktor");
            die();
        }
        header("Location:" . $base_path. "redaktor");
        die();
        }
    }else{
        $_SESSION["errorMessage"] = "Nepodarilo se prihlasit k databazi";
        header("Location:" . $base_path. "redaktor");
        die();
    }
}

?>