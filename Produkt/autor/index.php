<?php
// toto je hlavní stránka autora
// slouží pro zobrazování všech článků napsaných daným autorem a pro přidávání nových verzí

// potřebuji, aby si stránku zobrazil pouze autor
$role = "autor";

// cesta ke kořeni
$base_path = "../";
//přidám jQueryUI
$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
// přidám autor-only CSS
$head_str .= "<link rel='stylesheet' href='autor_style.css'>";

// include hlavičky
require($base_path."head.php");
?>

<script>
    // pokud je document načtený
    $(document).ready(function()
    {
        // nastavím accordion (jQueryUI)
        $(".accordion").accordion({
            heightStyle: "content", // proměnná velikost elementů
            collapsible: true, // je možnost zavřít všechny položky
            active: false // začíná se zavřenými položkami
        });
        // nastavím jQueryUI tlačítka pro přidávání nové verze a pro zobrazení článku
        $(".nova_verze").button();
        $(".zobrazit").button();
    });   
</script>

<div id="content" class="autor">
<?php
// pokusím se připojit do DB
if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // pokud se nepovedlo, nemá smysl pokračovat
else 
{
    // z DB si zjistím potřebné informace
    try
    {
        $query = $pdo -> prepare("SELECT id_clanku, verze, stav_autor, nazev, datum, cesta, tema, id_casopisu, stav_redaktor FROM clanek NATURAL JOIN verze NATURAL JOIN pise NATURAL JOIN casopis WHERE login = ?");
        $params = array($_SESSION[session_id()]);
        $query -> execute($params);
    }
    // pokud se nepovedlo
    catch(PDOException $ex)
    {
        // nemá smysl pokračovat
        die($ex -> getMessage());
    }

    // dotaz vrátil 0 - uživatel nemá žádné nahrané články
    if($query -> rowCount() == 0)
    {
        // pouze zobrazím text a končím
        echo("<h2>Zatím jste nevytvořil žádné články</h2>");
    }
    // pokud má uživatel nějaké články
    else
    {
        // pomocná proměnná pro kontrolu, zdali pracuji s další verzí, nebo s novým článkem
        $tmp = 0;
        // vypíšu začátek accordionu
        echo("<div class='accordion'>");
        // dokud máš z dotazu co číst
        while(($radek = $query->fetch(PDO::FETCH_ASSOC)) != FALSE)
        {
            // stáhnu si info
            $id_clanku = $radek["id_clanku"]; // id článku
            $verze = $radek["verze"]; // verzi článku
            $stav_autor = $radek["stav_autor"]; // jaký stav zobrazit autorovi
            $nazev = $radek["nazev"]; // název článku
            $datum = date("j.m.yy", strtotime($radek["datum"]));
            $cesta = $radek["cesta"]; // cestu ke článku
            $tema = $radek["tema"]; // do jakého časopisu byl článek odeslán
            $id_casopisu = $radek["id_casopisu"]; // id časopisu - pro pozdější práci s verzemi
            $stav_redaktor = $radek["stav_redaktor"]; // stav zobrazovaný redaktorovi - pro kontrolu možnosti přidávat nové verze

            // zobrazení dat
            // zobrazení pomocí accordionu - hlavička přes <h1>; content přes tabulku v extra DIVu
            // pokud se id článku rovná tomu pomocnému ID
            if($id_clanku == $tmp)
            {
                // jedná se pouze o verzi - vypíšu info k verzi do již existující sekce accordionu
                echo("<tr>");
                echo("<td>" . $verze . "</td><td>" . $stav_autor . "</td><td>" . $datum . "</td><td><a href='" . $base_path . $cesta . "' target='_blank'><button type='button' class='zobrazit'>Zobrazit</button></a></td>");
                // pokud byla verze vrácena k úpravě a neexistuje nová verze
                if(($stav_autor == "Vráceno k úpravě") && ($stav_redaktor != "Existuje nová verze"))
                {
                    // autor má možnost vytvořit novou verzi - zobrazím příslušný 'formulář'
                    echo("<td><form action='pridatClanekForm.php' method='POST'><input type='hidden' name='clanekNazev' value='" . $nazev . "'>");
                    echo("<input type='hidden' name='clanekCasopis' value='" . $id_casopisu . "'>");
                    echo("<input class='nova_verze' type='submit' name='verzeSubmit' value='Vytvořit novou verzi'></form></td>");
                } 
                echo("</tr>");
            }
            // pokud se id nerovná tmp - jedná se o další článek
            else
            {
                // pokud to není první článek
                if($tmp != 0)
                {
                    // musím ukončit sekci accordionu
                    echo("</table></div>");
                }
                // pomocná proměnná bude nové id článku
                $tmp = $id_clanku;
                // zobrazím hlavičku nové sekce accordionu (název článku a téma časopisu)
                echo("<h1>Název: <span class='nazev'>" . $nazev . "</span><span class='tema'>Téma: " . $tema . "</span></h1>");
                // vypíšu obsah sekce
                echo("<div><table class='acc-table' cellspacing='0'><tr>");
                echo("<th>Verze článku</th><th>Stav verze</th><th>Datum nahrání verze</th><th>Možnosti</th></tr><tr>");
                echo("<td>" . $verze . "</td><td>" . $stav_autor . "</td><td>" . $datum . "</td><td><a href='" . $base_path . $cesta . "' target='_blank'><button type='button' class='zobrazit'>Zobrazit</button></a></td>");
                // pokud byla verze vrácena k úpravě a neexistuje nová verze
                if(($stav_autor == "Vráceno k úpravě") && ($stav_redaktor != "Existuje nová verze"))
                {
                    // autor má možnost vytvořit novou verzi - zobrazím příslušný 'formulář'
                    echo("<td><form action='pridatClanekForm.php' method='POST'><input type='hidden' name='clanekNazev' value='" . $nazev . "'>");
                    echo("<input type='hidden' name='clanekCasopis' value='" . $id_casopisu . "'>");
                    echo("<input class='nova_verze' type='submit' name='verzeSubmit' value='Vytvořit novou verzi'></form></td>");
                }
                echo("</tr>");
            }       
        }
        // končím s výpisem
        // ukočím poslední sekci accordionu
        echo("</table></div></div>");
    }
?>

<?php } ?>

</div>

<?php /* patička */ require($base_path."foot.php"); $pdo = null; ?>