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
    date_default_timezone_set("Europe/Prague");         
    try {
        //SQL dotaz -> vytahnuti id, nazvu a verze clanku pro recenzenta
        $query3 = $pdo->prepare("SELECT id_clanku, nazev, verze, datum_vytvoreni, datum_uzaverky FROM clanek NATURAL JOIN posudek
                                 WHERE login_recenzenta = ? ORDER BY datum_vytvoreni");
        $params = array($login);
        $query3->execute($params);
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        //Vytahni data z db a vypis
            $tmp = "";
            $tmp2 = "";
            while(($radek = $query3 -> fetch(PDO::FETCH_BOTH)) != FALSE) {
                $id = $radek["id_clanku"];
                $nazev = $radek["nazev"];
                $verze = $radek["verze"];
                $datum = $radek["datum_vytvoreni"];
                $datum_uz = date_format(date_create($radek['datum_uzaverky']),"j.n.Y G:i");
                
                if($datum == "")
                {
                    $tmp2 .= "<tr><td class='recenzent_nazev'>" . $nazev . "</td><td>" . $verze . "</td><td>" . $datum_uz . "</td><td><a href='zobrazitDetail.php?id=" . $id . "&verze=" . $verze . "'><button class='recenzent_detail'>Zobrazit detail</button></a></td></tr>";
                }
                else
                {
                    $tmp .= "<tr><td class='recenzent_nazev'>" . $nazev . "</td><td>" . $verze . "</td><td><a href='zobrazitDetail.php?id=" . $id . "&verze=" . $verze . "'><button class='recenzent_detail'>Zobrazit detail</button></a></td></tr>";
                }
            }
            echo("<h2>Články k recenzi</h2>");
            if($tmp2 != "")
            {                
                echo("<table class='recenzent_clanky' cellspacing='0'>");
                echo("<tr><th class='recenzent_nazev'>Název článku</th><th>Verze</th><th>Datum uzávěrky</th><th>Možnosti</th></tr>");
                echo($tmp2);
                echo("</table><br>");
            }
            else
            {
                echo("<table class='recenzent_clanky'><tr><th><h3>Gratulace, nechybí Vám žádné články k recenzi.</h3></th></tr></table>");
            }
            echo("<h2>Vámi již zrecenzované články</h2>");
            if($tmp != "")
            {                
                echo("<table class='recenzent_clanky' cellspacing='0'>");
                echo("<th class='recenzent_nazev'>Název článku</th><th>Verze</th><th>Možnosti</th>");
                echo($tmp);
                echo("</table>");
            }
            else
            {
                echo("<table class='recenzent_clanky'><tr><th><h3>Zatím jste nesepsal žádné posudky</h3></th></tr></table>");
            }
?>
    
</div>

<?php
//Paticka stranky
require($base_path."foot.php");$pdo = null; 
?>