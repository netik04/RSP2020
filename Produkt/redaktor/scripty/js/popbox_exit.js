$(document).on("click", ".popbox", function(e) {
    var container = $(".popbox form");
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
      $('.popbox').hide();
      $('.popbox').html('');
    }
});
