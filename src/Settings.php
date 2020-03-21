<?php

namespace nicomartin\AoCriticalCSS;

class Settings
{

	private static $key = 'aoccssio';
	private static $optionsPage = 'aoccssio_options';

	public function __construct()
	{
	}

	public function run()
	{
		add_filter('autoptimize_filter_settingsscreen_tabs', [$this, 'addTab']);
		add_action('admin_menu', [$this, 'adminMenu']);
	}

	public function addTab($tabs)
	{
		return array_merge($tabs, [self::$optionsPage => __('critical-css.io', 'hello-aoccss')]);
	}

	public function adminMenu()
	{
		add_submenu_page(null, 'Autoptimize critical-css.io', 'Autoptimize critical-css.io', 'manage_options', self::$optionsPage, [$this, 'settingsPage']);

		add_settings_section(
			self::$key . '-section',
			'Example settings section',
			function () {
				echo 'test';
			},
			self::$optionsPage
		);

		add_settings_field(
			'critcalcssio_apikey',
			'API Key',
			function () {
				echo 'field';
			},
			self::$optionsPage,
			self::$key . '-section'
		);

		register_setting('aoccssio_options_group', 'critcalcssio_apikey');
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
				// Print AO settings tabs
				echo \autoptimizeConfig::ao_admin_tabs();

				$key = get_option('critcalcssio_apikey');
				var_dump($key);
				?>
				<form id="settings" method="post" action="options.php">
					<?php do_settings_fields(self::$optionsPage, self::$key . '-section'); ?>
					<table id="key" class="form-table">
						<tr>
							<th scope="row">
								<?php _e('Your API Key', 'autoptimize'); ?>
							</th>
							<td>
								<textarea id="critcalcssio_apikey" name="critcalcssio_apikey" rows='2' style="width:100%;" placeholder="<?php _e('Please enter your criticalcss.com API key here...', 'autoptimize'); ?>"><?php echo trim($key); ?></textarea>
								<p class="notes">
									<?php _e('Enter your <a href="https://criticalcss.com/account/api-keys?aff=1" target="_blank">criticalcss.com</a> API key above. The key is revalidated every time a new job is sent to it.<br />To obtain your API key, go to <a href="https://criticalcss.com/account/api-keys?aff=1" target="_blank">criticalcss.com</a> > Account > API Keys.<br />Requests to generate a critical CSS via the API are priced at £5 per domain per month.<br /><strong>Not sure yet? With the <a href="https://criticalcss.com/faq/?aff=1#trial" target="_blank">30 day free trial</a>, you have nothing to lose!</strong>', 'autoptimize'); ?>
								</p>
							</td>
						</tr>
					</table>
					<p class="submit left">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'autoptimize') ?>"/>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	function settingsCallbackInput()
	{
		echo '<input name="eg_setting_name" id="eg_setting_name" type="checkbox" value="1" class="code" ' . checked(1, get_option('eg_setting_name'), false) . ' /> Explanation text';
	}
}
