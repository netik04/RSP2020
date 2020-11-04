<?php
    $ignore = true;
    require("head.php");
?>

<?php
    if (!include("db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později."; // připojení do DB
    else
    {      
    ?>
        <script>
            $("document").ready(function()
            {
                if($("#reg_login").val() == "") // kontrola, zdali je login prázdný
                {
                    $("#reg_submit").prop("disabled", true); // pokud ano - nejde se registrovat, zamkne se tlačítko 'registrovat'
                }
                $("#reg_login").keyup(function() // uživatel něco zadal do pole loginu
                {
                    if($("#reg_login").val() != "")
                    {
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() 
                        {
                            if (this.readyState == 4 && this.status == 200) // pokud přišla odpověď z externího scriptu
                            {
                                if(this.responseText == "true") // login již existuje
                                {
                                    $("#error_login").text("Tento login již existuje.");
                                    $("#reg_submit").prop("disabled", true);
                                }
                                else if($("#reg_login").val().length > 50) // login je moc dlouhý
                                {
                                    $("#error_login").text("Login nesmí mít více než 50 znaků");
                                    $("#reg_submit").prop("disabled", true);
                                }
                                else // login splňuje podmínky a není v DB
                                {
                                    $("#error_login").text("Tento login můžete použít."); 
                                    $("#reg_submit").prop("disabled", false);
                                }
                            }
                        };
                        xmlhttp.open("GET","scripty/existujeLogin.php?login="+$("#reg_login").val(),true); // kontrola, zdali login už neexistuje
                        xmlhttp.send();
                    }
                    else
                    {
                        $("#error_login").text("");
                        $("#reg_submit").prop("disabled", true);
                    }
                });           
            });
        </script>
        <h2>Registrační formulář</h2>
        <?php
        if(isset($_SESSION["error_reg"]))
        {
            if($_SESSION["error_reg"] == 1)
            {
                echo("<span>Vyskytla se chyba při vytváření uživatele. Zkuste to prosim znovu</span><br />");
            }
            else if($_SESSION["error_reg"] == 2)
            {
                echo("<span>Váš účet byl vytvořen, nepodařilo se však nahrát vaší profilovou fotku. Pokud si jí přejete nahrát znovu, můžete tak učinit z vašeho profilu v sekci 'upravit'.</span><br />");
            }
            unset($_SESSION["error_reg"]);
        } 
        ?>
        <fieldset>
        <form action="scripty/registrace.php" method="POST" enctype="multipart/form-data">
            <label for="reg_login">Uživatelské jméno: </label><input type="text" name="reg_login" id="reg_login" required /><span id="error_login"></span><br />
            <label for="reg_passwd">Heslo: </label><input type="password" name="reg_passwd" required /><br />
            <label for="reg_jmeno">Jméno: </label><input type="text" name="reg_jmeno" required /><br />
            <label for="reg_prijmeni">Přijmení: </label><input type="text" name="reg_prijmeni" required /><br />
            <label for="reg_mail">Email: </label><input type="email" name="reg_mail" required /><br />
            <label for="reg_tel">Telefonní číslo: </label><input type="text" name="reg_tel" /><br />
            <label for="reg_pfp">Profilový obrázek: </label><input type="file" name="reg_pfp"><br />
            <?php
                $role = "redaktor";
                // pokud jsi redaktor
                if(include("scripty/maSpravnouRoli.php"))
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
    require("foot.php");
    ?>