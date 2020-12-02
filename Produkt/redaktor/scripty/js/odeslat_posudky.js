$(document).on("click", "button.a_sendP", function () {
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  $.ajax({
    type: "POST",
    url: "scripty/odeslat_posudky.php",
    data: inData,
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if(data == 1){
      $('.info .state')
        .html("Stav<br><span class=\"l2\">Posudky odeslány autorovi</span>");
      

      // možnosti: vrátit k úpravě, vydání, zamítnout
      $('.a_sendP')
        .text("Vrátit k úpravám")
        .attr('class', 'a_return');

      $('.a_return')
        .after("<button class=\"a_deny\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zamítnout</button>");
      
      $('.a_return')
        .after("<button class=\"a_release\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Přijmout k vydání</button>");

      zobrazZpravy(true);
    } else {
      alert("Nepodařilo se odeslat posudky autorovi :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

  })
  .fail(function() {
    alert("Nepodařilo se odeslat posudky autorovi :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button.a_return').blur();
  });
});