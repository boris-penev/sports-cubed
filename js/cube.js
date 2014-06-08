// These remember which buttons on the 3 filters have been clicked and are passed
// to the browser's storage when going to the map
var sportsToBeSubmitted = new Array();
var daysToBeSubmitted = new Array();
var priceToBeSubmitted = "all";

// the xAngle is set to 90 degrees because initially when the cube is spawned
// it has to be rotated on 90 degrees. Therefore initially the user sees
// the bottom side of the cube (the central one is actually the map)
var xAngle = 90, yAngle = 0;

// the distance on which the cube will have to be translated in order to be
// visually "resized"
var depth;

// specifies the current wall so that the others can be made invisible
var currentWall = "bottom";

// the new side of the screen which determines how far the cube will have to
// be translated; the formula according to which the translation happens was
// derived by Yordan and Dimitar Dimitrov (dimy93) in one sunny (or not that
// much actually) afternoon can be seen in the adjust() function
var determiningSide;

// used to reverse the adjust function, applied before another adjustment
var currentDeterminerAxis;

// used to reverse the adjust function, applied before another adjustment
var currentDeterminerDirection;

// the axis on which the cube will be translated in order to be visually
// "resized"
var determinerAxis;

// + or - direction of translation in the axis
var determinerDirection;

// used to manage the problem with the resizing of the default android
// browser when in landscape mode and when the address bar hides
var windowHeight;

// used to lock cube when demonstrating the tutorial
var cubeLocked = false;

// Determine browser
var props = 'transform WebkitTransform MozTransform OTransform msTransform'.split(' ');
var prop;
var el = document.createElement('div');

for (var i = 0, l = props.length; i < l; i++) {
  if (typeof el.style[props[i]] !== "undefined") {
    prop = props[i];
    break;
  }
}

// setting the interval at which activityTimer() is going to be executed
var inactivity = 1000;
var interval = setInterval(function(){activityTimer();}, inactivity);

// this function adjusts the cube (translates it) so that it is resized
// every time the orientation of the screen changes
// by default every side of the cube is 500px and every time the screen size is
// <500px adjust() helps the cube not to go out of the screen. Instead of literally
// resizing everything we just translate the cube in depth ("away" from the user)
// making it look smaller and fitting the screen. The result is having
// responsive website
function adjust(){
  var height = $(window).height();
  var width = $(window).width();

  // aligning the cube equally from the left and the righr of the screen; 500
  // is the side of the cube
  $('#cube').css('margin-top', ((height - 500) / 2) + "px");

  // this is still just the alignment; next is the "resizement"; if we use 620
  // for the alignment the cube won't be in the centre; this is because initially
  // it is 500px and after that the side seems to be like 610px;
  $('#cube').css('margin-left', ((width - 500) / 2) + "px");

  // Determining the side that will say how big the cube will be and how much
  // we will go away from the screen in order to see it as such; the cube is
  // never resized - it all comes from the perspective
  if (height > width){
    determiningSide = width;

    // It's 620 because when one of the sides that is 500px is translated
    // 360px towards us (280 + 80)it becomes as if it is 610px. We add 10px
    // for both up and bottom margins.
    if (determiningSide < 620) {
      $('#cube').css('margin-left', "-" + (250 - determiningSide / 2) + "px");
    }
  }
  else {
    determiningSide = height;
    if (determiningSide < 620){
      $('#cube').css('margin-top', "-" + (250 - determiningSide / 2) + "px");
    }
  }

  // Here is the GOLDEN FORMULA according to which happens the calculation of
  // the distance of translation; we use 610 because in the beginning the
  // side of the cube is 500px but after we translate it 360px towards us on
  // the Y axis in order to get the 3D effect it seems as it is 610px.
  depth = 2000 * (610 / determiningSide - 1);

  if (depth < 0){
    depth = 0;
  }

  // the actual transition
  document.getElementById('cube').style[prop] += "translate" + determinerAxis +
                              "(" + determinerDirection + "" + depth + "px)";
}


