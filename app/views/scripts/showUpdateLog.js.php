$(document).ready(function(){
	var url = $('#updateLogOutput').html();
	var refreshId = setInterval(function() {
		$('#updateLogOutput').load(encodeURI(url));
		$("#updateLogOutput").attr({ scrollTop: $("#updateLogOutput").attr("scrollHeight") });
	}, 3000);
});