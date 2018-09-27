<?php
/**
 * @wordpress-plugin
 * Plugin Name:       GA Head
 * Plugin URI:        https://github.com/rogerlos/ga-head
 * Description:       Adds google analytics to your site's html head. Configured via customizer. Extensible by filters.
 * Version:           1.3.1
 * Author:            Roger Los
 * Author URI:        https://github.com/rogerlos
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gahead
 *
 * @since 1.3.1 Modifications to plugin header.
 * @since 1.3.1 First version submitted to WP repository.
 */

/**
 * @since 1.3 Requires PHP 5.4 or greater
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) || floatval( phpversion() ) < 5.4 ) die;

include( 'Ga_Head.php' );

/**
 * Load plugin.
 *
 * @since 1.3 Configuration is now via JSON config file
 * @since 1.0
 */
$GA = new \gahead\Ga_Head();

/**
 * WordPress actions and filters. Plugin does not otherwise modify WP.
 *
 * @since 1.3
 */
add_action( 'customize_register', [ $GA, 'customizer' ] );
add_action( 'wp_head', [ $GA, 'head' ] );

/**
 * For developers who do not call wp_head() in theme template.
 *
 * @since 1.3
 *
 * @param bool $echo  Whether to echo return value or not
 *
 * @return string
 */
function ga_head( $echo = true ) {
	$GA = new \gahead\Ga_Head();
	return $GA->head( $echo );
}