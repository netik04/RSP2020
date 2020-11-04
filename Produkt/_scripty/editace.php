<?php
    session_start();

    if(isset($_REQUEST["submit"])) // pokud byl odeslán formulář
    {
        if (!include("../db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // připojení do DB
        else
        {
            $raw_login = $_REQUEST["login"];
            $login = htmlentities($raw_login, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $jmeno = htmlentities($_REQUEST["jmeno"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $prijmeni = htmlentities($_REQUEST["prijmeni"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $email = htmlentities($_REQUEST["mail"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $tel = htmlentities($_REQUEST["tel"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    try
    {
        $query = $pdo->prepare("UPDATE uzivatel set jmeno = ?, prijmeni = ?, email = ?, telefon = ? where login = ?");
        $params = array($jmeno, $prijmeni, $email, $tel, $login);
        $_SESSION["error_edit"] = $passwd . ' ' . $jmeno . ' ' . $prijmeni . ' ' . $email . ' ' . $tel . ' ' . $raw_login;
        $query->execute($params);
    }
    catch(PDOException $ex)
    {
        // pdo vyhodilo vyjímku - něco se nepovedlo - přesměruji zpět na stránku s upravu profilu s chybovým kódem
        $_SESSION["error_edit"] .= "Neco se nepovedlo, zkuste to porsim znovu.";
        header("Location: ../editProfile.php");
        exit();
    }
    // pokud vše prošlo, nebo uživatel nenahrál žádnou profilovku - přesměruj na index
    header("Location: ../editProfile.php");
    $_SESSION["error_edit"] = "Úprava proběhla úspěšně!";
    exit();
?>