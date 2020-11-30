$(document).on("click", "button.c_export", function () {
  var inData = {
    'id' : $(this).attr('c_id')
  };

  $.ajax({
    type: "POST",
    url: "scripty/export_casopisu.php",
    data: inData,
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if(data != 0){
      window.open("../" + data.cesta);

      if(data.error.length > 0){
        var str_cesty = "";
        data.error.forEach(cesta => {
          str_cesty += "\n"+cesta;
        }); 
        alert("Při exportování časopisu došlo k chybě :(\nJeden nebo více článků v uložišti chybí!\nKontaktujte administrátora.\nChybné cesty k článkům:"+str_cesty);
      }

    } else {
      alert("Nepodařilo se exportovat časopis :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

    //console.log(data);

  })
  .fail(function() {
    alert("Nepodařilo se exportovat časopis :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button').blur();
  });
});