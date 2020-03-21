<?php

namespace nicomartin\AoCriticalCSS;

class Plugin
{

	private static $name = '';
	private static $prefix = '';
	private static $version = '';
	private static $file = '';

	public static function initialize($file)
	{

		if ( ! function_exists('get_plugin_data')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$data = get_plugin_data($file);

		self::$prefix  = basename($file, '.php');
		self::$name    = $data['Name'];
		self::$version = $data['Version'];
		self::$file    = $file;
	}

	public static function name()
	{
		return self::$name;
	}

	public static function prefix()
	{
		return self::$prefix;
	}

	public static function version()
	{
		return self::$version;
	}

	public static function file()
	{
		return self::$file;
	}

	public static function baseUrl()
	{
		return get_home_url();
	}

	public static function apiBase()
	{
		return untrailingslashit(apply_filters('aoccssio/apiBaseURL', 'https://api.critical-css.io'));
	}
}
