<html>
	<head>
		<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0,user-scalable=no,width=device-width" />
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<title>RecycleFinder</title>
		<link type="text/css" rel="stylesheet" href="styles/main.css"/>
		<link type="text/css" rel="stylesheet" href="styles/index.css"/>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="scripts/index.js"></script>
	</head>
	<body>
		<div id="Index">
			<img id="Icon" src="graphics/logo.png"/>
			<div id="Name">weRecycle</div>
			<div id="Info"><p>Enter in your location or use geolocation.</p></div>
			<div id="GeoLocationButton" class="button" onclick="getGeoLocation()"><p>Use My Location</p></div>
			<div id="GeoCode">
				<div id="GeoCodeInput" class="input left"><input type="text" id="GeoCodeInputField" placeholder="Enter location..."/></div>
				<div id="GeoCodeButton" class="button left" onclick="getGeoCode()"><p>Search</p></div>
			</div>
		</div>
	</body>
</html>