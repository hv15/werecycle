var geocoder;
/*
	Deals with the geolocation
*/
function getGeoLocation(){
	spin("visible");
	if (navigator.geolocation) {
		var timeoutVal = 10000;
		navigator.geolocation.getCurrentPosition(
			returnPosition, 
			returnError,
			{ enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0 }
		);
	}
	else {
		spin("invisible");
		alert("Geolocation is not supported by this browser");
	}
}
function returnPosition(position) {
	window.location.href = "./map.php?latitude="+position.coords.latitude+"&longitude="+position.coords.longitude;
}
function returnError(position) {
	spin("invisible");
	alert("Geolocation did not work");
}


/*
	Deals with geocoding
*/
function getGeoCode() {
	geocoder = new google.maps.Geocoder();
	var address = document.getElementById('GeoCodeInputField').value;
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			window.location.href = "./map.php?latitude="+results[0].geometry.location.lat()+"&longitude="+results[0].geometry.location.lng();
		} else {
			alert('Geocode was not successful for the following reason: ' + status);
		}
    });
}

function spin(state){
	var spin = document.getElementById("GeoLocationSpin");
	spin.className = "visible";
}