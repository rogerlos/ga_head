# GA Head

GA Head adds Google Analytics code to your site's html head, as recommended by Google. 

You can optionally prevent users with a certain capability from being tracked--this can be useful if you don't
want administrators use of the site to affect your analytics, for example.

Options are set from within the Customizer. Look for the "Google Analytics by GA Head" section. You must at least
enter the tracking code for your website (as provided to you by Google) for the plugin to work!

## For Developers

GA Head can be called directly via a helper function, which may be useful if you do not call `wp_head()` in your
theme's code:

```php
// echoes javascript string
$google_analytics = ga_head();

// returns javascript string
$google_analytics = ga_head( false );
```

### Filters

Note: You can disable the ability for plugins and themes to use the filters described below by adding
`define( 'GAHEAD_FILTERS', false );` to `wp-config.php`.

GA Head has several filters. They all pass a single argument, the value being modified, and expect back a value of
the same type.

* `add_filter( 'gahead_config_file', 'your_function_pointing_to_different_json_file' )`  
  _String_  
  Expects a fully qualified path to a valid JSON file containing the plugin's configuration array.
* `add_filter( 'gahead_config_array', 'your_function_to_modify_configuration' )`  
  _Array_  
  The configuration array used by GA Head. Be careful!
* `add_filter( 'gahead_donottrack', 'your_function_to_change_WP_capability' )`  
  _String_  
  Expects a single WordPress capability (or empty string) as the return value. Signed-in users with this
capability will not be tracked. (This will override the setting in the customizer.)
* `add_filter( 'gahead_trackcode', 'your_function_for_tracking_code' )`  
  _String_  
  Expects a Google Analytics tracking code to be returned. GA Head does NOT try to determine if the string returned
is an actual valid GA tracking code! (This will override the setting in the customizer.)
* `add_filter( 'gahead_rawscript', 'your_function_to_modify_raw_javascript' )`  
  _String_  
  Filters the "raw" javascript, before the `{{code}}` placeholder has been replaced and before a check to be sure the 
  code is enclosed by `script` tags. (This will override the setting in the customizer.)
* `add_filter( 'gahead_script', 'your_function_to_modify_final_javascript' )`  
  _String_  
  Filters the "final" javascript exactly as it will be inserted into the page head. Should include wrapping `script` 
  tags and display the tracking code.
 
## Installation
 
1. Upload the directory `ga-head` to your `plugins` directory, or select `ga-head.zip` by using the "Add New" button
on the WordPress admin "Plugins" page.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the tracking code you acquired from Google by opening the Customizer and finding the "Google Analytics" section
 
## Changelog

#### 1.3
* First version in public repository

#### 1.0
* Initial release