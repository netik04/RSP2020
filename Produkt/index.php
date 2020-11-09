<?php
// ZMĚNIT PŘI KOPÍROVÁNÍ PROJEKTU
$base_path = ""; // pro absolutni referenci mezi soubory např. include($base_path."head.php"); 
$base_url = ""; // pro absolutni referenci url odkazu např. <link src="<?php echo $base_url>style.css">, <a href="<?php echo $base_url>clanky/cl1.pdf">
// bez předešlých se velice špatně používá relativná obzvláště, když se daná část přidává include (v případě head.php a style.css)
$ignore = true; // slouzi pro zastaveni odkazovani v indexu, protoze v headu testuji prihlaseni a neprihlaseneho by to na index odkazovalo nekonecnekrat
require("head.php");
?>

<?php require("foot.php"); $pdo = null; ?>