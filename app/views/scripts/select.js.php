recalculateSelected();

function recalculateSelected() {
	$('.category').each(function(index) {
		var newval = $(this).next().find('.typeCheckbox:checked').length;
		$(this).find('.selectedCount').text(newval);
	});
}

function setTypes() {
	var types = '';
	$('.typeCheckbox:checked').each(function(){
		types = $(this).val() + ',' + typescommas;
	});
	types = types.slice(0, - 1);
	
	var newSessionData = encodeURIComponent('{"types_selected":"'+typescommas+'"}');
	$.get('/setsession/'+newSessionData, function(setSessionResponse){
		$.get('/check/'+urlRand, function(checkResponse){
			eval(checkResponse);
			alert(check['code']);
			alert(check['message']);
			window.location.href = '/map';
		});
	});
}

$('.category').click(function(index) { 
	if(!$(this).hasClass("open"))
		$(this).addClass("open");
	else
		$(this).removeClass("open");
	$(this).next().slideToggle('slow');
});

$('.typeInfoButton').click(function(index) { 
	$(this).next().slideToggle('slow'); 
});

$('.typeCheckbox').change(function() {
	recalculateSelected();
});
