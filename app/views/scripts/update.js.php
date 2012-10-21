$(document).ready(function(){
   var refreshId = setInterval(function() {
		$('#output').load('/update/getLog/recycleForScotland');
   }, 4000);
});