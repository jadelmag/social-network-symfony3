$(document).ready(function () {


	var ias = jQuery.ias({
		container: '.profile-box #user-publications',
		item: '.publication-item',
		pagination: '.profile-box .pagination',
		next: '.profile-box .pagination .next_link',
		triggerPageThreshold: 5
	});

	ias.extension(new IASTriggerExtension({
		text: 'Mostrar más',
		offset: 3
	}));

	ias.extension(new IASSpinnerExtension({
		src: URL + '../assets/images/ajax-loader.gif'
//		src: 'http://localhost/curso-social-network/web/assets/images/ajax-loader.gif'
	}));

	ias.extension(new IASNoneLeftExtension({
		text: 'No hay más'
	}));

	ias.on('ready', function (event) {
		buttons();
		followButtons();
	});

	ias.on('rendered', function (event) {
		buttons();
	});
});


function buttons() {
	$('[data-toggle="tooltip"]').tooltip();

	$('.btn-img').unbind('click').click(function () {
		$(this).parent().find('.pub-image').fadeToggle();
	});

	$('.btn-delete-pub').unbind('click').click(function () {
		$(this).parent().parent().addClass('hidden');

		$.ajax({
			url: URL + '/publication/remove/' + $(this).attr('data-id'),
			type: 'GET',
			success: function (response) {
				console.log(response);
			}
		});
	});

	$('.btn-like').unbind('click').click(function () {
		$(this).addClass('hidden');
		$(this).parent().find('.btn-unlike').removeClass('hidden');

		$.ajax({
			url: URL + '/like/' + $(this).attr('data-id'),
			type: 'GET',
			success: function (response) {
				console.log(response);
			}
		});
	});

	$('.btn-unlike').unbind('click').click(function () {
		$(this).addClass('hidden');
		$(this).parent().find('.btn-like').removeClass('hidden');

		$.ajax({
			url: URL + '/unlike/' + $(this).attr('data-id'),
			type: 'GET',
			success: function (response) {
				console.log(response);
			}
		});
	});
}

function followButtons() {
	$('.btn-follow').unbind('click').click(function () {
		$(this).addClass('hidden');
		$(this).parent().find('.btn-unfollow').removeClass('hidden');
		
			$.ajax({
				url: URL + '/follow',
				type: 'POST',
				data: {followed: $(this).attr('data-followed')},
				success: function (response) {
					console.log(response);
				}
			});
	});

	$('.btn-unfollow').unbind('click').click(function () {
		$(this).addClass('hidden');
		$(this).parent().find('.btn-follow').removeClass('hidden');
		
			$.ajax({
				url: URL + '/unfollow',
				type: 'POST',
				data: {followed: $(this).attr('data-followed')},
				success: function (response) {
					console.log(response);
				}
			});
	});
}