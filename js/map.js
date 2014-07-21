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

$('#backToMap').click(function(){
	window.location = "http://testpilot.x10.mx/sportscubed/";
})

$(window).resize(function(){
	windowWidth = $(window).width();
	$('#helpCard1').css('margin-left',(windowWidth/2 - 125).toString()+"px");
	$('#goBackCard').css('margin-left',(windowWidth/2 - 125).toString()+"px");
	
	windowHeight = $(window).height();
	$('#backToMap').css('margin-top',(windowHeight/2 - 35).toString()+"px");
})

$(document).ready(function(){

		var cookieArray = document.cookie.split("; ")
		console.log(cookieArray)

		var windowHeight = $(window).height();
		$('#backToMap').css('margin-top',(windowHeight/2 - 35).toString()+"px")
		// creating the map and the infowindow
		var myLatlng = new google.maps.LatLng(55.950, -3.190);
	
		var mapOptions = {
		zoom: 12,
		center: myLatlng,
		minZoom: 10,
		mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var map = new google.maps.Map(document.getElementById('map-canvas'),
									mapOptions);
									
		var infoWindowOptions = {
		maxWidth: 300
		}
		var infowindow = new google.maps.InfoWindow(infoWindowOptions);
		
if( sessionStorage.tutorialModeOn == 'true' ){

    windowWidth = $(window).width();
	$('#helpCard1').fadeIn(1000);
	$('#helpCard1').css('margin-left',(windowWidth/2 - 125).toString()+"px")
	
	var myLatlng = new google.maps.LatLng("55.942665", "-3.218126");
	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map
	});
	
	contentStrings = "<div style='color:#939393; font-size: 15px; font-family:Calibri'><span style='color:black;'>Venue name goes here</span>" +
	"<br/><a href=mailto:venueemail@example.com>venueemail@example.com</a>" +
	"<br/><a href=tel:0131 XXXX XXXX>0131 XXXX XXXX</a>" +
	"<br/><span style='margin-top:10px;' >" + sessionStorage.sports + "</span></div>";
			
	google.maps.event.addListener(marker, 'click',function() {
		infowindow.setContent(contentStrings);
		infowindow.open(map,marker);
	});
	
	google.maps.event.addListener(infowindow,'closeclick',function(){
		$('#helpCard1').fadeOut(1000);
		$('#goBackCard').fadeIn(1000);
		$('#goBackCard').css('margin-left',(windowWidth/2 - 125).toString()+"px")
   });
	
	$('.helpCardClose').click(function(){
		$(this).parent().fadeOut(1000);
	})
}
else{

	// constructing the query to be sent to the server
	var sportsArray = sessionStorage.sports.split(",");
	var daysArray = sessionStorage.days.split(",");
	var priceArray = sessionStorage.price.split(",");
	
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
	request.open("GET","http://testpilot.x10.mx/sportscubed/query.php?sports=" +
				sports + "&days=" + days + "&price=" + price,true);
	request.send (null);
	
	request.onreadystatechange = function()
	{
	if (request.readyState==4 && request.status==200)
	{
		data = JSON.parse(request.responseText);
		console.log(data);
	
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
}

})