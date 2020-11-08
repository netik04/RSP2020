<?php
    if (include("scripty/jePrihlasen.php")){
        echo "<div id=\"menu\"><ul>";
        echo "<li> <a href=\"$base_path"."index.php\">Home</a> </li>";
        switch ($_SESSION["role"]) {
            case 'autor':
                # code...
                break;
            case 'recenzent':
                # code...
                break;    
            case "redaktor":
                echo "<li> <a href=\"$base_path"."redaktor/index.php\">Redaktor home</a> </li>";
                break;
            case "sefredaktor":
                #code
                break;
            case "administrator":
                #code
                break;
            default:
                # code...
                break;
        }
        echo "<li> <a href=\"$base_path"."helpdesk.php\">HelpDesk</a> </li>";
        echo "</ul></div>";
    }
?>
