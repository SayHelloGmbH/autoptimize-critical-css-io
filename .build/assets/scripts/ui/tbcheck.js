(function ($, plugin) {
	$(function () {
		const $input = $('.aoccssio-tbcheck__input');
		const $label = $('.aoccssio-tbcheck__label');
		const $styles = $('[data-loadcss="true"]');
		$input.prop('checked', false);

		$input.on('change', function () {
			const disable = $input.prop('checked');
			$styles.attr('rel', disable ? 'none' : 'stylesheet');
			$label.attr('data-disable', disable);
			console.log(disable);
			if (disable) {
				$('#wp-admin-bar-autoptimize-cache-info, #wp-admin-bar-autoptimize-delete-cache').hide();
			} else {
				$('#wp-admin-bar-autoptimize-cache-info, #wp-admin-bar-autoptimize-delete-cache').show();
			}
		});
	});
})(jQuery, window.AoCriticalCSSVars);
