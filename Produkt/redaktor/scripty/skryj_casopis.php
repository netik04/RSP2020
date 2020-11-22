<?php
/*echo(1);
die();*/

$base_path = "../../";

if (isset($_REQUEST["id"]) && require($base_path."db.php")) {
    $data = [
        'id' => $_REQUEST["id"]
    ];

    $sql = "UPDATE casopis SET zobrazit = 0 WHERE id_casopisu = :id";

    $stmt = $pdo->prepare($sql);

    try{
        $stmt->execute($data);
        if($stmt->rowCount() == 1)
            echo(1);
        else
            echo(0);
    }
    catch(PDOException $e){
        echo(0);
    }
    $pdo = null;
    die();
}
echo(0); //return false;
die();
