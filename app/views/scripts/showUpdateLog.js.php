$(document).ready(function(){
	var url = $('#updateLogOutput').html();
	var refreshId = setInterval(function() {
		$('#updateLogOutput').load(encodeURI(url));
		var psconsole = $('#updateLogOutput');
		psconsole.scrollTop(
			psconsole[0].scrollHeight - psconsole.height()
		);
	}, 3000);
});