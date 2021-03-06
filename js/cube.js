// These remember which buttons on the 3 filters have been clicked and are
// passed to the browser's storage when going to the map
var sportsToBeSubmitted = new Array();
var daysToBeSubmitted = new Array();
var priceToBeSubmitted;

// the xAngle is set to 90 degrees because initially when the cube is spawned
// it has to be rotated on 90 degrees. Therefore initially the user sees
// the bottom side of the cube (the central one is actually the map)
var xAngle = 90, yAngle = 0;

// the distance on which the cube will have to be translated in order to be
// visually "resized"
var depth;

// flag that determines in which direction the cube should go - inwards for
// zoom in or backwards for zoom out
var inwards = false;
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

// used to "recognize" the browser
var version

// + or - direction of translation in the axis
var determinerDirection;

// used to manage the problem with the resizing of the default android
// browser when in landscape mode and when the address bar hides
var windowHeight;

// determines whether the green slide buttons on Activities are allowed to be
// pressed
var allowedToPress = true;

// used to lock cube when demonstrating the tutorial
var cubeNotLocked = 'all';

// determines whether the user is in tutorial mode
var tutorialMode = false;

// var isComingFromMap = "no"

// does not allow for the help button to be pressed multiple times
var helpPressed = false;

// the number of pages in the activities slider
var numberOfSportsPages;

// pointing the page which is currently showed on the sports slider
var currentSportsPage = 1;

// used to determine whether the recursive swipe animation to be executed
var swipeAllowed = true

var cookieArray
var sportsList = new Array()

// Determine browser
var props =
    'transform WebkitTransform MozTransform OTransform msTransform'.split(' ');
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
var interval = setInterval(function() {
  activityTimer();
}, inactivity);

// this function adjusts the cube (translates it) so that it is resized every
// time the orientation of the screen changes by default every side of the cube
// is 500px and every time the screen size is <500px adjust() helps the cube not
// to go out of the screen. Instead of literally resizing everything we just
// translate the cube in depth ("away" from the user) making it look smaller and
// fitting the screen. The result is having a responsive website
function adjust() {
  var height = $(window).height();
  var width = $(window).width();

  // putting the help button on the right of the screen
  $('#help').css('margin-left', (width - 60) + "px");
  if (width > height) {
    $('#help').css('margin-top', (height / 2 - 20) + "px");
  } else {
    $('#help').css('margin-top', "10px");
  }

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
  if (height > width) {
    determiningSide = width;

    // It's 620 because when one of the sides that is 500px is translated
    // 360px towards us (280 + 80)it becomes as if it is 610px. We add 10px
    // for both up and bottom margins.
    if (determiningSide < 610) {
      $('#cube').css('margin-left', "-" + (250 - determiningSide / 2) + "px");
    }
  } else {
    determiningSide = height;
    if (determiningSide < 610) {
      $('#cube').css('margin-top', "-" + (250 - determiningSide / 2) + "px");
    }
  }

  // alert('Determinig Side: '+determiningSide);

  // Here is the GOLDEN FORMULA according to which happens the calculation of
  // the distance of translation; we use 630 because in the beginning the
  // side of the cube is 500px but after we translate it 360px towards us on
  // the Y axis in order to get the 3D effect it seems as it is 610px. We add
  // 20 extra px for the up and bottom margins of 10px each

  if ((determiningSide < 700 && inwards)
      || (determiningSide > 700 && inwards == false)) {
    if (determinerDirection == '+') {
      determinerDirection = '-';
      currentDeterminerDirection = '+';
    } else {
      determinerDirection = '+';
      currentDeterminerDirection = '-';
    }
  }

  // balances the the cube from coming too inwards the user
  var inwardsOffset = 0;
  inwards = false;
  // the default on my laptop is 643
  if (determiningSide > 700) {
    // 100 works fine on chrome and mozilla but because of the viewport problem
    // in the android default browser and safari mobile we differentiate it in 2
    // cases: when the user has android default or mobile safari we do not zoom
    // the cube too much if the screen is bigger, hence the 2 values - 100 and
    // 250
    //
    // 100 comes from the zoomIn func - 80px inwards + 20px margin
    if (version == 'chrome' || version == 'firefox' || version == 'iphone') {
      inwardsOffset = 100;
    } else if (version == 'ipad') {
      inwardsOffset = determiningSide - 630;
    } else {
      inwardsOffset = 250;
    }
    inwards = true;
  }
  depth = 1800 * (650 / (determiningSide - inwardsOffset) - 1);

  // alert(depth);

  if (depth < 0) {
    depth = Math.abs(depth);
  }

  // the actual transition
  document.getElementById('cube').style[prop] +=
      "translate" + determinerAxis + "(" + determinerDirection + "" + depth
          + "px)";
}

