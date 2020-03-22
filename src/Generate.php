<?php

namespace nicomartin\AoCriticalCSS;

class Generate
{

	public static $ajax_action = 'aoccssio_ajax_generate';
	public static $ajax_action_delete = 'aoccssio_ajax_delete';

	public function run()
	{
		add_action('wp_ajax_' . self::$ajax_action, [$this, 'ajaxGenerate']);
		add_action('wp_ajax_' . self::$ajax_action_delete, [$this, 'ajaxDelete']);
	}

	public function ajaxGenerate()
	{
		$url = esc_url($_POST['url']);
		if (strpos(untrailingslashit(get_site_url()), $url) != 0) {
			// translators: The requested URL is not a subpage of {url}
			Helpers::exitAjax('error', sprintf(__('The requested URL is not a subpage of %s', 'aoccssio'), untrailingslashit(get_site_url())));
		}

		$key  = sanitize_title($_POST['critical_key']);
		$dir  = Helpers::getCriticalDir();
		$file = $dir . $key . '.css';

		$css = self::fetchCss($url);
		if (201 != $css['status']) {
			// translators: Critical CSS could not be fetched: {message} ({status})
			$message = json_decode($css['message'], true);
			Helpers::exitAjax('error', sprintf(__('Critical CSS could not be fetched: %1$1s (%2$2s)', 'aoccssio'), $message['error'], $css['status']), $css);
		}

		$css_file = fopen($file, 'w');
		fwrite($css_file, $css['message']);
		fclose($css_file);

		if (isset($_POST['savepage']) && 'yes' == $_POST['savepage']) {
			$filesmatch = get_option(Helpers::$filesmatch_option);
			if ( ! is_array($filesmatch)) {
				$filesmatch = [];
			}
			$filesmatch[$key] = $url;
			update_option(Helpers::$filesmatch_option, $filesmatch);
		}

		$data = [
			'datetime' => Helpers::convertDate(),
			'option'   => Helpers::$filesmatch_option,
		];
		// translators: Critical CSS for "{key}" ({url}) generated
		Helpers::exitAjax('success', sprintf(__('Critical CSS for "%1$s" (%2$s) generated.', 'aoccssio'), $key, $url), $data);
	}

	public function ajaxDelete()
	{

		$key  = sanitize_title($_POST['critical_key']);
		$dir  = Helpers::getCriticalDir();
		$file = $dir . $key . '.css';
		unlink($file);

		Helpers::exitAjax('success', 'deleted');
	}

	public function fetchCss($url, $api_key = '')
	{

		if ('' == $api_key) {
			$api_key = Settings::getApiKey();
		}

		$atts = [
			'apiKey'     => $api_key,
			'url'        => $url,
			'dimensions' => Helpers::getDimensions(),
		];

		return Helpers::doPostRequest(Plugin::apiBase(), $atts);
	}
}
