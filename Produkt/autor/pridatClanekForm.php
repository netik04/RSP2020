<?php
// formulář sloužící pro přidání nového článku
// role, která může tuto stránku zobrazit
$role = "autor";

$base_path = "../"; // cesta ke kořeni
$head_str = "<link rel=\"stylesheet\" href=\"autor_style.css\">"; // přidám styly pro autora
// přidám hlavičku - zároveň kotrola přihlášení
require($base_path."head.php");
?>
<div id="content" class="autor">

<?php
// připojím se do DB
if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // pokud se nepodařilo připojit - nemá smysl pokračovat
else 
{
    // stáhnu si jméno a přijmení uživatele, který si zobrazil formulář (hlavní autor)
    try
    {
        $query = $pdo->prepare("SELECT jmeno, prijmeni FROM uzivatel WHERE login = ?");
        $params = array($_SESSION[session_id()]);
        $query->execute($params);
    }
    // pokud se nepodaří
    catch(PDOException $ex)
    {
        // nemá smysl pokračovat
        die("Nastala chyba s databází. Zkuste to prosím později.");
    }

    // pokud se podařilo - stáhnu si údaje o uživateli
    $tmp = $query->fetch(PDO::FETCH_ASSOC);
    $jmeno = $tmp["jmeno"];
    $prijmeni = $tmp["prijmeni"];
?>

<script>
// pokud je vše ready
$("document").ready(function ()
{
    $("body").on('keyup', 'input[id^="new_"]', function()
    {
        var id = $(this).attr('id');
        var input = $(this).val();
        var element = $(this);
        if($(this).val() != "")
        {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() 
            {
                if (this.readyState == 4 && this.status == 200)
                {
                    if(this.responseText == "true")
                    {
                            for(var i = 2; i <= $("#clanekPocetAutoru").val(); i++)
                            {
                                if((input === $("#new_" + i).val()) && (id != $("#new_" + i).attr('id')))
                                {                                
                                    $("#error" + id).html("Tento člověk je již uveden jako autor.");
                                    $("#error" + id).css("color", "red");
                                    $(".pridat_clanek").prop("disabled", true);
                                    break;
                                }
                                else
                                {
                                    $("#error" + id).html("&nbsp; &#x2713;");
                                    $("#error" + id).css("color", "green");
                                    $(".pridat_clanek").prop("disabled", false);
                                }
                            }                     
                    }
                    else if(this.responseText == "stejny_autor")
                    {
                        $("#error" + id).html("Vy už jste uveden jako autor");
                        $("#error" + id).css("color", "red");
                        $(".pridat_clanek").prop("disabled", true);
                    }   
                    else if(this.responseText == "neni_autor")
                    {
                        $("#error" + id).html("Tento uživatel není autorem!");
                        $("#error" + id).css("color", "red");
                        $(".pridat_clanek").prop("disabled", true);
                    }                             
                    else
                    {
                        $("#error" + id).html("Je nám líto, tento autor není v databázi evidován.");
                        $("#error" + id).css("color", "red");
                        $(".pridat_clanek").prop("disabled", true);
                    }
                 }
            };
            xmlhttp.open("GET","scripty/zkontrolujAutora.php?login="+$(this).val(),true);
            xmlhttp.send();
        }
        else
        {
            $("#error" + id).text("");
            $(".pridat_clanek").prop("disabled", true);
        }
    });

    $('.add').on('click', add);
    $('.remove').on('click', remove);

    function add() 
    {
        var pocetAutoru = parseInt($('#clanekPocetAutoru').val()) + 1;
        var novyAutor = "<label for='clanekAutor" + pocetAutoru + "'>Zadejte login autora č." + pocetAutoru + ": </label><input type='text' id='new_" + pocetAutoru + "' name='clanekAutor" + pocetAutoru + "'><span id='errornew_" + pocetAutoru + "'></span><br />";
        $('#clanekExtraAutor').append(novyAutor);
        $('#clanekPocetAutoru').val(pocetAutoru);
    }

    function remove() 
    {
        var posledni_cislo_autora = $('#clanekPocetAutoru').val();
        if (posledni_cislo_autora > 1) 
        {
            $('#new_' + posledni_cislo_autora).remove(); 
            $("#clanekExtraAutor").find("br:last").remove();
            $("#clanekExtraAutor").find("label:last").remove();
            $("#clanekExtraAutor").find("span:last").remove();
            $('#clanekPocetAutoru').val(posledni_cislo_autora - 1);
        }
    }

    $(document).on('change', 'select', function() 
    {
        $.ajax({
            type: 'GET',
            url: 'scripty/ziskejInfoCasopis.php',
            data: 'id=' + $(this).val(),
            dataType: 'json',
            cache: false,
            success: function(result) 
            {
                var datum = result[0].split("-").reverse().join(".");
                var kapacita = result[1];
                var pocetPrispevku = result[2];    
                $("#casopis_info").html("<table><tr><th>Počet příspěvků v recenzním řízení</th><th>Kapacita výtisku</th><th>Datum uzávěrky</th></tr>" + 
                "<tr><td>" + pocetPrispevku + "</td><td>" + kapacita + "</td><td>" + datum + "</td></tr></table>");           
            },
        });       
    });
});
</script>

<h2>Přidat článek</h2>

<?php
    // pokud je v session-ě error - něco se nepovedlo při odesílání článku
    if(isset($_SESSION["error"]))
    {
        // zobrazím chybu
        echo($_SESSION["error"]);
        // smažu session kvůli opakování výpisu při refresh-i
        unset($_SESSION["error"]);
    }
?>
<fieldset>
<!-- enctype=multipart/form-data ==> odesílám jak textové údaje, tak soubor -->
<form action="scripty/pridatClanek.php" method="POST" enctype="multipart/form-data">
<div id="casopis_info"></div>
<label for="clanekCasopis">Zvolte časopis:</label>
<select name="clanekCasopis" required>
<option disabled selected value>Vyberte...</option>
<?php
    try
    {
        // stáhnu si všechny časopisy z DB
        $queryCasopis = $pdo->prepare("SELECT id_casopisu, tema, datum_uzaverky FROM casopis");
        $queryCasopis -> execute();
    }
    // nepodařilo se stáhnout časopisy
    catch(PDOException $ex)
    {
        // vrátím se zpět a vypíšu chybu
        $_SESSION["error"] = "Nepodařilo se získat info o časopisech";
        header("Location: pridatClanekForm.php");
        exit();
    }
    // mám časopisy
    while(($radek = $queryCasopis->fetch(PDO::FETCH_ASSOC)) != FALSE)
    {
        // stáhnu si potřebná data
        $id = $radek["id_casopisu"];
        $tema = $radek["tema"];
        $datum_uzaverky = $radek["datum_uzaverky"];
        // pokud časopis ještě není uzavřený
        if($datum_uzaverky > date("Y-m-d"))
        {
            // přidám do selectu možnost
            echo("<option value=\"" . $id . "\">" . $tema . "</option>");
        }
    }
?>
</select><br />
<label for="clanekNazev">Název článku: </label><input type="text" name="clanekNazev" required><br />
<label for="clanekAutor">Autor: </label><input type="text" name="clanekAutor" value="<?php echo($jmeno . " " . $prijmeni); ?>" disabled>
<button class="add" type="button">Přidat autora</button>
<button class="remove" type="button">Odebrat pole</button>
<div id="clanekExtraAutor"></div>
<input type="hidden" value="1" id="clanekPocetAutoru" name="clanekPocetAutoru">
<label for="clanekSoubor">Nahrát článek:</label><input type="file" name="clanekSoubor" accept=".doc, .docx, .pdf, .odt" required><br />
<input type="submit" name="clanekSubmit" value="Přidat článek" class="pridat_clanek">
</form>
</fieldset>

<?php } ?>

</div>

<?php /* patička */ require($base_path."foot.php"); $pdo = null; ?>