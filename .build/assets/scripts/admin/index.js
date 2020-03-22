(function ($, plugin) {
	const $elements = $('.aoccssio-generate');
	if (!$elements.length) {
		return;
	}

	$(function () {

		const $wpbody = $('body #wpcontent');
		$wpbody.append('<div class="aoccssio-loader"></div>');
		const $loader = $wpbody.find('.aoccssio-loader');

		$elements.each(function () {
			const $e = $(this);
			const $url_input = $e.find('[name="aoccssio_url"]');
			const $trigger_generate = $e.find('#regenerate-criticalcss-aoccssio');
			const $trigger_delete = $e.find('#delete-criticalcss-aoccssio');

			$trigger_generate.on('click', function () {

				$url_input.removeClass('-error');
				const url = $url_input.val();

				if (!valid_url(url)) {
					$url_input.addClass('-error');
					$url_input.addClass('-pop');
					/*
					setTimeout(function () {
						$url_input.removeClass('-pop');
					}, settings_easing_speed);
					 */
					return false;
				}

				let vals = [];
				$e.find('input, textarea, select').each(function () {
					vals.push($(this).attr('data-aoccssio-name') + '=' + $(this).val());
				});

				const val = vals.join('&');
				$loader.fadeIn();

				$.ajax({
					url: plugin['AjaxURL'],
					type: 'POST',
					dataType: 'json',
					data: val
				}).done(function (data) {

					$loader.fadeOut();

					if (data['type'] === null || data['type'] !== 'success') {

						/**
						 * error
						 */

						let msg_content = data['message'];
						if (msg_content === '' || msg_content === undefined) {
							msg_content = 'error';
						}

						alert(msg_content);

					} else {

						/**
						 * success
						 */

						$e.find('.is_generated').text(data['add']['datetime']);
						$e.removeClass('aoccssio-generate--nofile');
					}
				});
			});

			$trigger_delete.on('click', function () {

				let vals = [];
				vals.push('action=' + $e.find('input[name=criticalapi_action_delete]').val());
				vals.push('critical_key=' + $e.find('input[name=criticalapi_key]').val());

				const val = vals.join('&');

				$e.removeClass('aoccssio-generate--file');
				$e.addClass('aoccssio-generate--nofile');

				$.ajax({
					url: vars['AjaxURL'],
					type: 'POST',
					dataType: 'json',
					data: val
				}).done(function (data) {

					if (data['type'] === null || data['type'] !== 'success') {

						let msg_content = data['message'];
						if (msg_content === '' || msg_content === undefined) {
							msg_content = 'error';
						}

						alert(msg_content);
					}
				});
			});
		});
	});

	function valid_url(url) {
		const pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
			'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
			'((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
			'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
			'(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
			'(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
		return pattern.test(url);
	}
})(jQuery, window.AoCriticalCSSVars);
