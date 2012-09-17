function recalculateSelected() {
	$('.category').each(function(index) {
		$(this).children('.selectedCount').text($(this).next().children(':checked').length);
	});
}

$('.category').click(function(index) { 
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});

$('.typeCheckbox').change(function() {
	recalculateSelected();
});