<?php
$role = "autor";

$base_path = "../";

$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
$head_str .= "<link rel='stylesheet' href='autor_style.css'>";

require($base_path."head.php");
?>

<script>
    $(document).ready(function()
    {
        $(".autor_button").button();
        $(".odpoved_button").button();
    });
</script>

<div id="content">

    <a href="index.php"><button class="autor_button">&#8592; Zpět na výpis</button></a><br /><br />

    <?php
        if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // pokud se nepovedlo, nemá smysl pokračovat
        else
        {
            ?>
            <fieldset>
                <h2>Informace o článku</h2>
            <?php
            $id = $_GET["id"];
            $verze = $_GET["verze"];
            try
            {
                $query = $pdo->prepare("SELECT stav_autor, stav_redaktor, datum, cesta, nazev, tema, id_casopisu FROM verze NATURAL JOIN clanek NATURAL JOIN casopis WHERE id_clanku = ? AND verze = ?");
                $params = array($id, $verze);
                $query -> execute($params);
            }
            catch(PDOException $ex)
            {
                die("Vyskytla se chyba. Zkuste to prosím později.");
            }

            $radek = $query->fetch(PDO::FETCH_ASSOC);
            $stav_autor = $radek["stav_autor"];
            $stav_redaktor = $radek["stav_redaktor"];
            $datum = date_format(date_create($radek["datum"]),"j.n.Y");
            $cesta = $radek["cesta"];
            $nazev = $radek["nazev"];
            $tema = $radek["tema"];
            $id_casopisu = $radek["id_casopisu"];

            echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Název článku:</span></th><td>" . $nazev . "</td></tr></table>");
            echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Verze článku:</span></th><td>" . $verze . "</td></tr></table>");
            echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Datum nahrání:</span></th><td>" . $datum . "</td></tr></table>");
            echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Stav:</span></th><td>" . $stav_autor . "</td></tr></table>");
            echo("<table class='detail_tabulka' cellspacing='0'><tr><th><span class=\"blue\">Téma:</span></th><td>" . $tema . "</td></tr></table><br />");

            echo("<div class='info_tlacitka'><a href='" . $base_path . $cesta . "' target='_blank'><button class='autor_button'>Zobrazit článek</button></a>");

            if(($stav_autor == "Vráceno k úpravě") && ($stav_redaktor != "Existuje nová verze"))
            {
                echo("<form action='pridatClanekForm.php' method='POST'><input type='hidden' name='clanekNazev' value='" . $nazev . "'>");
                echo("<input type='hidden' name='clanekCasopis' value='" . $id_casopisu . "'>");
                echo("<input class='autor_button' type='submit' name='verzeSubmit' value='Vytvořit novou verzi'></form></div>");
            }
            else
            {
                echo("</div>");
            }
            ?>
            </fieldset><br />
            <?php
            if($stav_autor == "Posudky doručeny" || $stav_autor == "Vráceno k úpravě" || $stav_autor == "Schváleno" || $stav_autor == "Zamítnuto")
            {
                echo("<fieldset>");
                try
                {
                    $query = $pdo->prepare("SELECT * FROM posudek WHERE id_clanku = ? AND verze = ?");
                    $params = array($id, $verze);
                    $query->execute($params);
                }
                catch(PDOException $ex)
                {
                    die("Nastala chyba. Zkuste to prosím později.");
                }

                if($query->rowCount() == 0 && $stav_autor == "Schváleno")
                {
                    echo("<h2>Článek byl schválen bez recenzního řízení.</h2></fieldset>");
                }
                else if($query->rowCount() == 0 && $stav_autor == "Zamítnuto")
                {
                    echo("<h2>Článek byl zamítnut bez recenzního řízení.</h2></fieldset>");
                }
                else if($query->rowCount() == 0 && $stav_autor == "Vráceno k úpravě")
                {
                    echo("<h2>Článek byl vrácen redaktorem bez recenzního řízení</h2></fieldset>");
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
                        if($vyjadreni_autora == "")
                        {
                            echo("<td><div id='odpoved_" . $i . "'><button class='odpoved_button'>Odpovědět</button></div></td>");
                            echo("<script>
                            $('.odpoved_button').click(function()
                            {
                                $(this).parent().html(\"<form action='scripty/pridatOdpoved.php' method='POST'><textarea name='odpoved_text' placeholder='Zde napište vaši odpověď'></textarea><input type='hidden' value='" . $id . "' name='odpoved_clanek'><input type='hidden' value='" . $verze . "' name='odpoved_verze'><input type='hidden' value='" . $login . "' name='odpoved_login'><input class='reg_button kekw' type='submit' name='odpoved_submit' value='Odeslat odpověď'></form>\");
                            });
                            </script>");
                        }
                        else
                        {
                            echo("<td>" . $vyjadreni_autora . "</td>");
                        }
                        echo("</tr>");
                        $i++;
                    }

                    echo("</table><br /></fieldset>");
                }
            }
            else
            {
                echo("<fieldset><h2>K tomuto článku zatím nejsou dostupné posudky.</h2></fieldset>");
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
                <h2 id='chatNadpis'>Chat s redakcí</h2>
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
                        <form id="messageSender" action="<?php echo $base_path;?>scripty/odeslatZpravu.php">
                                <input type="hidden" id="zpravaId" name="id" value="<?php echo $id?>">
                                <input type="hidden" id="zpravaVerze" name="verze" value="<?php echo $verze?>">
                                <input type="hidden" id="inter" name="interni" value="1">
                                <input type="text" id="message" name="message" required>
                                <input id="odeslatZpravu" type="submit" name="odeslatZpravu" value="Odeslat" <?php if($verze != $maxVerze)echo "disabled"; ?>>
                        </form>
                        <div id="errorMessage"><?php echo $_SESSION["errorMessage"]; unset($_SESSION["errorMessage"]); ?></div>
                    </div>

                <script>
                var timer;
                    function zobrazZpravy(neco){
                        $.ajax('<?php echo $base_path;?>scripty/zobrazZpravy.php', {
                                type: 'POST',  // http method
                                data: {
                                    article_id: <?php echo $id ?>,
                                    article_verze: <?php echo $verze ?>,
                                    interni: 0
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
                    $(document).ready(function()
                    {                                                                                     
                        $("#messageSender").submit(function(event){
                                event.preventDefault();
                                $.ajax('<?php echo $base_path;?>scripty/odeslatZpravu.php', {
                                    type: 'POST',
                                    data: {
                                        id: $("#zpravaId").val(),
                                        verze: $("#zpravaVerze").val(),
                                        interni: 0,
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