$(document).ready(function() {

// check if we are coming from the map and "click" on the filters that the user
// had already selected
// in this way we remember his/her preferences and they don't have to input them
// again at each transition between the map and the cube
  if (sessionStorage.isComingFromMap === "yes"){
    sessionStorage.isComingFromMap = "no";
    var days = sessionStorage.days.split(",");
    if (days.length === 7){
      $("#whole_toggler > a").trigger("click");
    }
    else {
      for (var x in days){
        $("#" + days[x] + "_toggler > a").trigger("click");
      }
    }

    var sports = sessionStorage.sports.split(",");
    for (var x in sports){
      $("#" + sports[x].replace(" ", "") + "_toggler > a").trigger("click");
    }

    var price = sessionStorage.price;
      $('#'+price+'_toggler > a').trigger("click");
  }
  else {
    sessionStorage.sports = null;
    sessionStorage.days = null;
    sessionStorage.price = null;
    $('#all_toggler > a').trigger("click");
  }
  
  // set some default properties and rotate the cube to the Bottom (Intro) side
  $('#bigWrapper').css('width', "100%");
  $('#bigWrapper').css('height', "100%");
  $('#bigWrapper').css('position', "absolute");
  $('#bigWrapper').css('left', "0");

  windowHeight = $(window).height();
  document.getElementById('cube').style[prop] =
                          "rotateX(" + xAngle + "deg) rotateY("+yAngle+"deg)";
  determinerAxis = "Y";
  determinerDirection = "-";
  currentDeterminerAxis = "Y";
  currentDeterminerDirection = "+";
  
  adjust();

})

// using the HTML5 web storage instead of cookies to remember the user's
// preferences when going from the cube to the map and backwards
$('#linkToMap').click(function(){
  sessionStorage.sports = sportsToBeSubmitted;
  sessionStorage.days = daysToBeSubmitted;
  sessionStorage.price = priceToBeSubmitted;
  sessionStorage.isComingFromMap = "yes";
  window.location = "http://testpilot.x10.mx/cubedtouch/map.html";
})

// this is the animation showing how to use the cube [TO BE CHANGED]
$('#how-to').click(function(){
  cubeLocked = true;
  $('#curtain').fadeIn();
  setTimeout(function(){$('#intro-explanation').fadeIn(1000);},2000);
  setTimeout(function(){$('#start-intro').fadeIn(1000);},2000);
  $('#start-intro').click(function(){
    $('#intro-explanation').fadeOut(1000);
    $('#start-intro').fadeOut(1000);
    setTimeout(function(){gesturePerformed("swiperight")},1000);
    setTimeout(function(){$('#time-explanation').fadeIn(1000);},3800);
    $('#next-time').click(function(){
      $('#time-explanation').fadeOut(1000);
      setTimeout(function(){gesturePerformed("swipedown")},1000);
      setTimeout(function(){$('#activities-explanation').fadeIn(1000);},3800);
      $('#next-activities').click(function(){
        $('#activities-explanation').fadeOut(1000);
        setTimeout(function(){gesturePerformed("swipeleft")},1000);
        setTimeout(function(){$('#price-explanation').fadeIn(1000);},3800);
        $('#next-price').click(function(){
          $('#price-explanation').fadeOut(1000);
          setTimeout(function(){gesturePerformed("swiperight")},1000);
          setTimeout(function(){$('#map-explanation').fadeIn(1000);},3800);
          $('#next-final').click(function(){
            $('#map-explanation').fadeOut(1000);
            setTimeout(function(){gesturePerformed("swipeup")},1000);
            setTimeout(function(){$('#curtain').fadeOut();},3800);
            cubeLocked = false;
          })
        })
      })
    })
  })
})

// toggling the yes-no-button for prices
$('.price-togglers > a').click(function(){
  var obj = $(this);
  clickPriceToggler(obj);
})

function clickPriceToggler(obj){
  var btn = obj.parent().text().toLowerCase().replace(" ", "");
  var state = obj.data('clicked');
  if (state === "yes"){
    $("#"+btn+" > a > div").css('background-position','0px -35px');
    $("#"+btn+" > a").data('clicked','yes');
  }
  else {
    $(".price-togglers > a > div").css('background-position','0px 0px');
    $(".price-togglers > a").data('clicked','no');
    $("#"+btn+"_toggler > a > div").css('background-position','0px -35px');
    $("#"+btn+"_toggler > a").data('clicked','yes');
  }
}

