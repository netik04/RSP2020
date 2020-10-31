<?php
    $ignore = true;
    require("head.php");
?>

<?php
    if (!include("db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else
    {      
    ?>
        <script>
            $("document").ready(function()
            {
                if($("#reg_login").val() == "")
                {
                    $("#reg_submit").prop("disabled", true);
                }
                $("#reg_login").keyup(function() 
                {
                    if($("#reg_login").val() != "")
                    {
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() 
                        {
                            if (this.readyState == 4 && this.status == 200) 
                            {
                                if(this.responseText == "true")
                                {
                                    $("#error").text("Tento login již existuje.");
                                    $("#reg_submit").prop("disabled", true);
                                }
                                else
                                {
                                    $("#error").text("Tento login můžete použít.");
                                    $("#reg_submit").prop("disabled", false);
                                }
                            }
                        };
                        xmlhttp.open("GET","Scripty/existujeLogin.php?login="+$("#reg_login").val(),true);
                        xmlhttp.send();
                    }
                    else
                    {
                        $("#error").text("");
                        $("#reg_submit").prop("disabled", true);
                    }
                });           
            });
        </script>
        <h2>Registrační formulář</h2>
        <fieldset>
        <form action="Scripty/registrace.php" method="POST" enctype="multipart/form-data">
            <label for="reg_login">Login:</label><input type="text" name="reg_login" id="reg_login" required /><span id="error"></span><br />
            <label for="reg_passwd">Heslo:</label><input type="password" name="reg_passwd" required /><br />
            <br />
            <label for="reg_jmeno">Jméno: </label><input type="text" name="reg_jmeno" required /><br />
            <label for="reg_prijmeni">Přijmení: </label><input type="text" name="reg_prijmeni" required /><br />
            <label for="reg_mail">Email: </label><input type="email" name="reg_mail" required /><br />
            <label for="reg_tel">Telefonní číslo: </label><input type="text" name="reg_tel" /><br />
            <label for="reg_pfp">Profilový obrázek: </label><input type="file" name="reg_pfp" accept="image/png, image/jpeg"><br />
            <input type="submit" name="reg_submit" id="reg_submit" value="Registrovat" />
        </form>
        </fieldset>
    <?php    
    }
    require("foot.php");
    ?>