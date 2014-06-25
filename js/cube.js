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
// derived by Yordan and Dimitar (dimy93) in one sunny (or not that
// much actually) afternoon and it can be seen in the adjust() function
// Later Boris (bob) reflowed this comment in 6 am and was appalled
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
var cubeNotLocked = 'all';

var tutorialMode = false;

var numberOfSportsPages;

// pointing the page which is currently showed on the sports slider
var currentSportsPage = 1;

// used to determine whether the recursive swipe animation to be executed
var swipeAllowed = true

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
// <500px adjust() helps the cube not to go out of the screen. Instead of
// literally resizing everything we just translate the cube in depth ("away"
// from the user) making it look smaller and fitting the screen. The result is
// having a responsive website
function adjust(){
  var height = $(window).height();
  var width = $(window).width();

  // aligning the cube equally from the left and the righr of the screen;
  // 500 is the side of the cube
  $('#cube').css('margin-top', ((height - 500) / 2) + "px");

  // this is still just the alignment; next is the "resizement"; if we use 620
  // for the alignment the cube won't be in the centre; this is because
  // initially it is 500px and after that the side seems to be like 610px;
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

// check if we are coming from the map and "click" on the filters
// that the user had already selected
// in this way we remember his/her preferences and they don't have to
// input them again at each transition between the map and the cube
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
    sessionStorage.price = 'all';
    $('#all_toggler > div').trigger("click");
  }

  // draw and populate the activities side
  var sportsList = ['Football', 'Basketball', 'Golf', 'Swimming','Cricket','BMX',
				  'Cycling','Badminton','Gym','Skateboard','Gymnastics',
				  'Table tennins','Rugby','Hockey','Tennins','Athletics',
				  'Volleyball','Bowling', '1','2','3','4','5','6','7','8','9',
				  '10','11','12','13','14','15','16','17','18','Football',
				  'Basketball', 'Golf', 'Swimming','Cricket','BMX','Cycling',
				  'Badminton','Gym','Skateboard','Gymnastics','Table tennins',
				  'Rugby','Hockey','Tennins','Athletics','Volleyball','Bowling',
				  'test1', 'test2', 'test3']
  numberOfSportsPages = Math.ceil(sportsList.length / 18);
  drawActivities(sportsList);

  // we have the click handler for the sports togglers here because unlike the
  // price and time togglers, the activities ones are generated dynamically by
  // the function above - drawActivities
  $('.yes-no-button-sports').click(function(){

    var btn = $(this);
    var state = btn.data('clicked');
    if (state === "yes"){
      btn.css('background-position','0px 0px');
      btn.data('clicked', 'no');
      var sport = btn.parent().text().toLowerCase();
      var index = sportsToBeSubmitted.indexOf(sport);
      sportsToBeSubmitted.splice(index, 1);
    }
    else {
      btn.css('background-position','0px -35px');
      btn.data('clicked', 'yes');
      var sport = btn.parent().text().toLowerCase();
      sportsToBeSubmitted.push(sport);
    }

    // if in tutorial mode - display the explanations
    if(tutorialMode == true){
    	 setTimeout(function(){$('#back-explanation').fadeIn(1000);
    	 $('#swipe-back').fadeIn(1000, function(){
    	 animateSwipe('left', 'back')
     });
    }, 500)}

  })

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
  alert("Sports: " + sportsToBeSubmitted + " Days: " + daysToBeSubmitted + " Price: " + priceToBeSubmitted)
  sessionStorage.isComingFromMap = "yes";
  window.location = "http://testpilot.x10.mx/cubedtouch/map.html";
})

// this is the animation showing how to use the cube [TO BE CHANGED]
// TODO Change this
$('#how-to').click(function(){
  tutorialMode = true;
  cubeNotLocked = 'left';
  $('#curtain').fadeIn();
  setTimeout(function(){$('#bottom-explanation').fadeIn(1000);},500);
  setTimeout(function(){$('#swipe-bottom').fadeIn(1000, function(){
	animateSwipe('left', 'bottom')
  });},500);
})

$('#sports-left-navigator').click(function(){
	if(currentSportsPage > 0){
		var currentMargin = parseInt($('#sports-slider').css('margin-left'))
		if(currentMargin < 0){
			currentMargin += 438;
		$('#sports-slider').stop(true, false).animate({
		'marginLeft': currentMargin + 'px'}, 200)
		}
		currentSportsPage -= 1;
	}
})

