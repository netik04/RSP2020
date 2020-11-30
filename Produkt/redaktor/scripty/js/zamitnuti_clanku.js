$(document).on("submit", "#message_box form.a_deny", function (event) {
  event.preventDefault();

  var formData = {
    'duvod' : $('textarea[name=duvod]').val(),
    'id' : $('input[name=id]').val(),
    'verze' : $('input[name=verze]').val()
  };

  if(formData['duvod'] == "")
    $('#message_box .error').text("*Vyplňtě prosím důvod navrácení!");
  else{
    $.ajax({
      type: "POST",
      url: "scripty/zamitnuti_clanku.php",
      data: formData,
      dataType: "json",
      encode: true
    })
    .done(function (data) {
      if(data == 1){
        $('#message_box').hide();
        $('#message_box').html('');

        $('.info .state')
          .html("Stav<br><span class=\"l2\">Příspěvek zamítnut</span>");

        $('.a_deny')
          .text("Zrušit zamítnutí")
          .attr('class', 'a_undeny');

          $('.a_setR').remove();
          $('.a_return').remove();
          $('.a_release').remove();

        zobrazZpravy();
      }
      else {
        alert("Nepodařilo se zamítnout článek :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
        //location.reload();
      }

    })
    .fail(function() {
      alert("Nepodařilo se zamítnout článek :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      //location.reload();
    })
    .always(function() {
      $('button').blur();
    });
  }
});