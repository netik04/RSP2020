$(document).on("submit", "#set_recenzenty form", function (event) {
  event.preventDefault();
  var page = $(this).parent().attr("page");
  var uzaverka = new Date($('input[name=uzaverka]').val());

  var formData = {
    'r1' : $('select[name=recenzent1]').val(),
    'r2' : $('select[name=recenzent2]').val(),
    'uzaverka' : $('input[name=uzaverka]').val(),
    'id' : $('input[name=id]').val(),
    'verze' : $('input[name=verze]').val()
  };

  if(formData['r1'] == "" || formData['r2'] == "")
    $('#set_recenzenty .error').text("*Vyplňtě prosím oba recenzenty!");
  else if(formData['uzaverka'] == "")
    $('#set_recenzenty .error').text("*Vyplňtě prosím datum uzávěrky!");
  else if(formData['r1'] == formData['r2'])
    $('#set_recenzenty .error').text("*Nelze přiřadit jednoho recenzenta dvakrát!");
  else{
    $.ajax({
      type: "POST", // define the type of HTTP verb we want to use (POST for our form)
      url: "scripty/prirazeni_recenzentu.php", // the url where we want to POST
      data: formData, // our data object
      dataType: "json", // what type of data do we expect back from the server
      encode: true
    }).done(function (data) {
      
      if(data == 1){
        $('#set_recenzenty').hide();
        $('#set_recenzenty').html('');

        if(page === "redaktor"){
          $('.article#'+formData['id']+' .a_setR').remove();
          $('.article#'+formData['id']+' .state')
            .html("Stav: Probíhá recenzní řízení" +
              "<br><span class=\"date\">Uzávěrka recenze: " + uzaverka.getDate() + "." +  + uzaverka.getMonth() + "." + uzaverka.getFullYear() + "</span>");
        }
        else if(page === "clanek"){
          $('.a_setR').remove();
          $('.a_deny').remove();
          $('.a_return').remove();
          $('.a_release').remove();

          $('.info .state')
            .html("Stav<br><span class=\"l2\">Probíhá recenzní řízení</span>" +
              "<br><span class=\"l3\">Uzávěrka recenze: " + uzaverka.getDate() + "." +  + (uzaverka.getMonth()+1) + "." + uzaverka.getFullYear() + "</span>");
        }
      } else {
        alert("Nepodařilo se stanovit recenzenty :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
        location.reload();
      }

    }).fail(function(){
      alert("Nepodařilo se stanovit recenzenty :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    });
  }
});