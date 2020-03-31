<?php

namespace nicomartin\AoCriticalCSS;

class Output
{
	public $base_path = '';
	public $base_url = '';
	public $default_critical_path = '';

	public $options = '';

	public function __construct()
	{

		$this->base_path             = ABSPATH;
		$this->base_url              = trailingslashit(get_site_url());
		$this->default_critical_path = trailingslashit(WP_CONTENT_DIR) . 'cache/awpp/critical/' . get_current_blog_id() . '/';

		if ( ! file_exists($this->default_critical_path)) {
			mkdir($this->default_critical_path, 0777, true);
		}
	}

	public function run()
	{
		if (Helpers::ccssEnabled() && Settings::getApiKey()) {
			add_action('wp_head', [$this, 'addCriticalCss'], 1);
			add_action('admin_bar_menu', [$this, 'addToolbarItem'], 101);
		}
	}

	/**
	 * Toolbar
	 */

	public function addToolbarItem($wp_admin_bar)
	{
		if (is_admin() || ! Settings::getApiKey()) {
			return;
		}

		$html = '';
		$html .= '<input type="checkbox" id="aoccssio-tbcheck" class="aoccssio-tbcheck__input" />';
		$html .= '<label for="aoccssio-tbcheck" class="aoccssio-tbcheck__label">';
		$html .= __('Disable CSS', 'aoccssio');
		$html .= '</label>';

		$args = [
			'id'     => 'aoccssio-admin-bar-item',
			'parent' => 'autoptimize',
			'title'  => __(' Critical CSS', 'aoccssio') . ' ⚡',
			'href'   => '',
			'meta'   => [
				'class' => 'wp-admin-bar-aoccssio-tbcheck',
				'html'  => '<div class="ab-item ab-empty-item aoccssio-tbcheck">' . $html . '</div>'
			],
		];

		$wp_admin_bar->add_node($args);
	}

	/**
	 * Header Output
	 */

	public function addCriticalCss()
	{
		$path = Helpers::getCriticalDir();

		$critical_id = '';
		$ids         = array_reverse(Helpers::getCriticalKeys());
		foreach ($ids as $id) {
			if (file_exists($path . $id . '.css')) {
				$critical_id = $id;
				break;
			}
		}

		$content = '';
		$content .= "/*\n";
		$content .= "Critical CSS: set by Autoptiize CriticalCSS.io.\n";

		$ccss_path = $path . $critical_id . '.css';
		$ccss_url  = str_replace(trailingslashit(ABSPATH), trailingslashit(get_site_url()), $ccss_path);

		if (is_user_logged_in()) {
			$content .= "\nDebug Information (for logged in users):\n";
			$content .= "- Critical ID: $critical_id\n";
			$content .= "- File: $ccss_url\n";
			if ( ! file_exists($ccss_path)) {
				$content .= "\nError: File does not exist!\n";
			}
		}
		if ('' == $critical_id) {
			$content .= "\nError: Critical CSS File not found!\n";
			$content .= "*/\n";
		} else {
			$content .= "*/\n";
			if (file_exists($ccss_path)) {
				$content .= file_get_contents($ccss_path);
			}
		}

		echo "<style type='text/css' id='criticalCSS' media='all'>\n{$content}\n</style>\n";
	}
}
