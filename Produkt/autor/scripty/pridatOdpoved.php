<?php
    // script, který přidá autorovu odpověď do posudku

    // zapnu vystupní bufferovaní
    ob_start();

    // pokud autor odeslal odpověď na posudek
    if(isset($_REQUEST["odpoved_submit"]))
    {
        // cesta do kořenového adresáře
        $base_path = "../../";

        // stáhnu si z POSTu potřebná data
        $odpoved = $_POST["odpoved_text"];
        $id = $_POST["odpoved_clanek"];
        $verze = $_POST["odpoved_verze"];
        $login = $_POST["odpoved_login"];

        // připojím se do DB
        require $base_path . "db.php";

        // připravím UPDATE
        $query = $pdo->prepare("UPDATE posudek SET vyjadreni_autora = ? WHERE id_clanku = ? AND verze = ? AND login_recenzenta = ?");
        // připravím parametry
        $params = array($odpoved, $id, $verze, $login);
        // provedu UPDATE záznamu
        $query->execute($params);

        // UPDATE prošel - přesměruji zpět na detail článku
        header("Location: ../zobrazitClanek.php?id=" . $id . "&verze=" . $verze);
        // vypnu výstupní bufferování
        ob_end_flush();
    }
?>