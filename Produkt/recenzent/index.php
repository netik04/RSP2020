<?php
// ROLE KTERÁ MÁ PŘÍSTUP    
$role = "recenzent";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

$head_str .= "<script src='" . $base_path . "ui/jquery-ui.js'></script>";
$head_str .= "<link rel='stylesheet' href='" . $base_path . "ui/jquery-ui.css'>";
$head_str .= "<link rel='stylesheet' href='recenzent-style.css'>";

//Nacteni hlavicky stranky
require($base_path."head.php");
?>

<?php
    //Pripojeni k db
    if(!include($base_path."db.php")) {
        echo("Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.");
    }
?>

<div id ="content">

<script>
    $(document).ready(function()
    {
        $(".recenzent_detail").button();
    });
</script>

<?php
//Vytahnu login prave prihlaseneho uzivatele
$login=$_SESSION[session_id()];
              
    try {
        //SQL dotaz -> vytahnuti id, nazvu a verze clanku pro recenzenta
        $query3 = $pdo->prepare("SELECT id_clanku, nazev, verze, datum_vytvoreni FROM clanek NATURAL JOIN posudek
                                 WHERE login_recenzenta = ? ORDER BY datum_vytvoreni");
        $params = array($login);
        $query3->execute($params);
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        //Vytahni data z db a vypis
        if($query3->rowCount() == 0)
        {
            echo("<h2>Nemáte zatím přidělené žádné články k recenzi.</h2>");
        }
        else
        {
            $tmp = "";
            echo("<h2>Články k recenzi</h2>");
            echo("<table class='recenzent_clanky' cellspacing='0'>");
            echo("<th class='recenzent_nazev'>Název článku</th><th>Verze</th><th>Možnosti</th>");
            while(($radek = $query3 -> fetch(PDO::FETCH_BOTH)) != FALSE) {
                $id = $radek["id_clanku"];
                $nazev = $radek["nazev"];
                $verze = $radek["verze"];
                $datum = $radek["datum_vytvoreni"];
                
                if($datum == "")
                {
                    echo("<tr>");
                    echo("<td class='recenzent_nazev'>" . $nazev . "</td><td>" . $verze . "</td><td><a href='zobrazitDetail.php?id=" . $id . "&verze=" . $verze . "'><button class='recenzent_detail'>Zobrazit detail</button></a></td>");
                    echo("</tr>");
                }
                else
                {
                    $tmp .= "<tr><td class='recenzent_nazev'>" . $nazev . "</td><td>" . $verze . "</td><td><a href='zobrazitDetail.php?id=" . $id . "&verze=" . $verze . "'><button class='recenzent_detail'>Zobrazit detail</button></a></td></tr>";
                }
            }
            echo("</table><br>");
            echo("<h2>Vámi již zrecenzované články</h2>");
            echo("<table class='recenzent_clanky' cellspacing='0'>");
            echo("<th class='recenzent_nazev'>Název článku</th><th>Verze</th><th>Možnosti</th>");
            echo($tmp);
            echo("</table>");
        }
?>
    
</div>

<?php
//Paticka stranky
require($base_path."foot.php");$pdo = null; 
?>