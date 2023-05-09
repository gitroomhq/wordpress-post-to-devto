<?php
/*
Plugin Name: GitHub20k - Post to DEV.to
Plugin URI: https://github.com/github20k/post-to-dev-to
Description: Post your content directly to DEV.to
Version: 1.0
Author: Nevo David
Author URI: https://github20k.com
Text Domain: wp-to-dev
*/

if(!defined('ABSPATH')){
	exit;
}

register_activation_hook(__FILE__, 'redirect_after_activation');

function redirect_after_activation() {
    add_option('redirect_after_activation_option', true);
}

add_action('admin_init', 'activation_redirect');

function activation_redirect() {
    if (get_option('redirect_after_activation_option', false)) {
        delete_option('redirect_after_activation_option');
        exit(wp_redirect(admin_url( 'admin.php?page=WordPressToDevAPI_setting.php' )));
    }
}
require_once "include/index.php";

/**
 * The Main Class Of Plugin
 */
final class WordPressToDev
{
	// Class construction
	private function __construct()
	{
		$this->define_function();

		add_action('plugins_loaded', [$this, 'init_plugin']);
		/**
 			* Load plugin textdomain.
		*/
		add_action( 'init', [$this, 'WordPressToDev_load_textdomain'] );		
	}

	/*
		Single instence 
	*/
	public static function init(){
		static $instance = false;

		if (!$instance) {
			$instance = new self();
		}

		return $instance;
	}


	public function define_function(){
		define("WPTODEV_FILE", __FILE__);
		define("WPTODEV_PATH", __DIR__);
		define("WPTODEV_URL", plugins_url('', WPTODEV_FILE));
		define("WPTODEV_ASSETS", WPTODEV_URL.'/assets');
	}

	public function init_plugin(){
		new WordPressToDev_SettingsPage();
		add_action( 'admin_notices', [$this, 'WordPressToDev_admin_notice'] );			
	}

	public function WordPressToDev_load_textdomain() {
	  load_plugin_textdomain( 'wp-to-dev', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}

	public function WordPressToDev_admin_notice() {
    	$class = 'notice notice-success is-dismissible';
    	$message = __( 'Welcome to GitHub20k - post to DEV.to plugin!<br />Do you want to grow your GitHub library?<br /> <a href="https://github20k.com">Check out GitHub20k newsletter!</a>', 'wp-to-dev' );
 
    	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
    }
	
}

/*
Initialize the main plugin
*/
function WordPressToDev_init(){
	return WordPressToDev::init();
}

/*
Active Plugin
*/
WordPressToDev_init();