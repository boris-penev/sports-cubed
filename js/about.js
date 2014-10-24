$().ready(function(){
  var windowHeight = $(window).height();
  $('#backToCube').css('margin-top',(windowHeight/2 - 35).toString()+"px");
})

$(window).resize(function(){
  windowHeight = $(window).height();
  $('#backToCube').css('margin-top',(windowHeight/2 - 35).toString()+"px");
});

$('#backToCube').click(function(){
  window.location.href = ".";
});
