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
		if ($url === '' || strpos(untrailingslashit(get_site_url()), $url) != 0) {
			// translators: The requested URL is not a subpage of {url}
			Helpers::exitAjax('error', sprintf(__('The requested URL is not a subpage of %s', 'aoccssio'), untrailingslashit(get_site_url())));
		}

		$key  = sanitize_title($_POST['critical_key']);
		$dir  = Helpers::getCriticalDir();
		$file = $dir . $key . '.css';

		$css = self::fetchCss($url);
		if (is_wp_error($css)) {
			Helpers::exitAjax('error', $css->get_error_message());
		}

		$css_file = fopen($file, 'w');
		fwrite($css_file, $css);
		fclose($css_file);

		$filesmatch = get_option(Helpers::$filesmatch_option);
		if ( ! is_array($filesmatch)) {
			$filesmatch = [];
		}
		$filesmatch[$key] = $url;
		update_option(Helpers::$filesmatch_option, $filesmatch);

		$data = [
			'datetime' => Helpers::convertDate(),
			'option'   => Helpers::$filesmatch_option,
			'css'      => $css,
			'cssFile'  => $css_file,
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
			'token'      => $api_key,
			'url'        => $url,
			'dimensions' => Helpers::getDimensions(),
		];

		$request = Helpers::doPostRequest(Plugin::apiBase(), $atts);
		if (201 != $request['status']) {
			$message = json_decode($request['message'], true);

			return new \WP_Error('ccss-request-failed', sprintf(__('Critical CSS for %1$1s could not be fetched: %2$2s (%3$3s)', 'aoccssio'), $url, $message['error'], $request['status']));
		}


		return $request['message'];
	}
}
