<?php
    // script sloužící pro získání potřebných informací o časopisu
    // tento script je volán z hlavního formuláře přes AJAX ve chvíli, kdy uživatel vybere časopis ve formuláři
    // vrací pole kódované do JSON s informacemi o časopisu, které si hlavní formulář přebírá a zobrazuje je do tabulky

    // připojení do DB
    require_once '../../db.php';

    // stáhnu si ID časopisu
    $id = $_REQUEST["id"];

    // stáhnu si datum uzávěrky a kapacitu časopisu z databáze
    try
    {
        $query = $pdo->prepare("SELECT datum_uzaverky, kapacita FROM casopis WHERE id_casopisu = ?");
        $params = array($id);
        $query -> execute($params);
    }
    // dotaz neprošel
    catch(PDOException $ex)
    {
        // nemá smysl pokračovat
        die($ex -> getMessage());
    }
    // dotaz prošel - stáhnu si data
    $radek = $query->fetch(PDO::FETCH_ASSOC);
    $datum = $radek["datum_uzaverky"];
    $kapacita = $radek["kapacita"];

    // zjsitím, kolik článků již bylo do časopisu odesláno
    try
    {
        $query = $pdo->prepare("SELECT COUNT(*) FROM clanek WHERE id_casopisu = ?");
        $params = array($id);
        $query->execute($params);
    }
    // dotaz neprošel
    catch(PDOException $ex)
    {
        // nemá smysl pokračovat
        die($ex -> getMessage());
    }
    // dotaz prošel - stáhnu si počet
    $pocetPrispevku = $query->fetchColumn(0);
    // všechny informace přidám do pole
    $data = array($datum, $kapacita, $pocetPrispevku);
    // pole zakóduji s JSON a pošlu přes echo hlavnímu scriptu
    echo(json_encode($data));
?>