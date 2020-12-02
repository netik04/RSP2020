<?php
//session_start();
$base_path = "../";
session_start();
if(!include("jePrihlasen.php")) die();

$article_id = $_REQUEST['article_id'];
$article_verze = $_REQUEST['article_verze'];
$interni = $_REQUEST['interni'];

if(require($base_path."db.php")){
    $query = $pdo->prepare("SELECT text_zpravy, datum_cas, login, jmeno, prijmeni, duvod, role FROM zprava NATURAL JOIN uzivatel WHERE id_clanku = ? AND verze = ? AND interni = ? ORDER BY datum_cas");
    $params = array($article_id, $article_verze, $interni);
    $query->execute($params);
    if($query->rowCount() > 0){
        while($row = $query->fetch(PDO::FETCH_ASSOC))
        {
            $row["datum_cas"] = strtotime($row["datum_cas"]);
            $row["datum_cas"] = date("j.m.yy H:i", $row["datum_cas"]);

            if($row['login'] == $_SESSION[session_id()]){
                echo "<div class = \"message userMessage ". ($row["duvod"] == 1 ? "vraceni " : "") . ($row["duvod"] == 2 ? " zamitnuti " : "") . ($row["duvod"] == 4 ? " schvaleni " : "") ." \"><span class=\"datetime\">" . $row["datum_cas"] . "</span> <span class=\"messageContent\">" .($row["duvod"] == 1 ? "[Důvod vrácení]: " : "") . ($row["duvod"] == 2 ? " [Důvod zamítnutí]: " : "") . $row["text_zpravy"] . "</span></div>";
            }else{
                if($row['login'] == ""){
                    echo "<div class = \"message ". ($row["duvod"] == 1 ? "vraceni " : "") . ($row["duvod"] == 2 ? " zamitnuti " : "") . ($row["duvod"] == 4 ? " schvaleni " : "") ."\"><span class=\"datetime\">" . $row["datum_cas"] . "</span> <span class=\"messageContent\">" .($row["duvod"] == 1 ? "[Důvod vrácení]: " : "") . ($row["duvod"] == 2 ? " [Důvod zamítnutí]: " : "") . $row["text_zpravy"] . "</span> <span class=\"messagerName\">[deleted]</span></div>";
                }else{
                    echo "<div class = \"message ". ($row["duvod"] == 1 ? "vraceni " : "") . ($row["duvod"] == 2 ? " zamitnuti " : "") . ($row["duvod"] == 4 ? " schvaleni " : "") ."\"><span class=\"datetime\">" . $row["datum_cas"] . "</span> <span class=\"messageContent\">" .($row["duvod"] == 1 ? "[Důvod vrácení]: " : "") . ($row["duvod"] == 2 ? " [Důvod zamítnutí]: " : "") . $row["text_zpravy"] . "</span> <span class=\"messagerName\">" . $row["jmeno"] . " " . $row["prijmeni"] . " - ". $row["role"] . "</span></div>";
                }
                
            }
            
        }
    }else{
        echo "<div class = \"message centeredMessage\">Zatím neproběhla žádná komunikace</div>";
    }
}else{
    echo  "<div class = \"message\">Nezdařilo se připojit k databázi</div>";
}
?>