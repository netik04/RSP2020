<?php
session_start();

require("../db.php");
$login = $_REQUEST["login"];
$pass = $_REQUEST["password"];
$saltedPass = hash("sha256", $login.$pass);
$login = htmlentities($_REQUEST["login"], ENT_QUOTES | ENT_HTML5, "UTF-8");

if (!include($base_path."../db.php")) {
    echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
}else{
    $query = $pdo->prepare("SELECT login, heslo, role from uzivatel where login = ?");
    $param = array($login);
    $query->execute($param);
    if ($query->rowCount() == 1){
        $fetchedUser = $query->fetch(PDO::FETCH_ASSOC);
        //echo (strtolower($fetchedUser['heslo']) . "<br / >");
        //echo ($saltedPass . "\n");
        if(strtolower($fetchedUser['heslo']) == $saltedPass){
            session_regenerate_id();
            $_SESSION[session_id()] = $fetchedUser["login"];
            $_SESSION["role"] = $fetchedUser["role"];
            switch($fetchedUser['role']){
                case "autor":
                    header("Location: ../autor/");
                break;
                case "redaktor":
                    header("Location: ../redaktor/");
                    die();
                break;
                case "recenzent":
                    header("Location: ../recenzent/");
                    die();
                break;
                case "administrator":
                    header("Location: ../administrator/");
                    die();
                break;
                case "sefredaktor":
                    header("Location: ../sefredaktor/");
                    die();
                break;
                default:
                //$_SESSION["error"] = "Neco se nezdarilo";
                header("Location: ../index.php");
                die();
                break;
            }
        }else{
            $_SESSION["error"] ="Spatne jmeno nebo heslo";
            header("Location: ../index.php");
            die();
        }
    }else{
        $_SESSION["error"] = "Spatne jmeno nebo heslo";
        header("Location: ../index.php");
        die();
    }
} 
?>