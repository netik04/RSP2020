<?php
$role = "autor";

$base_path = "../";

require($base_path."head.php");
?>
<div id="content" class="autor">

<?php
if (!include($base_path."db.php")) echo "Nepodařilo se navázat spojení s databází.<br>Zkuste to prosím později.";
else 
{
?>

<p>::TODO::</p>

<?php } ?>

</div>

<?php require($base_path."foot.php"); $pdo = null; ?>