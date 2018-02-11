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

	$('.my-remove-alert').click(function(e){
		var val = confirm("Etes vous sur de vouloir supprimer cette élément définitivement ?");

		if(val == false)
		{
			e.preventDefault();
		}
	})

});