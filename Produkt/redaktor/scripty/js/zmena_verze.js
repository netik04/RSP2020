$(document).on("change", "select.change_ver", function () {
  var id = $('.article').attr('id');
  var verze = $(this).val();//attr('value');

  //$.redirect('clanek.php', {'id': id, 'verze': verze});
  window.location = "clanek?id=" + id + "&verze=" + verze;
});