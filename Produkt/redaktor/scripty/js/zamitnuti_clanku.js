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
      $('.control .deny').hide();
      $('.control .release').hide();
      $('.info .state')
        .html("Stav<br><span class=\"l2\">Příspěvek zamítnut</span>");
      $('.control .accept').html("<button class=\"a_undeny\" page=\"clanek\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zrušit zamítnutí</button>");
      $('.control .accept').show();
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
    $('button.a_deny').blur();
  });
});