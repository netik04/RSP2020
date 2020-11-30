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
    <link rel="icon" 
      type="image/png" 
      href="<?php echo($base_path."img/icon.png");?>">
    <link rel="stylesheet" href="<?php echo($base_path."style.css");?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css ">
    <?php echo($head_str); //přidá další věci do hlavičky z volajícího php?>
    <style>

    </style>
    <script>

    </script>
</head>

<body>
    <div id="page">

        <header>
            <h1 class="logo"><a href="<?php echo($base_path."index.php");?>"><span class="logos">Logos</span>POLY<span class="logospolytechnos2">TECH</span>NIKOS</a></h1>

            <?php
            if (!include("scripty/jePrihlasen.php")){ // test jestli je uživatel přihlášen, pokud není, uživateli je zobrazeno pole pro přihlášení
            ?>

            <div class="login flex_horizontalne">
              <div class="flex_vodorovne">
                <form id="loginForm" action="scripty/prihlaseni.php" method="POST">
                    <input type="text" name="login" id="login" placeholder="Uživatelské jméno" required><br/>
                    <input type="password" name="password" id="password" placeholder="Heslo" required><br/>
                    <input class="button_login btn-three" type="submit" value="Přihlásit">
                    <input class="button_login btn-three" type="button" onclick="window.location.href='registr.php'" value="Registrace">
                </form>
                <p id="error">&nbsp;<?php echo $_SESSION["error"];unset($_SESSION["error"]) //zobrazení chyb z přihlášení pomoci session, nasledně unset této session aby se zobrazila pouze jednou?></p>
                </div>
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
                        <div><?php echo $fetchedUser['jmeno'] . ' ' . $fetchedUser['prijmeni']; //zobrazeni jmena a prijmeni ktere vratil dotaz?></div>
                        <div class="role"><?php echo $_SESSION['role'] //zobrazeni role ktera je prenasena v session role?></div>
                    </div>
                    <div class="flex_vodorovne">
                        <button class="buttonikOdhlasit btn-three" id="editProfile"><i class="fa fa-user-edit" aria-hidden="true"></i></button>
                        <button class="buttonikOdhlasit btn-three" id="logOut"><i class="fa fa-sign-out-alt" aria-hidden="true"></i></button>
                    </div>
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
