<?php

// ROLE KTERÁ MÁ PŘÍSTUP
$role = "redaktor";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

$head_str = "<link rel=\"stylesheet\" href=\"redaktor_style.css\">";
$head_str .= "<script src=\"scripty/js/prijmuti_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/zobraz_form_recenzenti.js\"></script>";
$head_str .= "<script src=\"scripty/js/prirazeni_recenzentu.js\"></script>";
$head_str .= "<script src=\"scripty/js/zamitnuti_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/obnoveni_clanku.js\"></script>";
$head_str .= "<script src=\"scripty/js/prijmout_k_vydani.js\"></script>";
$head_str .= "<script src=\"scripty/js/odeslat_posudky.js\"></script>";
$head_str .= "<script src=\"scripty/js/zmena_verze.js\"></script>";
$head_str .= "<script src=\"scripty/js/zobraz_messagebox.js\"></script>";
$head_str .= "<script src=\"scripty/js/vratit_autorovi.js\"></script>";
$head_str .= "<script src=\"scripty/js/popbox_exit.js\"></script>";
$head_str .= "<script src=\"scripty/js/upozorneni_sefredaktora.js\"></script>";
$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";

require($base_path."head.php");

if(!is_numeric($_GET['id'])){
    header("Location: ".$base_path."redaktor");
    die();
}
$article_id = $_GET['id'];

if(is_numeric($_GET['verze'])){
    $article_verze = $_GET['verze'];
}
?>

