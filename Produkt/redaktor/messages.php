<?php
    $base_path = "../";
    $head_str = "
    <style> 

    #messageWrap{
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    #messageBox{
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        max-height: 300px;
        overflow-y: auto;
    }
    .message{
        background-color: #777;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 2px;
        margin-right: 10px;
        
        /* flexing stuff */
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .userMessage {
        align-self: flex-end;
        background-color: #999;
    }

    #messageSender {
        align-self: center;
        background-color: black;
        margin-top: 10px;
        width: 65%;
        overflow: hidden;
        margin-top: 10px;

        /* flexing stuff */
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    #messageSender input[type=\"text\"]{
        flex: 2 2 auto;
    }
    #messageSender input[type=\"submit\"]{
        
    }

    </style>";
    require($base_path."head.php");
    require($base_path."db.php")
?>
<div id="content" class="redaktor">
    <?php
        $article_id = 1;
        $article_verze = 1;

        $query = $pdo->prepare("SELECT text_zpravy, datum_cas, login, jmeno, prijmeni FROM zprava NATURAL JOIN uzivatel WHERE id_clanku = ? AND verze = ? AND interni = true AND duvod = FALSE");
        $params = array($article_id, $article_verze);
        $query->execute($params);
    ?>
    <div class="article">
        <div id="messageWrap">
            <div id="messageBox">
                <?php
                    if($query->rowCount() > 0){
                        while($row = $query->fetch(PDO::FETCH_ASSOC))
                        {
                            if($row['login'] == $_SESSION[session_id()]){
                                echo "<div class = \"message userMessage\"><span class=\"datetime\">" . $row["datum_cas"] . "</span> <span class=\"messageContent\">" . $row["text_zpravy"] . "</span></div>";
                            }else{
                                echo "<div class = \"message\"><span class=\"datetime\">" . $row["datum_cas"] . "</span> <span class=\"messageContent\">" . $row["text_zpravy"] . "</span> <span class=\"messagerName\">" . $row["jmeno"] . " " . $row["prijmeni"] . "</span></div>";
                            }
                            
                        }
                    }
                ?>
            </div>
            <form id="messageSender" action="scripty/odeslatZpravu.php">
                    <input type="hidden" name="id" value="<?php echo $article_id?>">
                    <input type="hidden" name="verze" value="<?php echo $article_verze?>">
                    <input type="hidden" name="interni" value=1>
                    <input type="text" name="message" id="message">
                    <input type="submit" name="odeslatZpravu" value="Odeslat">
            </form>
            <div id="errorMessage"><?php echo $_SESSION["errorMessage"]; unset($_SESSION["errorMessage"]); ?></div>
        </div>
    </div>
</div>

<script>
    window.onload=function () {
     var objDiv = document.getElementById("messageBox");
     objDiv.scrollTop = objDiv.scrollHeight;
}
</script>

<?php require($base_path."foot.php"); $pdo = null; ?>