var data;
var marker;
var markers = new Array();
var contentStrings = new Array();
var lat1;
var lon1;

$(document).bind( "mobileinit", function(event) {
    $.extend($.mobile.zoom, {locked:false,enabled:true});
});

$('body').keydown(function(evt) {
	window.parent.keydownEvent( evt );
})


		
request = new XMLHttpRequest();
var sports = new Array(sessionStorage.sports.split(","));
var days = new Array(sessionStorage.days.split(","));
var price;
	switch(sessionStorage.price){
		
		case "all":
		price = null;
		break;
		
		case "free":
		price = 0;
		break;
		
		case "below20":
		price = 20;
		break;
		
		case "below35":
		price = 35;
		break;
		
		case "below50":
		price = 50;
		break;
	}
//alert(price);
if(sessionStorage.sports == ""){
	aaa=null;
}
else{
	var aaa = JSON.stringify(sports);
	aaa=aaa.substring(1,aaa.length-1);
	aaa = aaa.replace(/"/g, '\\"');
}
if(sessionStorage.sports == ""){
	bbb = null;
}
else{
	var bbb = JSON.stringify(days);
	bbb=bbb.substring(1,bbb.length-1);
	bbb = bbb.replace(/"/g, '\\"');
}
//alert("sports="+aaa+"&days="+bbb+"&price="+price);
request.open("GET","http://testpilot.x10.mx/sports/query.php?sports="+aaa+"&days="+bbb+"&price="+price,true);
request.send(null);
request.onreadystatechange=function()
  {
  if (request.readyState==4 && request.status==200){
    data = JSON.parse(request.responseText);
	//alert(request.responseText);
	//creating the map
	var myLatlng = new google.maps.LatLng(55.970, -3.150);
	
	var mapOptions = {
    zoom: 12,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
	var infowindow = new google.maps.InfoWindow({
	});
	
	var i = 0;
	
	//creating the markers
	for(var obj in data){
	var myLatlng = new google.maps.LatLng(data[i].latitude, data[i].longtitude);
	var marker = new google.maps.Marker({
	id : i,
    position: myLatlng,
    map: map,
	title: 'Marker ' + i
	});
	contentStrings[i] = "<h3>Name: " + data[i].name + "</h3><h3>Email: " + data[i].email + "</h3><h3>Phone: " + data[i].phone + "</h3><h3>Sports: " + data[i].sports + "</h3>";
	markers[i] = marker;
	google.maps.event.addListener(marker, 'click', function() { var id = this.id; clicked(id)});
	i++;
  }
  
  	function clicked(id) {
	var marker = markers[id];
	infowindow.setContent(contentStrings[id]);
	infowindow.open(map,marker);
	};

}
  }