var data;
var marker;
var markers = new Array();
var contentStrings = new Array();
var lat1;
var lon1;

// TODO for tomorrow - refactor the file thoroughly!!!
$(document).bind( "mobileinit", function(event) {
    $.extend($.mobile.zoom, {locked:false,enabled:true});
});

$('#backToCube').click(function(){
  window.location.href = ".";
});

$(window).resize(function(){
  windowWidth = $(window).width();
  $('#helpCard1').css('margin-left',(windowWidth/2 - 125).toString()+"px");
  $('#goBackCard').css('margin-left',(windowWidth/2 - 125).toString()+"px");

  windowHeight = $(window).height();
  $('#backToCube').css('margin-top',(windowHeight/2 - 35).toString()+"px");
});

$(document).ready(function(){

  cookieArray = [];
  initialCookieArray = document.cookie.split('; ').sort();
  for (var x in initialCookieArray){
    if (initialCookieArray[x].indexOf("sports") != -1 ||
        initialCookieArray[x].indexOf("days") != -1 ||
        initialCookieArray[x].indexOf("price") != -1 ||
        initialCookieArray[x].indexOf("isComingFromMap") !=-1 ||
        initialCookieArray[x].indexOf("tutorialModeOn") != -1)
    {
      cookieArray.push(initialCookieArray[x])
    }
  }
  cookieArray.sort();

  tutorialMode = cookieArray[4].split('=')[1];
  isComingFromMap = cookieArray[1].split('=')[1];

  console.log(cookieArray)

  var windowHeight = $(window).height();
  $('#backToCube').css('margin-top',(windowHeight/2 - 35).toString()+"px");
  // creating the map and the info window
  var myLatlng = new google.maps.LatLng(55.950, -3.190);  //Edinburgh center

  var mapOptions = {
    zoom: 12,
    center: myLatlng,
    minZoom: 10,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById('map-canvas'),
                mapOptions);

  var infoWindowOptions = {
    maxWidth: 300
  };
  var infowindow = new google.maps.InfoWindow(infoWindowOptions);

  if( tutorialMode == 'true' ){

    windowWidth = $(window).width();
    $('#helpCard1').fadeIn(1000);
    $('#helpCard1').css('margin-left',(windowWidth/2 - 125).toString()+"px");

    var myLatlng = new google.maps.LatLng("55.942665", "-3.218126");
    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map
    });

    contentStrings =
      "<div style='color:#939393; font-size: 15px; font-family:Calibri'>" +
        "<span style='color:black;'>Venue name goes here</span>" +
      "<br/><a href=mailto:venueemail@example.com>venueemail@example.com</a>" +
      "<br/><a href=tel:0131 XXXX XXXX>0131 XXXX XXXX</a>" +
      "<br/><span style='margin-top:10px;' >" +
        cookieArray[0].split('=')[1] + "</span>" +
      "</div>";

    google.maps.event.addListener(marker, 'click',function() {
      infowindow.setContent(contentStrings);
      infowindow.open(map,marker);
    });

    google.maps.event.addListener(infowindow,'closeclick',function(){
      $('#helpCard1').fadeOut(1000);
      $('#goBackCard').fadeIn(1000);
      $('#goBackCard').css('margin-left',(windowWidth/2 - 125).toString()+"px");
    });

    $('.helpCardClose').click(function(){
      $(this).parent().fadeOut(1000);
    });
  }
  else {

    // constructing the query to be sent to the server
    var sportsArray = cookieArray[3].split('=')[1].split(",");
    var daysArray = cookieArray[0].split('=')[1].split(",");
    var priceArray = cookieArray[2].split('=')[1].split(",");
    priceArray[1] = priceArray[1].replace('Below ','').replace('£','');
    if (sportsArray == ""){
      sports=null;
    }
    else {
      var sports = JSON.stringify(sportsArray);
      sports = sports.replace(/"/g, '\\"');
    }

    if(daysArray == ""){
      days = null;
    }
    else{
      var days = JSON.stringify(daysArray);
      days = days.replace(/"/g, '\\"');
    }

    var price = JSON.stringify(priceArray);
    price = price.replace(/"/g, '\\"');

    console.log(sports + "|" + days + "|" + price)

    // making the request to the server
    request = new XMLHttpRequest();
    request.open("GET","query.php?sports=" +
            sports + "&days=" + days + "&price=" + price, true);
    request.send (null);

    request.onreadystatechange = function()
    {
      if (request.readyState==4 && request.status==200)
      {
        data = JSON.parse(request.responseText);
        console.log(data.length)
        console.log(data);

        var i = 0;

        // creating the markers and populating the contentStrings array which has
        // the info window data for each marker populating the markers array
        for(var obj in data)
        {
          var myLatlng = new google.maps.LatLng(data[i].latitude,
                              data[i].longtitude);
          var marker = new google.maps.Marker({
            id : i,
            position: myLatlng,
            map: map,
            title: 'Marker ' + i
          });

          var email;
          
          if(data[i].email){
             email = "<a href=mailto:" + data[i].email + ">" + data[i].email + "</a>"
          }
          else{
             email = "<span>No email provided.</span>"
          }
          
          contentStrings[i] = "<div style='color:#939393; font-size: 15px; font-family:Calibri'><span style='color:black;'>" + data[i].name + "</span>" +
            "<br/>" + email +
            "<br/><a href=tel:" + data[i].phone + ">" + data[i].phone + "</a>" +
            "<br/><span style='margin-top:10px;' >" + data[i].sports + "</span></div>";

          console.log(email);
          markers[i] = marker;

          // event listener for each marker
          google.maps.event.addListener(marker, 'click',
            function() {
              var id = this.id;
              var marker = markers[id];
              infowindow.setContent(contentStrings[id]);
              infowindow.open(map,marker);
            });
          i++;
        }


      }
    }
  }

});
