<?php
    session_start();
    // role, která skript může spouštět
    $role = "administrator";

    // pokud nejsem přihlášen nebo nemám správnou roli - přesměruji na hlavní stránku
    if(!isset($_SESSION[session_id()]) && $_SESSION["role"] != $role){ header("Location: ../../index.php"); exit(); }

    // stáhnu si ID a verzi článku, kterou mažu
    $id = htmlentities($_POST["id"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $verze = htmlentities($_POST["verze"], ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // cesta do kořene
    $base_path = "../../";

    // připojím se do DB
    // pokud se nepodaří - nemá smysl pokračovat
    if(!require($base_path . 'db.php')){die("Problém s připojením.");}

    // zjistím, která je poslední verze článku
    try{
        $query = $pdo->prepare("SELECT max(verze) FROM verze where id_clanku = ?");
        $query->execute(array($id));
    }catch(PDOException $e){
        // pokud selže dotaz - vypíšu chybu
        die("Nastala chyba. Zkuste to znovu.");
    }

    // stáhnu si max verzi článku
    $maxVerze =  $query->fetch(PDO::FETCH_COLUMN);
    
    // pokud se jedná o první verzi, nebo nejde o poslední verzi
    if($verze == 1 || $verze < $maxVerze){
        // ukončím skript s chybou
        die("Lze smazat pouze posledni verzi. Pokud chcete smazat 1. verzi, smazte clanek");
    }

    // zjistím si cestu k souboru s verzí článku
    try{
        $query = $pdo->prepare("SELECT cesta FROM verze WHERE id_clanku = ? AND verze = ?");
        $params = array($id, $verze);
        $query->execute($params);
    }catch(PDOException $e){
        // pokud dotaz selže - vypíšu chybu
        die("Nastala chyba. Zkuste to znovu.");
    }

    // stáhnu si cestu
    $cesta = $query->fetch(PDO::FETCH_COLUMN);
    // pokusím se smazat soubor
    if(!unlink($base_path . $cesta)) {
        // pokud se nepodařilo smazat - vrátím chybu
        die("Chyba v mazání souboru...");
    }

    // připravím a spustím dotaz pro mazání verze
    try{
        $query = $pdo->prepare("DELETE from verze WHERE id_clanku = ? AND verze = ?");
        $params = array($id, $verze);
        $query->execute($params);
    }catch(PDOException $e){
        // pokud selže dotaz - vypíšu chybu
        die("Nastala chyba při odstraňování. Zkuste to znovu.");
    }

    // připravím a spustím dotaz pro aktualizaci stavů u předchozí verze - aby autor mohl nahrát další verzi
    try{
        $query = $pdo->prepare("UPDATE verze SET stav_autor='Vráceno k úpravě', stav_redaktor = 'Probíhá úprava textu autorem' WHERE id_clanku = ? AND verze = ?");
        $params = array($id, $verze-1);
        $query->execute($params);
    }catch(PDOException $e){
        // pokud se nepovedl UPDATE - vrátím chybu
        die("Nastala chyba při odstraňování. Zkuste to znovu.");
    }

?>