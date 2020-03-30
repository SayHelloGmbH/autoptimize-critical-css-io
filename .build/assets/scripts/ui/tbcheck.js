(function ($, plugin) {
	$(function () {
		const $input = $('#aoccssio-tbcheck');
		const $label = $('[for=aoccssio-tbcheck]');
		const $styles = $('[data-loadcss="true"]');
		$input.prop('checked', false);

		$input.on('change', function () {
			const disable = $input.prop('checked');
			$styles.attr('rel', disable ? 'none' : 'stylesheet');
			$label.attr('data-disable', disable);
		});
	});
})(jQuery, window.AoCriticalCSSVars);
