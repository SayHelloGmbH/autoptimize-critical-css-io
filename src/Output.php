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
		if (Helpers::ccssEnabled()) {
			//add_action('admin_bar_menu', [$this, 'addToolbarItem']);
			add_action('wp_head', [$this, 'addCriticalCss'], 1);
		}
	}

	/**
	 * Toolbar
	 */

	public function addToolbarItem($wp_admin_bar)
	{

		$html = '';
		$html .= '<input type="checkbox" id="awpp-check-criticalcss" />';
		$html .= '<label for="awpp-check-criticalcss">';
		$html .= __('Test Critical CSS', 'awpp');
		$html .= '<span class="_info -on">(on)</span>';
		$html .= '<span class="_info -off">(off)</span>';
		$html .= '</label>';

		$args = [
			'id'     => awpp_get_instance()->Init->admin_bar_id . '-criticalcss',
			'parent' => awpp_get_instance()->Init->admin_bar_id,
			'title'  => __('Critical CSS', 'awpp'),
			'href'   => '',
			'meta'   => [
				'class' => awpp_get_instance()->prefix . '-adminbar-criticalcss ' . (awpp_get_setting('deliverycss') ? '' : 'disabled'),
			],
		];

		if (awpp_is_frontend() && awpp_get_setting('deliverycss')) {
			$args['meta']['html'] = '<div class="ab-item ab-empty-item">' . $html . '</div>';
		}
		$wp_admin_bar->add_node($args);
	}

	/**
	 * Header Output
	 */

	public function addCriticalCss()
	{
		// todo: check if AO setting set

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
