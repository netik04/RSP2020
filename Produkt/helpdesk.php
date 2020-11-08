<?php
// ZMĚNIT PŘI KOPÍROVÁNÍ PROJEKTU
$base_path = ""; // pro absolutni referenci mezi soubory např. include($base_path."head.php"); 
// bez předešlých se velice špatně používá relativná obzvláště, když se daná část přidává include (v případě head.php a style.css)
$ignore = true; // slouzi pro zastaveni odkazovani v indexu, protoze v headu testuji prihlaseni a neprihlaseneho by to na index odkazovalo nekonecnekrat
require("head.php");
?>
<div id="content">
    <?php
    if($_SESSION["role"] != "redaktor"){
    ?>
    <div id="wrapHelpDeskQuestion">
        <form action="<?php echo $base_path;?>scripty/odeslatHelpdesk.php" class="flex_horizontalne">
            <textarea name="text"></textarea>
            <input type="submit">
        </form>
        
    </div>
    <?php
        }
    ?>
    <div id="wrapHelpDesk">
        <?php
        require_once($base_path . "db.php");

        if($_SESSION["role"] == "redaktor"){
            $sql = "SELECT id, zprava, jmeno, prijmeni FROM helpdesk NATURAL JOIN uzivatel WHERE id_otazky is NULL";
        }else{
            $sql = "SELECT id, zprava, jmeno, prijmeni FROM helpdesk NATURAL JOIN uzivatel WHERE login = ? AND id_otazky is NULL";
        }

        $query = $pdo->prepare($sql);
        $params = array($_SESSION[session_id()]);
        $query -> execute($params);

        if($query -> rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                echo "<div class=\"wrapotazka\">";
                echo "<div class=\"otazka\">" . $row["zprava"];
                if($_SESSION["role"] == "redaktor"){
                    echo " - " . $row["jmeno"]. " " . $row["prijmeni"];
                }
                echo "</div>";
                    $query2 = $pdo->prepare("SELECT id, zprava, jmeno, prijmeni FROM helpdesk NATURAL JOIN uzivatel WHERE id_otazky = ?");
                    $params = array($row['id']);
                    $query2 -> execute($params);
                    if($query2 -> rowCount() > 0){
                        $fetchedMessage = $query2->fetch(PDO::FETCH_ASSOC);
                        echo "<div class=\"odpoved\">" . $fetchedMessage["zprava"] . " - " . $fetchedMessage["jmeno"]. " " . $fetchedMessage["prijmeni"] . "</div>";
                    }else{
                        if($_SESSION["role"] == "redaktor"){
                            echo "<form action=\"". $base_path . "scripty/odeslatHelpdesk.php\"><input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\"><textarea name=\"text\" rows=\"1\"></textarea> <input type=\"submit\"></form>";
                        }
                    }
                echo "</div>";

            }
        }else{
            echo "nefuguje to!!!!";
        }
        ?>
    </div>
</div>
<?php require("foot.php"); $pdo = null; ?>