$('.dropdown.open').removeClass('open');
    $('.dropdown-menu').hide();
	
$('.dropdown.open .dropdown-toggle').dropdown('toggle');
	
$('[data-toggle="dropdown"]').parent().removeClass('open');