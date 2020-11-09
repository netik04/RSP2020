<?php
session_start(); // start session

    if(glob($base_path . "img/profile_pics/" . html_entity_decode($_SESSION[session_id()]) . ".*" )){ 
        // pokud se ve slozce s profilovkama nachází profilovka ze loginem prihlaseneho uzivatele
        $profilePic = $base_path . "img/profile_pics/" . html_entity_decode($_SESSION[session_id()]); //zapise se cesta do promenne
    }else{
        // pokud se ve slozce s profilovkama nenachazi profilovka ze loginem prihlaseneho uzivatele
    $profilePic = $base_path . "img/profile_pics/default"; //zapise se cesta k defaultni profilovce do promenne
    }
    echo "<img class=\"profilovka\" src=\"$profilePic\">"; //script vygeneruje tag img ve kterem bude profilovka prihlaseneho uzivatele nebo defaultni profilovka
?>
