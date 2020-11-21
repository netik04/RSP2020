<?php
$role = "administrator";

$base_path = "../";

$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
$head_str .= "<link rel='stylesheet' href='admin-style.css'>";

require($base_path."head.php");

$id = $_GET["id"];
$verze = $_GET["verze"];
$query = $pdo->prepare("SELECT stav_autor, stav_redaktor, datum, cesta, nazev, tema FROM verze NATURAL JOIN clanek NATURAL JOIN casopis WHERE id_clanku = ? AND verze = ?");
$params = array($id, $verze);
$query->execute($params);

$radek = $query->fetch(PDO::FETCH_ASSOC);
$stav_autor = $radek["stav_autor"];
$stav_redaktor = $radek["stav_redaktor"];
$datum = date("j.m.yy", strtotime($radek["datum"]));
$dateraw = $radek["datum"];
$cesta = $radek["cesta"];
$nazev = $radek["nazev"];
$tema = $radek["tema"];

?>

<script>
    $(document).ready(function()
    {
        $(".autor_button, #upravitVerzi,#upravitSubmit,#odstranitVerzi, #odstranitAno, #odstranitNe").button();      
        $("#modalUpravit").dialog({
            autoOpen: false,
            resizable: false, 
            width: 'auto',
            show: {
                effect: "fade", 
                duration: 200
            },
            hide: {
                effect: "fade", 
                duration: 200
            }
        });

        $("#modalOdstranit").dialog({
            autoOpen: false,
            resizable: false, 
            width: 'auto',
            show: {
                effect: "fade", 
                duration: 200
            },
            hide: {
                effect: "fade", 
                duration: 200
            }
        });

        $("#upravitVerzi").on("click", function(){
            $("#modalUpravit").dialog("open");
            $("#idClanku").val("<?php echo $id?>");
            $("#verzeClanku").val("<?php echo $verze?>");
            $("#stavAutor").val("<?php echo $stav_autor?>");
            $("#stavRedaktor").val("<?php echo $stav_redaktor?>");
            $("#datum").val("<?php echo $dateraw?>");
        });

        $("#upravitForm").submit(function(event){
            event.preventDefault();
            $.ajax('scripty/upravitVerzi.php', {
                type: 'POST',
                data: {
                    id: $("#idClanku").val(),
                    verze: $("#verzeClanku").val(),
                    stavAutor: $("#stavAutor").val(),
                    stavRedaktor: $("#stavRedaktor").val(),
                    datum: $("#datum").val()
                },
                success: function(result)
                {
                    if(result != "")
                    {
                        alert(result);
                    }
                    else
                    {
                        location.reload();
                    }
                }
            });
        });
        $("#odstranitVerzi").on("click", function(){
            $("#modalOdstranit").dialog("open");
        });

        $("#odstranitNe").on("click", function(){
            $("#modalOdstranit").dialog("close");
        });
        $("#odstranitAno").on("click", function(){
            $.ajax("scripty/odstraneniVerze.php", {
                type: "POST",
                data:{
                    id: <?php echo $id;?>,
                    verze: <?php echo $verze;?>
                },
                success: function(result)
                {
                    if(result != "")
                    {
                        alert(result);
                    }
                    else
                    {
                        document.location.replace("clanky.php");
                    }
                },
            });
        });
    });
</script>

