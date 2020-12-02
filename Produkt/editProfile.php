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
<div id="content" class="editprofile">
    <fieldset>
        <legend>Změna údajů</legend>
        <form action="scripty/editace.php" method="POST" enctype="multipart/form-data" class="editform">
            <label for="login">Login:</br></label><input type="text" value="<?php echo $_SESSION[session_id()]; //vyplneni aktualniho loginu?>" disabled/><br/>
            <label for="jmeno">Jméno: </br></label><input type="text" value="<?php echo $jmeno; //vyplneni aktualniho jmena?>" name="jmeno"/><br />
            <label for="prijmeni">Přijmení: </br></label><input type="text" value="<?php echo $prijmeni; //vyplneni aktualniho prijmeni?>" name="prijmeni" required /><br />
            <label for="mail">Email: </br></label><input type="email" value="<?php echo $email; //vyplneni aktualniho email?>" name="mail" required /><br />
            <label for="tel">Telefonní číslo:</br> </label><input type="text" value="<?php echo $tel; //vyplneni aktualniho telefoniho cisla?>" name="tel" /><br />
            <label for="reg_pfp">Profilový obrázek: </br></label><input type="file" name="reg_pfp"><br />
            <br /><label for="login">Zadejte heslo pro potvrzeni zmen</label><br />
            <input type="password" name="password"/><br/>
            <input type="submit" name="submit" id="submit" value="Provést změny" class="reg_button" /></br>
            <span class="error"><?php echo $_SESSION["error_edit"]; unset($_SESSION["error_edit"]) //zobrazení chyb z přihlášení pomoci session, nasledně unset této session aby se zobrazila pouze jednou?></span>
        </form>
    </fieldset>

    <fieldset class="fieldset1">
        <legend>Změna hesla</legend>
        <form action="scripty/zmenitHeslo.php" method="POST" enctype="multipart/form-data" class="editform">
          <span id="error_passwd"></span><br/>
            <label for="newPassword">Nové heslo:</br></label><input type="password" name="newPassword" id="newPassword" required/> <br/>
            <label for="newPassword">Potvrdit Nové heslo:</br></label><input type="password" name="new_passwd_potvrdit" id="new_passwd_potvrdit" required/><br/>
            <br /><label for="login">Zadejte svoje staré heslo pro potvrzeni zmen</label><br />
            <input type="password" name="oldPassword"/><br/>
            <input type="submit" name="submit" id="pass_submit" value="Změnit heslo" class="reg_button" /></br>
            <span class="error dole"><?php echo $_SESSION["error_edit_pass"]; unset($_SESSION["error_edit_pass"]) //zobrazení chyb z přihlášení pomoci session, nasledně unset této session aby se zobrazila pouze jednou?></span>
        </form>
    </fieldset>

    <script>
        $(document).ready(function(){
            //$("#pass_submit").prop("disabled", true)
            $("#new_passwd_potvrdit").keyup(function() // pokud uživatel zadal podruhé heslo
            {
                if($("#new_passwd_potvrdit").val() == "") // pokud nic nevyplnil
                {
                    // není potřeba prozatím nic zobrazovat
                    $("#error_passwd").text("");
                    $("#pass_submit").prop("disabled", true);
                }
                else if($("#new_passwd_potvrdit").val() != $("#newPassword").val()) // pokud se hesla neshodují
                {
                    // upozorni ho na to
                    $("#error_passwd").text(" Zadaná hesla se musí shodovat!");
                    $("#error_passwd").css("color", "red");
                    $("#pass_submit").prop("disabled", true);
                }
                else // pokud se shodují
                {
                    // vše je v pořádku - zobraz 'checkmark'
                    $("#error_passwd").html("&nbsp; &#x2713;");
                    $("#error_passwd").css("color", "green");
                    $("#pass_submit").prop("disabled", false);
                }
            });
        });
    </script>
</div>

<?php
    }
    require("foot.php");
?>
