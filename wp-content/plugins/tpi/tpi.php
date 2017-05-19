<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://transcent.tech
 * @since             1.0.0
 * @package           Tpi
 *
 * @wordpress-plugin
 * Plugin Name:       Transcent Plugin
 * Plugin URI:        http://transcent.tech/tpi/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Thomas J. Sherlock
 * Author URI:        http://transcent.tech/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tpi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {//stopped here tjs 2016Sept10
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_tpi() {
    /**
     * Check if WooCommerce is active
     **/

    $whatthis = plugin_dir_path( __FILE__ ) . 'includes/class-tpi-activator.php';
    require_once $whatthis;
    Tpi_Activator::activate();



}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_tpi() {
    $whatsthat = plugin_dir_path( __FILE__ ) . 'includes/class-tpi-deactivator.php';
	require_once $whatsthat;
	Tpi_Deactivator::deactivate();
}



register_activation_hook( __FILE__, 'activate_tpi' );
register_deactivation_hook( __FILE__, 'deactivate_tpi' );  //stopped here tjs 2016Sept10

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
//require plugin_dir_path( __FILE__ ) . 'includes/class-tpi.php';
$class_tpi =  plugin_dir_path( __FILE__ ) . 'includes/class-tpi.php';
require $class_tpi;



function activate_tpi_widget(){
    $whatsiz = plugin_dir_path( __FILE__ ) . 'widgets/tpi-widget.php';
    require_once $whatsiz;
    //$tpi = new Tpi();
    //new Tpi_Widget($tpi.get_plugin_name(), $tpi.get_version());
    new Tpi_Widget();
}


function register_tpi_widgets() {
    register_widget( 'Tpi_Widget' );
}


function activate_tpi_ajax(){

}


//add_action( 'admin_menu', 'tpi_admin_menu' );

function tpi_admin_menu_neg_1() {
    //add_menu_page( 'TPI Cast Die', 'Die Casting', 'manage_options', 'tpi-cast-die', 'render_tpi_cast_die_page', 'dashicons-tickets', 6  );

    //add_menu_page( 'TPI Cast Die', 'Die Casting', 'manage_options', 'tpi-cast-die', 'render_tpi_cast_die_page', 'dashicons-admin-generic', 6  );

    //$dice_image = plugin_dir_path( dirname( __FILE__ ) ) . 'tpi/public/images/plainicon.com-41644-128px.png';
    $dice_image = '/wp-content/plugins/tpi/public/images/tpi-die-with-four-corners-20x20.png';
    add_menu_page( 'TPI Cast Die', 'Die Casting', 'manage_options', 'tpi-cast-die', 'render_tpi_cast_die_page', $dice_image, 6  );


    // wp-svg-dice

    //add_action('admin_init', 'tpi_custom_settings');
}

/*
function render_tpi_cast_die_page() {
    //echo 'This is the Cast Die page.';

    $testpath =  plugin_dir_path( dirname( __FILE__ ) ) . 'tpi/admin/partials/tpi-admin-display.php';
    require_once $testpath;
}
*/

/*
add_filter('the_title', 'TPI_the_title', 10,2);

function TPI_the_title($title, $id){
    $title_discount=(array)get_option('TPI_discount_Options');

    return $title_discount[0];  //stopped here tjs 2016Sept10

    //return  sprintf('span style="')

}*/

register_activation_hook( __FILE__, 'TPI_Lowest_Discount_Activated');
function TPI_Lowest_Discount_Activated(){

    $default_lowest_discount = '1';
    add_option('Lowest_Discount_Option', $default_lowest_discount);
}

register_deactivation_hook( __FILE__, 'TPI_Lowest_Discount_Deactivated');
function TPI_Lowest_Discount_Deactivated(){

    delete_option('Lowest_Discount_Option');
}


register_activation_hook( __FILE__, 'TPI_Highest_Discount_Activated');
function TPI_Highest_Discount_Activated(){

    $default_highest_discount = '6';
    add_option('Highest_Discount_Option', $default_highest_discount);
}

register_deactivation_hook( __FILE__, 'TPI_Highest_Discount_Deactivated');
function TPI_Highest_Discount_Deactivated(){

    delete_option('Highest_Discount_Option');
}



register_activation_hook( __FILE__, 'apply_before_tax_activated');
function apply_before_tax_activated(){

    $default_apply_before_tax = 'yes';
    add_option('apply_before_tax', $default_apply_before_tax);
}

register_deactivation_hook( __FILE__, 'apply_before_tax_deactivated');
function apply_before_tax_deactivated(){

    delete_option('apply_before_tax');
}

register_activation_hook( __FILE__, 'use_woocommerce_activated');
function use_woocommerce_activated(){

    $default_use_commerce = 'yes';
    add_option('use_woocommerce', $default_use_commerce);
    // How to get default value to display?
}

register_deactivation_hook( __FILE__, 'use_woocommerce_deactivated');
function use_woocommerce_deactivated(){

    delete_option('use_woocommerce');
}


/*
 *
 *  tpi_shop_page
 *
 *  tpi_product_page
 *
 *  tpi_cart
 *
 *  tpi_checkout
 *
 */

register_activation_hook( __FILE__, 'TPI_Shop_Page_Activated');
function TPI_Shop_Page_Activated(){

    $hide = false;
    add_option('tpi_shop_page', $hide);
}

