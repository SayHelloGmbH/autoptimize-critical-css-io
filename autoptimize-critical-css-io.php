<?php

/**
 * Plugin Name: Autoptimize critical-css.io
 * Plugin URI: https://github.com/nico-martin/Advanced-WPPerformance
 * Description: This Plugin adds a way to generate CriticalCSS via critical-css.io
 * Author: Nico Martin
 * Version: 1.0.0
 * Author URI: https://nicomartin.ch
 * Text Domain: hello-aoccss
 * Domain Path: /languages
 */

namespace nicomartin\AoCriticalCSS;

global $wp_version;
if (version_compare($wp_version, '4.7', '<') || version_compare(PHP_VERSION, '5.4', '<')) {
	function aoccssCompatabilityWarning()
	{
		echo '<div class="error"><p>';
		// translators: Dependency waring
		echo sprintf(__('“%1$s” requires PHP %2$s (or newer) and WordPress %3$s (or newer) to function properly. Your site is using PHP %4$s and WordPress %5$s. Please upgrade. The plugin has been automatically deactivated.', 'awpp'), 'Autoptimize critical-css.io', '5.3', '4.7', PHP_VERSION, $GLOBALS['wp_version']);
		echo '</p></div>';
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}
	}

	add_action('admin_notices', 'aoccssCompatabilityWarning');

	function aoccssDeactivateSelf()
	{
		deactivate_plugins(plugin_basename(__FILE__));
	}

	add_action('admin_init', 'aoccssDeactivateSelf');

	return;

} else {

	require_once 'src/Plugin.php';
	Plugin::initialize( __FILE__ );

	include_once 'src/Assets.php';
	$Assets = new Assets();
	$Assets->run();

	include_once 'src/Settings.php';
	$aoccssSettings = new Settings();
	$aoccssSettings->run();

	include_once 'src/Output.php';
	$aoccssOutput = new Output();
	$aoccssOutput->run();
}