function browserRec(userAgent) {
  if (userAgent.indexOf('chrome') > -1)
    version = "chrome"
  else if (userAgent.indexOf('firefox') > -1)
    version = "firefox"
  else if (userAgent.indexOf('android') > -1
      || userAgent.indexOf('mobile safari') > -1)
    version = "default android"
  else if (userAgent.indexOf('iphone') > -1)
    version = "iphone";
  else if (userAgent.indexOf('ipad') > -1)
    version = "ipad"
  else if (userAgent.indexOf('chrome') == -1
      && userAgent.indexOf('safari') > -1)
    version = "safari"
}

$(document).ready(
    function() {
      sportsToBeSubmitted = new Array();
      cookieArray = []
      initialCookieArray = document.cookie.split('; ').sort();
      for ( var x in initialCookieArray) {
        if (initialCookieArray[x].indexOf("sports") != -1
            || initialCookieArray[x].indexOf("days") != -1
            || initialCookieArray[x].indexOf("price") != -1
            || initialCookieArray[x].indexOf("isComingFromMap") != -1
            || initialCookieArray[x].indexOf("tutorialModeOn") != -1) {
          cookieArray.push(initialCookieArray[x])
        }
      }
      cookieArray.sort();

      if (cookieArray.length == 5) {
        tutorialMode = cookieArray[4].split('=')[1];
        isComingFromMap = cookieArray[1].split('=')[1];
      } else {
        document.cookie = "isComingFromMap=no"
        isComingFromMap = "no"
      }

      // set some default properties and rotate the cube to the Bottom (Intro)
      // side

      windowHeight = $(window).height();

      $('#bigWrapper').css('width', "100%");
      $('#bigWrapper').css('height', windowHeight);
      $('#bigWrapper').css('position', "absolute");
      $('#bigWrapper').css('left', "0");

      // alert("Width: "+$('#bigWrapper').css('width')+", Height: "+
      // $('#bigWrapper').css('height'))

      document.getElementById('cube').style[prop] =
          "rotateX(" + xAngle + "deg) rotateY(" + yAngle + "deg)";
      determinerAxis = "Y";
      determinerDirection = "-";
      currentDeterminerAxis = "Y";
      currentDeterminerDirection = "+";

      // tries to differentiate between chrome, firefox and default
      // android/safari browsers to determine how much closer to bring the cube
      // inwards in case the screen is too big. If it is the default android
      // browser and safari the cube goes out of the viewport when it comes too
      // close, unlike chrome and firefox

      browserRec(navigator.userAgent.toLowerCase());

      adjust();

      // draw and populate the activities side
      request = new XMLHttpRequest();
      request.open("GET", "query-sports.php", true);
      request.send(null);

      request.onreadystatechange =
          function() {
            if (request.readyState == 4 && request.status == 200) {
              sportsList = JSON.parse(request.responseText);
              console.log(sportsList)

              for (var i = 0, len = sportsList.length; i < len; ++i) {
                if (sportsList[i] === 'bmx') {
                  sportsList[i] = 'BMX';
                } else if (sportsList[i] === 'football usa') {
                  sportsList[i] = 'Football USA';
                } else {
                  sportsList[i] = capitaliseFirstLetter(sportsList[i]);
                }
              }
              numberOfSportsPages = Math.ceil(sportsList.length / 12);
              drawActivities(sportsList);

              // we have the click handler for the sports togglers here because
              // unlike the price and time togglers, the activities ones are
              // generated dynamically by the function above - drawActivities
              $('.yes-no-button-sports').click(function() {
                var btn = $(this);
                var state = btn.data('clicked');
                if (state == "yes") {
                  btn.css('background-position', '0px 0px');
                  btn.data('clicked', 'no');
                  var sport = btn.parent().text().toLowerCase();
                  var index = sportsToBeSubmitted.indexOf(sport);
                  sportsToBeSubmitted.splice(index, 1);
                } else {
                  btn.css('background-position', '0px -35px');
                  btn.data('clicked', 'yes');
                  var sport = btn.parent().text().toLowerCase();
                  if (sportsToBeSubmitted.indexOf(sport) == -1) {
                    sportsToBeSubmitted.push(sport);
                  }
                }
                // if in tutorial mode - display the explanations
                if (tutorialMode == true) {
                  cubeNotLocked = 'left';
                  setTimeout(function() {
                    $('#back-explanation').fadeIn(1000);
                    $('#swipe-back').fadeIn(1000, function() {
                      animateSwipe('back')
                    });
                  }, 500)
                }
              })

              // finishes the tutorial if in tutorial mode
              if (tutorialMode == 'true') {
                $('#help').attr('src', 'img/help-dis.png').css('cursor',
                    'default')
                helpPressed = true;

                $('#curtain').fadeIn(1000);
                setTimeout(function() {
                  $('#front-explanation').fadeIn(1000)
                }, 2000);
                tutorialMode = 'false';
                cubeNotLocked = '';
              }

              // check if we are coming from the map and "click" on the filters
              // that the user had already selected in this way we remember
              // his/her preferences and they don't have to input them again at
              // each transition between the map and the cube

              if (isComingFromMap == "yes") {
                document.cookie = "isComingFromMap=no";
                isComingFromMap = "no";

                sportsToBeSubmitted = cookieArray[3].split('=')[1].split(',');
                daysToBeSubmitted = cookieArray[0].split('=')[1].split(',');
                priceArray = cookieArray[2].split('=')[1].split(',');

                // var days = sessionStorage.days.split(",");
                if (daysToBeSubmitted.length === 7) {
                  $("#whole_toggler > div").trigger("click");
                } else {
                  for ( var x in daysToBeSubmitted) {
                    $("#" + daysToBeSubmitted[x] + "_toggler > div").trigger(
                        "click");
                  }
                }

                // var sports = sessionStorage.sports.split(",");
                for ( var x in sportsToBeSubmitted) {
                  $(
                      "#" + sportsToBeSubmitted[x].replace(" ", "")
                          + "_toggler > div").trigger("click");
                }

                var button = priceArray[0]
                var value = priceArray[1]
                priceToBeSubmitted = button + "," + value
                value = value.replace(' ', ' £');
                $('#' + button).trigger('click');
                $('#' + button + '-select').val(value)
                $('#' + button + '-select').trigger('change')
              } else {
                deleteCookies();
                cookieArray = []
                priceToBeSubmitted = "membership,Free"
              }

              // console.log("In the end of document ready cookieArray is : " +
              // cookieArray )
              // console.log("In the end of document ready priceToBeSubmitted is
              // : " + priceToBeSubmitted )
            }
          }
    })

function capitaliseFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

// using cookies instead of HTML5 web storage to remember the user's
// preferences when going from the cube to the map and backwards
$('#linkToMap').click(function() {
  if (sportsToBeSubmitted[0] == '')
    sportsToBeSubmitted.splice(0, 1)
  document.cookie = "sports=" + sportsToBeSubmitted;
  document.cookie = "days=" + daysToBeSubmitted;
  priceToBeSubmitted = priceToBeSubmitted.replace('£', '')
  document.cookie = "price=" + priceToBeSubmitted;
  document.cookie = "tutorialModeOn=" + tutorialMode;
  document.cookie = "isComingFromMap=yes";

  // alert(document.cookie)
  // alert("Sports: " + sportsToBeSubmitted + " Days: " + daysToBeSubmitted + "
  // Price: " + priceToBeSubmitted)

  window.location = "map.html";
})

$('#top').click(function() {
  window.open('http://www.edinburgh.gov.uk/', '_blank');
})

function deleteCookies() {
  document.cookie = "sports=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
  document.cookie = "days=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
  document.cookie = "price=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
  document.cookie = "tutorialModeOn=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
  document.cookie = "isComingFromMap=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
}

$(document).on("click", "#how-to", function() {

  cubeNotLocked = "all";
  $('#help-window').remove();
  $('#' + currentWall + ' > div').show();

  switch (currentWall) {
  case "front":
    gesturePerformed("up")
    break;

  case "left":
    gesturePerformed("left")
    gesturePerformed("up")
    break;

  case "right":
    gesturePerformed("right")
    gesturePerformed("up")
    break;

  case "back":
    gesturePerformed("left")
    gesturePerformed("left")
    gesturePerformed("up")
    break;

  case "top":
    gesturePerformed("up")
    gesturePerformed("up")
    break;
  }

  tutorialMode = true;
  cubeNotLocked = 'left';
  setTimeout(function() {
    $('#bottom-explanation').fadeIn(1000);
  }, 500);
  setTimeout(function() {
    $('#swipe-bottom').fadeIn(1000, function() {
      animateSwipe('bottom')
    });
  }, 500);
})

