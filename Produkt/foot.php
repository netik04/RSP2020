        <footer>
            &copy; Študáci 2020


            <?php 
                // TADY JE BORDEL KVŮLI RAINBOW BCG
            ?>
            <script>
                function toggleRainbow(){
                    if(!$('body').hasClass('rainbow'))
                        /*alert("Pozor!\nTato volba pozadí mírně zatíží Váš procesor." +
                        "\nDoporučujeme Vám tuto funkci využívat čistě z relaxačních důvodů.");*/
                        alert("Relaxační funkce pro zpříjemnění práce :)"+
                        "\nPozor, lehce zatěžuje váš procesor. Nedoporučuje se slabším počítačům."+
                        "\n\nPokud se Vám tato funkce bude líbit, prosím, nezapomeňte kladně ohodnotit náš projekt <3");
                    $.ajax({
                        type: "POST", 
                        url: "<?php echo($base_path)?>scripty/toggleRainbow.php",
                    }
                    );
                    $('body').toggleClass('rainbow');
                }
            </script>
            <span
                style="
                    float:right;
                    background-image: linear-gradient(45deg, red, green, blue);
                    padding:0 .2rem;
                    border-radius:3px;
                    cursor:pointer;
                    "
                onclick="toggleRainbow()"
                >
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