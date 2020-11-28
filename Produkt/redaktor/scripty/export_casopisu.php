<?php
/*echo(1);
die();*/

$base_path = "../../";

$role = "redaktor";
session_start();
if($role !== $_SESSION['role']) die();

// čištění temp
$files = glob($base_path."redaktor/tmp/*");  
foreach($files as $file) {
    if(is_file($file) && strtotime("now") - filemtime($file) > 300)
        unlink($file);  
} 


if (isset($_REQUEST["id"]) && require($base_path."db.php")) {
    $data = [
        'id_casopisu' => $_REQUEST["id"]
    ];

    $sql = "SELECT cesta ".
            "FROM casopis ".
            "NATURAL JOIN clanek ".
            "NATURAL JOIN verze ".
            "WHERE verze.stav_redaktor = 'Příspěvek je přijat k vydání' AND ".
            "casopis.id_casopisu = :id_casopisu ";

    $stmt = $pdo->prepare($sql);

    try{
        $stmt->execute($data);
    }
    catch(PDOException $e){
        echo(0);
    }

    $error = array();

    if($stmt->rowCount() > 0){
        $zip = new ZipArchive;
        date_default_timezone_set("Europe/Prague");
        $path = "redaktor/tmp/casopis".$data['id_casopisu']."_".date("dmy_His").".zip";

        if (!file_exists($path) && $zip->open($base_path.$path, ZipArchive::CREATE) === TRUE){
            while($clanek = $stmt->fetch(PDO::FETCH_COLUMN)){
                if(!file_exists($base_path.$clanek))
                    $error[] = $clanek;
                else
                    $zip->addFile($base_path.$clanek, basename($clanek));
            }
            echo(json_encode(array('cesta' => $path, 'error' => $error)));
            $zip->close();
        }
        else echo(0);
    
    }
    //else
        //echo(1); // je prázdný


    $pdo = null;
    die();
}
echo(0); //return false;
die();
