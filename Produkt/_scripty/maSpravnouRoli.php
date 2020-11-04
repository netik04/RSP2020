<?php
if($role != null){
    if($role == $_SESSION['role']){
        return true;
    }else{
        return false;
    }
}
?>