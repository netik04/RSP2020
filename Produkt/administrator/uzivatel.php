<?php

$role = "administrator";

$base_path = "../";

$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
$head_str .= "<link rel='stylesheet' href='admin-style.css'>";

require($base_path . "head.php");
?>

<script>
    function zobrazData()
    {
        $.ajax({
            type: 'POST',
            url: 'scripty/zobrazUzivatele.php',
            cache: false,
            success: function(result)
            {
                $("#uziv").html(result);
                $(".admin_button").button();
                $(".admin_button_del").button();
                $(".admin_button").on("click", function()
                {
                    $("#login").val($(this).parent().parent().children().eq(0).text());
                    $("#jmeno").val($(this).parent().parent().children().eq(1).text());
                    $("#prijmeni").val($(this).parent().parent().children().eq(2).text());
                    $("#email").val($(this).parent().parent().children().eq(4).text());
                    $("#telefon").val($(this).parent().parent().children().eq(5).text());
                    $("#role").val($(this).parent().parent().children().eq(3).text());
                    $("#dialog").dialog("open");
                });
                $(".admin_button_del").on("click", function()
                {
                    $("#smazatLogin").val($(this).parent().parent().children().eq(0).text());
                    $("#smazatRole").val($(this).parent().parent().children().eq(3).text());
                    $("#smazatError").text("");
                    $("#dialog_smazat").dialog("open");
                });
            }
        });
    }
    $(document).ready(function()
    {
        $("#odeslat").button();
        $("#smazatAno").button();
        $("#smazatNe").button();
        zobrazData();
        $("#dialog").dialog({
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
        $("#dialog_smazat").dialog({
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
        $("#smazatNe").on("click", function()
        {
            $("#dialog_smazat").dialog("close");
        });

        $(".formular").submit(function(event)
        {
            event.preventDefault();

            $.ajax('scripty/upravUzivatele.php', {
                type: 'POST',
                data: {
                    login: $("#login").val(),
                    jmeno: $("#jmeno").val(),
                    prijmeni: $("#prijmeni").val(),
                    role: $("#role").val(),
                    email: $("#email").val(),
                    telefon: $("#telefon").val()
                },
                success: function(result)
                {
                    if(result != "")
                    {
                        alert(result);
                    }
                    zobrazData();
                }
            });
        });

        $(".formular_smazat").submit(function(event)
        {
            event.preventDefault();

            $.ajax('scripty/smazatUzivatele.php', {
                type: 'POST',
                data: {
                    login: $("#smazatLogin").val(),
                    role: $("#smazatRole").val()
                },
                success: function(result)
                {
                    if(result == "")
                    {
                        $("#dialog_smazat").dialog("close");
                        zobrazData();
                    }
                    else
                    {
                        $("#smazatError").text(result);
                    }
                },
                fail: function()
                {
                    alert("tohle je fail");
                }
            });
        });
    });
</script>

<div id="content">
    <div id="uziv">
        <div id="dialog" title="Upravit uživatele">
            <form method='POST' class='formular'>
                <input type='hidden' name='login' id='login'>
                <label for='jmeno'>Jméno: </label></br><input type='text' name='jmeno' id='jmeno' required><br />
                <label for='prijmeni'>Přijmení: </label></br><input type='text' name='prijmeni' id='prijmeni' required> <br />
                <label for='email'>Email: </label></br><input type='email' name='email' id='email' required><br />
                <label for='telefon'>Telefonní číslo: </label></br><input type='text' name='telefon' id='telefon'><br />
                <label for='role' required>Role: </label></br>
                <select name="role" id='role'>
                    <option value="recenzent">Recenzent</option>
                    <option value="sefredaktor">Šéfredaktor</option>
                    <option value="autor">Autor</option>
                    <option value="redaktor">Redaktor</option>
                    <option value="administrator">Administrátor</option>
                </select><br /><br />
                <input type="submit" name="odeslat" value="Upravit" id='odeslat'>
            </form>
        </div>
        <div id="dialog_smazat" title="Potvrdit smazání">
            <form method='POST' class='formular_smazat'>
                <h2>Opravdu chcete smazat tohoto uživatele?</h2>
                <input type='hidden' name='smazatLogin' id='smazatLogin'>
                <input type='hidden' name='smazatRole' id='smazatRole'>
                <button id='smazatAno'>Ano</button>
                <button type='button' id='smazatNe'>Ne</button>
                <span id='smazatError'></span>
            </form>
        </div>
    </div>
</div>

<?php require($base_path . "foot.php"); $pdo = null; ?>
