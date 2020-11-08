$(document).on("click", "button.a_deny", function () {
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  $.ajax({
    type: "POST",
    url: "scripty/zamitnuti_clanku.php",
    data: inData,
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if(data == 1){

      $('.info .state')
        .html("Stav<br><span class=\"l2\">Příspěvek zamítnut</span>");

      $('.a_deny')
        .text("Zrušit zamítnutí")
        .attr('class', 'a_undeny');

        $('.a_setR').remove();
        $('.a_return').remove();
        $('.a_release').remove();

    }
    else {
      alert("Nepodařilo se zamítnout článek :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

  })
  .fail(function() {
    alert("Nepodařilo se zamítnout článek :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button').blur();
  });
});