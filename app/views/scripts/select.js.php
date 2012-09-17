$('.category').each(function(index) { 
	var realindex = index+1;
	alert('realindex '+realindex);
	$('.category'+realindex).click(function() {
		alert('clicked!');
		$('category'+realindex+'Types').toggle("fast");
	});
});