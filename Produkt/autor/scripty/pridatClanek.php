<?php
    // script sloužící pro přidávání odeslaného článku do DB

    // nastartuji session a výstupní bufferování
    session_start();
    ob_start();
    // připojím se do DB
    include '../../db.php';  
    
    // pokud byl odeslán článek
    if(isset($_REQUEST["clanekSubmit"]))
    {
        // stáhnu si potřebné informace
        $login = htmlentities($_SESSION[session_id()], ENT_QUOTES | ENT_HTML5, 'UTF-8'); // login autora
        $nazev= htmlentities($_REQUEST["clanekNazev"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); // název článku
        $pocetAutoru = htmlentities($_REQUEST["clanekPocetAutoru"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); // kolik autorů bylo zadáno ve formuláři
        $casopis = htmlentities($_REQUEST["clanekCasopis"], ENT_QUOTES | ENT_HTML5, 'UTF-8'); // do kterého časopisu článek bude odeslán
        $datum = date("Y-m-d"); // datum odeslání (formát YYYY-MM-DD)
        $stav_autor = "Podáno"; // stav pro autora - článek (resp. verze) je nově odeslaný(á)
        $stav_redaktor = "Nově podaný"; // stav pro redaktora - to samé jako pro autora
        $tmpSoubor = $_FILES["clanekSoubor"]["name"]; // celý název odeslaného souboru (včetně přípony)

        // zjistím příponu a název souboru
        for($i = 3; $i < strlen($tmpSoubor); $i++)
        {
            // projdu název souboru
            // pokud najdu tečku
            if($tmpSoubor[$i] == '.')
            {
                // našel jsem příponu - uložím si příponu a název bez přípony
                $pripona = substr($tmpSoubor, $i, (strlen($tmpSoubor) - 1));
                $soubor = substr($tmpSoubor, 0, (strlen($tmpSoubor) - strlen($pripona)));
                break;
            }
        }

        // uložím si všechny autory do pole
        for($i = 0; $i < $pocetAutoru; $i++)
        {            
            if($i == 0)
            {
                $autori[$i] = $login;
            }
            else
            {
                $autori[$i] = htmlentities($_REQUEST["clanekAutor" . ($i+1)], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        // mám informace - můžu začít přidávat
        // podívám se, jestli daný článek už neexistuje
        try
        {
            $queryClanek = $pdo -> prepare("SELECT COUNT(*) FROM clanek WHERE nazev = ?");
            $params = array($nazev);
            $queryClanek -> execute($params);
        }
        // selhal dotaz
        catch(PDOException $ex)
        {
            // vracím se zpět na přidávací formulář s chybou
            $_SESSION["error"] = "Nepodařilo se nahrát článek (count). Zkuste to prosím znovu.";
            header("Location: ../pridatClanekForm.php");
            exit();
        }

        // pokud článek neexistuje - jedná se o nový článek
        if($queryClanek -> fetchColumn(0) == 0)
        {
            // zapíšu číslo verze
            $verze = 1;          
            // vytvořím článek v DB  
            try
            {
                $queryClanek = $pdo -> prepare("INSERT INTO clanek (nazev, id_casopisu) VALUES (?, ?)");
                $params = array($nazev, $casopis);
                $queryClanek -> execute($params);
            }
            // selhalo přidávání
            catch(PDOException $ex)
            {
                // vrátím se na formulář s chybou
                $_SESSION["error"] = "Nepodařilo se nahrát článek (insert). Zkuste to prosím znovu.";
                header("Location: ../pridatClanekForm.php");
                exit();
            }
        }
       
        // článek byl přidán nebo už existuje - zjistím jeho ID
        try
        {
            $queryIdClanku = $pdo -> prepare("SELECT id_clanku FROM clanek WHERE nazev = ?");
            $params = array($nazev);
            $queryIdClanku -> execute($params);
            // stáhnu si ID článku
            $id_clanku = $queryIdClanku -> fetchColumn(0);
            // zjistím, kolik verzí článek má
            $queryVerze = $pdo -> prepare("SELECT COUNT(*) FROM verze WHERE id_clanku = ?");
            $params = array($id_clanku);
            $queryVerze -> execute($params);
        }
        // selhal jeden ze dvou dotazů
        catch(PDOException $ex)
        {
            // pokud jde o první verzi
            if($verze == 1)
            {
                // smažu "prázdný" článek
                uklidit($id_clanku, $pdo);
            }
            // vrátím se do formuláře s chybou
            $_SESSION["error"] =  "Nepodařilo se vytvořit článek. Zkuste to prosím znovu.";
            header("Location: ../pridatClanekForm.php");
            exit();
        }
       
        // dotaz prošel - zjistím verzi
        $verze = ($queryVerze -> fetchColumn(0)) + 1;
         
        // pokud se nejedná o první verzi
        if($verze != 1)
        {
            // připravím cestu k souboru s článkem (formát název-souboru_v* -> * je číslo verze)
            $finalSoubor = "clanky/" . $soubor . "_v" . $verze . $pripona;
        }
        // jde o nový článek, tedy o první verzi
        else
        {
            // připravím cestu k souboru (u první verze nepotřebuji přidávat _v na konec)
            $finalSoubor = "clanky/" . $soubor . $pripona;
            // jelikož jde o první verzi - zapíšu, kdo všechno článek píše
            for($i = 0; $i < count($autori); $i++)
            {
                try
                {
                    $query_pise = $pdo -> prepare("INSERT INTO pise VALUES (?, ?)");
                    $params = array($autori[$i], $id_clanku);
                    $query_pise -> execute($params);
                }
                // selhal zápis
                catch(PDOException $ex)
                {
                    // pokud jde o první verzi
                    if($verze == 1)
                    {
                        // smažu článek
                        uklidit($id_clanku, $pdo);
                    }
                    // vrátím se na formulář s chybou
                    $_SESSION["error"] = "Nepodařilo se vytvořit verzi článku. Zkuste to prosím znovu.";
                    header("Location: ../pridatClanekForm.php");
                    exit();
                }
            }
        }

        // máme vyřešený článek a psaní článku - vytvořím verzi
        try
        {
            $queryVerze = $pdo -> prepare("INSERT INTO verze (id_clanku, verze, stav_autor, stav_redaktor, datum, cesta) VALUES (?, ?, ?, ?, ?, ?)");
            $params = array($id_clanku, $verze, $stav_autor, $stav_redaktor, $datum, $finalSoubor);
            $queryVerze -> execute($params);            
        }
        // nepodařilo se vytvořit verzi
        catch(PDOException $ex)
        {
            // pokud jde o první verzi
            if($verze == 1)
            {
                // smažu článek
                uklidit($id_clanku, $pdo);
            }
            // vrátím se na formulář s chybou
            $_SESSION["error"] = "Nepodařilo se vytvořit verzi článku. Zkuste to prosím znovu.";
            header("Location: ../pridatClanekForm.php");
            exit();
        }

        // práce s DB je kompletní
        // zjistím nahranou lokaci souboru (na školním serveru /tmp/*)
        $tmpSoubor = $_FILES["clanekSoubor"]["tmp_name"];

        // pokud se soubor podařilo nahrát
        if(is_uploaded_file($tmpSoubor))
        {
            // připravím finální cestu souboru
            $dest = "../../" . $finalSoubor;

            // pokusím se soubor přesunout do složky s články
            // pokud se přesun nepovedl
            if(!move_uploaded_file($tmpSoubor, $dest))
            {
                // u první verze opět smažu článek
                if($verze == 1)
                {
                    uklidit($id_clanku, $pdo);
                }
                // pokud nejde o první verzi - pouze smažu nahranou verzi
                else
                {
                    $uklid = $pdo -> prepare("DELETE FROM verze WHERE id_clanku = ? AND verze = ?");
                    $params = array($id_clanku, $verze);
                    $uklid -> execute($params);
                }
                // vrátím se na formulář s chybou
                $_SESSION["error"] = "Nepodařilo se nahrát soubor s článkem. Zkuste to prosím znovu.";
                header("Location: ../pridatClanekForm.php");
                exit();
            }
        }
        // soubor se nepodařilo ani nahrát
        else
        {
            // pokud jde o první verzi, smažu článek
            if($verze == 1)
            {
                uklidit($id_clanku, $pdo);
            }
            // jde o novou verzi již existujícího článku
            else
            {
                // smažu pouze nahranou verzi
                $uklid = $pdo -> prepare("DELETE FROM verze WHERE id_clanku = ? AND verze = ?");
                $params = array($id_clanku, $verze);
                $uklid -> execute($params);
            }
            // vrátím se do formuláře s chybou
            $_SESSION["error"] = "Nepodařilo se nahrát soubor s článkem. Zkuste to prosím znovu.";
            header("Location: ../pridatClanekForm.php");
            exit();
        }
      
        $queryUpdateVerze = $pdo->query("UPDATE verze SET stav_redaktor = 'Existuje nová verze' WHERE id_clanku = " . $id_clanku . " AND verze = " . ($verze - 1));                   
        // vše  prošlo - článek/verze je kompletně nahraná
        // přesměruji autora na jeho hlavní stránku
        header("Location: ../index.php");
        exit();
    }

    // funkce pro 'úklid' po neúspěšném nahrání nového článku
    // volá se pouze, pokud se nahrává kompletně nový článek (ne pouze verze) a nepodaří se nějaký krok
    // params -> $id_cl = id článku; $db = instance třídy PDO, se kterou se pracuje
    function uklidit($id_cl, $db)
    {    
        try
        {
            // připravím dotaz pro smazání článku
            // díky cizím klíčům, pokud smažu článek, smažou se i jeho verze a záznamy, kdo článek píše
            $uklidit = $db->prepare("DELETE FROM clanek WHERE id_clanku = ?");          
            $params = array($id_cl);
            $uklidit -> execute($params);

            // zároveň, pokud mažu článek, resetuji AUTO_INCREMENT (aby nevznikly moc velké mezery mezi IDčky článků)
            $uklidit2 = $db -> query("ALTER TABLE clanek AUTO_INCREMENT = " . $id_cl);
        }
        // pokud se nepodaří mazání
        catch(PDOException $ex)
        {
            // vrátím se na formulář s chybou
            $_SESSION["error"] = "Nastala chyba. Zkuste to prosím znovu";
            header("Location: ../pridatClanekForm.php");
            exit();
        }
    }
?>