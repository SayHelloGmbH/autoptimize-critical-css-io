<?php

namespace nicomartin\AoCriticalCSS;

class Assets
{
	public function run()
	{
		add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
		add_action('admin_enqueue_scripts', [$this, 'registerAdminAssets']);
	}

	public function registerAssets()
	{
		$script_version = Plugin::version();

		$min = true;
		if (is_user_logged_in()) {
			$min = false;
		}

		$dir_uri = plugin_dir_url(Plugin::file());

		wp_enqueue_style(Plugin::prefix() . '-style', $dir_uri . 'assets/styles/ui' . ($min ? '.min' : '') . '.css', [], $script_version);
		wp_enqueue_script(Plugin::prefix() . '-script', $dir_uri . 'assets/scripts/ui' . ($min ? '.min' : '') . '.js', ['jquery'], $script_version, false);

		/**
		 * JS Vars
		 */
		$defaults = [
			'AjaxURL'   => admin_url('admin-ajax.php'),
			'homeurl'   => get_home_url(),
			'pluginurl' => $dir_uri,
		];

		$vars = json_encode(apply_filters(Plugin::prefix() . '_js_vars', $defaults));
		wp_add_inline_script(Plugin::prefix() . '-script', "var AoCriticalCSSVars = {$vars};", 'before');
	}

	public function registerAdminAssets()
	{
		$script_version = Plugin::version();

		$min = true;
		if (is_user_logged_in()) {
			$min = false;
		}

		$dir_uri = plugin_dir_url(Plugin::file());

		wp_enqueue_style(Plugin::prefix() . '-admin-style', $dir_uri . 'assets/styles/admin' . ($min ? '.min' : '') . '.css', [], $script_version);
		wp_enqueue_script(Plugin::prefix() . '-admin-script', $dir_uri . 'assets/scripts/admin' . ($min ? '.min' : '') . '.js', ['jquery'], $script_version, true);

		/**
		 * Admin JS Vars
		 */
		$defaults = [
			'AjaxURL'   => admin_url('admin-ajax.php'),
			'homeurl'   => get_home_url(),
			'pluginurl' => $dir_uri,
		];

		$vars = json_encode(apply_filters(Plugin::prefix() . '_admin_js_vars', $defaults));
		wp_add_inline_script(Plugin::prefix() . '-admin-script', "var AoCriticalCSSVars = {$vars};", 'before');
	}
}
