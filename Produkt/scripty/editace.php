<?php
    session_start();

    if(isset($_REQUEST["submit"])) // pokud byl odeslán formulář
    {
        if (!include("../db.php")){ // připojení do DB
            $_SESSION["error_edit"] = "Neco se nepovedlo, zkuste to prosim znovu.";
            header("Location: ../editProfile.php");
            exit();
        } 
        else
        {
            //nacteni predanych udaju z formulare
            $raw_login = $_SESSION[session_id()];
            $login = htmlentities($raw_login, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $jmeno = htmlentities($_REQUEST["jmeno"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $prijmeni = htmlentities($_REQUEST["prijmeni"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $email = htmlentities($_REQUEST["mail"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $tel = htmlentities($_REQUEST["tel"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $pass = $_REQUEST["password"];
            $saltedPass = hash("sha256", $raw_login.$pass);

            $query = $pdo->prepare("SELECT login, heslo from uzivatel where login = ?"); // pripraveni dotazu
            $param = array($login); // pripraveni prommenych pro dotaz 
            $query->execute($param); // provedeni dotazu

            if ($query->rowCount() == 1){ //pokud je nalezen pouze jeden radek, tak se uzivatel se zadanym jmenem v databazi nachazi
                $fetchedUser = $query->fetch(PDO::FETCH_ASSOC); // do prommene si ulozim informace o uzivateli
                if(strtolower($fetchedUser['heslo']) == $saltedPass){ // porovnani vytvoreneho hashe s hashem z databaze
                    //uzivatel zadal spravne jmeno a heslo
                    try
                    {
                        $query = $pdo->prepare("UPDATE uzivatel set jmeno = ?, prijmeni = ?, email = ?, telefon = ? where login = ?");
                        $params = array($jmeno, $prijmeni, $email, $tel, $login);
                        $query->execute($params);
                    }
                    catch(PDOException $ex)
                    {
                        // pdo vyhodilo vyjímku - něco se nepovedlo - přesměruji zpět na stránku s upravu profilu s chybovým kódem
                        $_SESSION["error_edit"] = "Neco se nepovedlo, zkuste to prosim znovu.";
                        header("Location: ../editProfile.php");
                        exit();
                    }

                    // pokud vše prošlo  přesměruj na index
                    $_SESSION["error_edit"] = "Úprava proběhla úspěšně!";
                    header("Location: ../editProfile.php");
                    exit();

                }else{
                    //uzivatel zadal spatne heslo
                    $_SESSION["error_edit"] ="Špatně zadané heslo";
                    header("Location: ../editProfile.php");
                    die();
                }
            }else{
                //uzivatel zadal spatny 
                $_SESSION["error_edit"] = "Špatně zadané heslo";
                header("Location: ../editProfile.php");
                die();
            }
        }
    }
?>