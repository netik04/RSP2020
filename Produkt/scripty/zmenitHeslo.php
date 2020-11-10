<?php
    session_start();

    if(isset($_REQUEST["submit"])) // pokud byl odeslán formulář
    {
        if (!include("../db.php")){ // připojení do DB
            $_SESSION["error_edit_pass"] = "Neco se nepovedlo, zkuste to prosim znovu.";
            header("Location: ../editProfile.php");
            exit();
        } 
        else
        {
            //nacteni predanych udaju z formulare
            $login = $_SESSION[session_id()];
            $newpass = $_REQUEST["newPassword"];
            $oldpass = $_REQUEST["oldPassword"];
            $saltedOldPass = hash("sha256", $login.$oldpass); // vytvoreni sha256 hashe se soli(login uzivatele)
            $saltedNewPass = hash("sha256", $login.$newpass); // vytvoreni sha256 hashe se soli(login uzivatele)
            $login = htmlentities($login, ENT_QUOTES | ENT_HTML5, "UTF-8"); // protoze v databazi ukladame data ktera bez nebezpecnych znaku, je potreba prevest login na string bez nebezpecnych znaku, kvuli dotazu na databazi


            $query = $pdo->prepare("SELECT login, heslo from uzivatel where login = ?"); // pripraveni dotazu
            $param = array($login); // pripraveni prommenych pro dotaz 
            $query->execute($param); // provedeni dotazu

            if ($query->rowCount() == 1){ //pokud je nalezen pouze jeden radek, tak se uzivatel se zadanym jmenem v databazi nachazi
                $fetchedUser = $query->fetch(PDO::FETCH_ASSOC); // do prommene si ulozim informace o uzivateli
                if(strtolower($fetchedUser['heslo']) == $saltedOldPass){ // porovnani vytvoreneho hashe s hashem z databaze
                    //uzivatel zadal spravne jmeno a heslo
                    try
                    {
                        $query = $pdo->prepare("UPDATE uzivatel set heslo = ? where login = ?");
                        $params = array($saltedNewPass, $login);
                        $query->execute($params);
                    }
                    catch(PDOException $ex)
                    {
                        // pdo vyhodilo vyjímku - něco se nepovedlo - přesměruji zpět na stránku s upravu profilu s chybovým kódem
                        $_SESSION["error_edit_pass"] = "Neco se nepovedlo, zkuste to prosim znovu.";
                        header("Location: ../editProfile.php");
                        exit();
                    }

                    // pokud vše prošlo  přesměruj na index
                    $_SESSION["error_edit_pass"] = "Heslo uspěšně změněno!";
                    header("Location: ../editProfile.php");
                    exit();

                }else{
                    //uzivatel zadal spatne heslo
                    $_SESSION["error_edit_pass"] ="Špatně zadané staré heslo";
                    header("Location: ../editProfile.php");
                    die();
                }
            }else{
                //uzivatel zadal spatny 
                $_SESSION["error_edit_pass"] = "Špatně zadané staré heslo";
                header("Location: ../editProfile.php");
                die();
            }
        }
    }
?>