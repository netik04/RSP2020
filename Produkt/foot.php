        <footer>
            &copy; Študáci 2020


            <?php /* RAINBOW ANIMATION BUTTON */?>
            <span id="rainbow_button" onclick="toggleRainbow(<?php echo("'".$base_path."'")?>)">
                rainbow mode
            </span>
            <?php
            if(isset($_SESSION["rainbow"]))
                echo("<script>$('body').toggleClass('rainbow');</script>");
            ?>


        </footer>
    </div>
</body>

</html>
<?php
    ob_end_flush();
?>