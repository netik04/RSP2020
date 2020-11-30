<?php
session_start();
if(isset($_REQUEST["reg_submit"])) // pokud byl odeslán formulář
{
    require_once '../db.php'; // připojím se do DB
    // stáhnu data
    // metoda htmlentities() - ošetření před špatným inputem (html tagy, případně SQL nebo JS příkazy)
    $raw_login = $_REQUEST["reg_login"];
    $login = htmlentities($raw_login, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $passwd = hash("sha256", ($raw_login . $_REQUEST["reg_passwd"])); // salt (login) + heslo
    $jmeno = htmlentities($_REQUEST["reg_jmeno"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $prijmeni = htmlentities($_REQUEST["reg_prijmeni"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $email = $_REQUEST["reg_mail"];
    $tel = htmlentities($_REQUEST["reg_tel"], ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // pokud formulář odeslal redaktor
    if(isset($_REQUEST["reg_role"]))
    {
        // stáhni si roli
        $role = $_REQUEST["reg_role"];
    }
    else // pokud ne
    {
        // odeslal ho autor - do role přiřaď autor
        $role = "autor";
    }

    // zkusím přidat záznam do DB
    try
    {
        $query = $pdo->prepare("INSERT INTO uzivatel (login, heslo, role, jmeno, prijmeni, email, telefon) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $params = array($login, $passwd, $role, $jmeno, $prijmeni, $email, $tel);
        $query->execute($params);
    }
    catch(PDOException $ex)
    {
        // pdo vyhodilo vyjímku - něco se nepovedlo - přesměruji zpět na stránku s registrací s chybovým kódem
        $_SESSION["error_reg"] = 1;
        header("Location: ../registr.php");
        exit();
    }

    // INSERT prošel - stáhnu si profilovku
    // kontrola, zdali uživatel vůbec nějakou profilovku odeslal
    if($_FILES["reg_pfp"]['error'] != 4)
    {
        // zjistím lokaci, kam se fotka nahrála
        $tmpFile = $_FILES['reg_pfp']['tmp_name'];
        // připravím lokaci, kde se má na konci nacházet
        $newFile = '../img/profile_pics/'.$_FILES['reg_pfp']['name'];   

        // zjistím, jakou příponu má obrázek
        $extension = '';
        for($i = strlen($newFile) - 1; $i > 0; $i--)
        {
            if($newFile[$i] == '.')
            {
                $extension = substr($newFile, $i, (strlen($newFile) - 1));
                break;
            }                    
        }
        if($extension == ".jpg" || $extension == ".jpeg" || $extension == ".png" || $extension == ".gif")
        {

            // pokud se úspěšně nahrála
            if(is_uploaded_file($_FILES['reg_pfp']['tmp_name']))
            {
                // přesunu z dočasné složky do složky s profilovými fotkami
                // pokud se přesun zadaří
                if(move_uploaded_file($tmpFile, $newFile))
                {            
                    // přejmenuji přesunutý soubor podle loginu uživatele
                    rename($newFile, "../img/profile_pics/" . $raw_login . $extension);
                }
                else // pokud přesun neprojde
                {
                    // vygeneruj chybu a vrať uživatele na registraci
                    $_SESSION["error"] = "Váš účet byl vytvořen, nepodařilo se však nahrát vaší profilovou fotku.";
                    header("Location: ../index.php");
                    exit();
                }
            }
            else // pokud se fotku nepodařilo nahrát
            {
                // vygeneruj error a vrať uživatele na registraci
                $_SESSION["error"] = "Váš účet byl vytvořen, nepodařilo se však nahrát vaší profilovou fotku.";
                header("Location: ../index.php");
                exit(); 
            }
        }
        else
        {
            $_SESSION["error"] = "Váš účet byl vytvořen, ovšem tento formát fotek není podporován.";
            header("Location: ../index.php");
            exit();
        }
    }
    // pokud vše prošlo, nebo uživatel nenahrál žádnou profilovku - přesměruj na index
    header("Location: ../index.php");
    $_SESSION["error"] = "Registrace proběhla úspěšně!";
    exit();
}
?>