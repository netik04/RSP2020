<?php

// ROLE KTERÁ MÁ PŘÍSTUP 
$role = "redaktor";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

if(!is_numeric($_GET['id'])){
    header("Location: ".$base_path."redaktor");
    die();
}
$article_id = (int)$_GET['id'];

$head_str = "<link rel=\"stylesheet\" href=\"redaktor_style.css\">";
$head_str .= "<script src=\"scripty/js/prijmuti_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/zobraz_form_recenzenti.js\"></script>";
$head_str .= "<script src=\"scripty/js/prirazeni_recenzentu.js\"></script>";
$head_str .= "<script src=\"scripty/js/zamitnuti_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/obnoveni_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/prijmout_k_vydani.js\"></script>";

require($base_path."head.php");
?>

<div id="content" class="redaktor clanek">
    <a class="back_button" href="<?php echo($base_path."redaktor")?>">&larr; Zpět</a>
    <?php
    if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else {      
        $sql = "SELECT nazev, lv.verze, lv.datum, lv.datum_verze, verze.stav_redaktor, verze.cesta, casopis.*, Concat(jmeno, ' ', prijmeni) AS autor FROM clanek AS cl
        JOIN casopis ON cl.id_casopisu = casopis.id_casopisu
        JOIN pise ON cl.id_clanku = pise.id_clanku
        JOIN uzivatel ON pise.login = uzivatel.login
        JOIN (SELECT id_clanku, Max(verze) AS verze, Min(datum) AS datum, Max(datum) AS datum_verze FROM verze GROUP BY id_clanku) AS lv ON cl.id_clanku = lv.id_clanku
        JOIN verze ON cl.id_clanku = verze.id_clanku AND lv.verze = verze.verze
        WHERE cl.id_clanku = ".$article_id;
        $stmt = $pdo->query($sql);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        ?>
        <div class="main_title"><?php echo($article["nazev"])?></div>
        <div id="<?php echo($article_id)?>" class="article detail">
                <?php /*<div class="left"> */?>
                <div class="info">
                    <span>
                        <span class="author"><?php echo($article["autor"])?></span><br>
                        <span class="l2"><?php echo(date_format(date_create($article["datum"]),"j.n.Y"))?></span>
                    </span>
                    <span>
                        <span class="version"><?php echo($article["verze"])?>. verze</span><br>
                        <span class="l2"><?php echo(date_format(date_create($article["datum_verze"]),"j.n.Y"))?></span>
                    </span>
                    <span class="state">Stav<br><span class="l2"><?php echo($article["stav_redaktor"])?></span></span>
                    <span>
                        Časopis<br>
                        <span class="l2">Téma: <?php echo($article["tema"])?></span><br>
                        <span class="l2">Uzávěrka: <?php echo(date_format(date_create($article["datum_uzaverky"]),"j.n.Y"))?></span>
                    </span>
                </div>
                <?php /*</div>*/?>
                <div class="control">
                    <a class="download button" target="_blank" href="<?php echo($base_path.$article["cesta"])?>">Nahlédnout</a><?php
                    ?><span class="accept button">
                        <?php
                        switch($article["stav_redaktor"]){
                            case "Nově podaný":
                                echo("<button class=\"a_accept\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Přijmout</button>");
                            break;
                            case "Čeká na stanovení recenzentů":
                                echo("<button class=\"a_setR\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Stanovit recenzenty</button>");
                            break;
                            case "Příspěvek zamítnut":
                                echo("<button class=\"a_undeny\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zrušit zamítnutí</button>");
                            break;
                            case "Příspěvek je přijat k vydání":
                                echo("<button class=\"a_undeny\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zrušit vydání</button>");
                            break;
                            case "1. posudek doručen redakci":
                            case "2. posudek doručen redakci":
                            case "Posudky odeslány autorovi":
                            case "Probíhá úprava textu autorem":
                                //echo("<a href=\"clanek?id=".$article["id"].")\">Zobrazit posudky</a>");
                                echo("<script>$('.article#".$article_id." .accept').hide()</script>");
                            break;
                        }
                        ?>
                    </span><?php
                    ?><span class="deny button">
                        <?php
                        switch($article["stav_redaktor"]){
                            case "Čeká na stanovení recenzentů":
                            case "Probíhá recenzní řízení":
                            case "1. posudek doručen redakci":
                            case "2. posudek doručen redakci":
                            case "Posudky odeslány autorovi":
                            case "Probíhá úprava textu autorem":
                                echo("<button class=\"a_deny\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zamítnout</button>");
                            break;
                            case "Nově podaný":
                            case "Příspěvek zamítnut":
                            case "Příspěvek je přijat k vydání":
                                echo("<script>$('.article#".$article_id." .deny').hide()</script>");
                            break;
                        }
                        ?>
                    </span><?php
                    ?><span class="release button">
                        <?php
                        switch($article["stav_redaktor"]){
                            case "2. posudek doručen redakci":
                                echo("<button cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Přijmout k vydání</button>");
                            break;
                            case "Nově podaný":
                            case "Čeká na stanovení recenzentů":
                            case "Probíhá recenzní řízení":
                            case "1. posudek doručen redakci":
                            case "Posudky odeslány autorovi":
                            case "Probíhá úprava textu autorem":
                            case "Příspěvek je přijat k vydání":
                            case "Příspěvek zamítnut":
                                echo("<script>$('.article#".$article_id." .release').hide()</script>");
                            break;
                        }
                        ?>
                    </span>
                </div>
                <div class="posudky">
                        Tady budou posudky
                </div>
            </div>

    <?php } ?>

</div>

<?php require($base_path."foot.php"); $pdo = null; ?>