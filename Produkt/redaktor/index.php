<?php

// ROLE KTERÁ MÁ PŘÍSTUP    
$role = "redaktor";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

$head_str = "<link rel=\"stylesheet\" href=\"redaktor_style.css\">";
$head_str .= "<script src=\"scripty/js/prijmuti_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/zobraz_form_recenzenti.js\"></script>";
$head_str .= "<script src=\"scripty/js/prirazeni_recenzentu.js\"></script>";

require($base_path."head.php");
?>

<div id="content" class="redaktor">
    <?php // ZDE ZAČÍNÁ OBSAH STRÁNKY REDAKTOR ?>

    <?php
    if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else {
    ?>
        <div class="main_title">Články</div>

        <?php
        
        $sql = "SELECT cl.id_clanku AS id, nazev, lv.verze, lv.datum, lv.datum_verze, verze.stav_redaktor, verze.cesta, id_casopisu, Concat(jmeno, ' ', prijmeni) AS autor FROM clanek AS cl
        JOIN pise ON cl.id_clanku = pise.id_clanku
        JOIN uzivatel ON pise.login = uzivatel.login
        JOIN (SELECT id_clanku, Max(verze) AS verze, Min(datum) AS datum, Max(datum) AS datum_verze FROM verze GROUP BY id_clanku) AS lv ON cl.id_clanku = lv.id_clanku
        JOIN verze ON cl.id_clanku = verze.id_clanku AND lv.verze = verze.verze
        LIMIT 25";
        $stmt = $pdo->query($sql);

        while($article = $stmt->fetch(PDO::FETCH_ASSOC)){
        ?>
            <div class="article" id="<?php echo($article["id"])?>">
                <a class="left" href="clanek?id=<?php echo($article["id"])?>">
                    <div class="title"
                        <?php //ošetření délky názvu
                            if (strlen($article["nazev"]) > 70) {
                                echo(" title=\"".$article["nazev"]."\">"); //on hover vypíše celý název
                                $stringCut = substr($article["nazev"], 0, 70);
                                $endPoint = strrpos($stringCut, ' '); 
                                echo(($endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0))."..."); // vypíše useknutou část do poslední mezery
                            } else echo(">".$article["nazev"]);
                        ?>
                    </div>
                    <div class="info">
                        <span>
                            <span class="author"><?php echo($article["autor"])?></span><br>
                            <span class="date"><?php echo(date_format(date_create($article["datum"]),"j.n.Y"))?></span>
                        </span>
                        <span class="version"><?php echo($article["verze"])?>. verze</span>
                        <span class="state">Stav: <?php echo($article["stav_redaktor"])?></span>
                    </div>
                </a>
                <div class="control">
                    <a class="download button" target="_blank" href="<?php echo($base_path.$article["cesta"])?>">Nahlédnout</a><?php
                    ?><span class="accept button">
                        <?php
                            switch($article["stav_redaktor"]){
                                case "Nově podaný":
                                    echo("<button class=\"a_new\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\">Přijmout</button>");
                                break;
                                case "Čeká na stanovení recenzentů":
                                    echo("<button class=\"a_setR\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\">Stanovit recenzenty</button>");
                                break;
                                case "Probíhá recenzní řízení";
                                case "1. posudek doručen redakci":
                                case "2. posudek doručen redakci":
                                case "Posudky odeslány autorovi":
                                case "Probíhá úprava textu autorem":
                                case "Příspěvek je přijat k vydání":
                                case "Příspěvek zamítnut":
                                    //echo("<a href=\"clanek?id=".$article["id"].")\">Zobrazit posudky</a>");
                                    echo("<script>$('.article#".$article["id"]." .accept').hide()</script>");
                                break;
                            }
                        ?>
                    </span><?php
                    ?><a class="open button" href="clanek.php?id=<?php echo($article["id"])?>">Otevřít detail</a>
                </div>
            </div>
        
        <?php
        }
        
        ?>

    <?php } ?>

</div>

<?php require($base_path."foot.php"); $pdo = null; ?>