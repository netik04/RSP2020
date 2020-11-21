<?php

// ROLE KTERÁ MÁ PŘÍSTUP    
$role = "redaktor";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

$head_str = "<link rel=\"stylesheet\" href=\"redaktor_style.css\">";
$head_str .= "<script src=\"scripty/js/prijmuti_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/zobraz_form_recenzenti.js\"></script>";
$head_str .= "<script src=\"scripty/js/prirazeni_recenzentu.js\"></script>";
$head_str .= "<script src=\"scripty/js/popbox_exit.js\"></script>";

require($base_path."head.php");
?>

<div id="content" class="redaktor">
    <?php // ZDE ZAČÍNÁ OBSAH STRÁNKY REDAKTOR ?>

    <?php
    if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else {
    ?>
        <div class="main_title">Články</div>

        <div id="filter">
            <form action="" method="POST">
                <select name="state" class="button">
                    <?php 
                        $states = array(
                            "Nově podaný",
                            "Čeká na stanovení recenzentů",
                            "Probíhá recenzní řízení",
                            "1. posudek doručen redakci",
                            "2. posudek doručen redakci",
                            "Posudky odeslány autorovi",
                            "Probíhá úprava textu autorem",
                            "Příspěvek je přijat k vydání",
                            "Příspěvek zamítnut"/*,
                            "Existuje nová verze"*/
                        );

                        echo("<option value=\"\" disabled ".(!empty($_POST["state"]) ? "" : "selected").">Stav</option>");
                        echo("<option value=\"\">Vše</option>");

                        foreach($states as $s){
                            if(isset($_POST["state"]) && $s == $_POST["state"])
                                echo("<option selected>".$s."</option>");
                            else
                                echo("<option>".$s."</option>");
                        }
                    ?> 
                </select>
                <select name="uzaverka" class="button">
                    <?php
                        echo("<option value=\"\" disabled ".(!empty($_POST["uzaverka"]) ? "" : "selected").">Uzávěrka časopisu</option>");
                        echo("<option value=\"\">Vše</option>");

                        $stmt = $pdo->query("SELECT DISTINCT datum_uzaverky FROM casopis ORDER BY datum_uzaverky DESC;");
              
                        while($uzaverka = $stmt->fetch(PDO::FETCH_COLUMN)){
                            echo("<option".((isset($_POST["uzaverka"]) && $uzaverka == $_POST["uzaverka"]) ? " selected" : "").
                            " value=\"".$uzaverka."\">".
                            date_format(date_create($uzaverka),"j.n.Y")."</option>");
                        }

                    ?> 
                </select>

                <input class="button" type="submit" value="Filtruj">
            </form><?php
            ?><form action="" method="POST">
                <input class="button" type="text" name="search" placeholder="článek nebo autor&hellip;" <?php echo(isset($_POST["search"]) ? "value=\"".$_POST["search"]."\"" : "")?>>
                <input class="button" type="submit" value="Hledej">
            </form><?php
            ?><button class="button" onclick="location.replace('<?php echo($base_path."redaktor");?>')">Zrušit filtry</button>
        </div>

        <script>
            $(document).ready(function(){
                $('.left').hover(function(){
                    $(this).parent().css('background-color','#666');
                }, function(){
                    $(this).parent().css('background-color','#555');
                });
            });
        </script>
        <?php
        
        $sql = "SELECT cl.id_clanku AS id, nazev, lv.verze, lv.datum, lv.datum_verze, verze.stav_redaktor, verze.cesta, datum_uzaverky, tema, Concat(jmeno, ' ', prijmeni) AS autor, p.posudek_uzaverka FROM clanek AS cl
            JOIN casopis ON cl.id_casopisu = casopis.id_casopisu
            JOIN pise ON cl.id_clanku = pise.id_clanku
            JOIN uzivatel ON pise.login = uzivatel.login
            JOIN (SELECT id_clanku, Max(verze) AS verze, Min(datum) AS datum, Max(datum) AS datum_verze FROM verze GROUP BY id_clanku) AS lv ON cl.id_clanku = lv.id_clanku
            JOIN verze ON cl.id_clanku = verze.id_clanku AND lv.verze = verze.verze
            LEFT JOIN (SELECT id_clanku, verze, Max(datum_uzaverky) AS posudek_uzaverka FROM posudek GROUP BY id_clanku, verze) AS p ON cl.id_clanku = p.id_clanku AND lv.verze = p.verze ";
        if(!empty($_POST["state"]) || !empty($_POST['uzaverka']))
            $sql .= "WHERE ";
            if(!empty($_POST["state"])){
                $sql .= "stav_redaktor = '".$_POST["state"]."'";
                if(!empty($_POST['uzaverka'])) $sql .= " AND";
            }
            if(!empty($_POST['uzaverka']))
                $sql .= " datum_uzaverky = '".$_POST["uzaverka"]."'";
        else if(!empty($_POST["search"]))
            $sql .= "WHERE nazev LIKE '%".$_POST["search"]."%' OR prijmeni LIKE '%".$_POST["search"]."%' OR jmeno LIKE '%".$_POST["search"]."%' ";
        $sql .= " ORDER BY datum_uzaverky, lv.datum_verze DESC LIMIT 25";
        $stmt = $pdo->query($sql);//GROUP BY datum_uzaverky

        while($article = $stmt->fetch(PDO::FETCH_ASSOC)){
            //$lastUzaverka = $article['datum_uzaverky'];
            if($lastUzaverka != $article['datum_uzaverky']){
                echo("<div class=\"casopis\">Uzávěrka: ".date_format(date_create($article["datum_uzaverky"]),"j.n.Y")." | Téma: ".$article["tema"]."</div>");
                $lastUzaverka = $article['datum_uzaverky'];
            }
        ?>
            <div class="article" id="<?php echo($article["id"])?>">
                <div class="control">
                    <a class="download button" target="_blank" href="<?php echo($base_path.$article["cesta"])?>">Nahlédnout</a><?php
                        switch($article["stav_redaktor"]){
                            case "Nově podaný":
                                echo("<button class=\"a_accept\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\">Přijmout</button>");
                            break;
                            case "Čeká na stanovení recenzentů":
                                echo("<button class=\"a_setR\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\">Stanovit recenzenty</button>");
                            break;
                            //default:
                            case "Probíhá recenzní řízení";
                            case "1. posudek doručen redakci":
                            case "2. posudek doručen redakci":
                            case "Posudky odeslány autorovi":
                            case "Probíhá úprava textu autorem":
                            case "Příspěvek je přijat k vydání":
                            case "Příspěvek zamítnut":
                            case "Existuje nová verze":
                                //echo("<a href=\"clanek?id=".$article["id"].")\">Zobrazit posudky</a>");
                                //echo("<script>$('.article#".$article["id"]." .accept').hide()</script>");
                            break;
                        }
                    ?><a class="open button" href="clanek.php?id=<?php echo($article["id"])?>">Otevřít detail</a>
                </div>
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
                        <span class="version"><?php echo($article["verze"])?>. verze<br>
                            <?php 
                                if($article["verze"] > 1)
                                    echo("<span class=\"date\">".(date_format(date_create($article["datum_verze"]),"j.n.Y"))."</span>");
                            ?>
                        </span>
                        <span class="state">Stav:
                            <?php 
                                echo($article["stav_redaktor"]);
                                if($article["stav_redaktor"] == "Probíhá recenzní řízení" || $article["stav_redaktor"] == "1. posudek doručen redakci")
                                    echo("<br><span class=\"date\">Uzávěrka recenze: ".date_format(date_create($article['posudek_uzaverka']),"j.n.Y")."</span>");
                            ?>
                        </span>
                    </div>
                </a>
                
            </div>
        
        <?php
        }
        
        ?>

    <?php } ?>

</div>

<?php require($base_path."foot.php"); $pdo = null; ?>