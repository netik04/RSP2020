<?php
    session_start();
    $interni = $_REQUEST["interni"];
    $_SESSION["interni"] = $interni;
?>