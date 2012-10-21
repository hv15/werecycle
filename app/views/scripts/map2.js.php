<?php 
	$userdata = $this->session->all_userdata(); 
	if( !isset($userdata['latitude']) OR !isset($userdata['longitude']) OR !isset($userdata['types_selected']) ) {
		$this->session->set_flashdata('message', 'Your session expired, please start again.');
		echo 'window.location.href = "/";'; return;
	}
?>
// a global variable to access the map
var map;
var markerCluster;
var userdata;
var map_zoom = <?=(isset($userdata['map_zoom']) ? $userdata['map_zoom'] : 15)?>;
var map_lat  = <?=(isset($userdata['latitude']) ? $userdata['latitude'] : 55.95)?>;
var map_lon  = <?=(isset($userdata['longitude']) ? $userdata['longitude'] : -3.18)?>;
var map_pos  = new google.maps.LatLng(map_lat, map_lon);
var types  = '<?=$userdata['types_selected']?>';
var latitude = map_lat;
var longitude = map_lon;

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
function toggleLocation() {
	// If button active
	if( $("#ButtonLocation").hasClass('active') ) {
		$("#ButtonLocation").removeClass("active").addClass("inactive");
		GeoMarker.setMarkerOptions({visible:false});
		GeoMarker.setCircleOptions({fillOpacity: "0", strokeOpacity: "0"});
	} else {
		$("#ButtonLocation").removeClass("inactive").addClass("active");
		var GeoMarkerImage = new google.maps.MarkerImage(geoIcon, new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(7, 7), new google.maps.Size(15, 15));
		GeoMarker.setMarkerOptions({visible:true, icon: GeoMarkerImage});
		GeoMarker.setCircleOptions({fillColor: "#33CCCC", fillColor: "#33CCCC", strokeOpacity: "0.6", fillOpacity: "0.3"});
		google.maps.event.addListener(GeoMarker, "geolocation_error", function(e) {
			if(button.className.indexOf("inactive")==-1){
				alert("Position could not be established.");
				$("#ButtonLocation").removeClass("active").addClass("inactive");
			}
		});
		GeoMarker.setMap(map);
		setTimeout(function(){
			map.setCenter(GeoMarker.getPosition())
		},500);
	}
}

function drawMarkers(newlocation) {
	if(map_zoom > 14) {
		distance = 5;
	} else if(map_zoom > 11) {
		distance = 10;
	} else if(map_zoom <8) {
		distance = 1000;
	} else {
		var distance = (21 - map_zoom) * 5;
	}
	latitude = newlocation.lat();
	longitude = newlocation.lng();
	
	var newSessionData = encodeURIComponent('{"distance":'+distance+',"latitude":'+latitude+',"longitude":'+longitude+',"map_zoom":'+map_zoom+'}');
	var urlRand = Math.random();
	$.get('/setsession/'+newSessionData+'/'+urlRand, function(setSessionResponse){
		$.get('/data/'+urlRand, function(dataResponse) {
			eval(dataResponse);
			
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
						$(location).attr('href',"/info/"+id);
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
		});
	});
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
	
	google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
		drawMarkers(map_pos);
	});
	
	google.maps.event.addListener(map, 'dragend', function() {
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
	$(location).attr('href',"/select");
}

google.maps.event.addDomListener(window, 'load', initialize);
