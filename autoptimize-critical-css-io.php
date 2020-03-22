<?php

/**
 * Plugin Name: Autoptimize critical-css.io
 * Plugin URI: https://github.com/nico-martin/Advanced-WPPerformance
 * Description: This Plugin adds a way to generate CriticalCSS via critical-css.io
 * Author: Nico Martin
 * Version: 1.0.0
 * Author URI: https://nicomartin.ch
 * Text Domain: aoccssio
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

	include_once 'src/Plugin.php';
	include_once 'src/Helpers.php';
	include_once 'src/Assets.php';
	include_once 'src/Settings.php';
	include_once 'src/GeneratorFields.php';
	include_once 'src/Output.php';

	Plugin::initialize( __FILE__ );

	$aoccssioHelpers = new Helpers();
	$aoccssioHelpers->run();

	$aoccssioAssets = new Assets();
	$aoccssioAssets->run();

	$aoccssioSettings = new Settings();
	$aoccssioSettings->run();

	$aoccssioGeneratorFields = new GeneratorFields();
	$aoccssioGeneratorFields->run();

	$aoccssioOutput = new Output();
	$aoccssioOutput->run();
}
