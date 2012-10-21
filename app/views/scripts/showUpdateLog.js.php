$(document).ready(function(){
	var url = $('#output').html();
	var refreshId = setInterval(function() {
		$('#output').load(encodeURI(url));
	}, 4000);
});