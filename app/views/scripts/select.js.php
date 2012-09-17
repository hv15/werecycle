$('.category').each(function(index) { 
	var realindex = index+1;
	$('.category'+realindex).click(function() {
		$('category'+realindex+'Types').toggle("fast");
	});
});