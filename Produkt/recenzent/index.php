<?php
// ROLE KTERÁ MÁ PŘÍSTUP    
$role = "recenzent";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

require($base_path."head.php");
?>

<?php
    if(!include($base_path."db.php")) {
        echo("Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.");
    }
?>

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
            echo("<li>" . "id clanku: " . $id . "<br />" . "Nazev: " .$nazev . "<br />" . "Verze: " .$veze . "<br />" . "<br />" . "</li>");
        }
            echo("</ul>");
}
?>

<?php require($base_path."foot.php");$pdo = null; ?>