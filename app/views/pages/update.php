<div id="output">Lipsum</div>
<script>
$(document).ready(function(){
   var refreshId = setInterval(function() {
		$('#output').load('/update/getLog/'+encodeURI('<?=$outputPath?>') );
   }, 4000);
});
</script>