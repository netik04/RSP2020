$(document).on("click", ".release button", function () {
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  $.ajax({
    type: "POST", // define the type of HTTP verb we want to use (POST for our form)
    url: "scripty/prijmout_k_vydani.php", // the url where we want to POST
    data: inData, // our data object
    dataType: "json", // what type of data do we expect back from the server
    encode: true
  })
  .done(function (data) {
    if(data == 1){

      $('.control .release').hide();
      $('.info .state')
        .html("Stav<br><span class=\"l2\">Příspěvek je přijat k vydání</span>");

      $('.control .accept').html("<button class=\"a_undeny\" cl_rel=\"1\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zrušit vydání</button>");
      $('.control .accept').show();

      $('.control .deny').hide();
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
    $('.release button').blur();
  });
});