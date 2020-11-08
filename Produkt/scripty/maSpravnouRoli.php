<?php 
/*
    script ktery hlida jestli ma spravnou roli
    predpoklada se ze stranky ktere potrbuji omezeni pro urcitou roli, pred includovanim "head.php" ulozi do promenne $role string s nazvem role  
*/
if($role != null){ // pokud se promenna role nerovna null, pak stranka je omezena pro urcitou roli 
    if($role == $_SESSION['role']){ // pokud se promenna role v session rovna promenne role
        //uzivatel ma spravnou roli 
        return true;
    }else{
        //uzivatel nema spravnou roli 
        return false;
    }
}
?>