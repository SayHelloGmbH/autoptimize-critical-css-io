# Autoptimize - Critical CSS API
A WordPress Plugin that connects [Autoptimize](https://wordpress.org/plugins/autoptimize/) with the open source CriticalCSS generator [Critical CSS API](https://github.com/nico-martin/critical-css-api).

## Developers
### Filters
#### apiBaseURL
By default this plugin uses the hosted version of on https://api.critical-css.io. But if you want to use ya delf-hosted version you can change the API base url using `aoccssio/apiBaseURL`:

```php
function myplugin_api_base( $defaultAPIBase ) {
    return 'https://myapibaseurl.com';
}
add_filter( 'aoccssio/apiBaseURL', 'myplugin_api_base' );
```
