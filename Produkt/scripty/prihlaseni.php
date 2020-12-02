<?php
session_start();

//nacteni predanych udaju z formulare
$login = $_REQUEST["login"];
$pass = $_REQUEST["password"];
$saltedPass = hash("sha256", $login.$pass); // vytvoreni sha256 hashe se soli(login uzivatele)
$login = htmlentities($_REQUEST["login"], ENT_QUOTES | ENT_HTML5, "UTF-8"); // protoze v databazi ukladame data ktera bez nebezpecnych znaku, je potreba prevest login na string bez nebezpecnych znaku, kvuli dotazu na databazi

if (!include($base_path."../db.php")) { //pripojeni k databazi
    $_SESSION["error"] = "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; //pokud dojde k problemu s db, uzivatel je presmerovan na index s chybovou hlaskou.
    header("Location: ../index.php");
    die();
}else{
    $query = $pdo->prepare("SELECT login, heslo, role from uzivatel where login = ?"); // pripraveni dotazu
    $param = array($login); // pripraveni prommenych pro dotaz 
    $query->execute($param); // provedeni dotazu
    if ($query->rowCount() == 1){ //pokud je nalezen pouze jeden radek, tak se uzivatel se zadanym jmenem v databazi nachazi
        $fetchedUser = $query->fetch(PDO::FETCH_ASSOC); // do prommene si ulozim informace o uzivateli
        if(strtolower($fetchedUser['heslo']) == $saltedPass){ // porovnani vytvoreneho hashe s hashem z databaze
            //uzivatel zadal spravne jmeno a heslo
            session_regenerate_id(); // pregenerovani session id
            $_SESSION[session_id()] = $fetchedUser["login"]; // do session ulozim login uzivatele coz znamena ze je uzivatel prihlasen
            $_SESSION["role"] = $fetchedUser["role"]; // do session ulozim roli aktualne prihlaseneho uzivatele aby nebylo potreba odesilat dotaz na databazi na kazde strance
            switch($fetchedUser['role']){// po prihlaseni uzivatele presmeruji podle jeho role na prislusnou stranku
                case "autor":
                    header("Location: ../autor/index.php");
                break;
                case "redaktor":
                    header("Location: ../redaktor/index.php");
                    die();
                break;
                case "recenzent":
                    header("Location: ../recenzent/index.php");
                    die();
                break;
                case "administrator":
                    header("Location: ../administrator/uzivatel.php");
                    die();
                break;
                case "sefredaktor":
                    header("Location: ../sefredaktor/index.php");
                    die();
                break;
                default:
                $_SESSION["error"] = "Neco se nezdarilo";
                header("Location: ../index.php");
                die();
                break;
            }
        }else{
            //uzivatel zadal spatne heslo
            $_SESSION["error"] ="Spatne jmeno nebo heslo";
            header("Location: ../index.php");
            die();
        }
    }else{
        //uzivatel zadal spatny login
        $_SESSION["error"] = "Spatne jmeno nebo heslo";
        header("Location: ../index.php");
        die();
    }
} 
?>