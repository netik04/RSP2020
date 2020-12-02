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
        
        $('.a_undeny')
          .text("Stanovit recenzenty")
          .attr({class: 'a_setR', page: 'clanek'});

        $('.a_setR')
          .after("<button class=\"a_deny\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zamítnout</button>");

        if(inData['verze'] > 1)
          $('.a_setR')
            .after("<button class=\"a_release\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Přijmout k vydání</button>");
        
        $('.a_setR')
          .after("<button class=\"a_return\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Vrátit k úpravám</button>");
  
        
        /*$('.control .accept')
        .html("<button class=\"a_setR\" page=\"clanek\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Stanovit recenzenty</button>");
        */
      }
      else if(data == 2){
        $('.info .state').html("Stav<br><span class=\"l2\">Probíhá recenzní řízení</span>");
        $('.a_undeny').remove();

        //$('.control .accept').hide();
      }
      else if(data == 3){
        $('.info .state').html("Stav<br><span class=\"l2\">1. posudek doručen redakci</span>");
        $('.a_undeny').remove();
        
        //$('.control .accept').hide();
      }
      else if(data == 4){
        $('.info .state').html("Stav<br><span class=\"l2\">Posudky odeslány autorovi</span>");
        
        $('.a_undeny')
          .text("Vrátit k úpravám")
          .attr({class: 'a_return'});

        $('.a_return')
          .after("<button class=\"a_deny\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zamítnout</button>");

        $('.a_return')
          .after("<button class=\"a_release\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Přijmout k vydání</button>");
        
      }
      else if(data == 5){
        $('.info .state').html("Stav<br><span class=\"l2\">Probíhá úprava textu autorem</span>");
        
        $('.a_undeny')
          .text("Zamítnout")
          .attr('class', 'a_deny');
      }

      zobrazZpravy(true);
    } else {
      alert("Nepodařilo se zrušit zamítnutí článku :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

    //console.log(data);

  })
  .fail(function() {
    alert("Nepodařilo se zrušit zamítnutí článku :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button').blur();
  });
});