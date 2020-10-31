<?php
if(isset($_REQUEST["reg_submit"]))
{
    require_once '../db.php';
    $login = $_REQUEST["reg_login"];
    $passwd = hash("sha256", ($login . $_REQUEST["reg_passwd"]));
    $jmeno = $_REQUEST["reg_jmeno"];
    $prijmeni = $_REQUEST["reg_prijmeni"];
    $email = $_REQUEST["reg_mail"];
    $tel = $_REQUEST["reg_tel"];

    try
    {
        $query = $pdo->prepare("INSERT INTO uzivatel (login, heslo, jmeno, prijmeni, email, telefon) VALUES (?, ?, ?, ?, ?, ?)");
        $params = array($login, $passwd, $jmeno, $prijmeni, $email, $tel);
        $query->execute($params);
    }
    catch(PDOException $ex)
    {
        die($ex -> getMessage());
    }
    $tmpFile = $_FILES['reg_pfp']['tmp_name'];
    $newFile = '../img/profile_pics/'.$_FILES['reg_pfp']['name'];
    $result = move_uploaded_file($tmpFile, $newFile);
    $extension = '';
     for($i = 3; $i < strlen($newFile); $i++)
     {
         if($newFile[$i] == '.')
         {
            $extension = substr($newFile, $i, (strlen($newFile) - 1));
         break;
         }
     }
     echo($extension);
    rename($newFile, "../img/profile_pics/".$login . $extension);

    header("Location: ../index.php");
}
?>