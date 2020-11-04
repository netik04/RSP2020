<?php
session_start();

    if(glob($base_path . "img/profile_pics/" . html_entity_decode($_SESSION[session_id()]) . ".*" )){
        $profilePic = $base_path . "img/profile_pics/" . html_entity_decode($_SESSION[session_id()]);
    }else{
    $profilePic = $base_path . "img/profile_pics/default"; 
    }
    echo "<img class=\"profilovka\" src=\"$profilePic\">";
?>
