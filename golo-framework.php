<?php
/**
 *  Plugin Name: Golo Framework
 *  Plugin URI: https://uxper.co/
 *  Description: Golo Framework.
 *  Version: 1.5.7
 *  Author: Uxper
 *  Author URI: https://uxper.co/
 *  Text Domain: golo-framework
 *
 *  @package Golo Framework
 *  @author uxper
 *
 **/

if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Golo_Framework' ) ) {
	class Golo_Framework {

		public function __construct() {
			
            $this->define_constants();
            $this->load_textdomain();

            register_deactivation_hook(__FILE__, array( $this, 'golo_deactivate'));
            add_action('plugins_loaded', array( $this, 'includes'));
            add_filter('kirki/config', array( $this, 'kirki_update_url'), 10 , 1 );

            if( is_multisite() ) {
                $blog_id = get_current_blog_id();
                $upload_path = WP_CONTENT_DIR . '/uploads/sites/' . $blog_id . '/';
            }
		}

		/**
		 *  Define constant
		 **/
		private function define_constants() {

            $theme = wp_get_theme();
            if ( !empty( $theme['Template'] ) ) {
                $theme = wp_get_theme( $theme['Template'] );
            }
            $plugin_dir_name = dirname(__FILE__);
            $plugin_dir_name = str_replace('\\', '/', $plugin_dir_name);
            $plugin_dir_name = explode('/', $plugin_dir_name);
            $plugin_dir_name = end($plugin_dir_name);

            if (!defined('GOLO_PLUGIN_FILE')) {
                define('GOLO_PLUGIN_FILE', __FILE__);
            }

            if (!defined('GOLO_PLUGIN_NAME')) {
                define('GOLO_PLUGIN_NAME', $plugin_dir_name);
            }

            if (!defined('GOLO_PLUGIN_DIR')) {
                define('GOLO_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }
            if (!defined('GOLO_PLUGIN_URL')) {
                define('GOLO_PLUGIN_URL', trailingslashit( plugins_url( GOLO_PLUGIN_NAME ) ) );
            }

            if (!defined('GOLO_PLUGIN_PREFIX')) {
                define('GOLO_PLUGIN_PREFIX', 'golo');
            }

            if (!defined('GOLO_METABOX_PREFIX')) {
                define('GOLO_METABOX_PREFIX', 'golo-');
            }

            if (!defined('GOLO_OPTIONS_NAME')) {
                define('GOLO_OPTIONS_NAME', 'golo-framework');
            }

            if (!defined('GOLO_THEME_NAME')) {
                define( 'GOLO_THEME_NAME', $theme['Name'] );
            }

            if (!defined('GOLO_THEME_SLUG')) {
                define( 'GOLO_THEME_SLUG', $theme['Template'] );
            }

            if (!defined('GOLO_THEME_VERSION')) {
                define( 'GOLO_THEME_VERSION', $theme['Version'] );
            }

            if (!defined('GLF_THEME_DIR')) {
                define( 'GLF_THEME_DIR', get_template_directory() );
            }

            if (!defined('GLF_THEME_URL')) {
                define( 'GLF_THEME_URL', get_template_directory_uri() );
            }

            if (!defined('GLF_THEME_SLUG')) {
                define( 'GLF_THEME_SLUG', $theme['Template'] );
            }

            if (!defined('GOLO_PLUGIN_VER')) {
                define('GOLO_PLUGIN_VER', '1.0.0');
            }

            if (!defined('GOLO_AJAX_URL')) {
                $ajax_url = admin_url('admin-ajax.php', 'relative');
                define('GOLO_AJAX_URL', $ajax_url);
            }

            
        }

        public function load_textdomain() {
            $mofile = GOLO_PLUGIN_DIR . 'languages/' . 'golo-framework-' . get_locale() .'.mo';

            if (file_exists($mofile)) {
                load_textdomain('golo-framework', $mofile );
            }
        }

        /**
         * The code that runs during plugin deactivation.
         */
        public function golo_deactivate()
        {
            require_once GOLO_PLUGIN_DIR . 'includes/class-golo-deactivator.php';
            Golo_Deactivator::deactivate();
        }

        /**
         *  Includes
         **/
        public function includes() {

            if ( !class_exists('Base_Framework') ) {
                add_filter('golo_base_url', 'base_url', 1);

                function base_url() {
                    return GOLO_PLUGIN_URL . 'includes/base/';
                }
                require_once GOLO_PLUGIN_DIR . 'includes/base/base.php';
            }

            // Core
            include_once( GOLO_PLUGIN_DIR . 'includes/class-golo-core.php' );

            // Kirki
            include_once( GOLO_PLUGIN_DIR . 'includes/kirki/kirki.php' );

            // Base Widget
            include_once( GOLO_PLUGIN_DIR . 'modules/widgets/base.php' );

            $file_path         = 'elementor-pro/elementor-pro.php';
            $installed_plugins = get_plugins();
            if( isset($installed_plugins[ $file_path ]) && is_plugin_active($file_path) ) {
                // Base Elementor
                include_once( GOLO_PLUGIN_DIR . 'modules/elementor/base.php' );
            }
        }

        /**
         *  Kirki update url
         **/
        public function kirki_update_url( $config ) {
            $config['url_path'] = GOLO_PLUGIN_URL . '/includes/kirki/';

            return $config;
        }

        /**
         *  Fix Upload Path Multisite
         **/
        public function fix_upload_paths($data){
            $data['basedir'] = $data['basedir'].'/sites/'.get_current_blog_id();
            $data['path'] = $data['basedir'].$data['subdir'];
            $data['baseurl'] = $data['baseurl'].'/sites/'.get_current_blog_id();
            $data['url'] = $data['baseurl'].$data['subdir'];

            return $data;
        }

	}

	new Golo_Framework();
}