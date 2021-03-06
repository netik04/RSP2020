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

<div id="content" class="redaktor home">
    <?php // ZDE ZAČÍNÁ OBSAH STRÁNKY REDAKTOR ?>

    <?php
    if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else {
    ?>
        <div class="main_title">Články</div>

        <div id="filter">
            <form action="" method="POST">
                <select name="state" class="button2">
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
                <select name="uzaverka" class="button2">
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

                <input class="button2 reg_button" type="submit" value="Filtruj">
            </form><?php
            ?><form action="" method="POST">
                <input class="button2" type="text" name="search" placeholder="článek nebo autor&hellip;" <?php echo(isset($_POST["search"]) ? "value=\"".$_POST["search"]."\"" : "")?>>
                <input class="button2 reg_button" type="submit" value="Hledej">
            </form><?php
            ?><button class="button2 reg_button" onclick="location.replace('<?php echo($base_path."redaktor");?>')">Zrušit filtry</button>
        </div>

        <script>
            $(document).ready(function(){
                $('.left').hover(function(){
                    $(this).parent().css('background-color','#444');
                }, function(){
                    $(this).parent().css('background-color','#333');
                });
            });
        </script>
        <?php

        $sql = "SELECT cl.id_clanku AS id, nazev, lv.verze, lv.datum, lv.datum_verze, verze.stav_redaktor, verze.cesta, datum_uzaverky, tema,
            GROUP_CONCAT((SELECT CONCAT(\" \", jmeno, \"&nbsp;\", prijmeni) from uzivatel where login = pise.login)) as autor
            , p.posudek_uzaverka FROM clanek AS cl
            JOIN casopis ON cl.id_casopisu = casopis.id_casopisu

            JOIN pise ON cl.id_clanku = pise.id_clanku
            JOIN uzivatel ON pise.login = uzivatel.login

            JOIN (SELECT id_clanku, Max(verze) AS verze, Min(datum) AS datum, Max(datum) AS datum_verze FROM verze GROUP BY id_clanku) AS lv ON cl.id_clanku = lv.id_clanku
            JOIN verze ON cl.id_clanku = verze.id_clanku AND lv.verze = verze.verze
            LEFT JOIN (SELECT id_clanku, verze, Max(datum_uzaverky) AS posudek_uzaverka FROM posudek GROUP BY id_clanku, verze) AS p ON cl.id_clanku = p.id_clanku AND lv.verze = p.verze ";
        if(!empty($_POST["state"]) || !empty($_POST['uzaverka']))
        {
            $data = array();
            $sql .= "WHERE ";
            if(!empty($_POST["state"])){
                $sql .= "stav_redaktor = :state";
                if(!empty($_POST['uzaverka'])) $sql .= " AND";
                $data = ['state' => $_POST["state"]];
            }
            if(!empty($_POST['uzaverka'])){
                $sql .= " datum_uzaverky = :uzaverka";
                $data += array('uzaverka' => $_POST["uzaverka"]);
            }
            $sql .= " GROUP BY cl.id_clanku";
        }
        else if(!empty($_POST["search"])){
            $sql = "SELECT * FROM (".$sql." GROUP BY cl.id_clanku) AS subQ WHERE nazev LIKE :search OR autor LIKE :search ";
            $data = ['search' => "%".htmlentities($_POST["search"], ENT_QUOTES, ENT_HTML5, "UTF-8")."%"];
        }
        else {
           $sql .= " GROUP BY cl.id_clanku";
        }
        $sql .= " ORDER BY datum_uzaverky DESC, datum_verze DESC LIMIT 25";//lv.dat

        $stmt = $pdo->prepare($sql);

        try{
            //$stmt = $pdo->query($sql);
            $stmt->execute($data);
        }
        catch(PDOException $e){
            echo($e);
        }
        while($article = $stmt->fetch(PDO::FETCH_ASSOC)){
            //$lastUzaverka = $article['datum_uzaverky'];
            if($lastUzaverka != $article['datum_uzaverky']){
                echo("<div class=\"casopis\">Uzávěrka: ".date_format(date_create($article["datum_uzaverky"]),"j.n.Y")." | Téma: ".$article["tema"]."</div>");
                $lastUzaverka = $article['datum_uzaverky'];
            }
        ?>
            <div class="article" id="<?php echo($article["id"])?>">
                <div class="control">
                    <a class="download button2" target="_blank" href="<?php echo($base_path.$article["cesta"])?>">Nahlédnout</a><?php
                        if($article['verze'] > 1){
                            $sql = "SELECT login, Concat(jmeno, ' ', prijmeni) AS jmeno FROM posudek ".
                                    "JOIN uzivatel ON posudek.login_recenzenta = uzivatel.login ".
                                    "WHERE osobni_revize = 1 AND id_clanku = ".$article['id']." AND verze = ".($article['verze'] - 1).";";
                            try{
                                $r_osobni_revize = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                            } 
                            catch(PDOException $e){
                                echo("<br>Při komunikaci s databází došlo k chybě.<br>Zkuste to prosím později.");
                                echo($e);
                                die();
                            }
                        }
                        
                        
                        switch($article["stav_redaktor"]){
                            /*case "Nově podaný":
                                echo("<button class=\"a_accept\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\">Přijmout</button>");
                            break;*/
                            /*case "Čeká na stanovení recenzentů":
                                echo("<button class=\"a_setR\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\">Stanovit recenzenty</button>");
                            break;*/
                            case "Nově podaný":
                                $str_acc = "<button class=\"a_accept\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\"";
    
                                if(count($r_osobni_revize) > 0){
                                    $str_acc .= " cl_r1=\"".$r_osobni_revize[0]['login']."\"";
                                    if(count($r_osobni_revize) > 1){
                                        $str_acc .= " cl_r2=\"".$r_osobni_revize[1]['login']."\"";
                                    }
                                }
    
                                $str_acc .= ">Přijmout</button>";
                                echo($str_acc);
                            break;
                            case "Čeká na stanovení recenzentů":
                                $str_setR = "<button class=\"a_setR\" page=\"redaktor\" cl_id=\"".$article["id"]."\" cl_ver=\"".$article["verze"]."\"";
    
                                if(count($r_osobni_revize) > 0){
                                    $str_setR .= " cl_r1=\"".$r_osobni_revize[0]['login']."\"";
                                    if(count($r_osobni_revize) > 1){
                                        $str_setR .= " cl_r2=\"".$r_osobni_revize[1]['login']."\"";
                                    }
                                }
    
                                $str_setR .= ">Stanovit recenzenty</button>";
                                echo($str_setR);
                            break;

                            default:
                            break;
                        }
                    ?><a class="open button2" href="clanek.php?id=<?php echo($article["id"])?>">Otevřít detail</a>
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
                            <span class="author">
                            <?php
                                echo($article["autor"]);
                            ?>
                            </span><br>
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
