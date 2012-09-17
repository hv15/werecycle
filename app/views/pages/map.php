	<div id="MapContainer"><div id="Map"></div></div>
	<div id="ButtonZoomIn" class="button roundTop" onclick="zoomIn()"><p>+</p></div>
	<div id="ButtonZoomOut" class="button roundBottom" onclick="zoomOut()"><p>&#8211;</p></div>
	<div id="ButtonLocation" class="button inactive" onclick="toggleLocation()"><p><img src="/img/location.png" width="22"/></p></div>
	<a href="./help.php"><div id="ButtonHelp" class="button"><p>?</p></div></a>
	<a href="#" onclick="buttonSelect();"><div id="ButtonSelect" class="button"><p>Select Recyclables...</p></div></a>
	
	<script type="text/javascript" src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/geolocationmarker/src/geolocationmarker-compiled.js"></script>
	<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer_compiled.js"></script>
	<script type="text/javascript">
		var map_zoom = <?php echo (isset($_GET['zoom'])) ? $_GET['zoom'] : 12 ?>;
		var map_lat  = <?php echo (isset($_GET['latitude'])) ? $_GET['latitude'] : 55.9099 ?>;
		var map_lon  = <?php echo (isset($_GET['longitude'])) ? $_GET['longitude'] : -3.3220 ?>;
		var map_pos  = new google.maps.LatLng(map_lat, map_lon);
		var types  = '<?php echo (isset($_GET['types'])) ? $_GET['types'] : '6,7,2,16,1' ?>';
		
		google.maps.event.addDomListener(window, 'load', initialize);
	</script>