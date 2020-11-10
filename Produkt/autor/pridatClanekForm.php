<?php
// formulář sloužící pro přidání nového článku
// role, která může tuto stránku zobrazit
$role = "autor";

$base_path = "../"; // cesta ke kořeni
// přidám autor-only CSS
$head_str .= "<link rel='stylesheet' href='autor_style.css'>";
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
    // pro všechny inputy autora přidám funkci onkeyup
    $("body").on('keyup', 'input[id^="new_"]', function()
    {
        // potřebuji ID atributu, který událost zavolal a co do něj bylo zadáno
        var id = $(this).attr('id');
        var input = $(this).val();
        // pokud vůbec něco zadal
        if($(this).val() != "")
        {
            // vytvořím instanci XMLHttpRequest a vytvořím funkci, pokud se změní jeho stav
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() 
            {
                // pokud byl přijat výstup z externího scrpitu
                if (this.readyState == 4 && this.status == 200)
                {
                    // pokud script vrátí true - uživatel existuje a je autorem
                    if(this.responseText == "true")
                    {
                        // projdu všechny extra inputy pro autory
                        for(var i = 2; i <= $("#clanekPocetAutoru").val(); i++)
                        {
                            // pokud najdu shodu - autor byl již zadán předtím
                            if((input === $("#new_" + i).val()) && (id != $("#new_" + i).attr('id')))
                            {   
                                // vypíšu chybu a nedovolím uživatele odeslat formulář                             
                                $("#error" + id).html("Tento člověk je již uveden jako autor.");
                                $("#error" + id).css("color", "red");
                                $(".pridat_clanek").prop("disabled", true);
                                break;
                            }
                            // pokud shoda neexistuje - autor je zadán poprvé
                            else
                            {
                                // vše je v pořádku - uživatel může odeslat formulář
                                $("#error" + id).html("&nbsp; &#x2713;");
                                $("#error" + id).css("color", "green");
                                $(".pridat_clanek").prop("disabled", false);
                            }
                        }                     
                    }
                    // pokud script oznámí, že se jedná o stejného autora jako hlavní autor článku
                    else if(this.responseText == "stejny_autor")
                    {
                        // vypíšu chybu a nedovolím odeslat formulář
                        $("#error" + id).html("Vy už jste uveden jako autor");
                        $("#error" + id).css("color", "red");
                        $(".pridat_clanek").prop("disabled", true);
                    }  
                    // pokud uživatel existuje, ale není autor 
                    else if(this.responseText == "neni_autor")
                    {
                        // vypíšu chybu a nedovolím odeslat formulář
                        $("#error" + id).html("Tento uživatel není autorem!");
                        $("#error" + id).css("color", "red");
                        $(".pridat_clanek").prop("disabled", true);
                    } 
                    // pokud uživatel neexistuje                            
                    else
                    {
                        // vypíšu chybu a nedovolím odeslat formulář
                        $("#error" + id).html("Je nám líto, tento autor není v databázi evidován.");
                        $("#error" + id).css("color", "red");
                        $(".pridat_clanek").prop("disabled", true);
                    }
                 }
            };
            // nastavím, na který script se má odkazovat a předám hodnotu z inputu
            xmlhttp.open("GET","scripty/zkontrolujAutora.php?login="+$(this).val(),true);
            // odešlu požadavek na script
            xmlhttp.send();
        }
        // pokud nic nebylo zadáno
        else
        {
            // nezobrazím nic, ale nedovolím odeslat formulář
            $("#error" + id).text("");
            $(".pridat_clanek").prop("disabled", true);
        }
    });

    // přidám pro tlačítka na přidávání a odebírání autorů metody, které se volají po kliknutí na tlačítka
    $('.add').on('click', add);
    $('.remove').on('click', remove);

    // funkce pro přidávání více autorů
    function add() 
    {
        // zjistím, kolikátý autor to bude
        var pocetAutoru = parseInt($('#clanekPocetAutoru').val()) + 1;
        // vytvořím input a vše potřebné okolo
        var novyAutor = "<label for='clanekAutor" + pocetAutoru + "'>Zadejte login autora č." + pocetAutoru + ": </label><input type='text' id='new_" + pocetAutoru + "' name='clanekAutor" + pocetAutoru + "' required><span id='errornew_" + pocetAutoru + "'></span><br />";
        $('#clanekExtraAutor').append(novyAutor);
        // do inputu dám hodnotu počtu autorů
        $('#clanekPocetAutoru').val(pocetAutoru);
    }

    // funkce pro odebrání posledního inputu autora
    function remove() 
    {
        // zjistím, kolik máme autorů
        var posledni_cislo_autora = $('#clanekPocetAutoru').val();
        // pokud nemáme pouze jednoho extra autora
        if (posledni_cislo_autora > 1) 
        {
            // smažu input, odřádkování, span
            $('#new_' + posledni_cislo_autora).remove(); 
            $("#clanekExtraAutor").find("br:last").remove();
            $("#clanekExtraAutor").find("label:last").remove();
            $("#clanekExtraAutor").find("span:last").remove();
            // snížím počet autorů o jedničku
            $('#clanekPocetAutoru').val(posledni_cislo_autora - 1);
        }
    }

    // pokud uživatel vybere možnost v select-u
    $(document).on('change', 'select', function() 
    {
        // nastavím ajax pro zjištění informací o časopisu z externího scriptu
        $.ajax({
            type: 'POST', // jak budeme data odesílat
            url: 'scripty/ziskejInfoCasopis.php', //kam budeme odkazovat
            data: 'id=' + $(this).val(), // jaká data budeme předávat
            dataType: 'json', // jak je script vrátí
            cache: false,
            // pokud se vše povede
            success: function(result) 
            {
                // stáhnu si data z JSON pole
                var datum = result[0].split("-").reverse().join(".");
                var kapacita = result[1];
                var pocetPrispevku = result[2]; 
                // vypíšu všechny informace do tabulky   
                $("#casopis_info").html("<table class='form-table'><tr><th>Počet příspěvků v recenzním řízení</th><th>Kapacita výtisku</th><th>Datum uzávěrky</th></tr>" + 
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
<form action="scripty/pridatClanek.php" id="pridatForm" method="POST" enctype="multipart/form-data">
<div id="casopis_info"></div>
<label for="clanekCasopis">Zvolte časopis:</label>
<select name="clanekCasopis" id="clanekCasopis" required>
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
    // pokud přidávám specificky novou verzi
    if(isset($_REQUEST["verzeSubmit"]))
    {
        // nastavím pevnou value SELECTu, zakážu vybrání jiného časopisu a předám value do hidden inputu
        echo("<script>$('#clanekCasopis').val(" . $_REQUEST["clanekCasopis"] . ");$('#clanekCasopis').prop('disabled', true);</script><input type='hidden' name='clanekCasopis' value='" . $_REQUEST["clanekCasopis"] . "'>");
    }
?>
</select><br />
<label for="clanekNazev">Název článku: </label><input type="text" name="clanekNazev" id="clanekNazev" <?php 
if(isset($_REQUEST["verzeSubmit"])){echo("value='" . $_REQUEST["clanekNazev"] . "' readonly='readonly'");}?> required><br />
<label for="clanekAutor">Autor: </label><input type="text" name="clanekAutor" value="<?php echo($jmeno . " " . $prijmeni); ?>" disabled>
<button class="add" type="button" <?php/* Pokud přidávám specificky verzi - zakážu přidávání autorů */ if(isset($_REQUEST["verzeSubmit"])){echo("disabled");} ?> Přidat autora</button>
<button class="remove" type="button" <?php/* Pokud přidávám verzi - zakážu odebírání autorů */ if(isset($_REQUEST["verzeSubmit"])){echo("disabled");} ?>Odebrat pole</button>
<div id="clanekExtraAutor"></div>
<input type="hidden" value="1" id="clanekPocetAutoru" name="clanekPocetAutoru">
<label for="clanekSoubor">Nahrát článek:</label><input type="file" name="clanekSoubor" accept=".doc, .docx, .pdf, .odt" required><br />
<input type="submit" name="clanekSubmit" value="Přidat článek" class="pridat_clanek">
</form>
</fieldset>

<?php } ?>

</div>

<?php /* patička */ require($base_path."foot.php"); $pdo = null; ?>