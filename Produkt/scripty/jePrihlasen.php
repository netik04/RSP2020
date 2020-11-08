<?php
/*
    script ktery hlida jestli je uzivatel prihlasen
    uzivatel je prihlaseno pokud promenna na pozici "session_id()" v session
*/
if(isset($_SESSION[session_id()])){ //pokud je neco v promenne na pozici "session_id()" v session 
    //uzivatel je prihlasen
    return true;
}else{
    //uzivatel neni prihlasen
    return false;
}
?>