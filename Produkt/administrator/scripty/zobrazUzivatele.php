<?php
session_start();

$base_path = "../../";

if(!include($base_path . "db.php"))
{
    echo("Nastala chyba. Zkuste to prosím znovu.");
}
else
{
    /*$login = $_POST["login"];
    $jmeno = $_POST["jmeno"];*/

    try
    {
    $sql = "SELECT * FROM uzivatel ORDER BY CAST(role AS CHAR) ASC, prijmeni ASC";
    $query = $pdo->prepare($sql);
    $query->execute();
    }
    catch(PDOException $ex)
    {
        echo("<h2>nope</h2>");
        die();
    }

    echo("<table class='acc-table' cellspacing='0'>");
    echo("<tr><th>Login</th><th>Jméno</th><th>Přijmení</th><th>Role</th><th>Email</th><th>Telefon</th></tr>");
    while(($radek = $query->fetch(PDO::FETCH_ASSOC)) != FALSE)
    {   
        if($radek["login"] != "[deleted]")
        {     
            echo("<tr>");
            echo("<td>" . $radek["login"] . "</td><td>" . $radek["jmeno"] . "</td><td>" . $radek["prijmeni"] . "</td><td>" . $radek["role"] . "</td><td>" . $radek["email"] . "</td><td>" . $radek["telefon"] . 
            "</td><td><button class='admin_button'>Upravit</button></td><td><button class='admin_button_del'");
            if($radek["role"] != "redaktor" && $radek["login"] != $_SESSION[session_id()])
            {
                echo(">Smazat</button></td>");
            }
            else
            {
                echo("disabled>Smazat</button></td>");
            }
            echo("</tr>");
        }
    }
    echo("</table>");
}
?>