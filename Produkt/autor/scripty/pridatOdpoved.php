<?php
    // script, který přidá autorovu odpověď do posudku
    $role = "autor";
    // zapnu vystupní bufferovaní
    session_start();
    ob_start();

    if(!isset($_SESSION[session_id()]) && $_SESSION["role"] != $role){ header("Location: ../../index.php"); exit(); }

    // pokud autor odeslal odpověď na posudek
    if(isset($_POST["odpoved_submit"]))
    {
        // cesta do kořenového adresáře
        $base_path = "../../";

        // stáhnu si z POSTu potřebná data
        $odpoved = htmlentities($_POST["odpoved_text"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $id = htmlentities($_POST["odpoved_clanek"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $verze = htmlentities($_POST["odpoved_verze"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $login = htmlentities($_POST["odpoved_login"], ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // připojím se do DB
        require $base_path . "db.php";

        try
        {
            // připravím UPDATE
            $query = $pdo->prepare("UPDATE posudek SET vyjadreni_autora = ? WHERE id_clanku = ? AND verze = ? AND login_recenzenta = ?");
            // připravím parametry
            $params = array($odpoved, $id, $verze, $login);
            // provedu UPDATE záznamu
            $query->execute($params);
        }
        catch(PDOException $ex)
        {
            header("Location: ../zobrazitClanek.php?id=" . $id . "&verze=" . $verze);
            ob_end_flush();
            exit();
        }
        // UPDATE prošel - přesměruji zpět na detail článku
        header("Location: ../zobrazitClanek.php?id=" . $id . "&verze=" . $verze);
        // vypnu výstupní bufferování
        ob_end_flush();
    }
    else
    {
        header("Location: ../zobrazitClanek.php?id=" . $id . "&verze=" . $verze);
        ob_end_flush();
        exit();
    }
?>