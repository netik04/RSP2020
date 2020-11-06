<?php
    require("head.php"); // zavolani casti stranky kde je kod ktery se opkauje na kazde strance, za timto bodem je uzivatel prihlasen
    $base_path = ""; //pormenna pro zjednoduseni relativnich cest
    if (!include("db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // připojení do DB
    else
    {
    $query = $pdo->prepare("select * from uzivatel where login = ?"); // nacteni udaji o uzivateli prihlasenem uzivatelli
    $params = array($_SESSION[session_id()]); // pripraveni parametru pro dotaz, login aktualne prihlaseneho uzivatele 
    $query->execute($params);  //provedeni dotazu
    $fetchedUser = $query->fetch(PDO::FETCH_ASSOC); // do fetchedUser si ulozim informace z dotazu

    $jmeno = $fetchedUser["jmeno"];
    $prijmeni = $fetchedUser["prijmeni"];
    $email = $fetchedUser["email"];
    $tel = $fetchedUser["telefon"];
        
?>
    <fieldset>
        <form action="scripty/editace.php" method="POST" enctype="multipart/form-data">
            <label for="login">Login:</label><input type="text" value="<?php echo $_SESSION[session_id()]; //vyplneni aktualniho loginu?>" disabled/><br/>
            <input type="hidden" name="login" value="<?php echo $_SESSION[session_id()]; //nahrada za input login protoze se disabled objekty neodesilaji ?>">
            <label for="jmeno">Jméno: </label><input type="text" value="<?php echo $jmeno; //vyplneni aktualniho jmena?>" name="jmeno"/><br />
            <label for="prijmeni">Přijmení: </label><input type="text" value="<?php echo $prijmeni; //vyplneni aktualniho prijmeni?>" name="prijmeni" required /><br />
            <label for="mail">Email: </label><input type="email" value="<?php echo $email; //vyplneni aktualniho email?>" name="mail" required /><br />
            <label for="tel">Telefonní číslo: </label><input type="text" value="<?php echo $tel; //vyplneni aktualniho telefoniho cisla?>" name="tel" /><br />
            <!--<label for="reg_pfp">Profilový obrázek: </label><input type="file" name="reg_pfp"><br />-->
            <input type="submit" name="submit" id="reg_submit" value="Provést změny" />
            <span id="error"><?php echo $_SESSION["error_edit"]; unset($_SESSION["error_edit"]) //zobrazení chyb z přihlášení pomoci session, nasledně unset této session aby se zobrazila pouze jednou?></span>
        </form>
    </fieldset>

    


<?php    
    }
    require("foot.php");
?>