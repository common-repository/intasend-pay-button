<?php
/**
 * Plugin Name:       IntaSend Pay Button
 * Plugin URI:        https://intasend.com/
 * Description:       Collect Card and Mobile Payments using IntaSend Payment Gateway.
 * Version:           1.0.7
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Tested up to:      6.6.2
 * Author:            IntaSend Solutions Limited(Felix Cheruiyot, Mugendi Gitonga)
 * Author URI:        https://developers.intasend.com/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$table_prefix   = $wpdb->prefix;


define('INTAPYBTN_PRINTMETHOD_FILE_PATH', dirname(__FILE__));
define('INTAPYBTN_TABLE_PREFIX', $table_prefix);

/*
    Load necessary files
*/
function INTAPYBTN_loaded_init(){
    include_once INTAPYBTN_PRINTMETHOD_FILE_PATH."/admin/class.ButtonsListTable.php";
    include_once INTAPYBTN_PRINTMETHOD_FILE_PATH."/admin/admin-settings.php";
    include_once INTAPYBTN_PRINTMETHOD_FILE_PATH."/shortcode.php";
}

function INTAPYBTN_scripts () {
    wp_register_script( 'INTAPYBTN-inline', plugin_dir_url( __FILE__ ) . 'assets/js/intasend-inline-min.js', array( 'jquery' ));
    wp_register_script( 'INTAPYBTN-pay-btn', plugin_dir_url( __FILE__ ) . 'assets/js/intasend-pay-btn.js', array( 'jquery', 'INTAPYBTN-inline' ));
    wp_enqueue_script('INTAPYBTN-inline');
    wp_enqueue_script('INTAPYBTN-pay-btn');

    wp_register_style( 'INTAPYBTN-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), '1.0.0', 'all' );
    wp_enqueue_style( 'INTAPYBTN-style' );

    // Dynamic data (could be your button ID, URLs, nonce, etc.)
    global $wpdb;
    $table = INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";
    $intPayBtns = $wpdb->get_results( "SELECT * FROM $table ");

    $live = get_option('INTAPYBTN_live_key') == 1;

    $dynamic_data = array(
        'publishable_key' => get_option('INTAPYBTN_publishable_key'),
        'redirect_url'=> get_option('INTAPYBTN_redirect_url'),
        'live_key'=> $live,
        'buttons' => $intPayBtns,
    );

    // Pass the data to the script
    wp_localize_script( 'INTAPYBTN-pay-btn', 'intBtnData', $dynamic_data );
}

add_action( 'plugins_loaded', 'INTAPYBTN_loaded_init', 0 );
add_action( 'wp_enqueue_scripts', 'INTAPYBTN_scripts' );

function plugin_updates() {
    global $wpdb, $plugin_version;

    $table     = INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";
	$column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table} LIKE 'redirect_url'");

    if (empty($column_exists)) {
		$wpdb->query("ALTER TABLE $table ADD COLUMN `redirect_url` VARCHAR(150) NULL DEFAULT ''");
	}
    // update option
}
add_action( 'plugins_loaded', 'plugin_updates', 0 );

/*
    Create table on activation
*/
function INTAPYBTN_on_install_callback(){
    global $wpdb;
    $table     = INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";
    $structure = "CREATE TABLE $table (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,shortcode_data VARCHAR(40) NOT NULL,payment_currency VARCHAR(3) NOT NULL,button_label VARCHAR(40) NOT NULL,payment_amount INT(11) NOT NULL,method VARCHAR(40) NOT NULL,card_tarrif VARCHAR(40) NOT NULL,mobile_tarrif VARCHAR(40) NOT NULL,redirect_url VARCHAR(150) NULL,is_active TINYINT(1) NOT NULL)";
    $wpdb->query($wpdb->prepare($structure));
}
register_activation_hook(__FILE__, 'INTAPYBTN_on_install_callback');
