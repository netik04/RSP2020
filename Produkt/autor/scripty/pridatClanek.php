<?php
    session_start();
    ob_start();
    include '../../db.php';   
    if(isset($_REQUEST["clanekSubmit"]))
    {

        $login = $_SESSION[session_id()];
        $nazev= $_REQUEST["clanekNazev"];
        $pocetAutoru = $_REQUEST["clanekPocetAutoru"];
        $casopis = $_REQUEST["clanekCasopis"];
        $datum = date("Y-m-d");
        $stav_autor = "Podáno";
        $stav_redaktor = "Nově podaný";
        $tmpSoubor = $_FILES["clanekSoubor"]["name"];
        for($i = 3; $i < strlen($tmpSoubor); $i++)
        {
            if($tmpSoubor[$i] == '.')
            {
                $pripona = substr($tmpSoubor, $i, (strlen($tmpSoubor) - 1));
                $soubor = substr($tmpSoubor, 0, (strlen($tmpSoubor) - strlen($pripona)));
                break;
            }
        }
        $tmp = 0;
        for($i = 0; $i < $pocetAutoru; $i++)
        {            
            if($i == 0)
            {
                $autori[$tmp] = $login;
                $tmp++;
            }
            else
            {
                $autori[$tmp] = $_REQUEST["clanekAutor" . ($i+1)];
                $tmp++;
            }
        }

        try
        {
            $queryClanek = $pdo -> prepare("SELECT COUNT(*) FROM clanek WHERE nazev = ?");
            $params = array($nazev);
            $queryClanek -> execute($params);
        }
        catch(PDOException $ex)
        {
            $_SESSION["error"] = "Nepodařilo se nahrát článek. Zkuste to prosím znovu.";
            header("Location: ../pridatClanekForm.php");
            exit();
        }

        if($queryClanek -> fetchColumn(0) == 0)
        {
            $verze = 1;            
            try
            {
                $queryClanek = $pdo -> prepare("INSERT INTO clanek (nazev, id_casopisu) VALUES (?, ?)");
                $params = array($nazev, $casopis);
                $queryClanek -> execute($params);
            }
            catch(PDOException $ex)
            {
                $_SESSION["error"] = "Nepodařilo se nahrát článek. Zkuste to prosím znovu.";
                header("Location: ../pridatClanekForm.php");
                exit();
            }
        }
       
        try
        {
            $queryIdClanku = $pdo -> prepare("SELECT id_clanku FROM clanek WHERE nazev = ?");
            $params = array($nazev);
            $queryIdClanku -> execute($params);

            $id_clanku = $queryIdClanku -> fetchColumn(0);

            $queryVerze = $pdo -> prepare("SELECT COUNT(*) FROM verze WHERE id_clanku = ?");
            $params = array($id_clanku);
            $queryVerze -> execute($params);
        }
        catch(PDOException $ex)
        {
            if($verze == 1)
            {
                uklidit($id_clanku, $pdo);
            }
            $_SESSION["error"] =  "Nepodařilo se vytvořit článek. Zkuste to prosím znovu.";
            header("Location: ../pridatClanekForm.php");
            exit();
        }
       
        $verze = ($queryVerze -> fetchColumn(0)) + 1;
        if($verze != 1)
        {
            $finalSoubor = "clanky/" . $soubor . "_v" . $verze . $pripona;
        }
        else
        {
            $finalSoubor = "clanky/" . $soubor . $pripona;
            for($i = 0; $i < count($autori); $i++)
            {
                try
                {
                    $query_pise = $pdo -> prepare("INSERT INTO pise VALUES (?, ?)");
                    $params = array($autori[$i], $id_clanku);
                    $query_pise -> execute($params);
                }
                catch(PDOException $ex)
                {
                    if($verze == 1)
                    {
                        uklidit($id_clanku, $pdo);
                    }
                    $_SESSION["error"] = "Nepodařilo se vytvořit verzi článku. Zkuste to prosím znovu.";
                    header("Location: ../pridatClanekForm.php");
                    exit();
                }
            }
        }

        try
        {
            $queryVerze = $pdo -> prepare("INSERT INTO verze (id_clanku, verze, stav_autor, stav_redaktor, datum, cesta) VALUES (?, ?, ?, ?, ?, ?)");
            $params = array($id_clanku, $verze, $stav_autor, $stav_redaktor, $datum, $finalSoubor);
            $queryVerze -> execute($params);
        }
        catch(PDOException $ex)
        {
            if($verze == 1)
            {
                uklidit($id_clanku, $pdo);
            }
            $_SESSION["error"] = 2;
            header("Location: ../pridatClanekForm.php");
            exit();
        }

        $tmpSoubor = $_FILES["clanekSoubor"]["tmp_name"];

        if(is_uploaded_file($tmpSoubor))
        {
            $dest = "../../" . $finalSoubor;

            if(!move_uploaded_file($tmpSoubor, $dest))
            {
                if($verze == 1)
                {
                    uklidit($id_clanku, $pdo);
                }
                else
                {
                    $uklid = $pdo -> prepare("DELETE FROM verze WHERE id_clanku = ? AND verze = ?");
                    $params = array($id_clanku, $verze);
                    $uklid -> execute($params);
                }
                $_SESSION["error"] = "Nepodařilo se nahrát soubor s článkem. Zkuste to prosím znovu.";
                header("Location: ../pridatClanekForm.php");
                exit();
            }
        }
        else
        {
            if($verze == 1)
            {
                uklidit($id_clanku, $pdo);
            }
            else
            {
                $uklid = $pdo -> prepare("DELETE FROM verze WHERE id_clanku = ? AND verze = ?");
                $params = array($id_clanku, $verze);
                $uklid -> execute($params);
            }
            $_SESSION["error"] = "Nepodařilo se nahrát soubor s článkem. Zkuste to prosím znovu.";
            header("Location: ../pridatClanekForm.php");
            exit();
        }
      
        header("Location: ../index.php");
        exit();
    }


    function uklidit($id_cl, $db)
    {    
        try
        {
            $uklidit = $db->prepare("DELETE FROM clanek WHERE id_clanku = ?");          
            $params = array($id_cl);
            $uklidit -> execute($params);
            $uklidit2 = $db -> query("ALTER TABLE clanek AUTO_INCREMENT = " . $id_cl);
        }
        catch(PDOException $ex)
        {
            $_SESSION["error"] = $ex->getMessage();
            header("Location: ../pridatClanekForm.php");
            exit();
        }
    }
?>