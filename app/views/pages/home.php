	<div class="main-container">
            <div class="main wrapper clearfix">
		<div class="flashMessage"><?php echo $this->session->flashdata('message'); ?></div>
		<a onclick="window.location.href='/help'" class="button" id="help">?</a>
		<img id="logo" src="img/logo.png" alt="logo"/>
		<div id="name">RecycleFinder</div>
		<div id="geoLocationButton" class="button" onclick="getGeoLocation()">
			<p>Use My Location</p>
			<div id="geoLocationSpin" class="invisible"></div>
		</div>
		<div id="info">
			<p>or</p>
		</div>
		<div id="geoCode">
			<div id="geoCodeInput" class="input left">
				<input type="text" id="geoCodeInputField" placeholder="Enter location..."/>
			</div>
			<div id="geoCodeButton" class="button left" onclick="getGeoCode()">
				<p>Search</p>
			</div>
		</div>        
            </div> <!-- #main -->
        </div> <!-- #main-container -->