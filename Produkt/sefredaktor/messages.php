<?php
    $base_path = "../";
    $head_str = "
    <style> 

    #messageWrap{
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    #messageBox{
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        max-height: 300px;
        overflow-y: auto;
    }
    .message{
        background-color: #777;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 2px;
        margin-right: 10px;
        
        /* flexing stuff */
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .userMessage {
        align-self: flex-end;
        background-color: #999;
    }

    #messageSender {
        align-self: center;
        background-color: black;
        margin-top: 10px;
        width: 65%;
        overflow: hidden;
        margin-top: 10px;

        /* flexing stuff */
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    #messageSender input[type=\"text\"]{
        flex: 2 2 auto;
    }
    #messageSender input[type=\"submit\"]{
        
    }

    .article .button.active{
        display: inline-block;
        color: #fff;
        padding: 5px 10px;
        text-decoration: none;
        background-color: #777;
        border: 1px solid #aaa;
    }

    .article .button.active:hover{
        background-color: #777;
        border: 1px solid #aaa;
    }

    </style>";
    require($base_path."head.php");
    
?>


<div id="content" class="redaktor">
    <?php
        $article_id = 1;
        $article_verze = 1;
    ?>
    <div class="article">
        <div id="messageWrap">
            <div id="messagesMenu">
                <button id="interni" class="button">Redakce</button>
                <button id="autorsky" class="button">Autor</button>
            </div>
            <div id="messageBox">
            </div>
            <form id="messageSender" action="scripty/odeslatZpravu.php">
                    <input type="hidden" name="id" value="<?php echo $article_id?>">
                    <input type="hidden" name="verze" value="<?php echo $article_verze?>">
                    <input type="hidden" id="inter" name="interni" value="1">
                    <input type="text" name="message" id="message">
                    <input type="submit" name="odeslatZpravu" value="Odeslat">
            </form>
            <div id="errorMessage"><?php echo $_SESSION["errorMessage"]; unset($_SESSION["errorMessage"]); ?></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(function() {
            var interni = <?php if(!isset($_SESSION["interni"])) $_SESSION["interni"]=1; echo $_SESSION["interni"]; ?>;
            if(interni == 1){
                $('#interni').click();
            }else{
                $('#autorsky').click();
            }
            
        });
        $(".button").click(function(){
            if($(this).attr("id") == "interni"){
                interni = 1;
                $("#inter").val(1);
                $("#autorsky").removeClass("active");
                $("#interni").addClass("active");
                $.ajax('scripty/zapisSessionInterni.php', {
                    type: 'POST',  // http method
                    data: { 
                        interni: interni
                    },  // data to submit
                    success: function (data) {
                            
                    },
                    error: function (errorMessage) {
                        $('#errorMessage').text('Error' + errorMessage);
                    }
                });
            }else{
                interni = 0;
                $("#inter").val(0);
                $("#interni").removeClass("active");
                $("#autorsky").addClass("active");
                $.ajax('scripty/zapisSessionInterni.php', {
                    type: 'POST',  // http method
                    data: { 
                        interni: interni
                    },  // data to submit
                    success: function (data) {

                    },
                    error: function (errorMessage) {
                        $('#errorMessage').text('Error' + errorMessage);
                    }
                });
            }

            $.ajax('scripty/zobrazZpravy.php', {
                type: 'POST',  // http method
                data: { 
                    article_id: <?php echo $article_id ?>,
                    article_verze: <?php echo $article_verze ?>,
                    interni: interni
                },  // data to submit
                success: function (data) {
                    $('#messageBox').html(data);
                    var objDiv = document.getElementById("messageBox");
                    objDiv.scrollTop = objDiv.scrollHeight;
                },
                error: function (errorMessage) {
                    $('#errorMessage').text('Error' + errorMessage);
                }
            });
        });
    });
</script>

<?php require($base_path."foot.php"); $pdo = null; ?>