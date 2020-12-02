<?php
if(!include("jePrihlasen.php")) die("It just doesn't work");
?>
<div style="display: none;" id="notificationWrap">
    <h1>Notifikace</h1>
    <?php
        $notificationCount = 0;
        if (!include($base_path."db.php")) echo "Něco se nepovedlo.<br>Zkuste to prosím později.";
        else {
            if($_SESSION['role'] == 'redaktor' || $_SESSION['role'] == 'sefredaktor' ){
                
                if($_SESSION['role'] == 'redaktor'){
                    $query = $pdo->query("SELECT Count(*) FROM helpdesk h WHERE id_otazky IS NULL AND NOT EXISTS(SELECT * FROM helpdesk hh WHERE h.id = hh.id_otazky)");
                    //$notificationCount += $query->rowCount();
                    if(($pocet = $query->fetch(PDO::FETCH_COLUMN)) > 0){
                        ?>
                        <h2>Helpdesk</h2>
                        <?php
                        $notificationCount++;
                        ?>
                        <div class="notification">
                            <span id="helpdeskNotification"><?php echo "V Helpdesku se nachází: ". $pocet. " nezodpovězených dotazů";?></span>
                        </div>
                        <?php
                    }
                }

                $query = $pdo->query("SELECT * FROM casopis WHERE datum_uzaverky > CURRENT_DATE AND datum_uzaverky < CURRENT_DATE + 3");
                $query->execute();
                $notificationCount += $query->rowCount();
                if($query->rowCount() > 0){
                    ?>
                    <h2>Časopisy</h2>
                    <?php
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <div class="notification">
                                <span><?php echo "Název: ". $row["tema"];?></span>
                            <div class="flex_vodorovne">
                                <span><?php echo "Datum: " . date_format(date_create($row['datum_uzaverky']),"j.n.Y")?></span>
                                <span><?php 
                                    $now = time();
                                    $your_date = strtotime($row['datum_uzaverky']);
                                    $datediff = $your_date - $now;
                                    echo "Zbývá: ". ceil($datediff / (60 * 60 * 24)) ." dny";
                                ?></span>
                            </div>
                        </div>
                        <?php
                    }
                }
                $query = $pdo->prepare("SELECT * FROM clanek 
                NATURAL JOIN verze
                NATURAL JOIN posudek
                JOIN uzivatel on login = login_recenzenta
                WHERE posudek.datum_uzaverky >= CURRENT_DATE AND posudek.datum_uzaverky < CURRENT_DATE + 3 AND posudek.datum_vytvoreni IS NULL AND verze.stav_redaktor <> 'Příspěvek zamítnut'");
                $query->execute();
                $notificationCount += $query->rowCount();
                if($query->rowCount() > 0){
                    ?>
                    <h2>Posudky</h2>
                    <?php
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <div class="notification">
                                <span><?php echo "Název: ". $row["nazev"];?></span>
                                <span><?php echo "Recenzent: ". $row["jmeno"] . " " . $row["prijmeni"];?></span>
                            <div class="flex_vodorovne">
                                <span><?php echo "Datum: " . date_format(date_create($row['datum_uzaverky']),"j.n.Y")?></span>
                                <span><?php 
                                    $now = time();
                                    $your_date = strtotime($row['datum_uzaverky']);
                                    $datediff = $your_date - $now;
                                    echo "Zbývá: ". abs(ceil($datediff / (60 * 60 * 24))) ." dny";
                                ?></span>
                            </div>
                        </div>
                        <?php
                    }
                }
            }else if($_SESSION['role'] == 'recenzent'){
                $query = $pdo->prepare("SELECT * FROM clanek 
                NATURAL JOIN verze
                NATURAL JOIN posudek
                JOIN uzivatel on login = login_recenzenta
                WHERE login_recenzenta = ? AND posudek.datum_uzaverky >= CURRENT_DATE AND posudek.datum_uzaverky < CURRENT_DATE + 3 AND posudek.datum_vytvoreni IS NULL AND verze.stav_redaktor <> 'Příspěvek zamítnut'");
                $params = array($_SESSION[session_id()]);
                $query->execute($params);
                $notificationCount += $query->rowCount();
                if($query->rowCount() > 0){
                    ?>
                    <h2>Posudky</h2>
                    <?php
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <div class="notification">
                                <span><?php echo "Název: ". $row["nazev"];?></span>
                                <span><?php echo "Recenzent: ". $row["jmeno"] . " " . $row["prijmeni"];?></span>
                            <div class="flex_vodorovne">
                                <span><?php echo "Datum: " . date_format(date_create($row['datum_uzaverky']),"j.n.Y")?></span>
                                <span><?php 
                                    $now = time();
                                    $your_date = strtotime($row['datum_uzaverky']);
                                    $datediff = $your_date - $now;
                                    echo "Zbývá: ". abs(ceil($datediff / (60 * 60 * 24))) ." dny";
                                ?></span>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        }
    ?>
</div>
    <button class="buttonikOdhlasit btn-three" id="notificationButton"><i class="far fa-bell" aria-hidden="true"><?php if($notificationCount>0){?><span id="notificationCount"><?php if($notificationCount>0) echo $notificationCount?></span><?php }?></i></button>
<script>
$("#notificationButton").on("click", function(){
    $("#notificationWrap").toggle();
});
</script>