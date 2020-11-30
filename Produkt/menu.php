<?php
    if (include("scripty/jePrihlasen.php")){
        echo "<div id=\"menu\"><ul>";
        echo "<li> <a class=\"button btn-three\" href=\"$base_path"."index.php\">Home</a> </li>";
        switch ($_SESSION["role"]) {
            case 'autor':
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."autor/index.php\">Zobrazit články</a> </li>";
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."autor/pridatClanekForm.php\">Vytvořit nový článek</a> </li>";
                break;
             case 'recenzent':
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."recenzent/index.php\">Recenzent home</a> </li>";
                break;   
             case "redaktor":
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."redaktor/index.php\">Redaktor home</a> </li>";
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."registr.php\">Registrovat uživatele</a> </li>";
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."redaktor/casopisy.php\">Správa časopisů</a> </li>";
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."redaktor/terminy.php\">Přehled termínů</a> </li>";
                break;
            case "sefredaktor":
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."sefredaktor/index.php\">Šéfredaktor home</a> </li>";
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."sefredaktor/casopisy.php\">Časopisy</a> </li>";
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."sefredaktor/terminy.php\">Přehled termínů</a> </li>";
                break;
             case "administrator":
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."administrator/uzivatel.php\">Uzivatele</a> </li>";
                echo "<li> <a class=\"button btn-three\" href=\"$base_path"."administrator/clanky.php\">Články</a> </li>";
                break;
            default:
                # code...
                break;
        }
        echo "<li> <a class=\"button btn-three\" href=\"$base_path"."helpdesk.php\">HelpDesk</a> </li>";
        echo "</ul></div>";
    }
?>
<script>
$(document).ready(function(){
    var url = window.location.href;
    $("#menu a").each(function(){
        if(url == (this.href)){
            $(this).addClass("menuActive")
        }
    });
});
</script>
