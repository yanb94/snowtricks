$(document).ready(function () {

	var control = true;

	$('#show-media').click(function(e){

		if(control)
		{
			$('.my-list-media-tricks').attr('style','display:flex!important');
			control = false;
		}
		else
		{
			$('.my-list-media-tricks').removeAttr('style');	
			control = true;
		}

	});

	function exposeModal()
	{
		$('body').attr('style','height:100vh;overflow:hidden');
	}

	$('.my-img').click(function(e){
		
		var newElem = $("<img>").attr('src',$(this).attr('data-url')).addClass('my-img-modal');

		$('.my-modal-container').append(newElem);

		exposeModal()
		$('.my-modal-container').attr('style','display:flex!important');		

	});

	$('.my-video').click(function(e){
		
		var newElem = $($(this).attr('data-video')).addClass('my-video-modal');

		$('.my-modal-container').append(newElem);

		exposeModal()
		$('.my-modal-container').attr('style','display:flex!important');		

	});

});