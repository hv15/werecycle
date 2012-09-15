<html>
	<head>
		<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0,user-scalable=no,width=device-width" />
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<title>RecycleFinder</title>
		<link type="text/css" rel="stylesheet" href="styles/main.css"/>
		<link type="text/css" rel="stylesheet" href="styles/index.css"/>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	</head>
	<body>
		<div id="Index">
			<img id="Icon" src="graphics/logo.png"/>
			<div id="Name">weRecycle</div>
			<div id="Info"><p>Enter in your location or use geolocation.</p></div>
			<div id="GeoLocationButton" class="button" onclick="getGeoLocation()"><p>Use My Location</p></div>
			<div id="GeoName">
				<div id="GeoNameInput" class="input left"><input type="text"/></div>
				<div id="GeoNameButton" class="button left" onclick="getGeoName()"><p>Search</p></div>
			</div>
		</div>
	</body>
</html>