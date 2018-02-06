$(document).ready(function () {

	$('body').on('click', '.my-modal-container', function(e){

		$('.my-modal-container').hide();
		$('.my-modal-container').children().last().remove();
		$('body').removeAttr('style');

	});

	$('body').on('click', '.my-modal-container > * ', function(e){
		 e.stopPropagation();
	});

	$('.my-close-modal').click(function(e)
	{
		$('.my-modal-container').hide();
		$('.my-modal-container').children().last().remove();
		$('body').removeAttr('style');
	});

});