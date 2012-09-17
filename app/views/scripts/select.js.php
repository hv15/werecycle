recalculateSelected();
$('#debug').text($.cookie('recyclefinder_session'));
	
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

function readCookie(name) {
alert('reading cookie with name: '+name);
    name += '=';
    for (var ca = document.cookie.split(/;\s*/), i = ca.length - 1; i >= 0; i--)
        if (!ca[i].indexOf(name))
            return ca[i].replace(name, '');
}