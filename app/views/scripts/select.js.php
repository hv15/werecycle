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
		types = $(this).val() + ',' + types;
	});
	types = types.slice(0, - 1);
	
	var newSessionData = encodeURIComponent('{"types_selected":"'+types+'"}');
	$.get('/setsession/'+newSessionData, function(setSessionResponse){
		var urlRand = Math.random();
		$.get('/check/'+urlRand, function(checkResponse){
			eval(checkResponse);
			switch(check['code']) {
				case 2:
					window.location.href = '/map';
				break;
				case 10:
					alert(check['message']);
					window.location.href = '/map';
				break;
				case 30:
					alert(check['message']);
					window.location.href = '/map';
				break;
				case 50:
					alert(check['message']);
					window.location.href = '/map';
				break;
				case 500:
					alert(check['message']);
					window.location.href = '/map';
				break;
				case 0:
					alert(check['message']);
				break;
				default:
					alert('Error, please try again from the start');
			}
			
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
