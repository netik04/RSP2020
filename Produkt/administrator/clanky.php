<?php
// toto je hlavní stránka autora
// slouží pro zobrazování všech článků napsaných daným autorem a pro přidávání nových verzí

// potřebuji, aby si stránku zobrazil pouze autor
$role = "administrator";

// cesta ke kořeni
$base_path = "../";
//přidám jQueryUI
$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
// přidám autor-only CSS
$head_str .= "<link rel='stylesheet' href='admin-style.css'>";

// include hlavičky
require($base_path."head.php");
?>

<script>
    // pokud je document načtený
    $(document).ready(function()
    {
        // nastavím accordion (jQueryUI)
        $(".accordion").accordion({
            heightStyle: "content", // proměnná velikost elementů
            collapsible: true, // je možnost zavřít všechny položky
            active: false
            // začíná se zavřenými položkami
        });
        // nastavím jQueryUI tlačítka pro přidávání nové verze a pro zobrazení článku
        $(".zobrazit, #upravitSubmit, #buttonAno, #buttonNe").button();
        //$(".zobrazit").button();
        $(".hidden").hide();
        //$("#upravitSubmit").button();


        $(".upravitButton").on("click", function(event)
        {
            event.stopImmediatePropagation();
            $("#upravitID").val($(this).parent().children(".hidden").text());
            $("#upravitNazev").val($(this).parent().children(".nazev").text());
            $("#upravitCasopis").val($(this).parent().children(".hidden_2").val());
            $("#modalUpravit").dialog("open");
            $("#upravitNazev").css("width", $("#upravitCasopis").width());
        });

        $(".smazatButton").on("click", function(event)
        {
            event.stopImmediatePropagation();
            $("#smazatID").val($(this).parent().children(".hidden").text());
            $("#modalSmazat").dialog("open");
        });

        $("#smazatForm").submit(function(event)
        {
            event.preventDefault();
            $.ajax('scripty/smazatClanek.php', {
                type: 'POST',
                data: {
                    id: $("#smazatID").val()
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

        $("#buttonNe").on("click", function()
        {
            $("#modalSmazat").dialog("close");
        });

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

        $("#modalSmazat").dialog({
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

        $("#upravitForm").submit(function(event)
        {
            event.preventDefault();
            $.ajax('scripty/upravClanek.php', {
                type: 'POST',
                data: {
                    id_cl: $("#upravitID").val(),
                    nazev: $("#upravitNazev").val(),
                    id_cas: $("#upravitCasopis").val()
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
    });
</script>

<div id="content" class="autor">

<div id='modalUpravit' title='Upravit údaje o článku'>
    <form id='upravitForm'>
        <input type='hidden' id='upravitID' name='upravitID'>
        <input type='text' id='upravitNazev' name='upravitNazev' placeholder='Název článku' required><br />
        <select name='upravitCasopis' id='upravitCasopis' required>
        <?php
        $queryCasopisy = $pdo->query("SELECT * FROM casopis");

        while($radek = $queryCasopisy->fetch(PDO::FETCH_ASSOC))
        {
            echo("<option value='" . $radek["id_casopisu"] . "'>" . $radek["tema"] . "</option>");
        }
        ?>
        </select><br>
        <input type='submit' id='upravitSubmit' value='Upravit'>
    </form>
</div>

<div id='modalSmazat' title='Potvrdit smazání'>
    <h2>Opravdu chcete smazat tento článek?</h2>
    <form id='smazatForm'>
        <input type='hidden' id='smazatID'>
        <button id='buttonAno'>Ano</button>
        <button type='button' id='buttonNe'>Ne</button>
    </form>
</div>

<?php
// pokusím se připojit do DB
if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // pokud se nepovedlo, nemá smysl pokračovat
else
{
    // z DB si zjistím potřebné informace
    try
    {
        $query = $pdo -> prepare("SELECT * FROM clanek NATURAL JOIN verze NATURAL JOIN casopis");
        $query -> execute();
    }
    // pokud se nepovedlo
    catch(PDOException $ex)
    {
        // nemá smysl pokračovat
        die($ex -> getMessage());
    }

    // dotaz vrátil 0 - v db nejsou žádné články
    if($query -> rowCount() == 0)
    {
        // pouze zobrazím text a končím
        echo("<h2>Žádné články ještě nebyly vytvořeny.</h2>");
    }
    // pokud má uživatel nějaké články
    else
    {
        echo("<h2>Odeslané články</h2>");
        // pomocná proměnná pro kontrolu, zdali pracuji s další verzí, nebo s novým článkem
        $tmp = 0;
        // vypíšu začátek accordionu
        echo("<div class='accordion'>");
        // dokud máš z dotazu co číst
        while(($radek = $query->fetch(PDO::FETCH_ASSOC)) != FALSE)
        {
            // stáhnu si info
            $id_clanku = $radek["id_clanku"]; // id článku
            $verze = $radek["verze"]; // verzi článku
            $stav_autor = $radek["stav_autor"]; // jaký stav zobrazit autorovi
            $nazev = $radek["nazev"]; // název článku
            $datum = date("j.m.yy", strtotime($radek["datum"]));
            $cesta = $radek["cesta"]; // cestu ke článku
            $tema = $radek["tema"]; // do jakého časopisu byl článek odeslán
            $id_casopisu = $radek["id_casopisu"]; // id časopisu - pro pozdější práci s verzemi
            $stav_redaktor = $radek["stav_redaktor"]; // stav zobrazovaný redaktorovi - pro kontrolu možnosti přidávat nové verze

            // zobrazení dat
            // zobrazení pomocí accordionu - hlavička přes <h1>; content přes tabulku v extra DIVu
            // pokud se id článku rovná tomu pomocnému ID
            if($id_clanku == $tmp)
            {
                // jedná se pouze o verzi - vypíšu info k verzi do již existující sekce accordionu
                echo("<tr>");
                echo("<td>" . $verze . "</td><td>" . $stav_autor . "</td><td>" . $datum . "</td><td><a href='zobrazitClanek.php?id=" . $id_clanku . "&verze=" . $verze . "'><button type='button' class='zobrazit'>Zobrazit detail</button></a></td>");
                echo("</tr>");
            }
            // pokud se id nerovná tmp - jedná se o další článek
            else
            {
                // pokud to není první článek
                if($tmp != 0)
                {
                    // musím ukončit sekci accordionu
                    echo("</table></div>");
                }
                // pomocná proměnná bude nové id článku
                $tmp = $id_clanku;
                try
                {
                    $query_autor = $pdo->prepare("SELECT jmeno, prijmeni FROM uzivatel NATURAL JOIN pise WHERE id_clanku = ?");
                    $params = array($id_clanku);
                    $query_autor->execute($params);
                }
                catch(PDOException $ex)
                {
                    die($ex -> getMessage());
                }
                $autor = "";
                while(($radek2 = $query_autor->fetch(PDO::FETCH_ASSOC)) != FALSE)
                {
                    $autor .= $radek2["jmeno"] . " " . $radek2["prijmeni"] . "; ";
                }
                $autor = substr($autor, 0, -2);
                // zobrazím hlavičku nové sekce accordionu (název článku a téma časopisu)
                echo("<h1><span class='nazev'>" . $nazev . "</span><span class='tema'>Téma: " . $tema . "</span><br><span>Autoři: " . $autor . "</span><a class='smazatButton'>Smazat článek</a><a class='upravitButton'>Upravit článek</a><span class='hidden'>" . $id_clanku . "</span>");
                echo("<input type='hidden' value='" . $id_casopisu . "' class='hidden_2'></h1>");
                // vypíšu obsah sekce
                echo("<div><table class='ax-table' cellspacing='0'><tr>");
                echo("<th>Verze článku</th><th>Stav verze</th><th>Datum nahrání verze</th><th>Možnosti</th></tr><tr>");
                echo("<td>" . $verze . "</td><td>" . $stav_autor . "</td><td>" . $datum . "</td><td><a href='zobrazitClanek.php?id=" . $id_clanku . "&verze=" . $verze . "'><button type='button' class='zobrazit'>Zobrazit detail</button></a></td>");
                echo("</tr>");
            }
        }
        // končím s výpisem
        // ukočím poslední sekci accordionu
        echo("</table></div></div>");
    }
?>

<?php } ?>

</div>

<?php /* patička */ require($base_path."foot.php"); $pdo = null; ?>
