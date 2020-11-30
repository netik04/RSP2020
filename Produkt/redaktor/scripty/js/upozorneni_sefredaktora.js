$(document).on("click", "button.a_sefR", function () {
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  var bp = $(this).attr('bp');
  var state = $(this).attr('cl_s');
  var role = $(this).attr('r');

  $.ajax({
    type: "POST",
    url: bp+"redaktor/scripty/upozorneni_sefredaktora.php",
    data: inData,
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if(data == 1){
      //$('.a_sefR').remove();
      //$('.a_sefR').text("Zrušit ");
      //alert("Šéfredaktor byl upozorněn na tuto verzi článku.");

      if(state == 1){
        if(role == "redaktor"){
            $('.a_sefR').text("Upozornit šéfredaktora");
        } else if(role == "sefredaktor"){
            $('.a_sefR').text("Upozorňovat na tento článek");
        }
        $('.a_sefR').attr('cl_s', '0');
      }
      else{
        if(role == "redaktor"){
            $('.a_sefR').text("Zrušit upozornění šéfredaktora");
        } else if(role == "sefredaktor"){
            $('.a_sefR').text("Zrušit upozornění");
        }
        $('.a_sefR').attr('cl_s', '1');
      }

    } else {
      alert("Nepodařilo se změnit stav upozornění šéfredaktora :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

  })
  .fail(function() {
    alert("Nepodařilo se změnit stav upozornění šéfredaktora :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  })
  .always(function() {
    $('button.a_sefR').blur();
  });
});