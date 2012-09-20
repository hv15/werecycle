var geocoder;

document.onkeydown = checkKeycode;
/*
	Deals with the geolocation
*/
function getGeoLocation(){
	$('#geoLocationSpin').removeClass("invisible").addClass("visible");
	if (navigator.geolocation) {
		var timeoutVal = 10000;
		window.navigator.geolocation.watchPosition(
			returnPosition, 
			returnError,
			{ enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 250 }
		);
	}
	else {
		spin("invisible");
		alert("Geolocation is not supported by this browser");
	}
}
function returnPosition(position) {
	var newSessionData = encodeURIComponent('{"latitude":'+position.coords.latitude+',"longitude":'+position.coords.longitude+',"types_selected":"all"}');
	$.get('/setsession/'+newSessionData, function(data) {
		window.location.href = '/map';
	});
}
function returnError(position) {
	$('#geoLocationSpin').removeClass("visible").addClass("invisible");
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
			var newSessionData = encodeURIComponent('{"latitude":'+results[0].geometry.location.lat()+',"longitude":'+results[0].geometry.location.lng()+',"types_selected":"all"}');
			$.get('/setsession/'+newSessionData, function(data) {
				window.location.href = '/map';
			});
		} else {
			alert("Could not find that location. Try formatting in another way.");
		}
    });
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