<div id="content">
    <a href="clanky.php"><button class="autor_button">&#8592; Zpět na výpis</button></a><br /><br />

    <!-- MODAL EDIT-->
    <div id='modalUpravit' title='Upravit údaje o verzi'>
        <form id='upravitForm'> 
            <input type="hidden" name="idClanku" id="idClanku">
            <input type="hidden" name="verzeClanku" id="verzeClanku">
            <label for="stavAutor"></label><select name="stavAutor" id="stavAutor">
                <?php
                    $query = $pdo->query("SELECT SUBSTRING(COLUMN_TYPE,5) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='studaci' AND TABLE_NAME='verze' AND COLUMN_NAME='stav_autor'");
                    $row = $query->fetch(PDO::FETCH_BOTH);
                    $var = explode(",",substr($row[0],1,strlen($row[0])-2));
                    for($i = 0; $i < count($var); $i++){
                        echo ("<option value=\"". trim($var[$i],"'") ."\">".trim($var[$i],"'")."</option>");
                    }

                ?>
            </select><br/>
            <label for="stavRedaktor"></label><select name="stavRedaktor" id="stavRedaktor">
                <?php
                    $query = $pdo->query("SELECT SUBSTRING(COLUMN_TYPE,5) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='studaci' AND TABLE_NAME='verze' AND COLUMN_NAME='stav_redaktor'");
                    $row = $query->fetch(PDO::FETCH_BOTH);
                    $var = explode(",",substr($row[0],1,strlen($row[0])-2));
                    for($i = 0; $i < count($var); $i++){
                        echo ("<option value=\"". trim($var[$i],"'") ."\">".trim($var[$i],"'")."</option>");
                    }

                ?>
            </select><br/>
            <label for="datum"></label><input type="date" id="datum" name="datum">
            <br>
            <input type='submit' id='upravitSubmit' value='Upravit'>
        </form>
    </div>

    <!-- MODAL EDIT-->
    <div id='modalOdstranit' title='Potvrdit odstraneni'>
        <h3>Opravdu chcete odstranit verzi?</h3>
        <button id="odstranitAno">Ano</button>
        <button id="odstranitNe">Ne</button>
    </div>

    <?php
        if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // pokud se nepovedlo, nemá smysl pokračovat
        else
        {
            ?>
            <fieldset>
                <h2>Informace o článku</h2>
            <?php
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th>Název článku:</th><td>" . $nazev . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th>Verze článku:</th><td>" . $verze . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th>Datum nahrání:</th><td>" . $datum . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th>Stav autora:</th><td>" . $stav_autor . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th>Stav redaktora:</th><td>" . $stav_redaktor . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th>Téma:</th><td>" . $tema . "</td></tr></table><br />");
                
                echo("<div class='info_tlacitka'><a href='" . $base_path . $cesta . "' target='_blank'><button class='autor_button'>Zobrazit článek</button></a><button id='upravitVerzi'>Upravit</button><button id='odstranitVerzi'>Odstranit</button></div>");
            ?>
            </fieldset><br />
            <?php
            if($stav_autor == "Posudky doručeny" || $stav_autor == "Vráceno k úpravě" || $stav_autor == "Schváleno" || $stav_autor == "Zamítnuto")
            {  
                echo("<fieldset>");                              
                $query = $pdo->prepare("SELECT * FROM posudek WHERE id_clanku = ? AND verze = ?");
                $params = array($id, $verze);
                $query->execute($params); 
                
                if($query->rowCount() == 0 && $stav_autor == "Schváleno")
                {
                    echo("<h2>Verze byla schválena bez recenzního řízení.</h2>");
                }
                else
                {
                    echo("<h2>Oponentní posudky</h2>");
                    echo("<table class='autor_posudek' cellspacing='0'>");
                    echo("<tr><th>Č. posudku</th><th>Aktuálnost, zajímavost, přínosnost</th><th>Originalita</th><th>Odborná úroveň</th><th>Jazyková a stylistická úroveň</th><th>Otevřená odpověď</th><th>Vyjádření autora</th></tr>");
                    $i = 1;
                    while(($radek = $query->fetch(PDO::FETCH_ASSOC)) != FALSE)
                    {
                        $akt_zaj_prin = $radek["akt_zaj_prin"];
                        $jazyk_styl_prinos = $radek["jazyk_styl_prinos"];
                        $orig = $radek["originalita"];
                        $odbor = $radek["odbor_uroven"];
                        $otevrena_odpoved = $radek["otevrena_odpoved"];
                        $vyjadreni_autora = $radek["vyjadreni_autora"];
                        $login = $radek["login_recenzenta"];                   

                        echo("<tr><td>" . $i . "</td><td>" . $akt_zaj_prin . "</td><td>" . $jazyk_styl_prinos . "</td><td>" . $orig . "</td><td>" . $odbor . "</td><td>" . $otevrena_odpoved . "</td>");
                        echo("<td>" . $vyjadreni_autora . "</td>");
                        echo("</tr>");
                        $i++;
                    }
                    echo("</table><br />");
                }
                echo("</fieldset>");
            }
            else
            {
                echo("<fieldset><h2>K tomuto článku zatím nejsou dostupné posudky.</h2></fieldset>");
            }
            ?>
            <br />
            <fieldset>
                <h2>Chat s redakcí</h2>
            </fieldset>

            <?php
        }
    ?>
</div>

<?php require($base_path."foot.php"); ?>