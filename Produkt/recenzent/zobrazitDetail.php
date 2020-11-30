<?php
session_start();
$role = "recenzent";

$base_path = "../";

$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
$head_str .= "<link rel='stylesheet' href='recenzent-style.css'>";

require($base_path."head.php");

$id = $_GET["id"];
$verze = $_GET["verze"];
$login = $_SESSION[session_id()];
try
{
    $query = $pdo->prepare("SELECT datum, cesta, nazev, tema FROM verze NATURAL JOIN clanek NATURAL JOIN casopis WHERE id_clanku = ? AND verze = ?");
    $params = array($id, $verze);
    $query->execute($params);
}
catch(PDOException $ex)
{
    die("Nastala chyba v systému. Zkuste to prosím později.");
}

$radek = $query->fetch(PDO::FETCH_ASSOC);
$datum = date_format(date_create($radek["datum"]),"j.n.Y");
$cesta = $radek["cesta"];
$nazev = $radek["nazev"];
$tema = $radek["tema"];


try
{
    $queryRevize = $pdo -> prepare("SELECT osobni_revize FROM posudek WHERE login_recenzenta = ? AND id_clanku = ? AND verze = ?");
    $params = array($login, $id, ($verze - 1));
    $queryRevize -> execute($params);
}
catch(PDOException $ex)
{
    die("Nastala chyba v systému. Zkuste to prosím později");
}

if($queryRevize -> rowCount() == 0)
{
    $revize = false;
}
else
{
    $tmp = $queryRevize->fetchColumn(0);
    if($tmp == 0)
    {
        $revize = false;
    }
    else
    {
        $revize = true;
    }
}
?>

