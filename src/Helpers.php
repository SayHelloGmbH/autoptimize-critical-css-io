<?php

namespace nicomartin\AoCriticalCSS;

class Helpers
{
	public static $filesmatch_option = 'aoccssio_filesmatch';

	public static function doPostRequest($url, $data = [])
	{
		if (empty($data)) {
			return [
				'status'  => 'error',
				'message' => __('Invalid data', 'aoccssio'),
			];
		}
		if ( ! function_exists('curl_version')) {
			return [
				'status'  => 'error',
				'message' => __('curl is not enabled on your server', 'aoccssio'),
			];
		}

		$data_string = json_encode($data);
		$data_string = htmlspecialchars_decode($data_string);
		try {
			$ch = curl_init($url);
			if ($ch === false) {
				throw new \Exception('failed to initialize');
			}

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string),
			]);
			$content   = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($content === false) {
				throw new \Exception(curl_error($ch), curl_errno($ch));
			}

			curl_close($ch);
		} catch (\Exception $e) {
			return [
				'status'  => 0,
				'message' => sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()),
			];
		}

		return [
			'status'  => $http_code,
			'message' => $content,
			'debug'   => [
				'url'  => $url,
				'data' => $data,
			]
		];
	}

	public static function getDimensions()
	{
		$dimensions = [
			'desktop' => [
				'width'  => 1200,
				'height' => 800,
			],
			'mobile'  => [
				'width'  => 700,
				'height' => 300,
			],
		];

		$dimensions = apply_filters('aoccssio/dimensions', $dimensions);

		$i      = 0;
		$return = [];
		foreach ($dimensions as $device => $vals) {
			if ( ! array_key_exists('width', $vals) || ! array_key_exists('height', $vals)) {
				continue;
			}
			if (0 == intval($vals['width']) || 0 == intval($vals['height'])) {
				continue;
			}
			if ($i >= 2) {
				continue;
			}
			$i++;
			$return[$device] = [
				'width'  => intval($vals['width']),
				'height' => intval($vals['height']),
			];
		}

		return $return;
	}

	public static function getCriticalDir()
	{

		$dir = ABSPATH . 'wp-content/cache/aoccssio/';
		if (is_multisite()) {
			$dir = $dir . get_current_blog_id() . '/';
		}

		if ( ! is_dir($dir)) {
			mkdir($dir, 0777, true);
		}

		return $dir;
	}

	public static function getAllCriticalElements()
	{
		$elements = [];

		$elements['front-page'] = [
			'name' => __('Front Page', 'aoccssio'),
			'url'  => get_home_url(),
		];

		/**
		 * All Singular
		 */
		foreach (self::getPostTypes() as $key => $name) {
			$posts = get_posts([
				'posts_per_page' => -1,
				'post_type'      => $key,
			]);

			$elements['singular-' . $key] = [
				'name'     => $name,
				'elements' => [],
			];

			foreach ($posts as $post) {
				$elements['singular-' . $key]['elements']['singular-' . $post->ID] = [
					'name' => get_the_title($post),
					'url'  => get_permalink($post->ID),
				];
			}
		}

		/**
		 * All Taxonomies
		 */
		foreach (self::getTaxonomies() as $key => $name) {
			$terms = get_terms($key, [
				'hide_empty' => true,
			]);

			$elements['archive-taxonomy-' . $key] = [
				'name'     => $name,
				'elements' => [],
			];

			foreach ($terms as $term) {
				$elements['archive-taxonomy-' . $key]['elements']['archive-taxonomy-' . $term->term_id] = [
					'name' => apply_filters('the_title', $term->name),
					'url'  => get_term_link($term),
				];
			}
		}

		/**
		 * All Users
		 */
		$elements['archive-author'] = [
			'name'     => __('Author Pages', 'aoccssio'),
			'elements' => [],
		];
		foreach (get_users() as $user) {
			$elements['archive-author']['elements']['archive-author-' . $user->user_nicename] = [
				'name' => $user->display_name,
				'url'  => get_author_posts_url($user->ID),
			];
		}

		return $elements;
	}

	public static function getPostTypes()
	{

		$post_types = [];

		$post_types_objects = get_post_types([
			'public' => true,
		], 'objects');

		foreach ($post_types_objects as $pt => $object) {
			if ('attachment' == $pt) {
				continue;
			}
			$post_types[$pt] = $object->labels->name;
		}

		return $post_types;
	}

	public static function getTaxonomies()
	{

		$taxonomies = [];
		foreach (self::getPostTypes() as $pt => $name) {
			$post_taxonomies_objects = get_object_taxonomies($pt, 'objects');
			foreach ($post_taxonomies_objects as $tax => $tax_object) {
				if ( ! $tax_object->show_ui) {
					continue;
				}
				$taxonomies[$tax] = $tax_object->labels->name;
			}
		}

		return $taxonomies;
	}

	public static function convertDate($timestamp = '', $type = 'datetime')
	{
		if ('' == $timestamp) {
			$timestamp = time();
		}
		switch ($type) {
			case 'date':
				return date(get_option('date_format'), $timestamp);
				break;
			case 'time':
				return date(get_option('time_format'), $timestamp);
				break;
			default:
				return date(get_option('date_format') . ' ' . get_option('time_format'), $timestamp);
				break;
		}
	}

	/**
	 * Render
	 */
	public static function renderGenerate($critical_key, $title, $urls = '')
	{
		$file         = self::getCriticalDir() . $critical_key . '.css';
		$has_file     = file_exists($file);
		$saved_option = get_option(self::$filesmatch_option);
		$saved_url    = '';
		if (is_array($saved_option) && array_key_exists($critical_key, $saved_option)) {
			$saved_url = $saved_option[$critical_key];
		}

		$return = '';
		$return .= '<div class="aoccssio-generate aoccssio-generate--' . ($has_file ? 'file' : 'nofile') . '" id="' . $critical_key . '">';
		$return .= '<h3 class="aoccssio-generate__title">' . $title . '</h3>';
		$return .= '<div class="aoccssio-generate__content">';
		$return .= '<input name="aoccssio_action" data-aoccssio-name="action" type="hidden" value="' . Generate::$ajax_action . '"/>';
		$return .= '<input name="aoccssio_action_delete" data-aoccssio-name="action_delete" type="hidden" value="' . Generate::$ajax_action_delete . '"/>';
		$return .= '<input name="aoccssio_key" data-aoccssio-name="critical_key" type="hidden" value="' . $critical_key . '"/>';

		if ($urls === '') {
			$return .= '<input name="aoccssio_url" data-aoccssio-name="url" type="text" value="' . $saved_url . '" class="aoccssio-generate__input"/>';
		} elseif (is_array($urls)) {
			$return .= '<select name="aoccssio_url" data-aoccssio-name="url" class="aoccssio-generate__input">';
			if (array_key_exists('elements', $urls) && is_array($urls['elements'])) {
				foreach ($urls['elements'] as $element_key => $element) {
					$selected = '';
					if ($saved_url == $element['url']) {
						$selected = 'selected';
					}
					$return .= "<option data-key='{$element_key}' value='{$element['url']}' {$selected }>{$element['name']}</option>";
				}
			} else {
				foreach ($urls as $key => $val) {
					if (array_key_exists('elements', $val) && is_array($val['elements'])) {
						if (empty($val['elements'])) {
							continue;
						}
						$return .= "<optgroup label='{$val['name']}'>";
						foreach ($val['elements'] as $element_key => $element) {
							$selected = '';
							if ($saved_url == $element['url']) {
								$selected = 'selected';
							}
							$return .= "<option data-key='{$element_key}' value='{$element['url']}' {$selected }>{$element['name']}</option>";
						}
						$return .= '</optgroup>';
					} else {
						$selected = '';
						if ($saved_url == $val['url']) {
							$selected = 'selected';
						}
						$return .= "<option data-key='{$key}' value='{$val['url']}' {$selected }>{$val['name']}</option>";
					}
				}
			}
			$return .= '</select>';
		} else {
			$return .= '<input name="aoccssio_url" data-aoccssio-name="url" type="text" value="' . $urls . '" disabled class="aoccssio-generate__input"/>';
		}

		$return .= '<span class="aoccssio-generate__status">' . ($has_file ? self::convertDate(filemtime($file)) : 'not yet generated') . '</span>';
		$return .= '<div class="aoccssio-generate__controls">';
		$return .= '<button type="button" class="aoccssio-generate__regenerate button">' . __('regenerate', 'aoccssio') . '</button>';
		$return .= '<button type="button" class="aoccssio-generate__delete">' . __('delete', 'aoccssio') . '</button>';
		$return .= '</div>';
		$return .= '</div>';

		$return .= '</div>';

		return $return;
	}

	public static function renderGenerateList($critical_key, $title, $urls)
	{

		$file         = self::getCriticalDir() . $critical_key . '.css';
		$has_file     = file_exists($file);
		$saved_option = get_option(self::$filesmatch_option);
		$saved_url    = '';
		if (is_array($saved_option) && array_key_exists($critical_key, $saved_option)) {
			$saved_url = $saved_option[$critical_key];
		}

		$return = '<tr class="aoccssio-generate aoccssio-generate--' . ($has_file ? 'file' : 'nofile') . '" id="' . $critical_key . '">';
		$return .= '<td>';
		$return .= '<input name="aoccssio_action" data-aoccssio-name="action" type="hidden" value="' . Generate::$ajax_action . '"/>';
		$return .= '<input name="aoccssio_action_delete" data-aoccssio-name="action_delete" type="hidden" value="' . Generate::$ajax_action_delete . '"/>';
		$return .= '<input name="aoccssio_key" data-aoccssio-name="critical_key" type="hidden" value="' . $critical_key . '"/>';

		$return .= '<p><b>' . $title . '</b></p>';

		if (is_array($urls)) {
			$return .= '<select name="aoccssio_url" data-aoccssio-name="url" class="aoccssio-generate__input">';
			if (array_key_exists('elements', $urls) && is_array($urls['elements'])) {
				foreach ($urls['elements'] as $element_key => $element) {
					$selected = '';
					if ($saved_url == $element['url']) {
						$selected = 'selected';
					}
					$return .= "<option data-key='{$element_key}' value='{$element['url']}' {$selected }>{$element['name']}</option>";
				}
			} else {
				foreach ($urls as $key => $val) {
					if (array_key_exists('elements', $val) && is_array($val['elements'])) {
						if (empty($val['elements'])) {
							continue;
						}
						$return .= "<optgroup label='{$val['name']}'>";
						foreach ($val['elements'] as $element_key => $element) {
							$selected = '';
							if ($saved_url == $element['url']) {
								$selected = 'selected';
							}
							$return .= "<option data-key='{$element_key}' value='{$element['url']}' {$selected }>{$element['name']}</option>";
						}
						$return .= '</optgroup>';
					} else {
						$selected = '';
						if ($saved_url == $val['url']) {
							$selected = 'selected';
						}
						$return .= "<option data-key='{$key}' value='{$val['url']}' {$selected }>{$val['name']}</option>";
					}
				}
			}
			$return .= '</select>';
			$return .= '<input name="savepage" data-aoccssio-name="savepage" type="hidden" value="yes"/>';
		} elseif ('' == $urls) {
			$return .= '<input name="aoccssio_url" data-aoccssio-name="url" type="text" class="aoccssio-generate__input" value="' . $saved_url . '" placeholder="' . trailingslashit(get_home_url()) . '..."/>';
			$return .= '<input name="savepage" data-aoccssio-name="savepage" type="hidden" value="yes"/>';
		} else {
			$return .= '<input name="aoccssio_url" data-aoccssio-name="url" type="text" value="' . $urls . '" disabled class="aoccssio-generate__input"/>';
			$return .= '<input name="savepage" data-aoccssio-name="savepage" type="hidden" value="no"/>';
		} // End if().
		$return .= '</td>';

		// generated
		$return .= '<td class="aoccssio-generate__generated">';

		$filedate = '';
		if ($has_file) {
			$filedate = self::convertDate(filemtime($file));
		}
		$return .= '<span class="is_generated">' . $filedate . '</span>';
		$return .= '<span class="not_generated">' . __('not yet generated', 'aoccssio') . '</span>';
		$return .= '</td>';

		// controls
		$return .= '<td class="aoccssio-generate__controls">';
		$return .= '<a id="regenerate-criticalcss-aoccssio" class="button aoccssio-generate__regenerate">' . __('regenerate', 'aoccssio') . '</a>';
		$return .= '<br><a id="delete-criticalcss-aoccssio" class="aoccssio-generate__delete">' . __('delete', 'aoccssio') . '</a>';
		$return .= '</td>';
		$return .= '</tr>';

		return $return;
	}

	public static function renderGenerateSingle($critical_key, $url)
	{

		$file     = self::getCriticalDir() . $critical_key . '.css';
		$has_file = file_exists($file);

		$return = '<div class="aoccssio-generate aoccssio-generate--' . ($has_file ? 'file' : 'nofile') . '" id="' . $critical_key . '">';
		$return .= '<input name="aoccssio_action" data-aoccssio-name="action" type="hidden" value="' . Generate::$ajax_action . '"/>';
		$return .= '<input name="aoccssio_action_delete" data-aoccssio-name="action_delete" type="hidden" value="' . Generate::$ajax_action_delete . '"/>';
		$return .= '<input name="aoccssio_key" data-aoccssio-name="critical_key" type="hidden" value="' . $critical_key . '"/>';
		$return .= '<input name="aoccssio_url" data-aoccssio-name="url" type="hidden" value="' . $url . '"/>';

		// generated
		$return .= '<div class="aoccssio-generate__generated">';

		$filedate = '';
		if ($has_file) {
			$filedate = self::convertDate(filemtime($file));
		}
		$return .= '<b class="generated_title">' . __('Generated', 'aoccssio') . ':</b>';
		$return .= '<span class="is_generated">' . $filedate . '</span>';
		$return .= '<span class="not_generated">' . __('not yet generated', 'aoccssio') . '</span>';
		$return .= '</div>';

		// controls
		$return .= '<div class="aoccssio-generate__controls">';
		$return .= '<a id="regenerate-criticalcss-aoccssio" class="button aoccssio-generate__regenerate">' . __('regenerate', 'aoccssio') . '</a>';
		$return .= '<br><a id="delete-criticalcss-aoccssio" class="aoccssio-generate__delete">' . __('delete', 'aoccssio') . '</a>';
		$return .= '</div>';
		$return .= '</div>';

		return $return;
	}

	public static function exitAjax($type, $msg = '', $add = [])
	{

		$return = [
			'type'    => $type,
			'message' => $msg,
			'add'     => $add,
		];

		echo json_encode($return);

		wp_die();
	}
}
