<?php
// ZMĚNIT PŘI KOP�?ROV�?N�? PROJEKTU
//$base_path = "/home/studaci/public_html/product/development/v0_uzivatel/"; // pro absolutni referenci mezi soubory např. include($base_path."head.php"); 
//$base_url = "https://alpha.kts.vspj.cz/~studaci/product/development/v0_uzivatel/"; // pro absolutni referenci url odkazu např. <link src="<?php echo $base_url>style.css">, <a href="<?php echo $base_url>clanky/cl1.pdf">
// bez předešlých se velice špatně používá relativná obzvláště, když se daná �?ást přidává include (v případě head.php a style.css)

require("head.php");
?>
<form id="loginForm" action="Scripty/prihlaseni.php" method="POST">
Login <input type="text" name="login" id="login"><br/>
Heslo <input type="password" name="password" id="password"><br/>
<input type="submit" value="Přihlásit">
<p class="error"><?php echo $_SESSION["error"];unset($_SESSION["error"])?></p>
</form>



<?php require("foot.php"); $pdo = null; ?>