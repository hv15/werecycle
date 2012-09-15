function getGeoLocation(){
	if (navigator.geolocation) {
		var timeoutVal = 10 * 1000 * 1000;
		navigator.geolocation.getCurrentPosition(
			returnPosition, 
			returnError,
			{ enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0 }
		);
	}
	else {
		alert("Geolocation is not supported by this browser");
	}
	window.location.href = "map.php?latitude="+"&longitude="+;
}

function returnPosition(position) {
	alert("./map.php?latitude="+position.coords.latitude+"&longitude="+position.coords.longitude);
	window.location.href = "./map.php?latitude="+position.coords.latitude+"&longitude="+position.coords.longitude;
}
function returnError(position) {
  alert("Geolocation did not work");
}