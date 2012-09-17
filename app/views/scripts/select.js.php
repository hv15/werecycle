recalculateSelected();

function recalculateSelected() {
	$('.category').each(function(index) {
		var newval = $(this).next().find('.typeCheckbox:checked').length;
		$(this).find('.selectedCount').text(newval);
	});
}


$('.category').click(function(index) { 
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});

$('.typeCheckbox').change(function() {
	recalculateSelected();
});

$('.typeInfoButton').click(function(index) { 
	$(this).next().toggle(); 
});