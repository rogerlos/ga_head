<?php

/**
 * Adds Google Analytics to head. Allows a single google analytics code to be used per installation. Configured via
 * customizer
 *
 * @wordpress-plugin
 * Plugin Name:       Google Analytics in Site Head
 * Plugin URI:
 * Description:       Adds google analytics to head. Configured via customizer
 * Version:           1.2.1
 * Author:            Roger Los
 * Author URI:        https://github.com/rogerlos/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gahead
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * @var string $ph   The "placeholder" string users can add to any custom Javascript to insert the customizer
 *                   configured GA code into their custom JS structure. Used as a way to avoid having the
 *                   possibility of separate numbers.
 */
$ph = '[code]';

/**
 * @var array $args  class Ga_Head does not have any "hard-coded" text or var strings within it; all configuration
 *                   is done via this arguments array.
 */
$args = [
	'placeholder' => $ph,
	'no_tracking' => '',
	'customizer'  => [
		'section' => [
			[
				'id'    => 'gahead_ga',
				'title' => 'Google Analytics',
			],
		],
		'field'   => [
			[
				'id'    => 'gahead_code',
				'label' => 'Tracking Number',
			],
			[
				'id'         => 'gahead_snippet',
				'label'       => 'GA JavaScript',
				'type'        => 'textarea',
				'description' => 'Optional, theme will use standard Analytics code by default. If you use this box, use '
				                 . $ph . ' as placeholder for number entered above.',
			],
		],
	],
	'ga'          => '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){'
	                 . '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'
	                 . 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})('
	                 . 'window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');'
	                 . 'ga(\'create\', \'' . $ph . '\', \'auto\');ga(\'send\', \'pageview\');',
];

include( 'Ga_Head.php' );

$GA = new \gahead\Ga_Head( $args );
$GA->actions();