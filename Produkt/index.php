<?php
// ZMĚNIT PŘI KOP�?ROV�?N�? PROJEKTU
$base_path = "/home/studaci/public_html/product/development/v0_uzivatel/"; // pro absolutni referenci mezi soubory např. include($base_path."head.php"); 
$base_url = "https://alpha.kts.vspj.cz/~studaci/product/development/v0_uzivatel/"; // pro absolutni referenci url odkazu např. <link src="<?php echo $base_url>style.css">, <a href="<?php echo $base_url>clanky/cl1.pdf">
// bez předešlých se velice špatně používá relativná obzvláště, když se daná �?ást přidává include (v případě head.php a style.css)

require("head.php");
?>
<button><a href="https://alpha.kts.vspj.cz/~studaci/product/development/v0_uzivatel/login.php">Login</a></button><br />
<button><a href="https://alpha.kts.vspj.cz/~studaci/product/development/v0_uzivatel/registr.php">Registrace</a></button><br />
<?php
if (isset($_SESSION[session_id()])){
?>
<p><?php echo $_SESSION[session_id()];?></p>
<button id="logOut">buttonek pro odhlaseni</button>
<?php
}
?>
<script>
    $(document).ready(function(){
        $("#logOut").click(function(){
            <?php unset($_SESSION[session_id()]) ?> 
            location.reload();
        });
    });
</script>
<?php require("foot.php"); $pdo = null; ?>