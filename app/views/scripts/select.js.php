$('.category').click(function(index) { 
	$(this).next().toggle("fast", function() {
		$(this).children().each(function(index) {
			$(this).toggle("fast");
		});
	}); 
});