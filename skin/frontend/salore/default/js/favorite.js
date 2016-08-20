var cookieArr = [];
$.fn.slfavorite = function(aTarget, e) {
	//ajax save favourite
	var salonUrl = $(aTarget).attr('id');
	var salonName = $(aTarget).parent().prev().children().first().find('strong').text();
	var salonLogo = $(aTarget).parent().prev().prev().find('img').attr('src');
	this.children('em').css( "background", "url(/skin/frontend/salore/default/images/ajax-loader.gif) no-repeat scroll 8px 9px");
	$.ajax({
		url: location.protocol + "//" + location.host + '/salon/favourite/addfavourite',
		dataType: 'json',
		data: {salonUrl: salonUrl, salonName: salonName, salonLogo: salonLogo},
		method: 'POST',
		success: function(response){
			if(response.status === 'COOKIE')
			{
				//cookieArr.push(salonName);
				//Mage.Cookies.set('salore_favourite', cookieArr);
				addFavourite(salonUrl, salonName, salonLogo);
			}
			else if(response.status == 'SUCCESS')
			{
				addFavourite(salonUrl, salonName, salonLogo);
			}
			$('.dropdown-my-favorites').addClass('open');
			$(aTarget).children('em.lovest').css('background', '');
		},
	});
	
	e.preventDefault();
};
$('.btn-bonus').find('a').each(function(item){
	item.slfavorite();
});

function addFavourite(salonUrl, salonName, salonLogo)
{
	$('.favorites-empty').hide();
	var liEle = $('<li>').addClass('item-favour').css('position', 'relative');
	var aEle = $('<a>').attr({'id': salonUrl, 'href': location.protocol + "//" + location.host + '/' + salonUrl}).css({'padding-left': '40px', 'white-space': 'normal'}).html(salonName);
	var emEle = $('<em>').css({'margin-left': '7px', 'height': '100%', 'width': '39', 'position': 'absolute', 'top': '0', 'left': '0', 'background': 'url('+salonLogo+') no-repeat center center'});
	var closeEle = $('<span>').addClass('close-icon close').html('<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>');
	aEle.append(emEle);
	liEle.append(aEle, closeEle);
	$('.favorites-list').append(liEle);
	$(closeEle).bind('click',function(event){$(this).removeFavour(this, event)});
}
$.fn.removeFavour = function(arg, event){
	var salonUrl = $(arg).prev().attr('id');
	$.ajax({
		url: location.protocol + "//" + location.host + '/salon/favourite/delete',
		method: 'post',
		dataType: 'json',
		data: {salonUrl: salonUrl},
		success: function(response){
			if(response.status === 'SUCCESS'){
				$(arg).parent().remove();
			}
			if(!$('.item-favour').html()) {
				$('.favorites-empty').show();
			} 
		}
	});
	event.stopPropagation();
}
