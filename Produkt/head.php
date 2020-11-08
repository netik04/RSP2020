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
            if (!include("scripty/jePrihlasen.php")){
            ?>

            <div class="login">
                <form id="loginForm" action="scripty/prihlaseni.php" method="POST">
                    Login <input type="text" name="login" id="login"><br/>
                    Heslo <input type="password" name="password" id="password"><br/>
                    <input type="submit" value="Přihlásit">
                    <input type="button" onclick="window.location.href='registr.php'" value="Registrace">
                </form>
                <p id="error">&nbsp;<?php echo $_SESSION["error"];unset($_SESSION["error"])?></p>
            </div>

            <?php }else{ ?>

            <div class="login">
                <span><?php echo $_SESSION[session_id()];?></span>
                <button id="logOut">buttonek pro odhlaseni</button>
            </div>
            <script>
                $(document).ready(function(){
                    $("#logOut").click(function(){
                        document.location = '<?php echo($base_path);?>scripty/odhlaseni.php';
                    });
                });
            </script>

            <?php
            }
            ?>
        </header>
        <?php
        if(!($ignore == true)){
            if(!include($base_path."scripty/jePrihlasen.php")){
                header("Location: ../index.php");
                die();
            }
        }
        ?>