$('#help')
    .click(
        function() {
          if (helpPressed == false) {
            $(this).attr('src', 'img/help-dis.png').css('cursor', 'default')
            helpPressed = true;
            cubeNotLocked = '';
            $('#' + currentWall + ' > div').hide();
            $('#' + currentWall)
                .append(
                    '<div id="help-window">'
                        + '<img id="close-help" src="img/help-close.jpg"><br/><br/><br/>'
                        + '<img id="help-label" src="img/help_label.jpg" />'
                        + '<img id="how-to" class="help-buttons" src="img/how-to.jpg">'
                        + '<a href="about.html">'
                        + '<img id="about-auth" class="help-buttons" src="img/about-auth.jpg">'
                        + '</a>'
                        + '<a href="about-proj.html">'
                        + '<img id="about-proj" class="help-buttons" src="img/about-proj.jpg">'
                        + '</a>' + '</div>')
            $('#curtain').fadeIn();
          }

        })

$(document).on("click", "#close-help", function() {

  $('#help').attr('src', 'img/help.jpg').css('cursor', 'pointer')
  helpPressed = false;
  cubeNotLocked = 'all';
  $('#help-window').remove();
  $('#' + currentWall + ' > div').show();
  $('#curtain').hide();
});

$('#sports-left-navigator').click(function() {
  if (currentSportsPage > 1 && allowedToPress == true) {
    allowedToPress = false;
    var currentMargin = parseInt($('#sports-slider').css('margin-left'))
    if (currentMargin < 0) {
      currentMargin += 438;
      $('#sports-slider').stop(true, false).animate({
        'marginLeft' : currentMargin + 'px'
      }, 300, function() {
        allowedToPress = true
      })
    }
    currentSportsPage -= 1;
  }
})

$('#sports-right-navigator').click(function() {
  if (currentSportsPage < numberOfSportsPages && allowedToPress == true) {
    allowedToPress = false;
    var currentMargin = parseInt($('#sports-slider').css('margin-left'))
    if (currentMargin > -2190) {
      currentMargin -= 438;
      $('#sports-slider').stop(true, false).animate({
        'marginLeft' : currentMargin + 'px'
      }, 300, function() {
        allowedToPress = true
      })
    }
    currentSportsPage += 1;
  }
})

// draws all the choices on the activities side of the cube
// takes an array of sports that is fetched from the DB
function drawActivities(sportsList) {
  listLength = sportsList.length;
  while (listLength != 0) {
    if (listLength > 6) {
      drawSportsWrapper(sportsList.slice(0, 6))
      sportsList.splice(0, 6)
      listLength = sportsList.length;
    } else {
      drawSportsWrapper(sportsList.slice(0, listLength))
      sportsList.splice(0, listLength)
      listLength = sportsList.length;
    }
  }
}

