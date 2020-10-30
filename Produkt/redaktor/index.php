<?php
// ZMĚNIT PŘI KOP�?ROV�?N�? PROJEKTU
$base_path = "/home/studaci/public_html/product/development/v0_uzivatel/"; // pro absolutni referenci mezi soubory např. include($base_path."head.php"); 
$base_url = "https://alpha.kts.vspj.cz/~studaci/product/development/v0_uzivatel/"; // pro absolutni referenci url odkazu např. <link src="<?php echo $base_url>style.css">, <a href="<?php echo $base_url>clanky/cl1.pdf">
// bez předešlých se velice špatně používá relativná obzvláště, když se daná �?ást přidává include (v případě head.php a style.css)

require($base_path."head.php");
?>

<div id="content" class="redaktor">
    <?php // ZDE ZAČ�?N�? OBSAH STR�?NKY REDAKTOR ?>

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
            <div class="article">
                <div class="title">
                    <a href="clanek.php/?id=<?php echo($article["id"])?>"
                    <?php //ošetření délky názvu
                        if (strlen($article["nazev"]) > 50) {
                            echo("title=\"".$article["nazev"]."\">"); //on hover vypíše celý název
                            $stringCut = substr($article["nazev"], 0, 50);
                            $endPoint = strrpos($stringCut, ' '); 
                            echo(($endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0))."..."); // vypíše useknutou �?ást do poslední mezery
                        } else echo(">".$article["nazev"]);
                    ?>
                    </a>
                </div>
                <div class="control">
                    <span>
                        <span class="author"><?php echo($article["autor"])?></span><br>
                        <span class="date"><?php echo(date_format(date_create($article["datum"]),"j.n.Y"))?></span>
                    </span>
                    <span class="version"><?php echo($article["verze"])?>. verze</span>
                    <span class="state">Aktuální stav: <?php echo($article["stav_redaktor"])?></span>
                    <span class="buttons">
                        <a class="download button" target="_blank" href="<?php echo($base_url.$article["cesta"])?>">Nahlédnout</a><?php
                        ?><a class="accept button">Přijmout
                            <?php/*
                                switch($article["stav_redaktor"]){
                                    case "":
                                    break;
                                }
                            */
                            ?>
                        </a><?php
                        ?><a class="open button" href="clanek.php/?id=<?php echo($article["id"])?>">Otevřít detail</a>
                    </span>
                </div>
            </div>
        
        <?php
        }
        
        ?>

    <?php } ?>

</div>

<?php require($base_path."foot.php"); $pdo = null; ?>