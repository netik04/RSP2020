$(document).on("click", "#set_recenzenty", function(e) 
{
    var container = $("#set_recenzenty form");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
      $('#set_recenzenty').hide();
      $('#set_recenzenty').html('');
    }
});

$(document).on("submit", "#set_recenzenty form", function (event) {
  var page = $(this).parent().attr("page");
  
  //var formData = $(this).serialize();
  
  // nebo
  var formData = {
    'r1' : $('select[name=recenzent1]').val(),
    'r2' : $('select[name=recenzent2]').val(),
    'uzaverka' : $('input[name=uzaverka]').val(),
    'id' : $('input[name=id]').val(),
    'verze' : $('input[name=verze]').val()
  };

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
        $('.article#'+formData['id']+' .accept').hide();
        $('.article#'+formData['id']+' .state')
          .text('Stav: Probíhá recenzní řízení');
      }
      else if(page === "clanek"){
        $('.control .accept').hide();
        $('.info .state')
          .html("Stav<br><span class=\"l2\">Probíhá recenzní řízení</span>");
      }
    } else {
      alert("Nepodařilo se stanovit recenzenty :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
      location.reload();
    }

  }).fail(function(){
    alert("Nepodařilo se stanovit recenzenty :(\nZkuste to prosím později.\nPokud nebude funkce stále fungovat, kontaktujte administrátora.");
    location.reload();
  });
  event.preventDefault();
});