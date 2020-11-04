<?php
if(isset($_SESSION[session_id()])){
    return true;
}else{
    return false;
}
?>