register_deactivation_hook( __FILE__, 'TPI_Shop_Page_Deactivated');
function TPI_Shop_Page_Deactivated(){

    delete_option('tpi_shop_page');
}



register_activation_hook( __FILE__, 'TPI_Product_Page_Activated');
function TPI_Product_Page_Activated(){

    $hide = false;
    add_option('tpi_product_page', $hide);
}

register_deactivation_hook( __FILE__, 'TPI_Product_Page_Deactivated');
function TPI_Product_Page_Deactivated(){

    delete_option('tpi_product_page');
}




register_activation_hook( __FILE__, 'TPI_Cart_Activated');
function TPI_Cart_Activated(){

    $hide = false;
    add_option('tpi_cart', $hide);
}

register_deactivation_hook( __FILE__, 'TPI_Cart_Deactivated');
function TPI_Cart_Deactivated(){

    delete_option('tpi_cart');
}



register_activation_hook( __FILE__, 'TPI_Checkout_Activated');
function TPI_Checkout_Activated(){

    $hide = false;
    add_option('tpi_checkout', $hide);
}

register_deactivation_hook( __FILE__, 'TPI_Checkout_Deactivated');
function TPI_Checkout_Deactivated(){

    delete_option('tpi_checkout');
}







/*
     *      Add option page
     */
//add_action('admin_menu', 'TPI_Discount_Option_Page_Menu');
Function TPI_Discount_Option_Page_Menu_neg_1(){

    add_options_page('Discount Option Page', 'Discount Option', 'manage_options', 'discount-option-page', 'Discount_Option_Page_Setup');

}



function Discount_Option_Page_Setup_neg_1(){
    ?>
        <div class="wrap">
            <h2>Discount Option Page</h2>
            <form method="post" action="options.php">
                <!-- settings_fields( $option_group ) -->
                <?php settings_fields('tpi-discount-settings'); ?>
                <!-- do settings sections($page) -->
                <?php do_settings_sections('discount-option-page'); ?>
                <?php submit_button('Save Percentages'); ?>
            </form>
        </div>

<?php
}


/*
 *  Setup section, settings
 */
//add_action('admin_init', 'TPI_Discount_Settings_Init');

function TPI_Discount_Settings_Init_neg_1(){  //stopped here tjs 2016Sept10

    //add_settings_section($id, $title, $callback, $page)
    add_settings_section('TPI-Discount-Section', 'Different discount percentages for the die', 'TPI_discount_settings_setup', 'discount-option-page' );

    //add option fields in this section
    //add_settings_field($id, $title, $callback, $page, $section, $args)
    add_settings_field('TPI-Discount-Lowest-ID', 'Lowest Discount Percentage', 'TPI_Textbox', 'discount-option-page', 'TPI-Discount-Section',
        array('name' => 'Discount Lowest') );
    add_settings_field('TPI-Discount-Highest-ID', 'Highest Discount Percentage', 'TPI_Textbox', 'discount-option-page', 'TPI-Discount-Section',
        array('name' => 'Discount Highest') );

    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // Put your plugin code here

        add_settings_field('Use-WooCommerce-ID', 'Use Woocommerce', 'TPI_Textbox', 'discount-option-page', 'TPI-Discount-Section',//add as a checkbox
            array('name' => 'Use WooCommerce') );//set default value?

        add_settings_field('Apply-Before-Tax-ID', 'Apply before tax', 'TPI_Textbox', 'discount-option-page', 'TPI-Discount-Section',
            array('name' => 'Apply before tax') );
    }


    //Tell Wordpress about our option name in database
    // register_setting( $option_group, $option_name, $sanitize_callback)
    register_setting('tpi-discount-settings', 'TPI_Discount_Options', 'TPI_Discount_Validation');
}

function TPI_Discount_Validation_neg_1($input){

    return $input;
}

function TPI_Discount_Settings_Setup_neg_1(){
    echo '<p>Set the higher and lower discount rates here.</p>';
}

function TPI_Textbox_neg_1($args){
    extract($args);
    $optionArray=(array)get_option('TPI_Discount_Options');
    $current_value = $optionArray[$name];
    echo '<input type="text" name="TPI_Discount_Options[' . $name . ']" value="' .$current_value. '"/>';

}

/*function tpi_custom_settings(){
    register_setting(string $option_group, string $option_name, callback $sanitize_callback);
}*/


/*
function custom_woocommerce_locate_template( $template, $template_name, $template_path ) {

    global $woocommerce;

    $_template = $template;

    if ( ! $template_path ) $template_path = $woocommerce->template_path();

    $plugin_path  = $this->get_plugin_path() . '/commerce-systems/woocommerce/';

    // Look within passed path within the theme - this is priority

    $template = locate_template(

        array(

            $template_path . $template_name,

            $template_name

        )

    );



    // Modification: Get the template from this plugin, if it exists

    if ( ! $template && file_exists( $plugin_path . $template_name ) )

        $template = $plugin_path . $template_name;



    // Use default template

    if ( ! $template )

        $template = $_template;



    // Return what we found

    return $template;

}


function get_plugin_path() {

    // gets the absolute path to this plugin directory

    return untrailingslashit( plugin_dir_path( __FILE__ ) );

}

*/


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tpi() {

	$plugin = new Tpi();
	$plugin->run();

}
run_tpi();  //stopped here tjs 2016Sept10
