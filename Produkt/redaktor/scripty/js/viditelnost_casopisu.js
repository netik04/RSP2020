$(document).on("click", "button.c_vis", function () {
  var inData = {
    'id' : $(this).attr('c_id')
  };

  var vidState = $(this).attr('c_vis');

  if($(this).attr('c_vis') == 0){
    $.ajax({
      type: "POST", // define the type of HTTP verb we want to use (POST for our form)
      url: "scripty/zobrazit_casopis.php", // the url where we want to POST
      data: inData, // our data object
      dataType: "json", // what type of data do we expect back from the server
      encode: true
    })
    .done(function (data) {
      if(data == 1){
        $('#'+inData['id']+' .c_vis')
          .text("Skrýt");
          
        $('#'+inData['id']+' .visState')
          .text("Zobrazeno");

        $('#'+inData['id']+' .c_vis')
          .attr('c_vis', '1');

      } else {
        alert("Nepodařilo se zobrazit časopis :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
        location.reload();
      }

      //console.log(data);

    })
    .fail(function() {
      alert("Nepodařilo se zobrazit časopis :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    })
    .always(function() {
      $('button.c_vis').blur();
    });
  }
  else{
    $.ajax({
      type: "POST", // define the type of HTTP verb we want to use (POST for our form)
      url: "scripty/skryj_casopis.php", // the url where we want to POST
      data: inData, // our data object
      dataType: "json", // what type of data do we expect back from the server
      encode: true
    })
    .done(function (data) {
      if(data == 1){
        $('#'+inData['id']+' .c_vis')
          .text("Zobraz");
          
        $('#'+inData['id']+' .visState')
          .text("Skryto");

        $('#'+inData['id']+' .c_vis')
          .attr('c_vis', '0');

      } else {
        alert("Nepodařilo se skrýt časopis :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
        location.reload();
      }

      //console.log(data);

    })
    .fail(function() {
      alert("Nepodařilo se skrýt časopis :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    })
    .always(function() {
      $('button.c_vis').blur();
    });
  }
});