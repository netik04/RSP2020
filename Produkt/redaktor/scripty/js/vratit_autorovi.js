$(document).on("submit", "#message_box form.a_return", function (event) {
  event.preventDefault();

  var formData = {
    'duvod' : $('textarea[name=duvod]').val(),
    'id' : $('input[name=id]').val(),
    'verze' : $('input[name=verze]').val()
  };

  if(formData['duvod'] == "")
    $('#message_box .error').html("<br>*Vyplňtě prosím důvod navrácení!");
  else{
    $.ajax({
      type: "POST",
      url: "scripty/vratit_autorovi.php",
      data: formData,
      dataType: "json",
      encode: true
    }).done(function (data) {
      
      if(data == 1){
        $('#message_box').hide();
        $('#message_box').html('');

        $('.a_setR').remove();
        $('.a_return').remove();
        $('.a_release').remove();
        //$('.a_deny').remove();

        $('.info .state')
          .html("Stav<br><span class=\"l2\">Probíhá úprava textu autorem</span>");


        zobrazZpravy(true);
      } else {
        alert("Nepodařilo se vrátit článek k úpravám :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
        location.reload();
      }

    }).fail(function(){
      alert("Nepodařilo se vrátit článek k úpravámy :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    });/*.always(function(){
      //location.reload();
    })*/;
  }
});