<div id="content" class="redaktor clanek">
    <a class="back_button" href="<?php echo($base_path."redaktor/index.php")?>">&larr; Zpět</a><br>
    <?php
    if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else {
        if(isset($article_verze))
            $sql = "SELECT nazev, vs.verze, vs.datum, verze.datum AS datum_verze, verze.stav_redaktor, verze.cesta, verze.sefredaktor, casopis.*,
                GROUP_CONCAT((SELECT CONCAT(\" \", jmeno, \"&nbsp;\", prijmeni) from uzivatel where login = pise.login)) as autor
                , p.posudek_uzaverka, p.pocet_posudku, p2.poc_vypln_posudku
                FROM clanek AS cl
                JOIN casopis ON cl.id_casopisu = casopis.id_casopisu
                JOIN pise ON cl.id_clanku = pise.id_clanku
                JOIN uzivatel ON pise.login = uzivatel.login
                JOIN (SELECT id_clanku, Max(verze) AS verze, Min(datum) AS datum FROM verze GROUP BY id_clanku) AS vs ON cl.id_clanku = vs.id_clanku
                JOIN verze ON cl.id_clanku = verze.id_clanku
                LEFT JOIN (SELECT id_clanku, verze, Max(datum_uzaverky) AS posudek_uzaverka, Count(*) AS pocet_posudku FROM posudek GROUP BY id_clanku, verze) AS p ON cl.id_clanku = p.id_clanku AND verze.verze = p.verze
                LEFT JOIN (SELECT id_clanku, verze, Count(*) AS poc_vypln_posudku FROM posudek WHERE datum_vytvoreni IS NOT NULL GROUP BY id_clanku, verze) AS p2 ON cl.id_clanku = p2.id_clanku AND verze.verze = p2.verze
                WHERE cl.id_clanku = ".$article_id." AND verze.verze = ".$article_verze
            ;
        else
            $sql = "SELECT nazev, lv.verze, lv.datum, lv.datum_verze, verze.stav_redaktor, verze.cesta, verze.sefredaktor, casopis.*,
                GROUP_CONCAT((SELECT CONCAT(\" \", jmeno, \"&nbsp;\", prijmeni) from uzivatel where login = pise.login)) as autor
                , p.posudek_uzaverka, p.pocet_posudku, p2.poc_vypln_posudku
                FROM clanek AS cl
                JOIN casopis ON cl.id_casopisu = casopis.id_casopisu
                JOIN pise ON cl.id_clanku = pise.id_clanku
                JOIN uzivatel ON pise.login = uzivatel.login
                JOIN (SELECT id_clanku, Max(verze) AS verze, Min(datum) AS datum, Max(datum) AS datum_verze FROM verze GROUP BY id_clanku) AS lv ON cl.id_clanku = lv.id_clanku
                JOIN verze ON cl.id_clanku = verze.id_clanku AND lv.verze = verze.verze
                LEFT JOIN (SELECT id_clanku, verze, Max(datum_uzaverky) AS posudek_uzaverka, Count(*) AS pocet_posudku FROM posudek GROUP BY id_clanku, verze) AS p ON cl.id_clanku = p.id_clanku AND lv.verze = p.verze
                LEFT JOIN (SELECT id_clanku, verze, Count(*) AS poc_vypln_posudku FROM posudek WHERE datum_vytvoreni IS NOT NULL GROUP BY id_clanku, verze) AS p2 ON cl.id_clanku = p2.id_clanku AND verze.verze = p2.verze
                WHERE cl.id_clanku = ".$article_id
            ;

        try{
            $stmt = $pdo->query($sql);
        } catch (PDOException $e) {
            echo("Při komunikaci s databází nastala chyba.<br>Zkuste to prosím později.");
            //echo($e);
            $error = true;
        }

        if(!$error){
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!isset($article_verze))
                $article_verze = $article['verze'];

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
                        <span class="version"><?php echo($article_verze)?>. verze</span><br>
                        <?php
                        if($article_verze > 1)
                            echo("<span class=\"l2\">".date_format(date_create($article["datum_verze"]),"j.n.Y")."</span>");
                        ?>
                    </span>
                    <span class="state">Stav<br><span class="l2">
                        <?php
                            echo($article["stav_redaktor"]);
                            if($article["stav_redaktor"] == "Probíhá recenzní řízení" || $article["stav_redaktor"] == "1. posudek doručen redakci" || $article["stav_redaktor"] == "2. posudek doručen redakci"){
                                echo("<br><span class=\"l3\">Uzávěrka recenze: ".date_format(date_create($article['posudek_uzaverka']),"j.n.Y")."</span>");
                                echo("<br><span class=\"l3\">Hotové posudky: ".($article['poc_vypln_posudku'] == ""? "0" : $article['poc_vypln_posudku'])."/".$article['pocet_posudku']."</span>");
                            }
                        ?>
                    </span></span>
                    <span>
                        Časopis<br>
                        <span class="l2">Téma: <?php echo($article["tema"])?></span><br>
                        <span class="l2">Uzávěrka: <?php echo(date_format(date_create($article["datum_uzaverky"]),"j.n.Y"))?></span>
                    </span>
                    
                    <?php
                    if(/*$article['pocet_posudku'] > 0 && */$article_verze > 1){
                        $sql = "SELECT login, Concat(jmeno, ' ', prijmeni) AS jmeno FROM posudek ".
                                "JOIN uzivatel ON posudek.login_recenzenta = uzivatel.login ".
                                "WHERE osobni_revize = 1 AND id_clanku = ".$article_id." AND verze = ".($article_verze-1);
                        try{
                            $r_osobni_revize = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                        } 
                        catch(PDOException $e){
                            echo("<br>Při komunikaci s databází došlo k chybě.<br>Zkuste to prosím později.");
                            die();
                        }

                        if(count($r_osobni_revize) > 0){
                        ?>
                        <span>Vyžadována osobní revize<br><span class="l2">
                        <?php
                            echo($r_osobni_revize[0]['jmeno']);
                            if(count($r_osobni_revize) > 1){
                                echo(" a ".$r_osobni_revize[1]['jmeno']);
                            }
                        ?>
                        </span></span>
                        <?php
                        }
                    }

                    ?>
                </div>
                <div class="control">
                    <a class="download button2" target="_blank" href="<?php echo($base_path.$article["cesta"])?>">Nahlédnout</a><?php

                    switch($article["stav_redaktor"]){
                        case "Nově podaný":
                            $str_acc = "<button class=\"a_accept\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\"";

                            if(count($r_osobni_revize) > 0){
                                $str_acc .= " cl_r1=\"".$r_osobni_revize[0]['login']."\"";
                                if(count($r_osobni_revize) > 1){
                                    $str_acc .= " cl_r2=\"".$r_osobni_revize[1]['login']."\"";
                                }
                            }

                            $str_acc .= ">Přijmout</button>";
                            echo($str_acc);

                            //echo("<button class=\"a_accept\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Přijmout</button>");
                        break;
                        case "Čeká na stanovení recenzentů":
                            // stanovit recenzenty

                            $str_setR = "<button class=\"a_setR\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\"";

                            if(count($r_osobni_revize) > 0){
                                $str_setR .= " cl_r1=\"".$r_osobni_revize[0]['login']."\"";
                                if(count($r_osobni_revize) > 1){
                                    $str_setR .= " cl_r2=\"".$r_osobni_revize[1]['login']."\"";
                                }
                            }

                            $str_setR .= ">Stanovit recenzenty</button>";
                            echo($str_setR);

                            // vrátit k úpravám
                            echo("<button class=\"a_return\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Vrátit k úpravám</button>");

                            // přijmout k vydání
                            if($article_verze > 1){ //&& count($r_osobni_revize) == 0)
                                echo("<button class=\"a_release\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Přijmout k vydání</button>");
                            }
                                // zamítnout
                            echo("<button class=\"a_deny\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zamítnout</button>");
                        break;
                        case "Příspěvek zamítnut":
                            echo("<button class=\"a_undeny\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zrušit zamítnutí</button>");
                        break;
                        case "Příspěvek je přijat k vydání":
                            echo("<button class=\"a_undeny\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zrušit vydání</button>");
                        break;
                        case "1. posudek doručen redakci":
                            if(/*count($r_osobni_revize) != 1 &&*/ $article['pocet_posudku'] != 1){
                                break;
                            }
                        case "2. posudek doručen redakci":
                            echo("<button class=\"a_sendP\" page=\"clanek\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Odeslat posudky autorovi</button>");
                        break;
                        case "Posudky odeslány autorovi":
                            echo("<button class=\"a_return\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Vrátit k úpravám</button>");
                            if($article_verze > 1){
                                echo("<button class=\"a_release\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Přijmout k vydání</button>");
                            }
                            echo("<button class=\"a_deny\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zamítnout</button>");
                        break;
                        case "Probíhá úprava textu autorem":
                            if($article_verze == $article['verze'])
                                echo("<button class=\"a_deny\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\">Zamítnout</button>");
                        break;

                        case "Probíhá recenzní řízení":
                        case "Existuje nová verze":
                        break;
                    }

                    echo("<button class=\"a_sefR\" cl_id=\"".$article_id."\" cl_ver=\"".$article["verze"]."\" cl_s=\"".$article['sefredaktor']."\" r=\"".$role."\" bp=\"".$base_path."\">".($article['sefredaktor']?"Zrušit upozornění šéfredaktora":"Upozornit šéfredaktora")."</button>");

                    if($article['verze'] > 1){
                        echo("<select class=\"button2 change_ver\">".
                            "<option selected disabled>Zobrazit jinou verzi</option>"
                        );

                        $i = 1;
                        $str = "";

                        while($i <= (int)$article['verze']){
                            if((isset($_GET["verze"]) && $i == $article_verze) || (!isset($_GET["verze"]) && $i == $article['verze']))
                                echo("<option value=\"".$i."\">".$i." - zobrazená</option>");
                            else
                                echo("<option value=\"".$i."\">".$i."</option>");
                            $i++;
                        }

                        echo("</select>");
                    }
                ?></div>

                <?php
                    $sql = "SELECT Concat(jmeno, ' ', prijmeni) AS recenzent, akt_zaj_prin, jazyk_styl_prinos, originalita, odbor_uroven, otevrena_odpoved, datum_vytvoreni, osobni_revize, vyjadreni_autora FROM posudek
                    JOIN uzivatel ON posudek.login_recenzenta = uzivatel.login
                    WHERE id_clanku = ".$article_id. " AND verze = ".$article_verze." AND datum_vytvoreni IS NOT NULL
                    ORDER BY datum_vytvoreni";

                    try{
                        $stmt = $pdo->query($sql);
                    } catch (PDOException $e) {
                        echo("Při načítaní posudků došlo k chybě.<br>Zkuste to prosím později.");
                        $error = true;
                    }

                    if(!$error && $stmt->rowCount() > 0){
                        $posudek = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="posudky">Posudky<br>
                        <span class="l2">Datum uzávěrky: <?php echo(date_format(date_create($article['posudek_uzaverka']),"j.n.Y"))?></span>
                        <table><thead><tr>
                            <th>Recenzent</th>
                            <th>Aktuálnost, zajímavost a přínosnost</th>
                            <th>Jazyková a stylistická úroveň</th>
                            <th>Originalita</th>
                            <th>Odborná úroveň</th>
                            <th>Otevřená odpověď</th>
                            <th>Datum vytvoření</th>
                            <th>Vyjádření autora</th>
                            <th>Osobní revize</th>
                        </tr></thead>
                        <tbody>
                        <?php

                        while($posudek){
                            echo(
                                "<tr>" .
                                    "<td>".$posudek['recenzent']."</td>" .
                                    "<td>".$posudek['akt_zaj_prin']."</td>" .
                                    "<td>".$posudek['jazyk_styl_prinos']."</td>" .
                                    "<td>".$posudek['originalita']."</td>" .
                                    "<td>".$posudek['odbor_uroven']."</td>" .
                                    "<td>".$posudek['otevrena_odpoved']."</td>" .
                                    "<td>".date_format(date_create($posudek['datum_vytvoreni']),"j.n.Y")."</td>" .
                                    "<td>".$posudek['vyjadreni_autora']."</td>" .
                                    "<td>".($posudek['osobni_revize']?"ANO":"NE")."</td>" .
                                "</tr>"
                            );
                            $posudek = $stmt->fetch(PDO::FETCH_ASSOC);
                        }
                        echo("</tbody></table></div>");
                    }
                ?>
            </div>
                <script>
                    $(document).ready( function(){ $("#odeslatZpravu").button(); });
                </script>
                <div class="article">
                    <div id="messageWrap">
                        <div id="messagesMenu">
                            <button id="interni" class="button2">Redakce</button>
                            <button id="autorsky" class="button2">Autor</button>
                        </div>
                        <div id="messageBox">
                        </div>
                        <form id="messageSender" action="scripty/odeslatZpravu.php">
                                <input type="hidden" id="zpravaId" name="id" value="<?php echo $article_id?>">
                                <input type="hidden" id="zpravaVerze" name="verze" value="<?php echo $article_verze?>">
                                <input type="hidden" id="inter" name="interni" value="1">
                                <input type="text" id="message" name="message" required>
                                <input id="odeslatZpravu" type="submit" name="odeslatZpravu" value="Odeslat" <?php if($article_verze !== $article["verze"])echo "disabled"; ?>>
                        </form>
                        <div id="errorMessage"><?php echo $_SESSION["errorMessage"]; unset($_SESSION["errorMessage"]); ?></div>
                    </div>
                </div>

                <script>
                    function zobrazZpravy(){
                        $.ajax('scripty/zobrazZpravy.php', {
                                type: 'POST',  // http method
                                data: {
                                    article_id: <?php echo $article_id ?>,
                                    article_verze: <?php echo $article_verze ?>,
                                    interni: interni
                                },  // data to submit
                                success: function (data) {
                                    $('#messageBox').html(data);
                                    var objDiv = document.getElementById("messageBox");
                                    objDiv.scrollTop = objDiv.scrollHeight;
                                },
                                error: function (errorMessage) {
                                    $('#errorMessage').text('Error' + errorMessage);
                                }
                            });
                    }

                    $(document).ready(function(){
                        $(function() {
                            var interni = <?php if(!isset($_SESSION["interni"])) $_SESSION["interni"]=1; echo $_SESSION["interni"]; ?>;
                            if(interni == 1){
                                $('#interni').click();
                            }else{
                                $('#autorsky').click();
                            }

                        });
                        $(".button2").click(function(){
                            if($(this).attr("id") == "interni"){
                                interni = 1;
                                $("#inter").val(1);
                                $("#autorsky").removeClass("active");
                                $("#interni").addClass("active");
                                $.ajax('scripty/zapisSessionInterni.php', {
                                    type: 'POST',  // http method
                                    data: {
                                        interni: interni
                                    },  // data to submit
                                    success: function (data) {

                                    },
                                    error: function (errorMessage) {
                                        $('#errorMessage').text('Error' + errorMessage);
                                    }
                                });
                            }else if($(this).attr("id") == "autorsky"){
                                interni = 0;
                                $("#inter").val(0);
                                $("#interni").removeClass("active");
                                $("#autorsky").addClass("active");
                                $.ajax('scripty/zapisSessionInterni.php', {
                                    type: 'POST',  // http method
                                    data: {
                                        interni: interni
                                    },  // data to submit
                                    success: function (data) {

                                    },
                                    error: function (errorMessage) {
                                        $('#errorMessage').text('Error' + errorMessage);
                                    }
                                });
                            }

                            zobrazZpravy();
                        });

                        $("#messageSender").submit(function(event){
                                event.preventDefault();
                                $.ajax('scripty/odeslatZpravu.php', {
                                    type: 'POST',
                                    data: {
                                        id: $("#zpravaId").val(),
                                        verze: $("#zpravaVerze").val(),
                                        interni: $("#inter").val(),
                                        message: $("#message").val()
                                    },
                                    success: function(result)
                                    {
                                        if(result != "")
                                        {
                                            alert(result);
                                        }
                                        else
                                        {
                                            zobrazZpravy();
                                            $("#message").val("");
                                        }
                                    }
                                });
                            });
                    });
                </script>

    <?php } } ?>

</div>

<?php require($base_path."foot.php"); $pdo = null; ?>