<script>
    $(document).ready(function()
    {
        $(".autor_button, #recenzent_btnPosudek, #rec_submit").button();  
        
        $("#rec_posudekModal").dialog({
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

        $("#recenzent_btnPosudek").on("click", function()
        {
            $("#rec_posudekModal").dialog("open");
        });

        $("#rec_posudekForm").submit(function(event)
        {
            event.preventDefault();      
            var kontrola;
            if($("#rec_kontrola").prop("checked") == true)
            {
                kontrola = 1;
            }
            else
            {
                kontrola = 0;
            }

            $.ajax("scripty/odeslatPosudek.php", {
                type: "POST",
                data:{
                    login: "<?php echo$login;?>",
                    id: "<?php echo $id;?>",
                    verze: "<?php echo $verze;?>",
                    rec01: $("#rec_01").val(),
                    rec02: $("#rec_02").val(),
                    rec03: $("#rec_03").val(),
                    rec04: $("#rec_04").val(),
                    recOdpoved: $("#rec_odpoved").val(),
                    recKontrola: kontrola
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
    <div id='rec_posudekModal' title='Ohodnotit článek'>
        <form id='rec_posudekForm'>
            <table id='rec_posudekTabulka'>
                <tr><td><label for='rec_01'>Aktuálnost, zajímavost, přínosnost:</label></td></tr><tr><td><input type='number' id='rec_01' name='rec_01' min='1' max='5' placeholder='Známka 1-5' required></td></tr>
                <tr><td><label for='rec_02'>Originalita:</label></td></tr><tr><td><input type='number' id='rec_02' min='1' max='5' name='rec_02' placeholder='Známka 1-5' required></td></tr>
                <tr><td><label for='rec_03'>Odborná úroveň:</label></td></tr><tr><td><input type='number' id='rec_03' min='1' max='5' name='rec_03' placeholder='Známka 1-5' required></td></tr>
                <tr><td><label for='rec_04'>Jazyková a stylistická úroveň:</label></td></tr><tr><td><input type='number' id='rec_04' min='1' name='rec_04' max='5' placeholder='Známka 1-5' required></td></tr>
                <tr><td><label for='rec_odpoved'>Otevřená odpověď:</label></td></tr><tr><td><textarea id='rec_odpoved' name='rec_odpoved' placeholder='Zde se můžete ke článku vyjádřit'></textarea></td></tr>
                <tr><td><label for='rec_revize'>Vyžadována osobní kontrola:</label></td></tr><tr><td><input type='checkbox' id='rec_kontrola' name='rec_kontrola' value='1' <?php if($revize) echo("disabled title='K tomuto článku jste si již osobní revizi vyžádal.'");?> ></td></tr>
                <tr><td><input type='submit' id='rec_submit' value='Odeslat posudek'></td></tr>
</table>
        </form>
    </div>

    <a href="index.php"><button class="autor_button">&#8592; Zpět na výpis</button></a><br /><br />

    <?php
        if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // pokud se nepovedlo, nemá smysl pokračovat
        else
        {
            ?>
            <fieldset>
                <h2>Informace o článku</h2>
            <?php
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Název článku:</span></th><td>" . $nazev . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Verze článku:</span></th><td>" . $verze . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Datum nahrání:</span></th><td>" . $datum . "</td></tr></table>");
                echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Téma:</span></th><td>" . $tema . "</td></tr></table><br />");

                echo("<div class='info_tlacitka'><a href='" . $base_path . $cesta . "' target='_blank'><button class='autor_button'>Zobrazit článek</button></a></div>");
            ?>
            </fieldset><br />
            <?php
            $query = $pdo->prepare("SELECT * FROM posudek WHERE id_clanku = ? AND verze = ? AND login_recenzenta = ?");
            $params = array($id, $verze, $login);
            $query->execute($params);
            
            $radek = $query->fetch(PDO::FETCH_ASSOC);

            echo("<fieldset>");
            if($radek["datum_vytvoreni"] == "")
            {
                echo("<h2>Tuto verzi článku jste zatím nehodnotil</h2><br><button id='recenzent_btnPosudek'>Vytvořit posudek</button></fieldset>");
            }
            else
            {
                echo("<h2>Vaše hodnocení této verze článku</h2>");
                echo("<table class='recenzent_posudek' cellspacing='0'>");
                echo("<tr><th>Aktuálnost, zajímavost, přínosnost</th><th>Originalita</th><th>Odborná úroveň</th><th>Jazyková a stylistická úroveň</th><th>Otevřená odpověď</th><th>Vyjádření autora</th></tr>");                        
                $akt_zaj_prin = $radek["akt_zaj_prin"];
                $jazyk_styl_prinos = $radek["jazyk_styl_prinos"];
                $orig = $radek["originalita"];
                $odbor = $radek["odbor_uroven"];
                $otevrena_odpoved = $radek["otevrena_odpoved"];
                $vyjadreni_autora = $radek["vyjadreni_autora"];

                echo("<tr><td>" . $akt_zaj_prin . "</td><td>" . $jazyk_styl_prinos . "</td><td>" . $orig . "</td><td>" . $odbor . "</td><td>" . $otevrena_odpoved . "</td>");
                echo("<td>" . $vyjadreni_autora . "</td></tr></table>");
                echo("</fieldset>");
            }

            try
            {
                $queryMax = $pdo->prepare("SELECT MAX(verze) FROM verze WHERE id_clanku = ?");
                $params = array($id);
                $queryMax -> execute($params);
            }
            catch(PDOException $ex)
            {
                die("Nastala chyba. Zkuste to prosím později");
            }

            $maxVerze = $queryMax->fetchColumn(0);
            ?>
            <br />
            <fieldset>
                <h2>Chat s redakcí</h2>
                <script>
                    $(document).ready( function(){ 
                        $("#odeslatZpravu").button(); 
                        $('#messageBox').on('scroll', chk_scroll);   
                        zobrazZpravy(true);
                    });
                </script>
                    <div id="messageWrap">
                        <div id="messageBox">
                        </div>
                        <form id="messageSender" action="scripty/odeslatZpravu.php">
                                <input type="hidden" id="zpravaId" name="id" value="<?php echo $id?>">
                                <input type="hidden" id="zpravaVerze" name="verze" value="<?php echo $verze?>">
                                <input type="hidden" id="inter" name="interni" value="1">
                                <input type="text" id="message" name="message" required>
                                <input id="odeslatZpravu" type="submit" name="odeslatZpravu" value="Odeslat" <?php if($verze != $maxVerze)echo "disabled"; ?>>
                        </form>
                    </div>

                <script>
                    var timer;
                    function zobrazZpravy(neco){
                        $.ajax('<?php echo $base_path;?>scripty/zobrazZpravy.php', {
                                type: 'POST',  // http method
                                data: {
                                    article_id: <?php echo $id ?>,
                                    article_verze: <?php echo $verze ?>,
                                    interni: 1
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
                        $("#messageSender").submit(function(event){
                                event.preventDefault();
                                $.ajax('<?php echo $base_path;?>scripty/odeslatZpravu.php', {
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
            <?php
        }
    ?>
</div>

<?php require($base_path."foot.php"); ?>