function drawSportsWrapper(wrapperList) {
  n = wrapperList.length
  var sportsWrapperSource = '<section class="sports-wrapper">'
  for (var i = 0; i < n; i++) {
    sportsWrapperSource += drawSportsLine(wrapperList[i]);
  }
  sportsWrapperSource += '</section>';

  var sportsSliderSource = $('#sports-slider').html()
  $('#sports-slider').html(sportsSliderSource + sportsWrapperSource)
}

// draws a single line for a sport with the label and the button
function drawSportsLine(element) {
  elementID = element.toLowerCase();
  elementID = elementID.replace(' ', '');
  return '<div class="sports-togglers" id="' + elementID + '_toggler">'
      + element
      + '<br/><div class="yes-no-button-sports" data-clicked="no"></div></div>'
}

// the repeating swiping animation for the tutorial
function animateSwipe(facingWall) {
  if (swipeAllowed) {
    var swipeimage = $('#swipe-' + facingWall);

    swipeimage.animate({
      marginLeft : '10px'
    }, 1500, function() {
      setTimeout(function() {
        swipeimage.css('margin-left', '400px');
        animateSwipe(facingWall);
      }, 1000)
    })
  }
}

// setting what will be saved in the browser storage as price once we go to
// the map
$('#one-time').click(function() {

  var oneTimeCombo = $('#one-time').parent().find('select');
  var membershipCombo = $('#membership').parent().find('select');
  oneTimeCombo.removeAttr('disabled');
  membershipCombo.prop('disabled', 'disabled');
  priceToBeSubmitted = '';
  priceToBeSubmitted = 'one-time,' + oneTimeCombo.val();

})

$('#membership').click(function() {

  var oneTimeCombo = $('#one-time').parent().find('select');
  var membershipCombo = $('#membership').parent().find('select');
  oneTimeCombo.prop('disabled', 'disabled');
  membershipCombo.removeAttr('disabled');
  priceToBeSubmitted = '';
  priceToBeSubmitted = 'membership,' + membershipCombo.val()

})

$('#membership').parent().find('select').change(function() {

  // if in tutorial mode - display the explanations
  if (tutorialMode == true) {
    cubeNotLocked = 'left';
    setTimeout(function() {
      $('#left-explanation').fadeIn(1000);
      $('#swipe-left').fadeIn(1000, function() {
        animateSwipe('left')
      });
    }, 500)
  }

  var temp = priceToBeSubmitted.split(',')[0];
  priceToBeSubmitted = temp + ',' + $(this).val()

})

$('#one-time').parent().find('select').change(function() {

  // if in tutorial mode - display the explanations
  if (tutorialMode == true) {
    cubeNotLocked = 'left';
    setTimeout(function() {
      $('#left-explanation').fadeIn(1000);
      $('#swipe-left').fadeIn(1000, function() {
        animateSwipe('left')
      });
    }, 500)
  }

  var temp = priceToBeSubmitted.split(',')[0];
  priceToBeSubmitted = temp + ',' + $(this).val()

})

