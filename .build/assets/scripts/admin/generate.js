(function ($, plugin) {
	$(function () {
		const $elements = $('.aoccssio-generate');
		if (!$elements.length) {
			return;
		}

		const $wpbody = $('body #wpcontent');
		$wpbody.append('<div class="aoccssio-loader"></div>');
		const $loader = $('.aoccssio-loader');
		const $wp_spinner = $('<img src="./images/spinner.gif" />');
		const $wp_loading = $('<img src="./images/loading.gif" style="margin-right: 5px;" />');


		$elements.each(function () {
			const $e = $(this);
			const $urlInput = $e.find('[name="aoccssio_url"]');
			const $triggerGenerate = $e.find('.aoccssio-generate__regenerate');
			const $triggerDelete = $e.find('.aoccssio-generate__delete');
			const $triggerControls = $e.find('.aoccssio-generate__controls');
			const $triggerStatus = $e.find('.aoccssio-generate__status');

			const genLoading = function (loading) {
				if (loading) {
					$urlInput.attr('disabled', true);
					$triggerDelete.add($triggerGenerate).add($triggerStatus).hide();
					$triggerControls.append($wp_spinner);
					$wp_loading.insertBefore($triggerStatus);
				} else {
					$urlInput.attr('disabled', false);
					$triggerDelete.add($triggerGenerate).add($triggerStatus).show();
					$wp_spinner.add($wp_loading).remove();
				}
			};

			$triggerGenerate.on('click', function () {

				const url = $urlInput.val();

				if (!valid_url(url)) {
					$urlInput.addClass('aoccssio-generate__input--error-pop');
					setTimeout(function () {
						$urlInput.removeClass('aoccssio-generate__input--error-pop');
					}, 200);
					return false;
				}

				genLoading(true);
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

					genLoading(false);

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

						$triggerStatus.text(data['add']['datetime']);
						$e.removeClass('aoccssio-generate--nofile');
					}

				});
			});

			$triggerDelete.on('click', function () {

				genLoading(true);
				let vals = [];
				vals.push('action=' + $e.find('input[name=aoccssio_action_delete]').val());
				vals.push('critical_key=' + $e.find('input[name=aoccssio_key]').val());

				const val = vals.join('&');

				$e.removeClass('aoccssio-generate--file');
				$e.addClass('aoccssio-generate--nofile');

				$.ajax({
					url: plugin['AjaxURL'],
					type: 'POST',
					dataType: 'json',
					data: val
				}).done(function (data) {

					genLoading(false);

					if (data['type'] === null || data['type'] !== 'success') {

						let msg_content = data['message'];
						if (msg_content === '' || msg_content === undefined) {
							msg_content = 'error';
						}

						alert(msg_content);
					} else {
						$triggerStatus.text(data['add']['text']);
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
