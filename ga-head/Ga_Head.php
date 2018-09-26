<?php

namespace gahead;

/**
 * Class Ga_Head
 *
 * @package gahead
 */
class Ga_Head {
	
	/**
	 * @since 1.0
	 * @var array
	 */
	protected $GA = [
		'customizer'  => [],
		'ga'          => '',
		'no_tracking' => '',
		'placeholder' => '',
	];
	
	/**
	 * Ga_Head constructor.
	 *
	 * @since 1.0
	 *
	 * @param array $args
	 */
	public function __construct( $args = [] ) {
		
		$this->set_property( $args );
	}
	
	/**
	 * Allows setting an argument
	 *
	 * @since 1.0
	 *
	 * @param string $key
	 * @param mixed  $args
	 *
	 * @return mixed
	 */
	public function set_property( $args, $key = '' ) {
		
		if ( ! empty( $key ) && isset( $this->GA[ $key ] ) ) {
			
			$this->GA[ $key ] = $args;
			
		} else if ( ! empty( $args ) && is_array( $args ) ) {
			
			foreach ( $args as $key => $val ) {
				if ( isset( $this->GA[ $key ] ) ) {
					$this->GA[ $key ] = $val;
				}
			}
		}
		
		return $this->GA;
	}
	
	/**
	 * Adds actions to WP.
	 *
	 * @since 1.0
	 */
	public function actions() {
		
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_action( 'wp_head', [ $this, 'head' ] );
	}
	
	/**
	 * Adds options to customizer from passed arguments array.
	 *
	 * @param \WP_Customize_Manager $C
	 */
	public function customizer( \WP_Customize_Manager $C ) {
		
		$c = $this->GA['customizer'];
		
		if (
			empty( $c['section'] ) ||
			empty( $c['field'] ) ||
			! is_array( $c['section'] ) ||
			! is_array( $c['field'] )
		) {
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
	 * @since 1.2  Stripped all whitespace from script return value
	 * @since 1.0
	 */
	public function head() {
		
		if ( empty( $this->GA['no_tracking'] ) || ! current_user_can( $this->GA['no_tracking'] ) ) {
			
			$tracker       = get_theme_mod( 'gahead_code', '' );
			$custom_script = get_theme_mod( 'gahead_snippet', '' );
			
			$custom_script = preg_replace('/\s+/', '', $custom_script);
			
			$script = $tracker && empty( $custom_script ) ?
				$this->default_ga_code( $tracker ) : $this->script( $custom_script, $tracker );
			
			echo $script;
		}
	}
	
	/**
	 * Adds custom script, looking for placeholder token if present.
	 *
	 * @since 1.0
	 *
	 * @param string $tracker GA tracking number
	 * @param string $script  GA javascript
	 *
	 * @return mixed
	 */
	public function script( $script, $tracker = '' ) {
		
		$script = strpos( $script, '<script>' ) === FALSE ? '<script>' . $script . '</script>' : $script;
		
		return str_replace( $this->GA['placeholder'], $tracker, $script );
	}
	
	/**
	 * Google Analytics code.
	 *
	 * @since 1.1 Fixed order of arguments
	 * @since 1.0
	 *
	 * @param string $tracker
	 *
	 * @return string
	 */
	protected function default_ga_code( $tracker ) {
		
		return $tracker ? $this->script( $this->GA['ga'], $tracker ) : '';
	}
}