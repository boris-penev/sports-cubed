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

// constructing the query to be sent to the server
var sportsArray = new Array(sessionStorage.sports.split(","));
var daysArray = new Array(sessionStorage.days.split(","));
var priceArray = new Array(sessionStorage.price.split(","));

if(sessionStorage.sports == ""){
  sports=null;
}
else{
  var sports = JSON.stringify(sportsArray);
  sports = sports.replace(/"/g, '\\"');
}

if(sessionStorage.days == ""){
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
request.open("GET","http://testpilot.x10.mx/sports/query.php?sports=" +
             sports + "&days=" + days + "&price=" + price,true);
request.send (null);

request.onreadystatechange = function()
{
  if (request.readyState==4 && request.status==200)
  {
    data = JSON.parse(request.responseText);
    console.log(data);
    // creating the map and the infowindow
    var myLatlng = new google.maps.LatLng(55.970, -3.150);

    var mapOptions = {
      zoom: 12,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById('map-canvas'),
                                  mapOptions);
								  
	var infoWindowOptions = {
	  maxWidth: 300
	}
    var infowindow = new google.maps.InfoWindow(infoWindowOptions);

    var i = 0;

    // creating the markers and populating the contentStrings array which has
	// the info window data for each marker
	// populating the markers array
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
	  
      contentStrings[i] = "<div style='color:#939393; font-size: 15px; font-family:Calibri'><span style='color:black;'>" + data[i].name + "</span>" +
        "<br/><a href=mailto:" + data[i].email + ">" + data[i].email + "</a>" +
        "<br/><a href=tel:" + data[i].phone + ">" + data[i].phone + "</a>" +
        "<br/><span style='margin-top:10px;' >" + data[i].sports + "</span></div>";
		
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
