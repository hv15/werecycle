<?php 
	$userdata = $this->session->all_userdata(); 
	if( !isset($userdata['latitude']) OR !isset($userdata['longitude']) OR !isset($userdata['types_selected']) ) {
		$this->session->set_flashdata('message', 'Your session expired, please start again.');
		echo 'window.location.href = "/";'; return;
	}
?>
// a global variable to access the map
var map;
// global array to store all markers so we can delete them later
var allMarkers = [];

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
	// Clear any currently-displayed markers
	for (i in allMarkers) {     
		allMarkers[i].setMap(null);    
	}
	// Encode variables for passing to the session before loading the outlets/clusters data 
	var newSessionData = encodeURIComponent('{"lat":'+newlocation.lat()+',"lng":'+newlocation.lng()+',"map_zoom":'+map_zoom+'}');
	// Generate a random number and add it to the URL string so IE doesn't (stupidly!) cache the ajax request
	var urlRand = Math.random();
	// Set the session via ajax
	$.get('/setsession/'+newSessionData+'/'+urlRand, function(setSessionResponse){
		// Load the outlets/clusters data via ajax
		$.get('/datanew/'+urlRand, function(dataResponse) {
			// Eval the ajax response since it is in JSON format - this should give us two variables, clusterData and singleOutletData
			eval(dataResponse);
			
			for (var i = 0; i < clusterData.clusters.length; i++) {
				var cluster = clusterData.clusters[i];
				// Get the lat/lng of the marker to place
				var latLng = new google.maps.LatLng(cluster.lat,cluster.lng);
				if(cluster.count > 100) {
					var clusterImage = new google.maps.MarkerImage('/img/recycle55.png', new google.maps.Size(55, 55) );
					var clusterLabelAnchor = new google.maps.Point(28,37);
					var clusterLabelClass = "label55";
				} else if(cluster.count > 10) {
					var clusterImage = new google.maps.MarkerImage('/img/recycle45.png', new google.maps.Size(45, 45) );
					var clusterLabelAnchor = new google.maps.Point(23,31);
					var clusterLabelClass = "label45";
				} else {
					var clusterImage = new google.maps.MarkerImage('/img/recycle35.png', new google.maps.Size(35, 35) );
					var clusterLabelAnchor = new google.maps.Point(18,25);
					var clusterLabelClass = "label35";
				}
				
				var clusterMarker = new MarkerWithLabel({
					position: latLng,
					map: map,
					icon: clusterImage,
					draggable: true,
					labelContent: cluster.count,
					labelAnchor: clusterLabelAnchor,
					labelClass: clusterLabelClass
				});
				
				
				// Add the marker to our global array of currently visible markers
				allMarkers.push(clusterMarker);
				// Give each cluster an event that zooms and centers it.
				google.maps.event.addListener(clusterMarker, 'click', (function(clusterMarker, i) {
					return function() {
						map.setZoom(map.getZoom()+1);
						map.panTo(clusterMarker.position);
					} 
				})(clusterMarker, i));
			}
			
			for (var i = 0; i < singleOutletData.singleOutlets.length; i++) {
				var singleOutletMarker = singleOutletData.singleOutlets[i];
				// Get the lat/lng of the marker to place
				var latLng = new google.maps.LatLng(singleOutletMarker.lat,singleOutletMarker.lng);
				//var outletImage = new google.maps.MarkerImage('/img/outlet.png', new google.maps.Size(20, 20) );
				
				var singleOutletMarker = new google.maps.Marker({ position: latLng});
				// Add the marker, text to the memory.
				allMarkers.push(singleOutletMarker);
				singleOutletMarker.setMap(map);
				// Give each outlet an event that shows the info popup.
				google.maps.event.addListener(singleOutletMarker, 'click', (function(singleOutletMarker, i) {
					return function() {
						//$(location).attr('href',"/info/"+id);
						alert('You clicked an outlet with ID: '+singleOutletMarker.id);
					} 
				})(singleOutletMarker, i));
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
