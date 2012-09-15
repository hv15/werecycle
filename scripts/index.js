var geocoder;
/*
	Deals with the geolocation
*/
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
}
function returnPosition(position) {
	window.location.href = "./map.php?latitude="+position.coords.latitude+"&longitude="+position.coords.longitude;
}
function returnError(position) {
  alert("Geolocation did not work");
}


/*
	Deals with geocoding
*/
function getGeoCode() {
	var address = document.getElementById('address').value;
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			window.location.href = "./map.php?latitude="+results[0].geometry.location.lat()+"&longitude="+results[0].geometry.location.lng();
			results[0].geometry.location.lat();
		} else {
			alert('Geocode was not successful for the following reason: ' + status);
		}
    });
}