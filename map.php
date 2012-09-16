<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0,user-scalable=no,width=device-width" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>RecycleFinder!</title>
		<link type="text/css" rel="stylesheet" href="styles/main.css"/>
		<link type="text/css" rel="stylesheet" href="styles/map.css"/>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript" src="scripts/mapFunctions.js"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/geolocationmarker/src/geolocationmarker-compiled.js"></script>
		<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer_compiled.js"></script>
		<script type="text/javascript">
	 		var map_zoom = <?php echo (isset($_GET['zoom'])) ? $_GET['zoom'] : 12 ?>;
	 		var map_lat  = <?php echo (isset($_GET['latitude'])) ? $_GET['latitude'] : 55.9099 ?>;
	 		var map_lon  = <?php echo (isset($_GET['longitude'])) ? $_GET['longitude'] : -3.3220 ?>;
	 		var map_pos  = new google.maps.LatLng(map_lat, map_lon);
	 		var types  = '<?php echo (isset($_GET['types'])) ? $_GET['types'] : '1,13,16,17,2,29,41,6,7' ?>';
			
			google.maps.event.addDomListener(window, 'load', initialize);
		</script>
	</head>
	<body>
		<div id="MapContainer"><div id="Map"></div></div>
		<div id="ButtonZoomIn" class="button roundTop" onclick="zoomIn()"><p>+</p></div>
		<div id="ButtonZoomOut" class="button roundBottom" onclick="zoomOut()"><p>&#8211;</p></div>
		<div id="ButtonLocation" class="button inactive" onclick="toggleLocation()"><p><img src="graphics/location.png" width="22"/></p></div>
		<a href="./help.php"><div id="ButtonHelp" class="button"><p>?</p></div></a>
		<a href="#" onclick="buttonSelect();"><div id="ButtonSelect" class="button"><p>Select Recyclables...</p></div></a>
	</body>
</html>
