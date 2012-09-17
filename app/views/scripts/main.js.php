var geocoder;

document.onkeydown = checkKeycode;
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
	window.location.href = "./select.php?types=6,7,2,16,1&zoom=12&&latitude="+position.coords.latitude+"&longitude="+position.coords.longitude;
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
	var address = document.getElementById('geoCodeInputField').value;
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			window.location.href = "./select.php?types=6,7,2,16,1&zoom=12&latitude="+results[0].geometry.location.lat()+"&longitude="+results[0].geometry.location.lng();
		} else {
			alert("Could not find that location. Try formatting in another way.");
		}
    });
}

function spin(state){type="text/javascript" 
	var spin = document.getElementById("geoLocationSpin");
	spin.className = state;
}

function checkKeycode(e) {
	var keycode;
	if (window.event)
		keycode = window.event.keyCode;
	else if (e)
		keycode = e.which;
	if(keycode==13)
		getGeoCode();
}