$('#sports-right-navigator').click(function(){
    if(currentSportsPage < 4){
		var currentMargin = parseInt($('#sports-slider').css('margin-left'))
		if(currentMargin > -2190){
		currentMargin -= 438;
		$('#sports-slider').stop(true, false).animate({
		'marginLeft': currentMargin + 'px'}, 200)
		}
		currentSportsPage += 1;
	}
})

// draws all the choices on the activities side of the cube
// takes an array of sports that is fetched from the DB
// for now the array is static until Borkata works it out.
function drawActivities(sportsList){
	listLength = sportsList.length;
	while(listLength != 0){
		if(listLength > 6){
			drawSportsWrapper(sportsList.slice(0,6))
			sportsList.splice(0,6)
			listLength = sportsList.length;
		}
		else{
			drawSportsWrapper(sportsList.slice(0, listLength))
			sportsList.splice(0,listLength)
			listLength = sportsList.length;
		}
	}
}

function drawSportsWrapper(wrapperList){
	n = wrapperList.length
	var sportsWrapperSource = '<section class="sports-wrapper">'
	for(var i = 0; i < n; i++){
		sportsWrapperSource += drawSportsLine(wrapperList[i]);
	}
	sportsWrapperSource += '</section>';

	var sportsSliderSource = $('#sports-slider').html()
	$('#sports-slider').html(sportsSliderSource + sportsWrapperSource)
}

// draws a single line for a sport with the label and the button
function drawSportsLine(element){
	elementID = element.toLowerCase();
	elementID = elementID.replace(' ', '');
	return '<div class="sports-togglers" id="' + elementID + '_toggler">' + element +
		   '<br/><div class="yes-no-button-sports" data-clicked="no"></div></div>'
}


// the repeating swiping animation for the tutorial
function animateSwipe(direction, facingWall){

if(swipeAllowed){
	var swipeimage = $('#swipe-'+facingWall);

	switch (direction){
	case 'left':
		swipeimage.animate({
			marginLeft: '10px'
			}, 1500, function(){
			setTimeout(function(){swipeimage.css('margin-left','400px');animateSwipe(direction, facingWall);}, 1000)
			}
		)
		break;
	case 'right':
		swipeimage.animate({
			marginRight: '10px'
			}, 1500, function(){
			setTimeout(function(){swipeimage.css('margin-left','400px');animateSwipe(direction, facingWall);}, 1000)
			}
		)
		break;
	case 'down':
		swipeimage.animate({
			marginBottom: '10px'
			}, 1500, function(){
			setTimeout(function(){swipeimage.css('margin-left','400px');animateSwipe(direction, facingWall);}, 1000)
			}
		)
		break;
	case 'up':
		swipeimage.animate({
			marginTop: '10px'
			}, 1500, function(){
			setTimeout(function(){swipeimage.css('margin-left','400px');animateSwipe(direction, facingWall);}, 1000)
			}
		)
		break;
	}
}
}

// setting what will be saved in the browser storage as price once we go to
// the map
$('.yes-no-button-price').click(function(){

var btn = $(this)

var state = btn.data('clicked');
  if (state == "no")
  {
	$(".yes-no-button-price").css('background-position','0px 0px');
    $(".yes-no-button-price").data('clicked','no');
    btn.css('background-position','0px -35px');
    btn.data('clicked','yes');

	var price = btn.parent().text().toLowerCase().replace(" ", "");
    priceToBeSubmitted = price;
  }


  // if in tutorial mode - display the explanations
  if(tutorialMode == true){
	  setTimeout(function(){$('#left-explanation').fadeIn(1000);
	  $('#swipe-left').fadeIn(1000, function(){
	  animateSwipe('left', 'left')
    });
  }, 500)}

})

