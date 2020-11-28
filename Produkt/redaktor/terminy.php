<?php

// ROLE KTERÁ MÁ PŘÍSTUP    
$role = "redaktor";

// ZMĚNIT ABY VŽDY UKAZOVALA DO HLAVNÍ SLOŽKY
$base_path = "../";

$head_str = "<link rel=\"stylesheet\" href=\"redaktor_style.css\">";
$head_str .= "<link rel=\"stylesheet\" href=\"//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css\">";
$head_str .= "<script src=\"https://code.jquery.com/ui/1.12.1/jquery-ui.js\"></script>";


require($base_path."head.php");
?>

<div id="content" class="redaktor terminy">
    <?php // ZDE ZAČÍNÁ OBSAH STRÁNKY REDAKTOR ?>

    <?php
    if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
    else {
    ?>
        <div class="main_title">Přehled termínů časopisů a jejich posudků</div>

        <?php
        $sql = "SELECT * FROM casopis c
            WHERE datum_uzaverky > CURRENT_DATE() OR (
                SELECT COUNT(*) FROM clanek
                NATURAL JOIN verze
                NATURAL LEFT JOIN posudek
                WHERE clanek.id_casopisu = c.id_casopisu AND posudek.datum_vytvoreni IS NULL AND verze.stav_redaktor <> 'Příspěvek zamítnut'
            )
            ORDER BY c.datum_uzaverky ASC";
        $stmt = $pdo->query($sql);

        while($casopis = $stmt->fetch(PDO::FETCH_ASSOC)){
        ?>
            <div class="casopis article" id="<?php echo($casopis["id_casopisu"])?>">

                <div class="left">

                    <div class="title">Uzávěrka časopisu: <?php echo(date_format(date_create($casopis["datum_uzaverky"]),"j.n.Y"))?><br>Téma: <?php echo($casopis["tema"])?></div>
                        <?php
                            $sql_p = "
                            SELECT * FROM clanek as c
                            NATURAL JOIN verze as v
                            NATURAL LEFT JOIN posudek as p
                            JOIN uzivatel on login_recenzenta = uzivatel.login
                            WHERE c.id_casopisu = ".$casopis['id_casopisu']." AND p.datum_vytvoreni IS NULL AND v.stav_redaktor <> 'Příspěvek zamítnut'
                            ORDER BY c.nazev ASC, p.datum_uzaverky DESC
                            ";

                            $stmt_p = $pdo->query($sql_p);

                            if($stmt_p->rowCount() > 0){
                                ?>
                                <table class="posudky">
                                    <tr>
                                        <th>Název článku</th>
                                        <th>Verze</th>
                                        <th>Autor</th>
                                        <th>Datum uzávěrky</th>
                                    </tr>
                                <?php
                            }

                            while($posudek = $stmt_p->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <tr>
                                    <td><a href="<?php echo($base_path."redaktor/clanek?id=".$posudek['id_clanku'])?>"><?php echo($posudek['nazev']) ?></a></td>
                                    <td><?php echo($posudek['verze']) ?></td>
                                    <td><?php echo($posudek['jmeno']." ".$posudek['prijmeni']) ?></td>
                                    <td><?php echo(date_format(date_create($posudek["datum_uzaverky"]),"j.n.Y")) ?></td>
                                </tr>
                                <?php
                            }
                        
                        if($stmt_p->rowCount() > 0){
                        ?>
                            </table>
                        <?php
                        }
                        ?>
                </div>
            </div>
        
        <?php
        }
        
        ?>

    <?php } ?>

</div>

<?php require($base_path."foot.php"); $pdo = null; ?>