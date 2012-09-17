$('.category').click(function(index) { 
	$(this).next().toggle("fast", function() {
		$(this).next().children().each(function(index) {
			$(this).toggle("fast");
		});
	}); 
});