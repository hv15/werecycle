<html>
	<head>
		<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0,user-scalable=no,width=device-width" />
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<title>RecycleFinder : Select Recyclables</title>
		<link type="text/css" rel="stylesheet" href="styles/main.css"/>
		<link type="text/css" rel="stylesheet" href="styles/select.css"/>
		<script type="text/javascript" src="scripts/selectables.js"></script>
		<script type="text/javascript" src="scripts/selectablesInfo.js"></script>
		<script type="text/javascript" src="scripts/select.js"></script>
	</head>
	<body onload="loadSelectables(selectables,selectablesInfo);">
		<div id="Select">
			<div id="Info"><p>Select the items you want to recycle</p></div>
			<div id="Container"></div>
			<div id="SelectButton" class="button left" onclick="createQuery();"><p>Search recycling facilities...</p></div>
		</div>
	</body>
</html>