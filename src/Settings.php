<?php

namespace nicomartin\AoCriticalCSS;

class Settings
{

	private static $key = 'aoccssio';
	private static $optionsPage = 'aoccssio_options';
	private static $settingAPIKey = 'critcalcssio_apikey';
	private static $title = 'Autoptimize critical-css.io';

	public function __construct()
	{
	}

	public function run()
	{
		if (self::getApiKey()) {
			add_filter('aoccssio/criticalDir', [$this, 'changeCriticalDir'], 99);
		}
		add_filter('autoptimize_filter_settingsscreen_tabs', [$this, 'addTab'], 99);
		add_action('admin_menu', [$this, 'adminMenu']);
		add_action('admin_init', [$this, 'settingsInit']);
	}

	public function changeCriticalDir($dir)
	{
		return Helpers::getCriticalDir();
	}

	public function addTab($tabs)
	{
		unset($tabs['ao_critcss']);
		return array_merge($tabs, [self::$optionsPage => __('⚡ critical-css.io', 'aoccssio')]);
	}

	public function adminMenu()
	{
		add_submenu_page(null, self::$title, self::$title, 'manage_options', self::$optionsPage, [$this, 'settingsPage']);
	}

	public function settingsInit()
	{
		add_settings_section(
			self::$key . '-section',
			'CriticalCSS API Key',
			function () {
			},
			self::$optionsPage
		);

		add_settings_field(
			self::$settingAPIKey,
			'API Key',
			function () {
				$key = $this->getApiKey();
				echo '<input id="' . self::$settingAPIKey . '" type="text" name="' . self::$settingAPIKey . '" placeholder="' . __('Your API key', 'aoccssio') . '" value="' . $key . '" />';
				echo '<p>' . sprintf(__('API Key for %s', 'aoccssio'), '<b>' . Plugin::apiBase() . '</b>') . '</p>';
			},
			self::$optionsPage,
			self::$key . '-section'
		);

		register_setting(
			self::$optionsPage,
			self::$settingAPIKey,
			function ($key) {
				$key = str_replace(' ', '', $key);
				if ($key === '') {
					return '';
				}

				if ( ! $this->validateApiKey($key)) {
					add_settings_error(
						self::$settingAPIKey,
						'invalid',
						sprintf(__('The API Key is invalid or does not match the URL %s', 'aoccssio'), Plugin::baseUrl()),
						'error'
					);

					return '';
				}

				return str_replace(' ', '', $key);
			}
		);
	}

	public function settingsPage()
	{
		?>
		<div class="wrap">
			<div id="autoptimize_main">
				<div id="ao_title_and_button">
					<h1><?php _e('Autoptimize Settings', 'autoptimize'); ?></h1>
				</div>
				<?php
				echo \autoptimizeConfig::ao_admin_tabs();
				?>
				<?php settings_errors(); ?>
				<?php
				if (self::getApiKey()) {
					?>
					<div class="aoccssio-settings">
						<?php do_action('aoccssio/generatorFields') ?>
					</div>
					<?php
				} else {
					?>
					<div class="aoccssio-intro">
						<h2><?php _e('Critical CSS API', 'aoccssio'); ?></h2>
						<p>
							<?php printf(__('Critical CSS API is an open source tool to generate the Critical CSS of a given URL: %s', 'aoccssio'), '<a href="https://github.com/nico-martin/critical-css-api" target="_blank">https://github.com/nico-martin/critical-css-api</a>'); ?><br/>
							<?php printf(__('By default this plugin uses the hosted version on %1s. But you could also use a self-hosted version by adjusting the API Base URL using the %s-filter.', 'aoccssio'), '<a href="https://app.critical-css.io" target="_blank">app.critical-css.io</a>', '<code>aoccssio/apiBaseURL</code>'); ?>
						</p>
						<p>
							<?php printf(__('Current base URL: %s', 'aoccssio'), '<b>' . Plugin::apiBase() . '</b>'); ?>
						</p>
					</div>
					<?php
				}
				?>
				<form id="settings" method="post" action="options.php" class="aoccssio-key">
					<table class="form-table">
						<?php
						settings_fields(self::$optionsPage);
						do_settings_fields(self::$optionsPage, self::$key . '-section');
						?>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
		<?php
	}

	public static function getApiKey()
	{
		return get_option(self::$settingAPIKey);
	}

	public function validateApiKey($key = false)
	{
		if ( ! $key) {
			$key = $this->getApiKey();
		}

		$response = wp_remote_post(Plugin::apiBase() . '/key/isValid', [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body'    => json_encode([
				'token' => $key,
				'url'   => Plugin::baseUrl(),
			]),
		]);

		if (is_wp_error($response)) {
			return false;
		}

		return $response['response']['code'] === 403 ? false : true;
	}
}
