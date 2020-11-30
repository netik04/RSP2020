<?php
    session_start();
    if(isset($_SESSION["rainbow"]))
        unset($_SESSION["rainbow"]);
    else
        $_SESSION["rainbow"] = true;
?>