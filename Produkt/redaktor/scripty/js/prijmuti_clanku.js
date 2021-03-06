$(document).on("click", "button.a_accept", function () {
//$("form.accept").submit(function (event) {
  //var inData = $(this).serialize();
  
  // nebo
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  var page = $(this).attr('page');

  //console.log(inData);

  // print assoc pole
/*
  $.each(inData, function(index, val) {
    console.log(index + ':' + val);
  });*/

  $.ajax({
    type: "POST", // define the type of HTTP verb we want to use (POST for our form)
    url: "scripty/prijmuti_clanku.php", // the url where we want to POST
    data: inData, // our data object
    dataType: "json", // what type of data do we expect back from the server
    encode: true
  })
  .done(function (data) {
    if(data == 1){
      if(page === "redaktor"){
        $('.article#'+inData['id']+' button.a_accept')
          .text("Stanovit recenzenty")
          .attr('class', 'a_setR');
          
          //.html("<button class=\"a_setR\" page=\"redaktor\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Stanovit recenzenty</button>");
        $('.article#'+inData['id']+' .state')
          .text('Stav: Čeká na stanovení recenzentů');
      }
      else if(page === "clanek"){
        $('.a_accept')
          .text("Stanovit recenzenty")
          .attr('class', 'a_setR');
        /*$('.control .accept')
          .html("<button class=\"a_setR\" page=\"clanek\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Stanovit recenzenty</button>");*/
        $('.info .state')
          .html("Stav<br><span class=\"l2\">Čeká na stanovení recenzentů</span>");

        $('.a_setR').after("<button class=\"a_deny\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zamítnout</button>");
          /*$('.control .deny').html("<button class=\"a_deny\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Zamítnout</button>");
        $('.control .deny').show();*/

        if(inData['verze'] > 1)
          $('.a_setR')
            .after("<button class=\"a_release\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Přijmout k vydání</button>");
      
          $('.a_setR')
            .after("<button class=\"a_return\" cl_id=\"" + inData['id'] +"\" cl_ver=\"" + inData['verze'] + "\">Vrátit k úpravám</button>");
  
      }

    } else {
      alert("Nepodařilo se přijmout článek :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

    //console.log(data);

  })
  .fail(function() {
    alert("Nepodařilo se přijmout článek :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button.a_setR').blur();
  });
});