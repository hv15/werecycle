	<div class="main-container">
            <div class="main wrapper clearfix">

		<img id="homepageIcon" src="img/logo.png" alt="logo"/>
		<div id="Name">RecycleFinder</div>
		<div id="GeoLocationButton" class="button" onclick="getGeoLocation()">
			<p>Use My Location</p>
			<div id="GeoLocationSpin" class="invisible"></div>
		</div>
		<div id="Info">
			<p>or</p>
		</div>
		<div id="GeoCode">
			<div id="GeoCodeInput" class="input left"><input type="text" id="GeoCodeInputField" placeholder="Enter location..."/></div>
			<div id="GeoCodeButton" class="button left" onclick="getGeoCode()"><p>Search</p></div>
		</div>        

            </div> <!-- #main -->
        </div> <!-- #main-container -->