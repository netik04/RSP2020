function toggleRainbow(path){
    if(!$('body').hasClass('rainbow'))
        alert("Relaxační funkce pro zpříjemnění práce :)"+
        "\n\nPokud se Vám tato funkce bude líbit, prosím, nezapomeňte kladně ohodnotit náš projekt <3");
    $.ajax({
        type: "POST", 
        url: path + "scripty/toggleRainbow.php",
    }
    );
    $('body').toggleClass('rainbow');
}