$(document).on("click", ".popbox", function(e) {
    var container = $(".popbox form");
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
      $('.popbox').hide();
      $('.popbox').html('');
    }
});


$(document).on("click", "button.a_return", function () {
  var id = $(this).attr('cl_id');
  var verze = $(this).attr('cl_ver');

    if($('#message_box').length < 1)
      $("#page").prepend("<div id=\"message_box\" class=\"popbox\"></div>");
    else
      $('#message_box').show();

    $('#message_box').html(
      "<form action=\"scripty/vratit_autorovi.php\" method=\"POST\">" +
        "<div class=\"title\">Důvod vrácení k úpravě</div>" +
        "<textarea name=\"duvod\" placeholder=\"Důvod&hellip;\"></textarea>" +
        "<input name=\"id\" type=\"hidden\" value=\"" + id + "\">" +
        "<input name=\"verze\" type=\"hidden\" value=\"" + verze + "\">" +
        "<br><input type=\"submit\" class=\"button\" value=\"Odeslat\">" +
        "<br><br><span class=\"error\"></span>" +
      "</form>"
    );
  /*} else {
    alert("Nastala neočekávaná chyba.\nOpakujte pokus prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  }*/

});