// setting what will be saved in the browser storage as days once we go to
// the map
$('.yes-no-button-time')
    .click(
        function() {

          var btn = $(this);
          var btnLabel = btn.parent().text()
          var clicked = btn.data('clicked');
          if (btnLabel == "Whole week") {
            switch (clicked) {
            case "no":
              $('.yes-no-button-time').css('background-position', '0px -35px');
              $('.yes-no-button-time').data('clicked', 'yes');

              var length = daysToBeSubmitted.length;
              daysToBeSubmitted.splice(0, length);
              if (daysToBeSubmitted.length != 7) {
                daysToBeSubmitted.push("monday", "tuesday", "wednesday",
                    "thursday", "friday", "saturday", "sunday");
              }
              break;

            case "yes":
              $('.yes-no-button-time').css('background-position', '0px 0px');
              $('.yes-no-button-time').data('clicked', 'no');

              daysToBeSubmitted.splice(0, 7);
              break;
            }
          } else {
            switch (clicked) {
            case "no":
              btn.css('background-position', '0px -35px');
              btn.data('clicked', 'yes');

              var day = btn.parent().text().toLowerCase();
              if (daysToBeSubmitted.indexOf(day) == -1) {
                daysToBeSubmitted.push(day);
                ;
              }
              break;

            case "yes":
              btn.css('background-position', '0px 0px');
              btn.data('clicked', 'no');
              $("#whole_toggler").find("div").css('background-position',
                  '0px 0px');
              $("#whole_toggler > div").data('clicked', 'no');

              var day = btn.parent().text().toLowerCase();
              var index = daysToBeSubmitted.indexOf(day);
              daysToBeSubmitted.splice(index, 1);
              break;
            }
          }

          // if in tutorial mode - display the explanations
          if (tutorialMode == true) {
            cubeNotLocked = 'left';
            setTimeout(function() {
              $('#right-explanation').fadeIn(1000);
              $('#swipe-right').fadeIn(1000, function() {
                animateSwipe('right')
              });
            }, 500)
          }
        })

// triggered if the screen dimensions change
$(window).resize(
    function() {

      // reseting the perpective and returning the center of the cube at (0,0,0)
      document.getElementById('cube').style[prop] +=
          "translate" + currentDeterminerAxis + "("
              + currentDeterminerDirection + "" + depth + "px)";

      adjust();

      $('#bigWrapper').css('width', $(window).width());
      $('#bigWrapper').css('height', $(window).height());

      // here we pass the new window height for the next check about the address
      // bar
      windowHeight = $(window).height();

    })

// the end point of the tutorial
$('#front-explanation').click(function() {
  tutorialMode = false;
  cubeNotLocked = 'all';
  document.cookie = "tutorialModeOn=false";
  $(this).fadeOut(1000, function() {
    $('#linkToMap').fadeIn(500)
  });

  $('#curtain').fadeOut(1000)
  $('#help').attr('src', 'img/help.png').css('cursor', 'pointer')
  helpPressed = false;
})

// preventing the elastic bounce effect in Safari under iOS
$(document).bind('touchmove', function(event) {
  event.preventDefault();
});

// prevents images from being draggable
$('img').on('dragstart', function(event) {
  event.preventDefault();
});

// detecting when a key is pressed and passing this key to the keydownEvent
// function
$('body').keydown(
    function(evt) {

      var type = '';
      switch (evt.keyCode) {
      case 39: // right arrow
      case 68: // d
      case 102: // numpad 6
        type = 'left';
        break;
      case 37: // left arrow
      case 65: // a
      case 100: // numpad 4
        type = 'right';
        break;
      case 40: // down arrow
      case 83: // s
      case 98: // numpad 2
        type = 'up';
        break;
      case 38: // up arrow
      case 87: // w
      case 104: // numpad 8
        type = 'down';
        break;
      }

      if ((cubeNotLocked == 'all' && (type == 'up' || type == 'down'
          || type == 'left' || type == 'right'))
          || (cubeNotLocked == 'left' && type == 'left')
          || (cubeNotLocked == 'right' && type == 'right')
          || (cubeNotLocked == 'up' && type == 'up')
          || (cubeNotLocked == 'down' && type == 'down')) {
        gesturePerformed(type);
      }
    });

// detecting touch gestures on the screen (body)
Hammer('body')
    .on(
        "swipeup swipedown swipeleft swiperight dragup dragdown dragleft dragright",
        function(event) {
          var type
          if (event.type == "swipedown" || event.type == "dragdown") {
            type = "down";
          } else if (event.type == "swipeleft" || event.type == "dragleft") {
            type = "left";
          } else if (event.type == "swiperight" || event.type == "dragright") {
            type = "right";
          } else if (event.type == "swipeup" || event.type == "dragup") {
            type = "up";
          }
          if (cubeNotLocked == 'all'
              || (cubeNotLocked == 'left' && type == 'left')
              || (cubeNotLocked == 'right' && type == 'right')
              || (cubeNotLocked == 'up' && type == 'up')
              || (cubeNotLocked == 'down' && type == 'down')) {
            event.gesture.stopDetect();
            gesturePerformed(type);
          }
        });

