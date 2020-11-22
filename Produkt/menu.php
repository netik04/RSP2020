<?php
    if (include("scripty/jePrihlasen.php")){
        echo "<div id=\"menu\"><ul>";
        echo "<li> <a class=\"button\" href=\"$base_path"."index.php\">Home</a> </li>";
        switch ($_SESSION["role"]) {
            case 'autor':
                echo "<li> <a class=\"button\" href=\"$base_path"."autor/index.php\">Zobrazit články</a> </li>";
                echo "<li> <a class=\"button\" href=\"$base_path"."autor/pridatClanekForm.php\">Vytvořit nový článek</a> </li>";
                break;
            case 'recenzent':
                # code...
                break;    
            case "redaktor":
                echo "<li> <a class=\"button\" href=\"$base_path"."redaktor/index.php\">Redaktor home</a> </li>";
                echo "<li> <a class=\"button\" href=\"$base_path"."registr.php\">Registrovat uživatele</a> </li>";
                echo "<li> <a class=\"button\" href=\"$base_path"."redaktor/casopisy.php\">Správa časopisů</a> </li>";
                break;
            case "sefredaktor":
                echo "<li> <a class=\"button\" href=\"$base_path"."sefredaktor\">Šéfredaktor home</a> </li>";
                echo "<li> <a class=\"button\" href=\"$base_path"."sefredaktor/casopisy.php\">Časopisy</a> </li>";
                break;
            case "administrator":
                #code
                break;
            default:
                # code...
                break;
        }
        echo "<li> <a class=\"button\" href=\"$base_path"."helpdesk.php\">HelpDesk</a> </li>";
        echo "</ul></div>";
    }
?>
