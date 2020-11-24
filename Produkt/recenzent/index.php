<?php
// ROLE KTERÁ MÁ PŘÍSTUP    
$role = "recenzent";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";
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

<?php
//Vytahnu login prave prihlaseneho uzivatele
if (isset($_SESSION[session_id()])){
    $login=$_SESSION[session_id()];
}
            
//kontrola jakej recenzent je zrovna prihlasen
if(isset($_SESSION[session_id()])) {  
    try {
        //SQL dotaz -> vytahnuti id, nazvu a veze clanku pro recenzenta
        $query3 = $pdo->prepare("SELECT id_clanku, nazev, verze FROM clanek NATURAL JOIN posudek
                                 WHERE login_recenzenta = ?;");
        $params = array($login);
        $query3->execute($params);
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        //Vytahni data z db a vypis
        echo("<h3> Články pro recenzenta </h3>");
        echo("<ul>");
        while(($radek = $query3 -> fetch(PDO::FETCH_BOTH)) != FALSE) {
            $id = $radek["id_clanku"];
            $nazev = $radek["nazev"];
            $veze = $radek["verze"];
            echo("<li>" . "Nazev: " .$nazev . "<br />" . "Verze: " .$veze . "<br />" . "<br />" . "</li>");
        }
            echo("</ul>");
            
            echo("<br /><br />");
            
            
        //Odkaz na stahnuti clanku
        try {
            $query = $pdo->prepare("SELECT nazev, cesta, verze FROM clanek NATURAL JOIN verze NATURAL JOIN posudek WHERE login_recenzenta = ?;");
            $params2 = array($login);
            $query->execute($params2);
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        //Vytahni data z db a vypis
        echo("<h3> Odkazy na stažení článků </h3>");
        while(($radek = $query -> fetch(PDO::FETCH_BOTH)) != FALSE) {
            $nazev = $radek["nazev"];
            $path = $radek["cesta"];
            $verze_c = $radek["verze"];
            echo("<a href = '../$path' download><br /> $nazev (verze: $verze_c) </a><br />");
        }
        
        echo("<br /><br /><br />");
        
        
        //Vytahnuti id_clanku a verze do posudku
        try {
            $query4 = $pdo->prepare("SELECT id_clanku, verze FROM posudek WHERE login_recenzenta = ?;");
            $params3 = array($login);
            $query4->execute($params3);
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        //Vytahni data z db 
        while(($radek = $query4 -> fetch(PDO::FETCH_BOTH)) != FALSE) {
            $id_clanku = $radek["id_clanku"];
            $verze1 = $radek["verze"];
        }
        
        
        //Generovani formularu pro nevyplnene posudky
        try {
        $query8 = $pdo->prepare("SELECT id_clanku, nazev, verze FROM clanek NATURAL JOIN posudek
                                 WHERE login_recenzenta = ? AND datum_vytvoreni IS NULL;");
        $params4 = array($login);
        $query8->execute($params4);
        } catch (PDOException $ex) {
            echo("Selhal dotaz " . $ex->getMessage());
        }
        while(($radek = $query8 -> fetch(PDO::FETCH_BOTH)) != FALSE) {
            $id_cl = $radek["id_clanku"];
            $nazev_c = $radek["nazev"];
            $verze = $radek["verze"];
           
        
        //Formular
        echo("<form action=\"index.php\" method=\"POST\" id = 'posudek'>\n");
            echo("<fieldset>");
            
                echo("<legend>" . "Posudek: " . $nazev_c . " verze: " . $verze . " id: " .$id_cl . "</legend>");
                
                    echo("<input type=\"hidden\" name=\"upravit_id\" value=\"$id_cl\" /><br />\n");
                    echo("<input type=\"hidden\" name=\"upravit_verze\" value2=\"$verze\" /><br />\n");
                    
                    echo("<label for = 'akt_pr'>Aktuálnost, zajimavost, přínos: </label>");
                        echo("<select name = 'akt_pr' id = 'akt_pr' form = 'posudek'>");
                           echo("<option value='1'>1</option>");
                           echo("<option value='2'>2</option>");
                           echo("<option value='3'>3</option>");
                           echo("<option value='4'>4</option>");
                           echo("<option value='5'>5</option>");
                        echo("</select>");
                        echo("<br />");
                        
                    echo("<label for = 'jazyk_pr'>Jazykový přínos: </label>");
                        echo("<select name = 'jazyk_pr' id = 'jazyk_pr' form = 'posudek'>");
                           echo("<option value='1'>1</option>");
                           echo("<option value='2'>2</option>");
                           echo("<option value='3'>3</option>");
                           echo("<option value='4'>4</option>");
                           echo("<option value='5'>5</option>");
                        echo("</select>");
                        echo("<br />");
                        
                    echo("<label for = 'origin_pr'>Originalita: </label>");
                        echo("<select name = 'origin_pr' id = 'origin_pr' form = 'posudek'>");
                           echo("<option value='1'>1</option>");
                           echo("<option value='2'>2</option>");
                           echo("<option value='3'>3</option>");
                           echo("<option value='4'>4</option>");
                           echo("<option value='5'>5</option>");
                        echo("</select>");
                        echo("<br />");
                        
                    echo("<label for = 'odb_pr'>Odborná úroveň: </label>");
                        echo("<select name = 'odb_pr' id = 'odb_pr' form = 'posudek'>");
                           echo("<option value='1'>1</option>");
                           echo("<option value='2'>2</option>");
                           echo("<option value='3'>3</option>");
                           echo("<option value='4'>4</option>");
                           echo("<option value='5'>5</option>");
                        echo("</select>");
                        echo("<br />");
                        
                    echo("<input type='checkbox' id='revize' name='revize' value='1' form = 'posudek'>");   
                        echo("<label for='revize'> Vyžádat osobní revizi </label><br>");
                                  
                    echo("<label for='datum'>Datum vytvoření: </label>");
                        echo("<input type='date' id='datum' name='datum'>");
                        echo("<br /> <br />");
                        
                        echo("<textarea form='posudek' id='odpoved' name='odpoved' rows='4' cols='50' value='' maxlength='256' 
                                        placeholder='Zde napište svoji odpověď' style='height:150px; width:300px;'>");
                        echo("</textarea>");
                        echo("<br />");
                      
            echo("</fieldset>");
            echo("<input type='submit' name=\"upravit_submit\" value='Odeslat' /> \n");
        echo("</form>");
        echo("<br />");
        
        
        //Aktualizovani dat v databazi
        if(isset($_REQUEST["upravit_submit"])) {
            //Stahnu data z formulare
            $akt_pr = htmlspecialchars($_REQUEST["akt_pr"]);
            $jazyk_pr = htmlspecialchars($_REQUEST["jazyk_pr"]);
            $origin_pr = htmlspecialchars($_REQUEST["origin_pr"]);
            $odb_pr = htmlspecialchars($_REQUEST["odb_pr"]);
            $osob_rev_y = htmlspecialchars($_REQUEST["revize"]);
            $datum = htmlspecialchars($_REQUEST["datum"]);
            $odpoved = htmlspecialchars($_REQUEST["odpoved"]);
            //Nastaveni hodnoty do revize
            if(empty($_REQUEST["revize"])) {
                $osob_rev_y = 0;
            } else {
                $osob_rev_y = 1;
            }
            
            //Kontrola vyplnenosti
            if($akt_pr == "" || $jazyk_pr == "" || $origin_pr == "" || $odb_pr == "" || $datum == "" || $odpoved == "") {
                //Neco nebylo vyplneno
                echo("chyba v podmince");
            } else {
            //Proved UPDATE do db
            try {
                $query5 = $pdo->prepare("UPDATE posudek SET akt_zaj_prin = ?, jazyk_styl_prinos = ?, originalita = ?, odbor_uroven = ?,
                                         datum_vytvoreni = ?, osobni_revize = ?, otevrena_odpoved = ? 
                                         WHERE id_clanku = $id_clanku AND verze = $verze1 AND login_recenzenta = '$login';");
                $params4 = array($akt_pr, $jazyk_pr, $origin_pr, $odb_pr, $datum, $osob_rev_y, $odpoved);
                $query5->execute($params4);
            } catch (PDOException $ex) {
                echo("Selhal dotaz " . $ex->getMessage());
            }
            //Kontrola provedeni
            if($query5 == TRUE) {
                //Update se provedl
                echo("Posudek úspěšně odeslán!");     
            } else {
                die("Chyba pri update");
            }
            }
            
            //Kontrola jestli uz nebyl posudek odeslan
            try {
                $query6 = $pdo->prepare("SELECT Count(*) FROM verze WHERE stav_redaktor = '1. posudek doručen redakci' 
                                         AND id_clanku = $id_clanku AND verze = $verze1;");
                $query6->execute();
            } catch (PDOException $ex) {
                echo("Selhal dotaz " . $ex->getMessage());
            }
            
            //Vrati pocet vyplnenich radku
            $p = $query6->fetch(PDO::FETCH_COLUMN)+ 1;
  
            try {
            //Nastaveni stavu posudku 
            $query7 = $pdo->prepare("UPDATE verze SET stav_redaktor = '$p. posudek doručen redakci' 
                                     WHERE id_clanku = $id_clanku AND verze = $verze1;");
            $query7->execute();
            } catch (PDOException $ex) {
                echo("Selhal dotaz " . $ex->getMessage());
            }
        }
}
}
?>
    
</div>

<?php
//Paticka stranky
require($base_path."foot.php");$pdo = null; 
?>