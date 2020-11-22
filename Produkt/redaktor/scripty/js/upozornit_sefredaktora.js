$(document).on("click", "button.a_sefR", function () {
  var inData = {
    'id' : $(this).attr('cl_id'),
    'verze' : $(this).attr('cl_ver')
  };

  $.ajax({
    type: "POST",
    url: "scripty/upozornit_sefredaktora.php",
    data: inData,
    dataType: "json",
    encode: true
  })
  .done(function (data) {
    if(data == 1){
      // možnosti: vrátit k úpravě, vydání, zamítnout
      $('.a_sefR').remove();
      alert("Šéfredaktor byl upozorněn na tuto verzi článku.");

    } else {
      alert("Nepodařilo se upozornit šéfredaktora :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

  })
  .fail(function() {
    alert("Nepodařilo se upozornit šéfredaktora :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  });
});