recalculateSelected();
console.log($.cookie('recyclefinder_session', { json: true }));
	
function recalculateSelected() {
	$('.category').each(function(index) {
		var newval = $(this).next().find('.typeCheckbox:checked').length;
		$(this).find('.selectedCount').text(newval);
	});
}

function createQuery() {
	var types = '';
	$('.typeCheckbox:checked').each(function(){
		types = $(this).val() + '/' + types;
	});
	window.location.href = '/map/'+types;
}

$('.category').click(function(index) { 
	$(this).next().toggle(); 
	$(this).next().children().toggle();
});

$('.typeCheckbox').change(function() {
	recalculateSelected();
});
