<?php
$base_path = "../../";

if (require($base_path . "db.php")) {
    try {
        $stmt = $pdo->query("SELECT login, jmeno, prijmeni FROM uzivatel WHERE role = \"recenzent\"");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo(json_encode($result));
    } catch (PDOException $e) {
        echo(0);
    }

    $pdo = null;
    //die();

}
?>

<?php /*
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {

        var data = JSON.parse($('#test').html());
        console.log(data);

        var htmlStr = "\
          <select name=\"recenzent1\">\
        ";
        $.each(data, function(index, val) {
            console.log(val['jmeno']);
        })

    });
</script>
*/ ?>