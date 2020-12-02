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
        $(".autor_button, #upravitVerzi,#upravitSubmit,#odstranitVerzi, #odstranitAno, #odstranitNe, .posudekButton").button();
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

        $("#posudekModal").dialog({
            autoOpen: false,
            resizable: false,
            width: 369,
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
        var log;
        $(".posudekButton").on("click", function()
        {
            log = $(this).parent().parent().children('.log').text();
            $("#posudekModal").dialog("open");
        });

        $("#posudekOdeslat").on("click", function()
        {
            $.ajax("scripty/upravitPosudek.php", {
                type: "POST",
                data:{
                    login: $("#posudekLogin").val(),
                    id: <?php echo $id; ?>,
                    verze: <?php echo $verze; ?>,
                    login_rec: log
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
                try
                {
                    $query_autor = $pdo->prepare("SELECT jmeno, prijmeni FROM uzivatel NATURAL JOIN pise WHERE id_clanku = ?");
                    $params = array($id);
                    $query_autor->execute($params);
                }
                catch(PDOException $ex)
                {
                    die("Nastala chyba. Zkuste to prosím později.");
                }
                $autor = "";
                while(($radek2 = $query_autor->fetch(PDO::FETCH_ASSOC)) != FALSE)
                {
                    $autor .= $radek2["jmeno"] . " " . $radek2["prijmeni"] . "; ";
                }
                $autor = substr($autor, 0, -2);            
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Název článku:</span></th><td>" . $nazev . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Verze článku:</span></th><td>" . $verze . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class='blue'>Autoři:</span></th><td>" . $autor . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Datum nahrání:</span></th><td>" . $datum . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Stav autora:</span></th><td>" . $stav_autor . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Stav redaktora:</span></th><td>" . $stav_redaktor . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Téma:</span></th><td>" . $tema . "</td></tr></table><br />");

                echo("<div class='info_tlacitka'><a href='" . $base_path . $cesta . "' target='_blank'><button class='autor_button'>Zobrazit článek</button></a><button id='upravitVerzi'>Upravit</button><button id='odstranitVerzi'>Odstranit</button></div>");
            ?>
            </fieldset><br />
            <?php
            $query = $pdo->prepare("SELECT * FROM posudek WHERE id_clanku = ? AND verze = ?");
            $params = array($id, $verze);
            $query->execute($params);
            if($query->rowCount() == 0 && $stav_autor == "Schváleno")
            {
                echo("<fieldset><h2>Verze byla schválena bez recenzního řízení.</h2></fieldset>");
            }
            else if($query->rowCount() > 0)
            {
                echo("<div id='fieldsetPosudek'><fieldset>");
                    echo("<h2>Oponentní posudky</h2>");
                    echo("<table class='autor_posudek' cellspacing='0'>");
                    echo("<tr><th>Č. posudku</th><th>Aktuálnost, zajímavost, přínosnost</th><th>Originalita</th><th>Odborná úroveň</th><th>Jazyková a stylistická úroveň</th><th>Otevřená odpověď</th><th>Vyjádření autora</th><th>Přiděleno recenzentovi</th><th>Možnosti</th></tr>");
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
                        echo("<td>" . $vyjadreni_autora . "</td><td class='log'>" . $login . "</td><td><button class='posudekButton'");

                        if($radek["datum_vytvoreni"] != "")
                        {
                            echo("disabled>Změnit</button></td>");
                        }
                        else
                        {
                            echo(">Změnit</button></td>");
                        }
                        echo("</tr>");
                        $i++;
                    }
                    echo("</table><br />");
                    echo("</fieldset></div>");
            }
            else
            {
                echo("<fieldset><h2>K tomuto článku zatím nejsou dostupné posudky.</h2></fieldset>");
            }
            ?>
            <br />
            <fieldset>

                <script>
                    $(document).ready( function(){ 
                        $("#odeslatZpravu").button(); 
                        $('#messageBox').on('scroll', chk_scroll);
                    });
                </script>
                    <div id="messageWrap">
                        <div id="messagesMenu">
                            <button id="interni" class="button2">Redakce</button>
                            <button id="autorsky" class="button2">Autor</button>
                        </div>
                        <div id="messageBox">
                        </div>
                        <form id="messageSender" action="scripty/odeslatZpravu.php">
                                <input type="hidden" id="zpravaId" name="id" value="<?php echo $id?>">
                                <input type="hidden" id="zpravaVerze" name="verze" value="<?php echo $verze?>">
                                <input type="hidden" id="inter" name="interni" value="1">
                                <input type="text" id="message" name="message" required>
                                <input id="odeslatZpravu" type="submit" name="odeslatZpravu" value="Odeslat" <?php if($article_verze !== $article["verze"])echo "disabled"; ?>>
                        </form>
                        <div id="errorMessage"><?php echo $_SESSION["errorMessage"]; unset($_SESSION["errorMessage"]); ?></div>
                    </div>

                <script>
                    var timer;

                    function zobrazZpravy(neco){
                        $.ajax('<?php echo $base_path?>scripty/zobrazZpravy.php', {
                                type: 'POST',  // http method
                                data: {
                                    article_id: <?php echo $id ?>,
                                    article_verze: <?php echo $verze ?>,
                                    interni: interni
                                },  // data to submit
                                success: function (data) {
                                    $('#messageBox').html(data);
                                    if(neco == true){
                                        var objDiv = document.getElementById("messageBox");
                                        objDiv.scrollTop = objDiv.scrollHeight;
                                    }
                                    
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
                                $.ajax("<?php echo $base_path?>scripty/zapisSessionInterni.php", {
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
                                $.ajax("<?php echo $base_path?>scripty/zapisSessionInterni.php", {
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
                            zobrazZpravy(true);
                            startTimer(true);
                        });

                        $("#messageSender").submit(function(event){
                                event.preventDefault();
                                $.ajax("<?php echo $base_path?>scripty/odeslatZpravu.php", {
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
                                            zobrazZpravy(true);
                                            $("#message").val("");
                                        }
                                    }
                                });
                            });
                    });


                    function startTimer(neco){
                        clearInterval(timer);
                        timer = setInterval(function(){ zobrazZpravy(neco) }, 2000);
                    }

                    function stopTimer(){
                        clearInterval(timer);
                    }

                    function chk_scroll(e) {
                        var elem = $(e.currentTarget);
                        if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()) {
                            stopTimer();
                            startTimer(true);
                        }else{
                            stopTimer();
                            startTimer(false);
                        }
                    }
                </script>


            </fieldset>
            <div id='posudekModal' title='Přidělit posudek jinému recenzentovi'>
                <select id='posudekLogin'>
                    <?php
                        $queryRecenzent = $pdo->prepare("SELECT login, jmeno, prijmeni FROM uzivatel WHERE role = 'recenzent' AND login NOT IN (SELECT login FROM posudek JOIN uzivatel ON posudek.login_recenzenta = uzivatel.login WHERE id_clanku = ? AND verze = ?)");
                        $params = array($id, $verze);
                        $queryRecenzent->execute($params);
                        while($radek = $queryRecenzent->fetch(PDO::FETCH_ASSOC))
                        {
                            echo("<option value='" . $radek["login"] . "'>" . $radek["jmeno"] . " " . $radek["prijmeni"] . "</option>");
                        }
                    ?>
                </select>
                <button class="reg_button" id='posudekOdeslat'>Změnit</button>
            </div>
            <?php
        }
    ?>
</div>

<?php require($base_path."foot.php"); ?>
