$(document).on("click", "button.a_undeny", function () {
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  $.ajax({
    type: "POST",
    url: "scripty/obnoveni_clanku.php",
    data: inData,
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if(data > 0){
      if(data == 1){
        $('.info .state').html("Stav<br><span class=\"l2\">Čeká na stanovení recenzentů</span>");
        $('.control .accept')
        .html("<button class=\"a_setR\" page=\"clanek\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Stanovit recenzenty</button>");
      }
      else if(data == 2){
        $('.info .state').html("Stav<br><span class=\"l2\">Probíhá recenzní řízení</span>");
        $('.control .accept').hide();
      }
      else if(data == 3){
        $('.info .state').html("Stav<br><span class=\"l2\">1. posudek doručen redakci</span>");
        $('.control .accept').hide();
      }
      else if(data == 4){
        $('.info .state').html("Stav<br><span class=\"l2\">2. posudek doručen redakci</span>");
        $('.control .accept').hide();
        $('.control .release')
          .html("<button cl_id=\"" + inData['id'] + "\" cl_ver=\"" + inData['verze'] + "\">Přijmout k vydání</button>");
        $('.control .release').show();
      }

      $('.control .deny').html("<button class=\"a_deny\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zamítnout</button>");
      $('.control .deny').show();

    } else {
      alert("Nepodařilo se zrušit zamítnutí článku :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

    //console.log(data);

  })
  .fail(function() {
    alert("Nepodařilo se zrušit zamítnutí článeku :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button.a_undeny').blur();
  });
});