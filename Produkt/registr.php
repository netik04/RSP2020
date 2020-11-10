<?php
    // tuhle stránku si může zobrazit nepřihlášený uživatel - nekontroluji roli
    $ignore = true; 
    // přidám hlavičku
    require("head.php"); 
?>

<?php
    // připojení do DB
    if (!include("db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // pokud se nepodaří - nemá smysl pokračovat dál 
    else // pokud se úspěšně připojím k DB - můžu pracovat
    {      
    ?>
        <script>
            $("document").ready(function()
            {
                $("#reg_submit").prop("disabled", true); // pokud ano - nejde se registrovat, zamkne se tlačítko 'registrovat'
                $("#reg_login").keyup(function() // uživatel něco zadal do pole loginu
                {
                    if($("#reg_login").val() != "") // kontrola, zdali je login prázdný
                    {
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() 
                        {
                            if (this.readyState == 4 && this.status == 200) // pokud přišla odpověď z externího scriptu
                            {
                                if(this.responseText == "true") // login již existuje
                                {
                                    $("#error_login").text("Tento login již existuje.");
                                    $("#error_login").css("color", "red");
                                    $("#reg_submit").prop("disabled", true);
                                }
                                else if($("#reg_login").val().length > 50) // login je moc dlouhý
                                {
                                    $("#error_login").text("Login nesmí mít více než 50 znaků");
                                    $("#error_login").css("color", "red");
                                    $("#reg_submit").prop("disabled", true);
                                }
                                else // login splňuje podmínky a není v DB
                                {
                                    $("#error_login").html("&nbsp; &#x2713;"); 
                                    $("#error_login").css("color", "green");
                                    $("#reg_submit").prop("disabled", false);
                                }
                            }
                        };
                        xmlhttp.open("GET","scripty/existujeLogin.php?login="+$("#reg_login").val(),true); // kontrola, zdali login už neexistuje
                        xmlhttp.send(); // odešli požadavek PHP scriptu
                    }
                    else
                    {
                        // login je prázdný - není potřeba nic zobrazovat
                        $("#error_login").text("");
                        $("#reg_submit").prop("disabled", true);
                    } 
                });                                              
                $("#reg_passwd_potvrdit").keyup(function() // pokud uživatel zadal podruhé heslo
                {
                    if($("#reg_passwd_potvrdit").val() == "") // pokud nic nevyplnil
                    {
                        // není potřeba prozatím nic zobrazovat
                        $("#error_passwd").text("");
                        $("#reg_submit").prop("disabled", true);
                    }
                    else if($("#reg_passwd_potvrdit").val() != $("#reg_passwd").val()) // pokud se hesla neshodují
                    {
                        // upozorni ho na to
                        $("#error_passwd").text(" Zadaná hesla se musí shodovat!");
                        $("#error_passwd").css("color", "red");
                        $("#reg_submit").prop("disabled", true);
                    }
                    else // pokud se shodují
                    {
                        // vše je v pořádku - zobraz 'checkmark'
                        $("#error_passwd").html("&nbsp; &#x2713;");
                        $("#error_passwd").css("color", "green");
                        $("#reg_submit").prop("disabled", false);
                    }
                });        
            });
        </script>
        <h2>Registrační formulář</h2>
        <?php
        // pokud nastala chyba při registraci
        if(isset($_SESSION["error_reg"]))
        {
            // mohla nastat jen jedna (INSERT) - zobraz chybovou hlášku
            echo("<span>Vyskytla se chyba při vytváření uživatele. Zkuste to prosim znovu</span><br />");
            unset($_SESSION["error_reg"]);
        } 
        ?>
        <fieldset>
        <!-- registrační formulář -->
        <!-- nastavuji navíc enctype=multipart/form-data => říkám, že posílám více typů dat (text + soubor) -->
        <form action="scripty/registrace.php" method="POST" enctype="multipart/form-data"> 
            <label for="reg_login">Uživatelské jméno: </label><input type="text" name="reg_login" id="reg_login" required /><span id="error_login"></span><br />
            <label for="reg_passwd">Heslo: </label><input type="password" name="reg_passwd" id="reg_passwd" required /><br />
            <label for="reg_passwd_potvrdit">Potvrdit heslo: </label><input type="password" id="reg_passwd_potvrdit" required /><span id="error_passwd"></span><br />
            <label for="reg_jmeno">Jméno: </label><input type="text" name="reg_jmeno" required /><br />
            <label for="reg_prijmeni">Přijmení: </label><input type="text" name="reg_prijmeni" required /><br />
            <label for="reg_mail">Email: </label><input type="email" name="reg_mail" required /><br />
            <label for="reg_tel">Telefonní číslo: </label><input type="text" name="reg_tel" /><br />
            <label for="reg_pfp">Profilový obrázek: </label><input type="file" name="reg_pfp" accept="image/png, image/jpeg" /><br />
            <?php
                $role = "redaktor";
                if(include("scripty/maSpravnouRoli.php")) // pokud jsi redaktor
                {
                    // zobraz input navíc pro přiřazení role nového uživatele
                    echo("<label for=\"reg_role\">Role nového uživatele: </label><select name=\"reg_role\"><option value=\"recenzent\">Recenzent</option><option value=\"sefredaktor\">Šéfredaktor</option><option value=\"administrator\">Administrátor</option></select><br />");
                }
            ?>
            <input type="submit" name="reg_submit" id="reg_submit" value="Registrovat" />
        </form>
        </fieldset>
    <?php    
    }
    require("foot.php"); // připojení patičky
    ?>