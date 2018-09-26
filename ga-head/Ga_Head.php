<?php

namespace gahead;

/**
 * Class Ga_Head
 *
 * @since   1.3 Added property 'FILTERS'
 * @since   1.3 Removed method 'set_property'
 * @since   1.3 Added method 'configuration'
 * @since   1.0
 *
 * @package gahead
 */
class Ga_Head {
	
	/**
	 * @since 1.3 Keys no longer pre-defined
	 * @since 1.0
	 *
	 * @var array  Configuration array used by plugin
	 */
	protected $GA;
	
	/**
	 * @since 1.3
	 *
	 * @var bool  Whether to allow plugin to be filtered by "add_filter()"
	 */
	protected $FILTERS;
	
	/**
	 * Ga_Head constructor.
	 *
	 * @since 1.3 Allows setting of GAHEAD_FILTERS constant to prevent filtering of plugin if set to false
	 * @since 1.3 No longer accepts configuration array, use filter instead
	 * @since 1.0
	 */
	public function __construct() {
		
		$this->FILTERS = defined( 'GAHEAD_FILTERS' ) ? GAHEAD_FILTERS : TRUE;
		
		$this->GA = $this->configuration();
	}
	
	/**
	 * Adds options to customizer from passed arguments array.
	 *
	 * @since 1.3 Added test to be sure 'customizer' property is not empty, and is an array
	 * @since 1.0
	 *
	 * @param \WP_Customize_Manager $C
	 */
	public function customizer( \WP_Customize_Manager $C ) {
		
		$c = ! empty( $this->GA['customizer'] ) && is_array( $this->GA['customizer'] ) ? $this->GA['customizer'] : [];
		
		if ( empty( $c['section'] ) || empty( $c['field'] ) ||
		     ! is_array( $c['section'] ) || ! is_array( $c['field'] ) ) {
			return;
		}
		
		foreach ( $c['section'] as $s ) {
			
			if ( empty( $s['id'] ) || empty( $s['title'] ) ) {
				continue;
			}
			
			$C->add_section( $s['id'], [
				'title'      => $s['title'],
				'priority'   => empty( $s['priority'] ) ? 160 : $s['priority'],
				'capability' => empty( $s['capability'] ) ? 'edit_theme_options' : $s['capability'],
			] );
		}
		
		foreach ( $c['field'] as $f ) {
			
			if ( empty( $f['id'] ) || empty( $f['label'] ) ) {
				continue;
			}
			
			$C->add_setting( $f['id'], [
				'default'           => empty( $f['default'] ) ? '' : $f['default'],
				'sanitize_callback' => [ $this, 'sanitize' ],
			] );
			$C->add_control( $f['id'], [
				'label'       => $f['label'],
				'section'     => empty( $f['section'] ) ? $c['section'][0]['id'] : $f['section'],
				'type'        => empty( $f['type'] ) ? 'text' : $f['type'],
				'description' => empty( $f['description'] ) ? '' : $f['description'],
			] );
		}
	}
	
	/**
	 * Sanitizes user input from customizer
	 *
	 * @since 1.0
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function sanitize( $text ) {
		
		return wp_kses_post( force_balance_tags( $text ) );
	}
	
	/**
	 * Callback to add code to head. Checks user's capability before adding script.
	 *
	 * @since 1.3  Added filters
	 * @since 1.3  Added $echo to allow returning string
	 * @since 1.2  Stripped all whitespace from script return value
	 * @since 1.0
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function head( $echo = TRUE ) {
		
		$script     = '';
		$notrack    = get_theme_mod( 'gahead_donottrack', '' );
		$track_code = get_theme_mod( 'gahead_code', '' );
		
		$ignore     = $this->FILTERS ? apply_filters( 'gahead_donottrack', $notrack ) : $notrack;
		$track_code = $this->FILTERS ? apply_filters( 'gahead_trackcode', $track_code ) : $track_code;
		
		$add_tracking = $ignore ? ! current_user_can( $ignore ) : TRUE;
		
		if ( $add_tracking && $track_code ) {
			$custom = preg_replace( '/\s+/', '', get_theme_mod( 'gahead_snippet', '' ) );
			$script = empty( $custom ) ? $this->default_script( $track_code ) : $this->script( $custom, $track_code );
		}
		
		if ( $echo ) {
			echo $script;
		}
		
		return $script;
	}
	
	/**
	 * Gets the plugin configuration from cfg.json. Can be filtered to allow custom configuration
	 *
	 * @since 1.3
	 *
	 * @return array
	 */
	protected function configuration() {
		
		$file      = __DIR__ . '/cfg.json';
		$json_file = $this->FILTERS ? apply_filters( 'gahead_config_file', $file ) : $file;
		$cfg       = file_exists( $json_file ) ? json_decode( file_get_contents( $json_file ), TRUE ) : [];
		$json      = $cfg === NULL ? [] : $cfg;
		
		return $this->FILTERS ? apply_filters( 'gahead_config_array', $json ) : $json;
	}
	
	
	/**
	 * Google Analytics javascript. Returns an empty string if $tracker is empty
	 *
	 * @since 1.3 Added test for existence of 'ga' property in $this->GA
	 * @since 1.1 Fixed order of arguments
	 * @since 1.0
	 *
	 * @param string $track_code Google analytics tracking code
	 *
	 * @return string
	 */
	protected function default_script( $track_code ) {
		
		return $track_code && ! empty( $this->GA['ga'] ) ? $this->script( $this->GA['ga'], $track_code ) : '';
	}
	
	/**
	 * Adds custom script, looking for {{code}} token if present.
	 *
	 * @since 1.3 Check $script is a string
	 * @since 1.3 Added filter
	 * @since 1.0
	 *
	 * @param string $tracker GA tracking number
	 * @param string $script  GA javascript
	 *
	 * @return mixed
	 */
	protected function script( $script, $tracker = '' ) {
		
		$script = $this->FILTERS ? apply_filters( 'gahead_rawscript', $script ) : $script;
		$script = is_string( $script ) ? $script : '';
		
		$script = strpos( $script, '<script>' ) === FALSE ? '<script>' . $script . '</script>' : $script;
		$script = str_replace( '{{code}}', $tracker, $script );
		
		return $this->FILTERS ? apply_filters( 'gahead_script', $script ) : $script;
	}
}