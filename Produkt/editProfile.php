<?php
    require("head.php");
    $base_path = "";
    if (!include("db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // připojení do DB
    else
    {
    $query = $pdo->prepare("select * from uzivatel where login = ?");
    $params = array($_SESSION[session_id()]);
    $query->execute($params);   
    $fetchedUser = $query->fetch(PDO::FETCH_ASSOC);

    $jmeno = $fetchedUser["jmeno"];
    $prijmeni = $fetchedUser["prijmeni"];
    $email = $fetchedUser["email"];
    $tel = $fetchedUser["telefon"];
        
?>
    <fieldset>
        <form action="scripty/editace.php" method="POST" enctype="multipart/form-data">
            <label for="login">Login:</label><input type="text" value="<?php echo $_SESSION[session_id()]; ?>" disabled/><br/>
            <input type="hidden" name="login" value="<?php echo $_SESSION[session_id()]; ?>">
            <label for="jmeno">Jméno: </label><input type="text" value="<?php echo $jmeno;?>" name="jmeno"/><br />
            <label for="prijmeni">Přijmení: </label><input type="text" value="<?php echo $prijmeni;?>" name="prijmeni" required /><br />
            <label for="mail">Email: </label><input type="email" value="<?php echo $email;?>" name="mail" required /><br />
            <label for="tel">Telefonní číslo: </label><input type="text" value="<?php echo $tel;?>" name="tel" /><br />
            <!--<label for="reg_pfp">Profilový obrázek: </label><input type="file" name="reg_pfp"><br />-->
            <input type="submit" name="submit" id="reg_submit" value="Provést změny" />
            <span id="error"><?php echo $_SESSION["error_edit"]; unset($_SESSION["error_edit"])?></span>
        </form>
    </fieldset>

    


<?php    
    }
    require("foot.php");
?>