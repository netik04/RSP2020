<?php

    $role = "sefredaktor";
    session_start();
    if($role !== $_SESSION['role']) die();

    //session_start();
    $interni = $_REQUEST["interni"];
    $_SESSION["interni"] = $interni;
?>