$(document).on("click", "button.a_setR", function () {
  var id = $(this).attr('cl_id');
  var verze = $(this).attr('cl_ver');

  var page = $(this).attr('page');

  $.ajax({
    //type: "POST", // define the type of HTTP verb we want to use (POST for our form)
    url: "scripty/get_recenzenty.php", // the url where we want to POST
    dataType: "json", // what type of data do we expect back from the server
    encode: true
  })
  .done(function (data) {
    if (data) {
      if($('#set_recenzenty').length < 1)
        $("#page").prepend("<div id=\"set_recenzenty\"></div>");
      else
        $('#set_recenzenty').show();

      var htmlStr = "<option value=\"null\"></option>";

      $.each(data, function (index, val) {
        //console.log(val["login"] + ": " + val['jmeno'] + " " + val['prijmeni']);
        htmlStr +=
          "<option value=\"" +
          val["login"] +
          "\">" +
          val["jmeno"] +
          " " +
          val["prijmeni"] +
          "</option>";
      });

      htmlStr += "</select>";

      $('#set_recenzenty').attr("page", page);

      $('#set_recenzenty').html(
        "<form action=\"scripty/prirazeni_recenzentu.php\" method=\"POST\">" +
          "<label for=\"recenzent1\">1. recenzent: </label>" +
          "<select name=\"recenzent1\">" + htmlStr +
          "<br><label for=\"recenzent2\">2. recenzent: </label>" +
          "<select name=\"recenzent2\">" + htmlStr +
          "<br><label for=\"uzaverka\">Datum uzávěrky: </label>" +
          "<input name=\"uzaverka\" type=\"date\">" +
          "<input name=\"id\" type=\"hidden\" value=\"" + id + "\">" +
          "<input name=\"verze\" type=\"hidden\" value=\"" + verze + "\">" +
          "<br><input type=\"submit\" value=\"Přiřadit\">" +
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