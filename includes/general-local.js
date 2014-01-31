/*
  General js functions
*/
function hideNotification ( )
{
	setTimeout ( function() {
			var array = document.getElementsByClassName("notification");
			for (i=0;i<array.length;i++) {
				array[i].style.visibility = 'hidden';
			}
		}, 2000 );
}

function setGlobalOpen(){
	var val = document.getElementById("time_open_global").value;
	var elements = document.getElementsByClassName('selectSport');
	for(var x in elements){
		if(elements[x].checked === true){
			for(i=1; i<=7; i++){
			id=elements[x].getAttribute('id').substring(12,elements[x].getAttribute('id').length);
			document.getElementById("timeOpenDay"+i+"_"+id).value = val;
			}
		}
	}
}

function setGlobalClose(){
	var val = document.getElementById("time_close_global").value;
	var elements = document.getElementsByClassName('selectSport');
	for(var x in elements){
		if(elements[x].checked === true){
			for(i=1; i<=7; i++){
			id=elements[x].getAttribute('id').substring(12,elements[x].getAttribute('id').length);
			document.getElementById("timeCloseDay"+i+"_"+id).value = val;
			}
		}
	}
}

document.getElementById("time_open_global_submit").onclick=function(){setGlobalOpen()};
document.getElementById("time_close_global_submit").onclick=function(){setGlobalClose()};

hideNotification ( );

