<?php
    session_start();
    ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logos Polytechnikos</title>
    <link rel="stylesheet" href="<?php echo($base_path."style.css");?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <?php echo($head_str); //přidá další věci do hlavičky z volajícího php?> 
    <style>

    </style>
    <script>

    </script>
</head>

<body>
    <div id="page">

        <header>
            <h1><a href="<?php echo($base_path."index.php");?>">Logos Polytechnikos</a></h1> 

            <?php
            if (!include("scripty/jePrihlasen.php")){ // test jestli je uživatel přihlášen, pokud není, uživateli je zobrazeno pole pro přihlášení
            ?>

            <div class="login flex_horizontalne">
                <form id="loginForm" action="scripty/prihlaseni.php" method="POST">
                    <table>
                        <tr><td class="labelRegistr">Login: </td><td><input type="text" name="login" id="login" required><br/></td></tr>
                        <tr><td class="labelRegistr">Heslo: </td><td><input type="password" name="password" id="password" required><br/></td></tr>
                    </table>
                    <div class="flex_vodorovne">
                        <input class="button" type="submit" value="Přihlásit">
                        <input class="button" type="button" onclick="window.location.href='registr.php'" value="Registrace">
                    </div>
                </form>
                <p id="error">&nbsp;<?php echo $_SESSION["error"];unset($_SESSION["error"]) //zobrazení chyb z přihlášení pomoci session, nasledně unset této session aby se zobrazila pouze jednou?></p>
            </div>

            <?php 
            }
                else //pokud je přihlášen, je uživateli zobrazeno jmeno, příjmení a role uživatel a tláčítko pro odhlášení 
            { 
            ?> 

            <div class="login flex_horizontalne">
                <div class="flex_vodorovne">
                    <?php include($base_path . "scripty/view_profile_pic.php")?> 
                    <div class="flex_horizontalne">
                        <?php 
                            require("db.php"); // připojení do databázení
                            $query = $pdo->prepare("select jmeno, prijmeni from uzivatel where login = ?"); //připravení dotazu který vrátí jmeno a prijmeni pro prave prihlaseneho uzivatele 
                            $params = array($_SESSION[session_id()]); // pripraveni parametru pro dotaz
                            $query->execute($params);   //provedeni dotazu
                            $fetchedUser = $query->fetch(PDO::FETCH_ASSOC); //do promenne fetchedUser si ulozim z dotazu jeden radek?>
                        <div><?php echo $fetchedUser['jmeno'] . ' ' . $fetchedUser['prijmeni']; //zobrazeni jmena a prijmeni ktere vratil dotaz?></div><br/>
                        <div><?php echo $_SESSION['role'] //zobrazeni role ktera je prenasena v session role?></div>
                    </div>
                </div>
                <div class="flex_vodorovne">
                    <button class="button" id="logOut">Odhlášení</button>
                    <button class="button" id="editProfile">Upravit profil</button>
                </div>
            </div>


            <script>
                // kdyz se klikne na button logOut, zavola se script ktery odhlasi uzivatele
                $(document).ready(function(){
                    $("#logOut").click(function(){
                        document.location = '<?php echo($base_path);?>scripty/odhlaseni.php';
                    });

                    $("#editProfile").click(function(){
                        document.location = '<?php echo($base_path);?>editProfile.php';
                    });
                });
            </script>

            <?php
            }
            ?>
        </header>

        <?php
        include_once("menu.php");
        /* tato cast slouzi pro overeni jestli je uzivatel prihlaseni a jestli ma spravnou roli*/
        if(!($ignore == true)){ // promenna ignore slouzi k zameceni zacykleni odkazovani na index, na strance index.php je prommena ignore = true
            if(!include($base_path."scripty/jePrihlasen.php")){ 
                header("Location: " . $base_path . "index.php"); // pokud uzivatel neni prihlaseni, je presmerovan na index, pokud je prihlasen 
                die();
            }

            if(!include($base_path."scripty/maSpravnouRoli.php")){ // pokud nema spravnou roli, je presmerovan na index, pokud ma spravnou roli je mu zobrazena stranka
                header("Location: " . $base_path . "index.php");
                die();
            }
        }
        ?>