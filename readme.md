# Autoptimize - Critical CSS API
A WordPress Plugin that connects [Autoptimize](https://wordpress.org/plugins/autoptimize/) with the open source CriticalCSS generator [Critical CSS API](https://github.com/nico-martin/critical-css-api).

## conditonal Critical CSS
The Critical CSS follows a template hierarchy similar to the WordPress template hierarchy.

```
index.css
| singular.css
| | singular-{$post_type}.css
| | | singular-{$post_id}.css
| archive.css
| | archive-{$post_type}.css
| | archive-author.css
| | | archive-author-{$author_name}.css
| | archive-date.css
| | | archive-date-year.css
| | | archive-date-month.css
| | | archive-date-day.css
| | archvie-taxonomy.css
| | | archvie-taxonomy-{$taxonomy}.css
| | | | archvie-taxonomy-{$term_id}.css
| front-page.css
| 404.css
| search.css
```

The plugin allows you to create the Critical CSS for a specific page (as post meta box) or various fallbacks (on the settings page).

## Developers
### Filters
#### apiBaseURL
By default this plugin uses the hosted version of [Critical CSS API](https://github.com/nico-martin/critical-css-api) on https://api.critical-css.io. But if you want to use a self-hosted version you can change the API base url using `aoccssio/apiBaseURL`:

```php
function myplugin_api_base( $defaultAPIBase ) {
    return 'https://myapibaseurl.com';
}
add_filter( 'aoccssio/apiBaseURL', 'myplugin_api_base' );
```

#### dimensions

```php
function myplugin_dimensions( $defaultDimensions ) {
    return [
        'desktop' => [
            'width'  => 1200,
            'height' => 800,
        ],
        'mobile'  => [
            'width'  => 700,
            'height' => 300,
        ],
    ];
}
add_filter( 'aoccssio/dimensions', 'myplugin_dimensions' )
```
