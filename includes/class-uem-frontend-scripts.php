<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UEM_frontendScripts{
    
	/**
	 * Contains an array of script handles registered by HST.
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by HST.
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by HST.
	 * @var array
	 */
	private static $wp_localize_scripts = array();

    
    public static function init(){
        
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
        
    }
    
    public static function load_scripts(){
        
        $assets_path          = UEM()->plugin_url() . '/assets/';
        $frontend_script_path = $assets_path . 'js/dist/frontend/';
        
   
       
        
       
       self::enqueue_script( 'front-uem', $frontend_script_path . 'front-uem'. '.js', array('jquery-ui-sortable','jquery')); 
       
       
       
       
       
        self::enqueue_style( 'front-uem-style', $assets_path . 'css/vc_uem_frontend.css' );
        }
    
    
    /**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = WC_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = WC_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}
        
        
        /**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = WC_VERSION, $media = 'all' ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = WC_VERSION, $media = 'all' ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media );
		}
		wp_enqueue_style( $handle );
	}
    
    
    
}

UEM_frontendScripts::init();