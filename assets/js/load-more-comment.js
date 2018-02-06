$(document).ready(function () {

	var page = parseInt($('#load-more').attr('data-page'));
	var url = $('#load-more').attr('data-url').split('/',4);
	url = url.join('/');

	$('#load-more').click(function(e){

		$.ajax({
			url: url+'/'+page,
			type: 'GET',
			success: function(data){
				$('#all-comment').append(data);
				page++;
			}
		});

	});

});