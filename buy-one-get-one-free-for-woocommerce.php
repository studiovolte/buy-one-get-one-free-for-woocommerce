<?php
/**
 * 
 * Plugin name: Buy One Get One Free for WooCommerce
 * Description: Completely free and simple plugin to add buy one get one free offers to WooCommerce. No ads, no upsells.
 * Version: 1.0.0
 * Text Domain: studio-volte-bogo
 * Author: Studio Volte
 * Author URI: https://studiovolte.com/
 * 
 */

define('SVBOGO_PLUGIN_NAME', 'Buy One Get One Free for WooCommerce');
define('SVBOGO_PLUGIN_SLUG', 'studio-volte-bogo');

if( !defined('ABSPATH') )
{
      exit; // Exit if accessed directly
}

if( !class_exists('SVBOGO_StudioVolteBogo') )
{

    class SVBOGO_StudioVolteBogo {

        public function __construct()
        {
            define('SVBOGO_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
            define('SVBOGO_PLUGIN_URL', plugin_dir_url( __FILE__ ));
            require_once(SVBOGO_PLUGIN_PATH . '/vendor/autoload.php');
        }

        public function initialize()
        {
            include_once SVBOGO_PLUGIN_PATH . 'includes/options-page.php';
            include_once SVBOGO_PLUGIN_PATH . 'includes/trigger-bogo.php';

            // Load custom js/css
            add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
            function enqueue_admin_scripts()
            {
                wp_enqueue_script(SVBOGO_PLUGIN_SLUG . '-scripts', SVBOGO_PLUGIN_URL . 'assets/js/scripts.js');
                wp_enqueue_style(SVBOGO_PLUGIN_SLUG . '-styles', SVBOGO_PLUGIN_URL . 'assets/css/styles.css');
            }

            add_action('wp_enqueue_scripts', 'enqueue_frontend_scripts');
            function enqueue_frontend_scripts()
            {
                wp_enqueue_script(SVBOGO_PLUGIN_SLUG . '-scripts', SVBOGO_PLUGIN_URL . 'assets/js/frontend.js');
            }

            add_action('admin_init', 'svbogo_check_woocommerce');
            
        }

    }

    $studioVolteBogo = new SVBOGO_StudioVolteBogo;
    $studioVolteBogo->initialize();

}

function svbogo_error()
{
    ?>
        <div class="error notice">
            <p><?php _e( 'Please install the WooCommerce plugin before using <strong>' . SVBOGO_PLUGIN_NAME . '</strong>', SVBOGO_PLUGIN_SLUG); ?></p>
        </div>
    <?php
}
function svbogo_check_woocommerce()
{
    // show error if woocommerce is not installed
    if (!is_plugin_active( 'woocommerce/woocommerce.php')){
        add_action( 'admin_notices', 'svbogo_error' );
        return;
    }
}