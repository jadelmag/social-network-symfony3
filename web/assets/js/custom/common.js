
$(document).ready(function () {


	if ($('.label-notifiactions').text() == 0) {
		$('.label-notifiactions').addClass('hidden');
	} else {
		$('.label-notifiactions').removeClass('hidden');
	}

	if ($('.label-notifiactions-msg').text() == 0) {
		$('.label-notifiactions-msg').addClass('hidden');
	} else {
		$('.label-notifiactions-msg').removeClass('hidden');
	}

	setInterval(function () {
		notifications();
	}, 60000);
});

function notifications() {

	$.ajax({
		url: URL + '/notifications/get',
		type: 'GET',
		success: function (response) {
			$('.label-notifiactions').html(response);

			if (response == 0) {
				$('.label-notifiactions').addClass('hidden');
			} else {
				$('.label-notifiactions').removeClass('hidden');
			}

		}
	});


	$.ajax({
		url: URL + '/private_message/notification/get',
		type: 'GET',
		success: function (response) {
			$('.label-notifiactions-msg').html(response);

			if (response == 0) {
				$('.label-notifiactions-msg').addClass('hidden');
			} else {
				$('.label-notifiactions-msg').removeClass('hidden');
			}

		}
	});

}