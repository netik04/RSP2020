$(document).on("click", "button.a_return, button.a_deny", function () {
  var id = $(this).attr('cl_id');
  var verze = $(this).attr('cl_ver');

    if($('#message_box').length < 1)
      $("#page").prepend("<div id=\"message_box\" class=\"popbox\"></div>");
    else
      $('#message_box').show();

    if($(this).attr('class') == "a_return"){
      var action = "scripty/vratit_autorovi.php";
      var title = "Důvod vrácení k úpravě";
    }
    else if($(this).attr('class') == "a_deny"){
      var action = "scripty/zamitnuti_clanku.php";
      var title = "Důvod zamítnutí";
    }
    $('#message_box').html(
      "<form class=\""+$(this).attr('class')+"\" action=\""+action+"\" method=\"POST\">" +
        "<div class=\"title\">"+title+"<div class=\"exit\"></div></div>" +
        "<div class=\"f_item\"><textarea name=\"duvod\" placeholder=\"Důvod&hellip;\"></textarea></div>" +
        "<input name=\"id\" type=\"hidden\" value=\"" + id + "\">" +
        "<input name=\"verze\" type=\"hidden\" value=\"" + verze + "\">" +
        "<div class=\"f_item submit\"><input type=\"submit\" class=\"button2\" value=\"Odeslat\"></div>" +
        "<span class=\"error\"></span>" +
      "</form>"
    );
  /*} else {
    alert("Nastala neočekávaná chyba.\nOpakujte pokus prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  }*/

});