// setting what will be saved in the browser storage as price once we go to
// the map
$('.price-togglers').click(function(){
  var state = $(this).find('a').data('clicked');
  var price = $(this).text().toLowerCase().replace(" ", "");
  if (state === "yes"){
    priceToBeSubmitted = price;
  }
  else {
    priceToBeSubmitted = null;
  }
})

// toggling the yes-no-buttons for the sports-togglers
$('.sports-togglers > a').click(function(){
  var obj = $(this);
  clickSportsToggler(obj);
})

function clickSportsToggler(obj){
  var state = obj.data('clicked');
  if (state === "yes"){
    obj.find("div").css('background-position','0px 0px');
    obj.data('clicked', 'no');
  }
  else {
    obj.find("div").css('background-position','0px -35px');
    obj.data('clicked', 'yes');
  }
}

// setting what will be saved in the browser storage as clicked sports-togglers once
// we go to the map
$('.sports-togglers').click(function(){
  var state = $(this).find('a').data('clicked');
  if (state === "yes"){
    var sport = $(this).text().toLowerCase();
    sportsToBeSubmitted.push(sport);
  }
  else {
    var sport = $(this).text().toLowerCase();
    var index = sportsToBeSubmitted.indexOf(sport);
    sportsToBeSubmitted.splice(index, 1);
  }
})

// setting what will be saved in the browser storage as days once we go to
// the map
$('.time-togglers').click(function(){
var state = $(this).find('a').data('clicked');
var day = $(this).text().toLowerCase();
 clickTimeTogglers(state, day);
})

function clickTimeTogglers(state, day){
  if (state === "yes"){
    if (day === "whole week"){
      var length = daysToBeSubmitted.length;
      daysToBeSubmitted.splice(0, length);
      daysToBeSubmitted.push("monday", "tuesday", "wednesday",
                             "thursday", "friday", "saturday", "sunday");
    }
    else {
      daysToBeSubmitted.push(day);
    }
  }
  else {
    if (day === "whole week"){
      daysToBeSubmitted.splice(0, 7);
    }
    else {
      var index = daysToBeSubmitted.indexOf(day);
      daysToBeSubmitted.splice(index, 1);
    }
  }
}

// toggling the yes-no-buttons for the days
$('.time-togglers > a').click(function(){
  var btn = $(this).parent().text();
  var clicked = $(this).data('clicked');
  if (btn === "Whole week"){
    switch (clicked){
    case "no":
      $('.time-togglers > a > div').css('background-position','0px -35px');
      $('.time-togglers > a').data('clicked','yes');
      break;
    case "yes":
      $('.time-togglers > a > div').css('background-position','0px 0px');
      $('.time-togglers > a').data('clicked','no');
      break;
    }
  }
  else {
    switch (clicked){
    case "no":
      $(this).find("div").css('background-position','0px -35px');
      $(this).data('clicked','yes');
      break;
    case "yes":
      $(this).find("div").css('background-position','0px 0px');
      $(this).data('clicked','no');
      $("#whole_toggler").find("div").css('background-position','0px 0px');
      $("#whole_toggler > a").data('clicked','no');
      break;
    }
  }
})

// triggered if the screen dimensions change
$(window).resize(function() {

// reseting the perpective and returning the center of the cube at (0,0,0)
  document.getElementById('cube').style[prop] +=
          "translate" + currentDeterminerAxis +
          "(" + currentDeterminerDirection + "" + depth +"px)";
  adjust();
  $('#bigWrapper').css('width', $(window).width());
  $('#bigWrapper').css('height', $(window).height());
// here we pass the new window height for the next check about the address bar
  windowHeight = $(window).height();

})

//preventing the elastic bounce effect in Safari under iOS
$(document).bind('touchmove', function(e) { e.preventDefault();});

//detecting touch gestures on the screen (body)
Hammer('body').on("swipeup swipedown swipeleft swiperight dragup dragdown dragleft dragright",
  function(event) {
    if (cubeLocked === false){
      event.gesture.stopDetect();
      gesturePerformed(event.type);
    }
  });

