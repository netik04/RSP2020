$(document).on("click", "button.a_release", function () {
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  $.ajax({
    type: "POST",
    url: "scripty/prijmout_k_vydani.php",
    data: inData,
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if(data == 1){
      $('.info .state')
        .html("Stav<br><span class=\"l2\">Příspěvek je přijat k vydání</span>");

      $('.a_deny').remove();
      $('.a_return').remove();
      $('.a_setR').remove();

      $('.a_release')
        .text("Zrušit vydání")
        .attr('class', 'a_undeny');


      /*$('.control .accept').html("<button class=\"a_undeny\" cl_rel=\"1\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zrušit vydání</button>");
      $('.control .accept').show();

      $('.control .deny').hide();*/

      zobrazZpravy(true);
    } else {
      alert("Nepodařilo se přijmout článek k vydání :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

  })
  .fail(function() {
    alert("Nepodařilo se přijmout článek k vydání :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button').blur();
  });
});