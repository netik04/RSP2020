$(document).on("click", "button.a_setR", function () {
  var id = $(this).attr('cl_id');
  var verze = $(this).attr('cl_ver');

  var page = $(this).attr('page');
  var rs = [$(this).attr('cl_r1'), $(this).attr('cl_r2')];

  $.ajax({
    url: "scripty/get_recenzenty.php",
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if (data) {
      if($('#set_recenzenty').length < 1)
        $("#page").prepend("<div id=\"set_recenzenty\" class=\"popbox\"></div>");
      else
        $('#set_recenzenty').show();
   

      rs_left = 0;
      if(rs[0]) rs_left++;
      if(rs[1]) rs_left++;

      var osob_rev = "";
      if(rs_left > 0){
        osob_rev += "<div class=\"note f_item\">Recenzenti vyžadující osobní revizi:<br>";

        $.each(data, function (index, val) {
          if(val['login'] == rs[0] || val['login'] == rs[1]){
            osob_rev += val['jmeno'] + ' ' + val['prijmeni'];
            
            rs_left--;
            if(rs_left == 0) return false;
            else{
              osob_rev += " a ";
            }
          }
        });
        osob_rev += "</div>";
      }

      var htmlStr = ["", ""];

      htmlStr[0] = htmlStr[1] = "<option value=\"\">Nevybráno</option>";

      var i = 0;
      while(i < 2)
      {
        $.each(data, function (index, val) {
          htmlStr[i] +="<option ";
          if(val['login'] == rs[i])
            htmlStr[i] += " selected "
            
          htmlStr[i] += "value=\"" +
            val["login"] +
            "\">" +
            val["jmeno"] +
            " " +
            val["prijmeni"] +
            "</option>";
        });

        i++;
      }

      $('#set_recenzenty').attr("page", page);

      $('#set_recenzenty').html(
        "<form action=\"scripty/prirazeni_recenzentu.php\" method=\"POST\">" +
          "<div class=\"title\">Stanovit recenzenty</div>" +
          osob_rev +
          "<div class=\"f_item\">" +
            "<label for=\"recenzent1\">1. recenzent: </label>" +
            "<select name=\"recenzent1\">" + htmlStr[0] + "</select>" +
          "</div>" +
          "<div class=\"f_item\">" +
            "<label for=\"recenzent2\">2. recenzent: </label>" +
            "<select name=\"recenzent2\">" + htmlStr[1] + "</select>" +
          "</div>" +
          "<div class=\"f_item\">" +
            "<label for=\"uzaverka\">Datum uzávěrky: </label>" +
            "<input name=\"uzaverka\" type=\"date\">" +
          "</div>" +
          "<input name=\"id\" type=\"hidden\" value=\"" + id + "\">" +
          "<input name=\"verze\" type=\"hidden\" value=\"" + verze + "\">" +
          "<div class=\"f_item\">" +
            "<input type=\"submit\" value=\"Přiřadit\">" +
          "</div>" +
          "<span class=\"error\"></span>" +
        "</form>"
      );
    } else {
      alert("Nastala neočekávaná chyba.\nOpakujte pokus prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

  })
  .fail(function() {
    alert("Nastala neočekávaná chyba.\nOpakujte pokus prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {$('button.a_setR').blur();});
});
/*
$(document).on("click", "#set_recenzenty", function(e) 
{
    var container = $("#set_recenzenty form");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
      $('#set_recenzenty').hide();
      $('#set_recenzenty').html('');
    }
});*/