// the equavelent of keydownEvent() but for the gestures
function gesturePerformed(type)
{
  if (type == "swipedown" || type == "dragdown") {
    type = "swipedown";
  }
  else if (type == "swipeleft" || type == "dragleft") {
    type = "swipeleft";
  }
  else if (type == "swiperight" || type == "dragright"){
    type = "swiperight";
  }
  else if (type == "swipeup" || type == "dragup"){
    type = "swipeup";
  }
    $('#linkToMap').delay(500).fadeOut(800);
	
    switch (type) {
      case "swiperight": // left
        if ( isFront() ) {
          determinerAxis = "X";
          determinerDirection = "+";
          yAngle += 90;
          currentWall = "left";
          zoomIn();
        }
        else if ( isRight() ) {
          determinerAxis = "Z";
          determinerDirection = "-";
          yAngle += 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isTop() ) {
          determinerAxis = "X";
          determinerDirection = "+";
          xAngle += 90;
          yAngle += 90;
          currentWall = "left";
          zoomIn();
        }
        else if ( isBottom() ) {
          determinerAxis = "X";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 90;
          currentWall = "left";
          zoomIn();
        }
        else if ( isLeft ( ) )
        {
          determinerAxis = "Z";
          determinerDirection = "+";
          yAngle += 90;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBack ( ) )
        {
          determinerAxis = "X";
          determinerDirection = "-";
          yAngle += 90;
          currentWall = "right";
          zoomIn();
        }
        break;

      case "swipedown": // up
        if ( isFront() )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          currentWall = "top";
          zoomIn();
        }
        else if ( isTop() ) {
          determinerAxis = "Z";
          determinerDirection = "+";
          xAngle += 90;
          yAngle += 180;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBottom() )
        {
          determinerAxis = "Z";
          determinerDirection = "-";
          xAngle -= 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isLeft () )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle -= 90;
          currentWall = "top";
          zoomIn();
        }
        else if ( isRight () )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 90;
          currentWall = "top";
          zoomIn();
        }
        else if ( isBack ( ) )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 180;
          currentWall = "top";
          zoomIn();
        }
        break;

      case "swipeleft": // right
        if ( isFront() ) {
          determinerAxis = "X";
          determinerDirection = "-";
          yAngle -= 90;
          currentWall = "right";
          zoomIn();
        }
        else if ( isLeft() ) {
          determinerAxis = "Z";
          determinerDirection = "-";
          yAngle -= 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isTop() ) {
          determinerAxis = "X";
          determinerDirection = "-";
          xAngle += 90;
          yAngle -= 90;
          currentWall = "right";
          zoomIn();
        }
        else if ( isBottom() ) {
          determinerAxis = "X";
          determinerDirection = "-";
          xAngle -= 90;
          yAngle -= 90;
          currentWall = "right";
          zoomIn();
        }
        else if ( isRight () )
        {
          determinerAxis = "Z";
          determinerDirection = "+";
          yAngle -= 90;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBack () )
        {
          determinerAxis = "X";
          determinerDirection = "+";
          yAngle -= 90;
          currentWall = "left";
          zoomIn();
        }
        break;

      case "swipeup": // down
        if ( isFront() ) {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          currentWall = "bottom";
          zoomIn();
        }
        else if ( isTop () )
        {
          determinerAxis = "Z";
          determinerDirection = "-";
          xAngle += 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isLeft () )
        {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          yAngle -= 90;
          currentWall = "bottom";
          zoomIn();
        }
        else if ( isRight () )
        {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          yAngle += 90;
          currentWall = "bottom";
          zoomIn();
        }
        else if ( isBottom() ) {
          determinerAxis = "Z";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 180;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBack ( ) )
        {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          yAngle += 180;
          currentWall = "bottom";
          zoomIn();
        }
        break;
    }
	
	// check the beginning of the document for the variables that follow
	// when we rotate the cube and it is moved inwards to make it fit the screen
	// first we move it outwards
    document.getElementById('cube').style[prop] +=
            "translate" + currentDeterminerAxis  + "(" +
            currentDeterminerDirection + "" + depth + "px)";
			
	// then we rotate it
    document.getElementById('cube').style[prop] =
            "rotateX(" + xAngle + "deg) rotateY(" + yAngle + "deg)";
	
	// then move it inwards by the respective axis
    document.getElementById('cube').style[prop] +=
            "translate" + determinerAxis +
            "(" + determinerDirection + "" + depth + "px)";
			
    currentDeterminerAxis = determinerAxis;
    if (determinerDirection === "+"){
      currentDeterminerDirection = "-";
    }
    else {
      currentDeterminerDirection = "+";
    }
}