// setting what will be saved in the browser storage as days once we go to
// the map
$('.yes-no-button-time').click(function(){

  var btn = $(this);
  var btnLabel = btn.parent().text()
  var clicked = btn.data('clicked');
  if (btnLabel == "Whole week"){
    switch (clicked){
    case "no":
      $('.yes-no-button-time').css('background-position','0px -35px');
      $('.yes-no-button-time').data('clicked','yes');

	  var length = daysToBeSubmitted.length;
      daysToBeSubmitted.splice(0, length);
      daysToBeSubmitted.push("monday", "tuesday", "wednesday",
                             "thursday", "friday", "saturday", "sunday");
      break;
    case "yes":
      $('.yes-no-button-time').css('background-position','0px 0px');
      $('.yes-no-button-time').data('clicked','no');

      daysToBeSubmitted.splice(0, 7);
      break;
    }
  }
  else {
    switch (clicked){
    case "no":
      btn.css('background-position','0px -35px');
      btn.data('clicked','yes');

	  var day = btn.parent().text().toLowerCase();
	  daysToBeSubmitted.push(day);
      break;
    case "yes":
      btn.css('background-position','0px 0px');
      btn.data('clicked','no');
      $("#whole_toggler").find("div").css('background-position','0px 0px');
      $("#whole_toggler > div").data('clicked','no');

	  var day = btn.parent().text().toLowerCase();
      var index = daysToBeSubmitted.indexOf(day);
      daysToBeSubmitted.splice(index, 1);
      break;
    }
  }

  // if in tutorial mode - display the explanations
  if(tutorialMode == true){
	  setTimeout(function(){$('#right-explanation').fadeIn(1000);
	  $('#swipe-right').fadeIn(1000, function(){
	  animateSwipe('left', 'right')
    });
  }, 500)}
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

// the end point of the tutorial
$('#front-explanation').click(function(){ tutorialMode = false;
										  cubeNotLocked = 'all';
										  $(this).fadeOut(1000, function(){
												$('#linkToMap').fadeIn(500) });
										  $('#curtain').fadeOut(1000)})

//preventing the elastic bounce effect in Safari under iOS
$(document).bind('touchmove', function(e) { e.preventDefault();});

// prevents images from being draggable
$('img').on('dragstart', function(event) { event.preventDefault(); });

// detecting when a key is pressed and passing this key to the keydownEvent
// function
$('body').keydown( function (evt){

    var type;
    switch (evt.keyCode)
    {
      case 39:  // right arrow
      case 68:  // d
      case 102: // numpad 6
        type = 'left';
        break;
      case 37:  // left arrow
      case 65:  // a
      case 100: // numpad 4
        type = 'right';
        break;
      case 40:  // down arrow
      case 83:  // s
      case 98:  // numpad 2
        type = 'up';
        break;
      case 38:  // up arrow
      case 87:  // w
      case 104: // numpad 8
        type = 'down';
        break;
    }

    if (cubeNotLocked == 'all' ||
        (cubeNotLocked == 'left'  && type == 'left') ||
        (cubeNotLocked == 'right' && type == 'right') ||
        (cubeNotLocked == 'up'    && type == 'up') ||
        (cubeNotLocked == 'down'  && type == 'down')){
      gesturePerformed(type);
    }
});

//detecting touch gestures on the screen (body)
Hammer('body').on("swipeup swipedown swipeleft swiperight dragup dragdown dragleft dragright",
  function(event) {
	var type
	if (event.type == "swipedown" || event.type == "dragdown") {
		type = "down";
	}
	else if (event.type == "swipeleft" || event.type == "dragleft") {
		type = "left";
	}
	else if (event.type == "swiperight" || event.type == "dragright"){
		type = "right";
	}
	else if (event.type == "swipeup" || event.type == "dragup"){
		type = "up";
	}
    if (cubeNotLocked == 'all' || (cubeNotLocked == 'left' && type == 'left') ||
							   (cubeNotLocked == 'right' && type == 'right') ||
							   (cubeNotLocked == 'up' && type == 'up') ||
							   (cubeNotLocked == 'down' && type == 'down')){
      event.gesture.stopDetect();
      gesturePerformed(type);
    }
  });

// the function that manipulates the cube
function gesturePerformed(type)
{
    // hides the red map button
    $('#linkToMap').delay(500).fadeOut(800);

    switch (type) {
      case "right": // left
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

      case "down": // up
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

      case "left": // right
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

      case "up": // down
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

	// if in tutorial mode hide the explanations that the user leaves behind
	if(tutorialMode == true){
		switch (currentWall){
			case 'right':
			$('#swipe-bottom').fadeOut(1500)
			$('#bottom-explanation').fadeOut(1500)
			break;
			case 'back':
			$('#swipe-right').fadeOut(1500)
			$('#right-explanation').fadeOut(1500)
			break;
			case 'left':
			$('#swipe-back').fadeOut(1500)
			$('#back-explanation').fadeOut(1500)
			break;
			case 'front':
			$('#swipe-left').fadeOut(1500)
			$('#left-explanation').fadeOut(1500, function(){
											$('#front-explanation').fadeIn(1000);
											cubeNotLocked = '';
											})
			break;
		}
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
  if(tutorialMode == false){
    $('#linkToMap').delay(500).fadeIn(800);
  }
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
