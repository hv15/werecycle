// a global variable to access the map
var map;
var markerCluster;
var latitude;
var longitude;
// Used for geolocation
var GeoMarker;
var GeoLatLng;
var GeoBounds;
// icons and styles used
var geoIcon = "/img/geoicon.png";
//var recyclePointIcon = "/img/recyclePoint.png";
//var recycleCenterIcon = "/img/recycleCenter.png";
var clusterStyle = [{
	url: "/img/recycle35.png",
	height: 35,
	width: 35,
	anchor: [10, 0],
	textColor: "#000",
	textSize: 14
      }, {
	url: "/img/recycle45.png",
	height: 45,
	width: 45,
	anchor: [15, 0],
	textColor: "#D2FFB5",
	textSize: 16
      }, {
	url: "/img/recycle55.png",
	height: 55,
	width: 55,
	anchor: [18, 0],
	textColor: "#D2FFB5",
	textSize: 18
      }];
// parsed JSON to store markers
var data;

// Prevents scrolling on the page for mobile phones.
document.onload = function(){
	document.ontouchmove = function(e){
		e.preventDefault();
	}
};


// Handles the buttons on the map used to zoom in
// or out.
function zoomIn(){ map.setZoom(map.getZoom()+1);}
function zoomOut(){ map.setZoom(map.getZoom()-1);}

/*
	Turns Geolocation on or off.
*/
function toggleLocation(){
	// We get the button so that we can change its style, and
	// loosly determine if GPS is on or not.
	var button = document.getElementById("ButtonLocation");
	if(button.className.indexOf("inactive")==-1&&!map.getCenter().equals(GeoLatLng)){
		map.setCenter(GeoLatLng);
		return;
	}
	// Does the button not contain the word inactive?
	if(button.className.indexOf("inactive")==-1){
		// This means it is active, so we therefore turn it off
		button.className = button.className.replace("active","inactive");
		GeoMarker.setMarkerOptions({visible:false});
		GeoMarker.setCircleOptions({fillOpacity: "0", strokeOpacity: "0"});
	} else {
		// This means it is not active, slatitudeo we therefore turn it on
		button.className = button.className.replace("inactive","active");
		var GeoMarkerImage = new google.maps.MarkerImage(geoIcon, new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(7, 7), new google.maps.Size(15, 15));
		GeoMarker.setMarkerOptions({visible:true, icon: GeoMarkerImage});
		GeoMarker.setCircleOptions({fillColor: "#33CCCC", fillColor: "#33CCCC", strokeOpacity: "0.6", fillOpacity: "0.3"});
		google.maps.event.addListenerOnce(GeoMarker, "position_changed", function() {
			GeoLatLng = this.getPosition();
			GeoBounds = this.getBounds();
		map.setCenter(GeoLatLng);
		map.setZoom(15);
	});
	google.maps.event.addListener(GeoMarker, "position_changed", function() {
			GeoLatLng = this.getPosition();
			//GeoBounds = this.getBounds();
	});
	google.maps.event.addListener(GeoMarker, "geolocation_error", function(e) {
		if(button.className.indexOf("inactive")==-1){
			alert("Position could not be established.");
			button.className = button.className.replace("active","inactive");
		}
	});
	GeoMarker.setMap(map);
		//alert("GPS is now on");
	}
}

function drawMarkers(newlocation) {
	var distance = 21 - map_zoom * 5;
	latitude = newlocation.lat();
	longitude = newlocation.lng();
	
	// get dynamically the JSON data via data.php for the markers
	var urly = "http://recyclefinder.co.uk/beforeAndrew/data.php?longitude="+longitude+"&latitude="+latitude+"&distance="+distance+"&types="+types;

	$.ajax({ type: 'GET', url: urly, success: function(check) {
		eval(check);	
		
		var markers = [];
		for (var i = 0; i < data.outlets.length; i++) {
			var outlet = data.outlets[i];
			var latLng = new google.maps.LatLng(outlet.lat,outlet.lon);
			var marker = new google.maps.Marker({ position: latLng});
			// Add the markers, text to the memory.
			markers.push(marker);
			// Give each marker an event that opens the window.
			google.maps.event.addListener(marker, 'click', (function(marker, i, name, id, type) {
				return function() {
					$(location).attr('href',"./info.php?id="+id+"&latitude="+latitude+"&longitude="+longitude+"&types="+types+"&zoom="+map_zoom);
				}    
			})(marker, i, outlet.name, outlet.id, outlet.type));
		}
		
		// Clear all markers
		if(markerCluster) {
			markerCluster.clearMarkers();
			markerCluster.addMarkers(markers);
		} else {
			// Put all the markers into the cluster.
			markerCluster = new MarkerClusterer(map, markers, {styles: clusterStyle});
		}
	}});
	google.maps.event.clearListeners(map, 'bounds_changed');
}

/*
	1. Positions the view
	2. Creates a GeolocationMarker
	3. Adds all the points found in the requested file based on parameters.
*/
function initialize(){
	/*
		Create a google map interface with the following params
	*/
	map = new google.maps.Map(document.getElementById('Map'), {
		zoom: map_zoom,
		center: map_pos,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		disableDefaultUI: true
	});

	/*
		This part of the code sets up geolocation
	*/
	GeoMarker = new GeolocationMarker();

	/*
		This part of the code create the marker clusters,
		and the info windows with all the details of what
		was clicked on.
	*/
	
	google.maps.event.addListener(map, 'bounds_changed', function() {
		drawMarkers(map_pos);
	});
	
	google.maps.event.addListener(map, 'center_changed', function() {
		var newlocation = map.getCenter();
		drawMarkers(newlocation);
	});
	
	google.maps.event.addListener(map, 'zoom_changed', function() {
		map_zoom = map.getZoom();
		var newlocation = map.getCenter();
		drawMarkers(newlocation);
	});
	// Create the graphics that we will use
	//var recyclePointMarkerImage = new google.maps.MarkerImage(recyclePointIcon , new google.maps.Size(64, 64), new google.maps.Point(0, 0), new google.maps.Point(32, 32), new google.maps.Size(64, 64));
	//var recycleCenterMarkerImage = new google.maps.MarkerImage(recycleCenterIcon, new google.maps.Size(64, 64), new google.maps.Point(0, 0), new google.maps.Point(32, 32), new google.maps.Size(64, 64));


}

function buttonSelect() {
	$(location).attr('href',"./select.php?latitude="+latitude+"&longitude="+longitude+"&types="+types+"&zoom="+map_zoom);
}

var map_zoom = <?php echo (isset($_GET['zoom'])) ? $_GET['zoom'] : 12 ?>;
var map_lat  = <?php echo (isset($_GET['latitude'])) ? $_GET['latitude'] : 55.9099 ?>;
var map_lon  = <?php echo (isset($_GET['longitude'])) ? $_GET['longitude'] : -3.3220 ?>;
var map_pos  = new google.maps.LatLng(map_lat, map_lon);
var types  = '<?php echo (isset($_GET['types'])) ? $_GET['types'] : '6,7,2,16,1' ?>';

google.maps.event.addDomListener(window, 'load', initialize);