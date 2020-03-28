<?php

namespace nicomartin\AoCriticalCSS;

class GeneratorFields
{
	private static $name = 'Critical CSS';

	public function run()
	{
		if (Helpers::showGenerateBoxes()) {
			add_action('aoccssio/generatorFields', [$this, 'sectionRecommended']);
			add_action('aoccssio/generatorFields', [$this, 'sectionPosttypes']);
			add_action('aoccssio/generatorFields', [$this, 'sectionTaxonomies']);
			add_action('aoccssio/generatorFields', [$this, 'sectionSpecial']);
			add_action('aoccssio/generatorFields', [$this, 'sectionDate']);

			add_action('add_meta_boxes', function () {
				foreach (Helpers::getPostTypes() as $key => $name) {
					add_meta_box('aoccssio-meta-box', self::$name, [$this, 'registerMetaBbox'], $key, 'side', 'low');
				}
			});
			foreach (Helpers::getTaxonomies() as $key => $name) {
				add_action($key . '_edit_form_fields', [$this, 'registerTaxField'], 10, 2);
			}
			add_action('show_user_profile', [$this, 'registerUserField'], 10, 1);
			add_action('edit_user_profile', [$this, 'registerUserField'], 10, 1);
		}
	}

	public function sectionRecommended()
	{
		echo '<div class="aoccssio-wrap">';
		echo '<h2 class="aoccssio-wrap__title">' . __('Recommended Pages', 'aoccssio') . '</h2>';

		$select_array = array_merge([
			'-' => [
				'name' => __('Select Page', 'aoccssio'),
				'url'  => '',
			],
		], Helpers::getAllCriticalElements());

		echo '<div class="aoccssio-wrap__elements">';
		echo Helpers::renderGenerate('index', __('Fallback (index.css)', 'aoccssio'), $select_array);
		echo Helpers::renderGenerate('front-page', __('Front Page', 'aoccssio'), get_home_url());
		echo Helpers::renderGenerate('singular', __('Singular', 'aoccssio'), $select_array);
		echo Helpers::renderGenerate('archive', __('Archive', 'aoccssio'), '');
		echo '</div>';
		echo '</div>';
	}

	public function sectionPosttypes()
	{
		echo '<div class="aoccssio-wrap">';
		echo '<h2 class="aoccssio-wrap__title">' . __('Post Types', 'aoccssio') . '</h2>';

		$select_array = array_merge([
			'-' => [
				'name' => __('Select Page', 'aoccssio'),
				'url'  => '',
			],
		], Helpers::getAllCriticalElements());

		echo '<div class="aoccssio-wrap__elements">';
		foreach (Helpers::getPostTypes() as $key => $name) {
			echo Helpers::renderGenerate('singular-' . $key, $name, $select_array['singular-' . $key]);
			if ('' != get_post_type_archive_link($key)) {
				echo Helpers::renderGenerate('archive-' . $key, __('Archive', 'aoccssio') . ': ' . $name, get_post_type_archive_link($key));
			}
		}
		echo '</div>';
		echo '</div>';
	}

	public function sectionTaxonomies()
	{
		echo '<div class="aoccssio-wrap">';
		echo '<h2 class="aoccssio-wrap__title">' . __('Taxonomies', 'aoccssio') . '</h2>';

		$select_array = array_merge([
			'-' => [
				'name' => __('Select Page', 'aoccssio'),
				'url'  => '',
			],
		], Helpers::getAllCriticalElements());

		echo '<div class="aoccssio-wrap__elements">';
		foreach (Helpers::getTaxonomies() as $key => $name) {
			echo Helpers::renderGenerate('archvie-taxonomy-' . $key, $name, $select_array['archive-taxonomy-' . $key]);
		}
		echo '</div>';
		echo '</div>';
	}

	public function sectionSpecial()
	{
		echo '<div class="aoccssio-wrap">';
		echo '<h2 class="aoccssio-wrap__title">' . __('Special Pages', 'aoccssio') . '</h2>';

		$select_array = array_merge([
			'-' => [
				'name' => __('Select Page', 'aoccssio'),
				'url'  => '',
			],
		], Helpers::getAllCriticalElements());

		echo '<div class="aoccssio-wrap__elements">';
		echo Helpers::renderGenerate('archive-author', __('Archive Author', 'aoccssio'), $select_array['archive-author']);
		echo Helpers::renderGenerate('404', __('404 Page', 'aoccssio'), '');
		echo Helpers::renderGenerate('search', __('Search Page', 'aoccssio'), '');
		echo '</div>';
		echo '</div>';
	}

	public function sectionDate()
	{
		echo '<div class="aoccssio-wrap">';
		echo '<h2 class="aoccssio-wrap__title">' . __('Archive Date', 'aoccssio') . '</h2>';

		echo '<div class="aoccssio-wrap__elements">';
		echo Helpers::renderGenerate('archive-date', __('Archive Date', 'aoccssio'));
		echo Helpers::renderGenerate('archive-date-year', '- ' . __('Archive Date Year', 'aoccssio'), '');
		echo Helpers::renderGenerate('archive-date-month', '- ' . __('Archive Date Month', 'aoccssio'), '');
		echo Helpers::renderGenerate('archive-date-day', '- ' . __('Archive Date Day', 'aoccssio'), '');
		echo '</div>';
		echo '</div>';
	}

	public function registerMetaBbox($post)
	{
		if ('publish' != $post->post_status) {
			echo '<p>' . __('Please publish the post before generating the Critical CSS.', 'aoccssio') . '</p>';
		} else {
			echo Helpers::renderGenerateSingle("singular-{$post->ID}", get_permalink($post->ID));
		}
	}

	public function registerTaxField($term)
	{
		echo '<tr class="form-field term-criticalapi-wrap">';
		echo '<th scope="row"><label for="description">' . self::$name . '</label></th>';
		echo '<td>' . Helpers::renderGenerateSingle("archvie-taxonomy-{$term->term_id}", get_term_link($term)) . '</td>';
		echo '</tr>';
	}

	public function registerUserField($user)
	{
		echo '<table class="form-table">';
		echo '<tr class="form-field term-criticalapi-wrap">';
		echo '<th scope="row"><label for="description">' . self::$name . '</label></th>';
		echo '<td>' . Helpers::renderGenerateSingle("archive-author-{$user->user_nicename}", get_author_posts_url($user->ID)) . '</td>';
		echo '</tr>';
		echo '</table>';
	}
}
