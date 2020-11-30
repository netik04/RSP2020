<?php

// ROLE KTERÁ MÁ PŘÍSTUP    
$role = "sefredaktor";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

$head_str = "<link rel=\"stylesheet\" href=\"".$base_path."redaktor/redaktor_style.css\">";
$head_str .= "<link rel=\"stylesheet\" href=\"sefredaktor_style.css\">";
/*
$head_str .= "<script src=\"scripty/js/viditelnost_casopisu.js\"></script>";
$head_str .= "<link rel=\"stylesheet\" href=\"//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css\">";
$head_str .= "<script src=\"https://code.jquery.com/ui/1.12.1/jquery-ui.js\"></script>";
*/

require($base_path."head.php");
?>

<div id="content" class="redaktor casopisy">
    <?php // ZDE ZAČÍNÁ OBSAH STRÁNKY REDAKTOR ?>

    <?php
    if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else {
    ?>
        <div class="main_title">Časopisy</div>
        <?php /*<button class="button" id="add_casopis">Přidat časopis</button>*/?>

        <?php
        $sql = "SELECT casopis.*, cl.pocet_clanku FROM casopis
            NATURAL LEFT JOIN (
                SELECT id_casopisu, Count(*) AS pocet_clanku FROM clanek
                NATURAL JOIN verze
                WHERE stav_redaktor = 'Příspěvek je přijat k vydání'
                GROUP BY id_casopisu
            ) AS cl
            ORDER BY datum_uzaverky DESC";
        $stmt = $pdo->query($sql);

        while($casopis = $stmt->fetch(PDO::FETCH_ASSOC)){
        ?>
            <div class="casopis article" id="<?php echo($casopis["id_casopisu"])?>">

                <?php /*<div class="control">
                    <button class="button c_vis" c_id="<?php echo($casopis["id_casopisu"])?>" c_vis="<?php echo($casopis["zobrazit"])?>">
                    <?php
                        if($casopis["zobrazit"] == 0)
                            echo("Zobrazit");
                        else
                            echo("Skrýt");
                    ?>
                    </button>
                    <?php
                    if($casopis["pocet_clanku"] > 0) { ?>
                        <button class="button c_export" c_id="<?php echo($casopis["id_casopisu"])?>">Exportovat</button>
                    <?php }?>
                </div>*/?>
                <div class="left">
                    <div class="title">Téma: <?php echo($casopis["tema"])?></div>
                    <div class="info">
                        <span>Uzávěrka: <?php echo(date_format(date_create($casopis["datum_uzaverky"]),"j.n.Y"))?></span>
                        <span>Kapacita: <?php echo($casopis["kapacita"])?></span>
                        <span>Přiřazené články: <?php echo(empty($casopis["pocet_clanku"]) ? '0' : $casopis["pocet_clanku"])?></span>
                        <span class="visState"><?php if($casopis["zobrazit"] == 0) echo("Skryto"); else echo("Zobrazeno");?></span>

                    </div>
                </div>
            </div>
        
        <?php
        }
        
        ?>

    <?php } ?>

</div>

<?php /* Přidání časopisu
<script>
    $(document).ready(function(){
        $("#dialog").dialog({
            autoOpen: false,
            resizable: false, 
            width: 500,
            show: {
                effect: "fade", 
                duration: 200
            },
            hide: {
                effect: "fade", 
                duration: 200
            }
        });
        $(".formular").submit(function(event){
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: "scripty/vytvoreni_casopisu.php",
                data:{
                    tema: $("#tema").val(),
                    uzaverka: $("#uzaverka").val(),
                    kapacita: $("#kapacita").val()
                },
                success: function(data){
                    if(data == 1){
                        location.reload();
                    }else{
                        alert("Nepodařilo se vytvořit časopis :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
                    }
                },
            });
        });
    });
    $("#add_casopis").on("click", function(){
        $("#dialog").dialog("open");
    });
</script>

<div id="dialog" title="Vytvořit časopis" style="display:none">
    <form method='POST' class='formular'>
        <label for='tema'>Téma: </label><input type='text' name='tema' id='tema' placeholder="Téma" required><br />
        <label for='uzaverka'>Datum uzávěrky: </label><input type='date' name='uzaverka' id='uzaverka' required> <br />           
        <label for='kapacita'>Kapacita: </label><input type='number' name='kapacita' placeholder="Kapacita" id='kapacita' min="1" required><br />
        </select><br /><br />
        <input type="submit" name="odeslat" value="Přidat" id='odeslat'>
    </form>
</div>
*/?>

<?php require($base_path."foot.php"); $pdo = null; ?>