// the function that manipulates the cube
function gesturePerformed(type) {
  // hides the red map button
  $('#linkToMap').delay(500).fadeOut(800);

  switch (type) {
  case "right": // left
    if (isFront()) {
      determinerAxis = "X";
      determinerDirection = "+";
      yAngle += 90;
      currentWall = "left";
      zoomIn();
    } else if (isRight()) {
      determinerAxis = "Z";
      determinerDirection = "-";
      yAngle += 90;
      currentWall = "front";
      zoomIn();
    } else if (isTop()) {
      determinerAxis = "X";
      determinerDirection = "+";
      xAngle += 90;
      yAngle += 90;
      currentWall = "left";
      zoomIn();
    } else if (isBottom()) {
      determinerAxis = "X";
      determinerDirection = "+";
      xAngle -= 90;
      yAngle += 90;
      currentWall = "left";
      zoomIn();
    } else if (isLeft()) {
      determinerAxis = "Z";
      determinerDirection = "+";
      yAngle += 90;
      currentWall = "back";
      zoomIn();
    } else if (isBack()) {
      determinerAxis = "X";
      determinerDirection = "-";
      yAngle += 90;
      currentWall = "right";
      zoomIn();
    }
    break;

  case "down": // up
    if (isFront()) {
      determinerAxis = "Y";
      determinerDirection = "+";
      xAngle -= 90;
      currentWall = "top";
      zoomIn();
    } else if (isTop()) {
      determinerAxis = "Z";
      determinerDirection = "+";
      xAngle += 90;
      yAngle += 180;
      currentWall = "back";
      zoomIn();
    } else if (isBottom()) {
      determinerAxis = "Z";
      determinerDirection = "-";
      xAngle -= 90;
      currentWall = "front";
      zoomIn();
    } else if (isLeft()) {
      determinerAxis = "Y";
      determinerDirection = "+";
      xAngle -= 90;
      yAngle -= 90;
      currentWall = "top";
      zoomIn();
    } else if (isRight()) {
      determinerAxis = "Y";
      determinerDirection = "+";
      xAngle -= 90;
      yAngle += 90;
      currentWall = "top";
      zoomIn();
    } else if (isBack()) {
      determinerAxis = "Y";
      determinerDirection = "+";
      xAngle -= 90;
      yAngle += 180;
      currentWall = "top";
      zoomIn();
    }
    break;

  case "left": // right
    if (isFront()) {
      determinerAxis = "X";
      determinerDirection = "-";
      yAngle -= 90;
      currentWall = "right";
      zoomIn();
    } else if (isLeft()) {
      determinerAxis = "Z";
      determinerDirection = "-";
      yAngle -= 90;
      currentWall = "front";
      zoomIn();
    } else if (isTop()) {
      determinerAxis = "X";
      determinerDirection = "-";
      xAngle += 90;
      yAngle -= 90;
      currentWall = "right";
      zoomIn();
    } else if (isBottom()) {
      determinerAxis = "X";
      determinerDirection = "-";
      xAngle -= 90;
      yAngle -= 90;
      currentWall = "right";
      zoomIn();
    } else if (isRight()) {
      determinerAxis = "Z";
      determinerDirection = "+";
      yAngle -= 90;
      currentWall = "back";
      zoomIn();
    } else if (isBack()) {
      determinerAxis = "X";
      determinerDirection = "+";
      yAngle -= 90;
      currentWall = "left";
      zoomIn();
    }
    break;

  case "up": // down
    if (isFront()) {
      determinerAxis = "Y";
      determinerDirection = "-";
      xAngle += 90;
      currentWall = "bottom";
      zoomIn();
    } else if (isTop()) {
      determinerAxis = "Z";
      determinerDirection = "-";
      xAngle += 90;
      currentWall = "front";
      zoomIn();
    } else if (isLeft()) {
      determinerAxis = "Y";
      determinerDirection = "-";
      xAngle += 90;
      yAngle -= 90;
      currentWall = "bottom";
      zoomIn();
    } else if (isRight()) {
      determinerAxis = "Y";
      determinerDirection = "-";
      xAngle += 90;
      yAngle += 90;
      currentWall = "bottom";
      zoomIn();
    } else if (isBottom()) {
      determinerAxis = "Z";
      determinerDirection = "+";
      xAngle -= 90;
      yAngle += 180;
      currentWall = "back";
      zoomIn();
    } else if (isBack()) {
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
  if (tutorialMode == true) {
    switch (currentWall) {
    case 'right':
      $('#swipe-bottom').fadeOut(1500);
      $('#bottom-explanation').fadeOut(1500);
      cubeNotLocked = '';
      setTimeout(function() {
        alert("Choose a time for your practices")
      }, 1200);
      break;

    case 'back':
      $('#swipe-right').fadeOut(1500);
      $('#right-explanation').fadeOut(1500);
      cubeNotLocked = '';
      setTimeout(function() {
        alert("Choose an activity")
      }, 1200);
      break;

    case 'left':
      $('#swipe-back').fadeOut(1500);
      $('#back-explanation').fadeOut(1500);
      cubeNotLocked = '';
      setTimeout(function() {
        alert("Choose type and range of the price")
      }, 1200);
      break;

    case 'front':
      $('#swipe-left').fadeOut(1500);
      $('#left-explanation').fadeOut(1500);
      cubeNotLocked = '';
      setTimeout(function() {
        alert("Click on the grey button to explore the map with results")
      }, 1200);
      break;
    }
  }

  // alert(inwards + '|' + determinerDirection + "|" +
  // currentDeterminerDirection);
  // if the cube is supposed to come inwards just swap the the direction
  if (inwards) {
    if (determinerDirection == '+') {
      determinerDirection = '-';
    } else {
      determinerDirection = '+';
    }
  }
  // alert(determinerDirection + "|" + currentDeterminerDirection);

  // check the beginning of the document for the variables that follow
  // when we rotate the cube and it is moved inwards to make it fit the screen
  // first we move it outwards
  document.getElementById('cube').style[prop] +=
      "translate" + currentDeterminerAxis + "(" + currentDeterminerDirection
          + "" + depth + "px)";

  // then we rotate it
  document.getElementById('cube').style[prop] =
      "rotateX(" + xAngle + "deg) rotateY(" + yAngle + "deg)";

  // then move it inwards by the respective axis
  document.getElementById('cube').style[prop] +=
      "translate" + determinerAxis + "(" + determinerDirection + "" + depth
          + "px)";

  currentDeterminerAxis = determinerAxis;
  if (determinerDirection === "+") {
    currentDeterminerDirection = "-";
  } else {
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
  return document.getElementById('top');
}

function getBackPanel() {
  return document.getElementById('back');
}

// executed when the user is facing the Front (Map) side of the cube
function frontpanel() {
  $('#linkToMap').delay(500).fadeIn(800);
}

// checks which side we are facing and zooms it by making all other sides
// invisible; if the side is the Front one we also show the Map button
function activityTimer() {
  clearInterval(interval);
  if (isBack())
    document.getElementById('cube').style[prop] += "translateZ(-80px)";
  else
    getBackPanel().style.opacity = 0.0;
  if (isLeft())
    document.getElementById('cube').style[prop] += "translateX(-80px)";
  else
    getLeftPanel().style.opacity = 0.0;
  if (isRight())
    document.getElementById('cube').style[prop] += "translateX(80px)";
  else
    getRightPanel().style.opacity = 0.0;
  if (isFront()) {
    document.getElementById('cube').style[prop] += "translateZ(80px)";
    frontpanel();
  } else
    getFrontPanel().style.opacity = 0.0;
  if (isTop())
    document.getElementById('cube').style[prop] += "translateY(-80px)";
  else
    getTopPanel().style.opacity = 0.0;
  if (isBottom())
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
function zoomIn() {
  clearInterval(interval);
  showAllPanels();
  interval = setInterval(function() {
    activityTimer();
  }, inactivity);
}