// detecting when a key is pressed and passing this key to the keydownEvent 
// function
$('body').keydown( function (evt){
  if (cubeLocked === false){
    keydownEvent(evt);
    }
});

// 65 is left
// 68 is right
// 87 is up
// 83 is down
function keydownEvent( evt ) {
  if ( evt.keyCode == 65 || evt.keyCode == 68 ||
       evt.keyCode == 87 || evt.keyCode == 83 )
  {
    $('#linkToMap').delay(500).fadeOut(800);
	  
    switch (evt.keyCode) {
      case 65: // left
        if ( isFront() ) {
          determinerAxis = "X";
          determinerDirection = "+";
          yAngle += 90;
          currentWall = "left";
          zoomIn();
        }
        else if ( isRight() ) {
          determinerAxis = "Z";
          determinerDirection = "-";
          yAngle += 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isTop() ) {
          determinerAxis = "X";
          determinerDirection = "+";
          xAngle += 90;
          yAngle += 90;
          currentWall = "left";
          zoomIn();
        }
        else if ( isBottom() ) {
          determinerAxis = "X";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 90;
          currentWall = "left";
          zoomIn();
        }
        else if ( isLeft ( ) )
        {
          determinerAxis = "Z";
          determinerDirection = "+";
          yAngle += 90;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBack ( ) )
        {
          determinerAxis = "X";
          determinerDirection = "-";
          yAngle += 90;
          currentWall = "right";
          zoomIn();
        }
        break;

      case 87: // up
        if ( isFront() )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          currentWall = "top";
          zoomIn();
        }
        else if ( isTop() ) {
          determinerAxis = "Z";
          determinerDirection = "+";
          xAngle += 90;
          yAngle += 180;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBottom() )
        {
          determinerAxis = "Z";
          determinerDirection = "-";
          xAngle -= 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isLeft () )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle -= 90;
          currentWall = "top";
          zoomIn();
        }
        else if ( isRight () )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 90;
          currentWall = "top";
          zoomIn();
        }
        else if ( isBack ( ) )
        {
          determinerAxis = "Y";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 180;
          currentWall = "top";
          zoomIn();
        }
        break;

      case 68: // right
        if ( isFront() ) {
          determinerAxis = "X";
          determinerDirection = "-";
          yAngle -= 90;
          currentWall = "right";
          zoomIn();
        }
        else if ( isLeft() ) {
          determinerAxis = "Z";
          determinerDirection = "-";
          yAngle -= 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isTop() ) {
          determinerAxis = "X";
          determinerDirection = "-";
          xAngle += 90;
          yAngle -= 90;
          currentWall = "right";
          zoomIn();
        }
        else if ( isBottom() ) {
          determinerAxis = "X";
          determinerDirection = "-";
          xAngle -= 90;
          yAngle -= 90;
          currentWall = "right";
          zoomIn();
        }
        else if ( isRight () )
        {
		  alert("breakPoint");
          determinerAxis = "Z";
          determinerDirection = "+";
          yAngle -= 90;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBack () )
        {
          determinerAxis = "X";
          determinerDirection = "+";
          yAngle -= 90;
          currentWall = "left";
          zoomIn();
        }
        break;

      case 83: // down
        if ( isFront() ) {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          currentWall = "bottom";
          zoomIn();
        }
        else if ( isTop () )
        {
          determinerAxis = "Z";
          determinerDirection = "-";
          xAngle += 90;
          currentWall = "front";
          zoomIn();
        }
        else if ( isLeft () )
        {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          yAngle -= 90;
          currentWall = "bottom";
          zoomIn();
        }
        else if ( isRight () )
        {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          yAngle += 90;
          currentWall = "bottom";
          zoomIn();
        }
        else if ( isBottom() ) {
          determinerAxis = "Z";
          determinerDirection = "+";
          xAngle -= 90;
          yAngle += 180;
          currentWall = "back";
          zoomIn();
        }
        else if ( isBack ( ) )
        {
          determinerAxis = "Y";
          determinerDirection = "-";
          xAngle += 90;
          yAngle += 180;
          currentWall = "bottom";
          zoomIn();
        }
        break;
    }
	
	// check the beginning of the document for the variables that follow
	// when we rotate the cube and it is moved inwards to make it fit the screen
	// first we move it outwards
	//console.log(prop)
	//console.log(document.getElementById('cube').style[prop]);
    document.getElementById('cube').style[prop] +=
            "translate" + currentDeterminerAxis  + "(" +
            currentDeterminerDirection + "" + depth + "px)";
			
	// then we rotate it
    document.getElementById('cube').style[prop] =
            "rotateX(" + xAngle + "deg) rotateY(" + yAngle + "deg)";
	
	// then move it inwards by the respective axis
    document.getElementById('cube').style[prop] +=
            "translate" + determinerAxis +
            "(" + determinerDirection + "" + depth + "px)";
			
    currentDeterminerAxis = determinerAxis;
    if (determinerDirection === "+"){
      currentDeterminerDirection = "-";
    }
    else {
      currentDeterminerDirection = "+";
    }
  }
}

