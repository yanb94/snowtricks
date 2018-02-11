$(document).ready(function () {

	var page = parseInt($('#load-more-trick').attr('data-page'));
	var url = $('#load-more-trick').attr('data-url');

	$('#load-more-trick').click(function(e){

		$.ajax({
			url: url+'/'+page,
			type: 'GET',
			success: function(data){
				$('#all-trick').append(data);
				page++;
			}
		});		

	});

});