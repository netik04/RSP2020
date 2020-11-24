<?php
// ZMĚNIT PŘI KOPÍROVÁNÍ PROJEKTU
$base_path = ""; // pro absolutni referenci mezi soubory např. include($base_path."head.php"); 
$base_url = ""; // pro absolutni referenci url odkazu např. <link src="<?php echo $base_url>style.css">, <a href="<?php echo $base_url>clanky/cl1.pdf">
// bez předešlých se velice špatně používá relativná obzvláště, když se daná část přidává include (v případě head.php a style.css)
$ignore = true; // slouzi pro zastaveni odkazovani v indexu, protoze v headu testuju prihlaseni a neprihlaseneho by to na index odkazovalo nekonecnekrat
require("head.php");
?>

<?php
    if(!include($base_path."db.php")) {
        echo("Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.");
    }
?>
<div id ="content">
    
<p align = "justify">LOGOS POLYTECHNIKOS je vysokoškolský odborný recenzovaný časopis, který slouží pro publikační aktivity akademických pracovníků 
    Vysoké školy polytechnické Jihlava i jiných vysokých škol, univerzit a výzkumných organizací. Je veden na seznamu recenzovaných
    odborných a vědeckých časopisů ERIH PLUS - European Reference Index for the Humanities and the Social Sciences 
    (https://dbh.nsd.uib.no/publiseringskanaler/erihplus/periodical/info?id=488187).</p>

<p align = "justify">Od roku 2010 do roku 2018 byl časopis vydáván čtyřikrát ročně v elektronické a tištěné podobě. Od roku 2019 vychází třikrát ročně v 
    elektronické verzi. Redakční rada časopisu sestává z interních i externích odborníků. Funkci šéfredaktora zastává prorektor pro
    tvůrčí a projektovou činnost Vysoké školy polytechnické Jihlava. Funkce odpovědných redaktorů jednotlivých čísel přísluší vedoucím 
    kateder Vysoké školy polytechnické Jihlava. Veškeré vydávané příspěvky prochází recenzním řízením a jsou pečlivě redigovány. </p>

<p align = "justify">Tematické a obsahové zaměření časopisu reflektuje potřeby oborových kateder Vysoké školy polytechnické Jihlava.
    Na základě souhlasu odpovědného redaktora mohou katedry poskytnout publikační prostor i odborníkům bez zaměstnanecké vazby 
    k Vysoké škole polytechnické Jihlava.</p>

<p align = "justify">V časopise je možné publikovat odborné články, statě, přehledové studie, recenze a další typy odborných příspěvků 
    v českém, slovenském a anglickém jazyce. Do recenzního řízení jsou přijímány příspěvky tematicky odpovídající zaměření časopisu a 
    formálně upravené dle redakční šablony</p><br />


<!-- Vypis prijatych clanku -->
<?php
    try {
        //SQL dotaz -> vytahnuti id a nazvu clanku
        $query = $pdo->prepare("SELECT clanek.id_clanku, clanek.nazev FROM clanek JOIN
                               verze ON clanek.id_clanku = verze.id_clanku WHERE stav_autor LIKE 'Schváleno';");
        $query->execute();
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        //Vytahni data z db a vypis
          echo("<h3> Operační systémy, Hardware, Switche </h3>");
          echo("<ul>");
            while(($radek = $query -> fetch(PDO::FETCH_BOTH)) != FALSE) {
                $id = $radek["id_clanku"];
                $nazev = $radek["nazev"];
                echo("<li>" .$nazev . "</li>");
            }
        echo("</ul>");
?>

<br />

<!-- Vypis casopisu s datem uzaverky -->
<?php
    try {
        //SQL dotaz -> vytahnuti tema a data uzaverky casopisu
        $qry = $pdo->prepare("SELECT tema, datum_uzaverky FROM casopis WHERE datum_uzaverky > CURRENT_DATE;");
        $qry->execute();
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        //Vytahni data z db a vypis
          echo("<h3> Termíny uzávěrky pro časopisy </h3>");
          echo("<ul>");
            while(($radek = $qry -> fetch(PDO::FETCH_BOTH)) != FALSE) {
                $tema = $radek["tema"];
                $datum = date("j.m.yy", strtotime($radek["datum_uzaverky"]));
                echo("<li>" . $tema . " " . "(" .$datum . ")" . "</li>");
            }
        echo("</ul>");
?>
</div>
<?php require("foot.php"); $pdo = null; ?>