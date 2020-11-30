<?php

    session_start();
    if(!include("jePrihlasen.php")) die();
    //session_start();
    $interni = $_REQUEST["interni"];
    $_SESSION["interni"] = $interni;
?>