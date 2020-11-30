<?php
// ZMĚNIT PŘI KOPÍROVÁNÍ PROJEKTU
$base_path = ""; // pro absolutni referenci mezi soubory např. include($base_path."head.php"); 
$base_url = ""; // pro absolutni referenci url odkazu např. <link src="<?php echo $base_url>style.css">, <a href="<?php echo $base_url>clanky/cl1.pdf">
// bez předešlých se velice špatně používá relativná obzvláště, když se daná část přidává include (v případě head.php a style.css)
$ignore = true; // slouzi pro zastaveni odkazovani v indexu, protoze v headu testuju prihlaseni a neprihlaseneho by to na index odkazovalo nekonecnekrat
//Pridani jQueryUI
$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
require("head.php");
?>

<!-- Script pro accordion casopisu -->
<script>
    $(document).ready(function() {
        //nastava­m accordion - jQueryUI
        $(".accordion").accordion({
            // promenna velikost elementu
            heightStyle: "content", 
            // je moznost zavolat vsechny polozky
            collapsible: true, 
            active: false            
        });
        $(".butt").button();
    });
</script>

<!-- Pripojeni k db -->
<?php
    if(!include($base_path."db.php")) {
        echo("Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.");
    }
?>

<div id ="content" class = "text">
    
<p align = "justify"><span class="logospolytechnos">LOGOS POLYTECHNIKOS</span>  je vysokoškolský odborný recenzovaný časopis, který slouží pro publikační aktivity akademických pracovníků 
    Vysoké školy polytechnické Jihlava i jiných vysokých škol, univerzit a výzkumných organizací. Je veden na seznamu recenzovaných
    odborných a vědeckých časopisů ERIH PLUS - <a href="https://dbh.nsd.uib.no/publiseringskanaler/erihplus/periodical/info?id=488187">Reference Index for the Humanities and the Social Sciences</a>.</p>

<p align = "justify">Od roku 2010 do roku 2018 byl časopis vydáván čtyřikrát ročně v elektronické a tištěné podobě. Od roku 2019 vychází třikrát ročně v 
    elektronické verzi. Redakční rada časopisu sestává z interních i externích odborníků. Funkci šéfredaktora zastává prorektor pro
    tvůrčí a projektovou činnost Vysoké školy polytechnické Jihlava. Funkce odpovědných redaktorů jednotlivých čísel přísluší vedoucím 
    kateder Vysoké školy polytechnické Jihlava. Veškeré vydávané příspěvky prochází recenzním řízením a jsou pečlivě redigovány. </p>

<p align = "justify">Tematické a obsahové zaměření časopisu reflektuje potřeby oborových kateder Vysoké školy polytechnické Jihlava.
    Na základě souhlasu odpovědného redaktora mohou katedry poskytnout publikační prostor i odborníkům bez zaměstnanecké vazby 
    k Vysoké škole polytechnické Jihlava.</p>

<p align = "justify">V časopise je možné publikovat odborné články, statě, přehledové studie, recenze a další typy odborných příspěvků 
    v českém, slovenském a anglickém jazyce. Do recenzního řízení jsou přijímány příspěvky tematicky odpovídající zaměření časopisu a 
    formálně upravené dle redakční šablony.</p><br />


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
          echo("<span class='logospolytechnos'><h3> Termíny uzávěrek pro časopisy </h3></span>");
          echo("<ul>");
            while(($radek = $qry -> fetch(PDO::FETCH_BOTH)) != FALSE) {
                $tema = $radek["tema"];
                $datum = date_format(date_create($radek["datum_uzaverky"]),"j.n.Y");
                echo("<li>" . $tema . " " . "(" .$datum . ")" . "</li>");
            }
        echo("</ul>");
        echo("<br />");
?>


<!-- Vypis casopisu a jejich clanku-->
<?php
    try {
        //SQL dotaz -> vytahnuti tema a id casopisu
        $qry2 = $pdo->prepare("SELECT tema, id_casopisu FROM casopis WHERE zobrazit = 1");
        $qry2->execute();
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        echo("<span class='logospolytechnos'><h3>Vydané časopisy</h3></span>");
        //obalovaci div pro accordion
        echo("<div class='accordion'>");
            //Vytahni data z db 
            while(($radek = $qry2 -> fetch(PDO::FETCH_BOTH)) != FALSE) {
                $tema2 = $radek["tema"];
                $id_cas = $radek["id_casopisu"];
                //Header accordionu
                echo("<h3> $tema2 </h3>");
                //Div contentu accordionu
                echo("<div>");
             
                try {
                    //SQL dotaz -> vypis clanky a autory patrici do daneho casopisu
                    $query = $pdo->prepare("SELECT id_clanku, nazev, cesta FROM clanek NATURAL JOIN casopis 
                                            NATURAL JOIN verze 
                                            WHERE stav_autor LIKE 'Schváleno' AND id_casopisu = $id_cas;");
                    $query->execute();
                } catch (PDOException $ex) {
                    echo("Selhal dotaz " . $ex->getMessage());
                }

                if($query->rowCount() == 0)
                {
                    echo("<h2>Tento časopis nemá žádné schválené články.</h2></div>");
                }
                else
                {
                    echo("<table class='casopis_clanky' cellspacing='0'>");
                    echo("<tr><th class='casopis_nazev'>Název článku</th><th>Autoři</th><th>Možnosti</th></tr>");
                    while(($radek = $query -> fetch(PDO::FETCH_BOTH)) != FALSE) {
                    $nazev = $radek["nazev"];            
                    $path = $radek["cesta"];
                    $id_clanku = $radek["id_clanku"];
                    try
                    {
                        $query_autor = $pdo->prepare("SELECT jmeno, prijmeni FROM uzivatel NATURAL JOIN pise WHERE id_clanku = ?");
                        $params = array($id_clanku);
                        $query_autor->execute($params);
                    }
                    catch(PDOException $ex)
                    {
                        die($ex -> getMessage());
                    }
                    $autor = "";
                    while(($radek2 = $query_autor->fetch(PDO::FETCH_ASSOC)) != FALSE)
                    {
                        $autor .= $radek2["jmeno"] . " " . $radek2["prijmeni"] . "; ";
                    }
                    $autor = substr($autor, 0, -2);

                    echo("<tr><td class='casopis_nazev'>" .$nazev . "</td>" . "<td>". $autor . "</td>" .
                        "<td><a href = '$path' target='_blank'> <button type = 'button' class = 'butt'> Zobrazit článek </button></a></td></tr>");
                    }
                    
                    echo("</table>");
                    echo("</div>");
                }
            }
        echo("</div>");
?>
</div>
<?php require("foot.php"); $pdo = null; ?>