// methods used to verify which side we are facing (currentWall)
function isBack() {
  return currentWall == "back";
}

function isLeft() {
  return currentWall == "left";
}

function isRight() {
  return currentWall == "right";
}

function isTop() {
  return currentWall == "top";
}

function isBottom() {
  return currentWall == "bottom";
}

function isFront() {
  return currentWall == "front";
}

// methods that return the specified sides/panels
function getLeftPanel() {
  return document.getElementById('left');
}

function getRightPanel() {
  return document.getElementById('right');
}

function getBottomPanel() {
  return document.getElementById('bottom');
}

function getFrontPanel() {
  return document.getElementById('front');
}

function getTopPanel() {
  return  document.getElementById('top');
}

function getBackPanel() {
  return  document.getElementById('back');
}

// executed when the user is facing the Front (Map) side of the cube
function frontpanel(){
  $('#linkToMap').delay(500).fadeIn(800);
}

// checks which side we are facing and zooms it by making all other sides 
// invisible; if the side is the Front one we also show the Map button
function activityTimer( ) {
  clearInterval(interval);
  if ( isBack() )
    document.getElementById('cube').style[prop] += "translateZ(-80px)";
  else
    getBackPanel().style.opacity = 0.0;
  if ( isLeft() )
    document.getElementById('cube').style[prop] += "translateX(-80px)";
  else
    getLeftPanel().style.opacity = 0.0;
  if ( isRight()  )
    document.getElementById('cube').style[prop] += "translateX(80px)";
  else
    getRightPanel().style.opacity = 0.0;
  if ( isFront() ) {
    document.getElementById('cube').style[prop] += "translateZ(80px)";
	frontpanel();
  }
  else
    getFrontPanel().style.opacity = 0.0;
  if ( isTop() )
    document.getElementById('cube').style[prop] += "translateY(-80px)";
  else
    getTopPanel().style.opacity = 0.0;
  if ( isBottom() )
    document.getElementById('cube').style[prop] += "translateY(80px)";
  else
    getBottomPanel().style.opacity = 0.0;
}

// making all the sides visible before deciding which one to hide and which one 
// not, according to which side we are facing (activityTimer)
function showAllPanels() {
  getLeftPanel().style.opacity = 1.0;
  getRightPanel().style.opacity = 1.0;
  getFrontPanel().style.opacity = 1.0;
  getTopPanel().style.opacity = 1.0;
  getBottomPanel().style.opacity = 1.0;
  getBackPanel().style.opacity = 1.0
}

// zooming the side we are currently facing by moving it 80px "towards" the user
// the method is executed each time we move from 1 side to another
function zoomIn ( )
{
  clearInterval(interval);
  showAllPanels();
  interval = setInterval(function(){activityTimer();}, inactivity);
}
