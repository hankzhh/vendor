function showFaceBook(face)
	{
		$(face).parent().parent().children('.face-comment').toggle();
	}          
	jQuery(document).ready(function() {
		lazyLoad();
	 	/*var allow = true;
	 	var page = 2;
	 	var imgLoadingSalon = $('<div>', {id: 'image-load-salon'}).addClass('col-sm-12 text-center').html('<img src="/skin/frontend/salore/default/images/load-vertical.gif" style="text-align: center"/>');
		$(window).scroll(function(){
		    if ( ($(window).scrollTop() >= $(document).height() - ($(window).height() + 200) ) && allow !== false && page <= pageTotal) {
			    allow = false;
			    $('.content').append(imgLoadingSalon);
		       $.ajax({
					url: location.protocol + "//" + location.host + '/salon/index/pagination',
					type: 'get',
					datatype: 'json',
					data: {p: page},
					success: function(response){
						$(imgLoadingSalon).remove();
						$('.content').append(response);
						allow = true;
						page++;
						lazyLoad();
					}
			   });
		    }
		});*/
		
	}); 
	var lazyLoad = function()
	{
		jQuery("img.lazy").lazy({
		    effect: "fadeIn",
		    effectTime: 1500,
		    bind: "event"
		});
	}