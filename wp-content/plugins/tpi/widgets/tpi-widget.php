<?php
/**
 * Tpi Widget
 *
 * The WordPress Widget Boilerplate is an organized, maintainable boilerplate for building widgets using WordPress best practices.
 *
 * @package   Tpi Widget
 * @author    Thomas J. Sherlock <Tom@Transcent.tech>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 *
 * @wordpress-plugin
 * Plugin Name:       Tpi
 * Plugin URI:        Transcent.tech/tpi
 * Description:       Displays the plugin as a widget
 * Version:           1.0.0
 * Author:            Thomas J. Sherlock <Tom@Transcent.tech>
 * Author URI:        Transcent.tech/tjsherlock
 * Text Domain:       tpi-widget
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /lang
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */
 
 // Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}


class Tpi_Widget extends WP_Widget {

    /**
     *
     *
     * Unique identifier for your widget.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'tpi-widget';



    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    //private $plugin_name; //tjs 2016Aug05 added

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    //private $version;  //tjs 2016Aug05 added

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

        // load plugin text domain
		//add_action( 'init', array( $this, 'widget_textdomain' ) );
        load_plugin_textdomain( 'tpi_widget' );//tjs 2016Aug05

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// TODO: update description
		parent::__construct(
			$this->get_widget_slug(),
			__( 'Tpi Widget', $this->get_widget_slug() ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'This is the Tpi description.', $this->get_widget_slug() )
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );




        //_cast_die_for_sale_price
        add_action( 'wp_ajax_nopriv_tpi_cast_die_for_sale_price',  array( $this, '_cast_die_for_sale_price') );
        add_action( 'wp_ajax_tpi_cast_die_for_sale_price', array( $this, '_cast_die_for_sale_price') );


        //_cast_die_for_coupon
        add_action( 'wp_ajax_nopriv_tpi_cast_die_for_coupon',  array( $this, '_cast_die_for_coupon') );
        add_action( 'wp_ajax_tpi_cast_die_for_coupon', array( $this, '_cast_die_for_coupon') );


        add_action( 'wp_ajax_nopriv_get_sale_prices_ajax',  array( $this, 'get_sale_prices_ajax') );
        add_action( 'wp_ajax_get_sale_prices_ajax', array( $this, 'get_sale_prices_ajax') );

        add_action( 'wp_ajax_nopriv_retain_selection',  array( $this, 'retain_selection') );
        add_action( 'wp_ajax_retain_selection', array( $this, 'retain_selection') );

        //call up sale price
        add_action('woocommerce_single_product_summary', array($this, 'add_sale_price'));



        add_action('wp_ajax_nopriv_dice_reset_flags', array($this, 'dice_reset_flags'));
        add_action('wp_ajax_dice_reset_flags', array($this, 'dice_reset_flags'));

        add_action('wp_ajax_nopriv_dice_get_coupon', array($this, 'dice_get_coupon'));
        add_action('wp_ajax_dice_get_coupon', array($this, 'dice_get_coupon'));

        add_action('wp_ajax_nopriv_dice_get_sale_price', array($this, 'dice_get_sale_price'));
        add_action('wp_ajax_dice_get_sale_price', array($this, 'dice_get_sale_price'));


        //call up index of selected option to get assocation price
        add_action('wp_ajax_nopriv_tpi_get_dice_saleprice', array($this, '_get_dice_saleprice'));
        add_action('wp_ajax_dice_tpi_get_dice_saleprice', array($this, '_get_dice_saleprice'));

        add_action('wp_ajax_nopriv_dice_select_product', array($this, 'dice_select_product'));
        add_action('wp_ajax_dice_select_product', array($this, 'dice_select_product'));

        add_action( 'wp_ajax_nopriv_clear_discount',  array( $this, 'clear_discount') );
        add_action( 'wp_ajax_clear_discount', array( $this, 'clear_discount') );

        add_action('wp_ajax_nopriv_delete_dice_thrown_option', array($this, 'delete_dice_thrown_option'));
        add_action('wp_ajax_delete_dice_thrown_option', array($this, 'delete_dice_thrown_option'));

	} // end constructor



    public function get_settings()
    {
        return parent::get_settings(); // TODO: Change the autogenerated stub
    }

    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    string //Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		
		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset ( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset ( $cache[ $args['widget_id'] ] ) )
			return print $cache[ $args['widget_id'] ];
		
		// go on with your widget logic, put everything into a string and …


		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;//where is this defined and valued tjs 2016Sept13

		/* TODO: Here is where you manipulate your widget's values based on their input fields */
		ob_start();
		//include( plugin_dir_path( __FILE__ ) . 'views/tpi-widget-view.php' );
        $tpi_widget_view = plugin_dir_path( __FILE__ ) . 'views/tpi-widget-view.php';
        include $tpi_widget_view;
		$widget_string .= ob_get_clean();


		$widget_string .= $after_widget;  //where is this defined and valued tjs 2016Sept13

        $widget_string = $this->die_roll_display($widget_string);  //tjs 2016Oct27 Reactivated. Revisit html and css
		$cache[ $args['widget_id'] ] = $widget_string;

		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		print $widget_string;

	} // end widget
	
	
	public function flush_widget_cache() {
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// TODO: Here is where you update your widget's old values with the new, incoming values

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		// TODO: Define default values for your variables
		$instance = wp_parse_args(
			(array) $instance
		);

        // Check values
        if( $instance) {
            $title = esc_attr($instance['title']);
            $textarea = $instance['textarea'];
        } else {
            $title = '';
            $textarea = '';
        }

		// TODO: Store the values of the widget in their own variable

		// Display the admin form
		//include( plugin_dir_path(__FILE__) . 'views/tpi-widget-admin-view.php' );
        $tpi_widget_admin_view = plugin_dir_path(__FILE__) . 'views/tpi-widget-admin-view.php';
        include $tpi_widget_admin_view;

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		// TODO be sure to change 'widget-name' to the name of *your* plugin
		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide = false ) {// tjs 2016Aug04 added default value of false
		// TODO define activation functionality here, what functionality should that be?  How is this different from a constructor or init?

	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here what functionality should that be?
	} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-admin-styles', plugins_url( 'css/tpi-admin.css', __FILE__ ) );

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/tpi-admin.js', __FILE__ ), array('jquery') );

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/tpi-widget.css', __FILE__ ) );

	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

        $params = array(
            'save_variations_nonce'               => wp_create_nonce( 'save-variations' ),// nonce creation
        );

        wp_register_script( $this->get_widget_slug().'-script', plugins_url( 'js/tpi-widget.js', __FILE__ ), array('jquery') );
            wp_localize_script( $this->get_widget_slug().'-script', 'tpiAjax', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
            wp_localize_script( $this->get_widget_slug().'-script', 'woocommerce_admin_meta_boxes_variations', $params);//tjs 2016Dec13

        wp_enqueue_script( 'jquery' );
        //wp_enqueue_script( $this->get_widget_slug().'-script' );
        wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/tpi-widget.js', __FILE__ ), array('jquery') );

	} // end register_widget_scripts



    public static function load_widget() { register_widget("Tpi_Widget"); }

    public function die_roll_display( $content ) {

        $coupon_percentage = get_option('coupon-percentage');

        $die_text = null;

        if($coupon_percentage){

            $die_text = '<div class="discount-block"><div class="discount-title" >Discount:  </div><div id="discount-block">' . $coupon_percentage["spelled"] . '</div>'; //</p>';
            $die_text .= '<a href="' . esc_url(get_site_url() .'/clear_discount')  . '" class="tpi-clear-discount" >' . __( ' [Remove]  ', 'woocommerce' ) . '</a> </div>';

        } else {
            $die_text = '<div class="discount-block"><div class="discount-title" >Discount:  </div><div id="discount-block">' . $coupon_percentage . '</div>'; //</p>';
            $die_text .= '<a href="' . esc_url(get_site_url() .'/clear_discount' )  . '" class="tpi-clear-discount" >' . __( '  [Remove]  ', 'woocommerce' ) . '</a> </div>';
        }

        return $content . $die_text;
    }


    public function clear_discount(){

        update_option('coupon_amount', '0');
        update_option('coupon-percentage', null);

        $whatshere = $_POST['product_price'];

        wp_send_json( '0' );
    }


    public function delete_dice_thrown_option(){

        delete_option( 'dice_thrown' );

    }

    public function _cast_die(){

        $optionArray=(array)get_option('TPI_Discount_Options');
        $random_number = mt_rand((int)$optionArray['Discount Lowest'],(int)$optionArray['Discount Highest']);
        add_option('coupon_amount', $random_number);

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));

        $cart = array();

        if($use_woocommerce){

            /* Check if WooCommerce is active */
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

                $product_id = $_POST['product_id'];//get product ids, may be an array of ids for shop page for example

                $_pf = new WC_Product_Factory();
                $_product = $_pf->get_product($product_id);

                if($random_number){
                    $coupon = $this->generate_wc_coupon($random_number);
                }

                global $product;   //tjs 2017 Dec 3
                global $woocommerce;

                //$cart = $this->apply_sale_price($_product, $cart);
                $cart['product_price'] = $this->apply_sale_price($_product, $random_number);

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    $percentage = $random_number . '%';
                    $cart['percentage']['spelled'] = $random_number . '%';
                    $cart['percentage']['decimal'] = ($random_number * .01);
                    update_option('coupon-percentage', $cart['percentage']);
                }
                else {
                    wp_redirect( get_permalink( $_REQUEST['random_number'] ) );//revisit
                    exit();
                }

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                    if ( $price_html = $_product->get_price_html() ) {
                        $cart['new_price'] = '<span class="price">' . $price_html . '</span>';
                    }

                    wp_send_json( $cart );
                }
            }
        }
    }


    public function get_next_dice_thrower(){

        $previous_dice_thrower = get_option('dice_thrower');

        //get_current_user_id()

        $current_dice_thrower = (int)$previous_dice_thrower + 1;

        update_option('dice_thrower', $current_dice_thrower );

        return $current_dice_thrower;
    }



    public function _cast_die_for_sale_price(){

       if($_POST['dice_thrown']){

        $optionArray=(array)get_option('TPI_Discount_Options');
        $random_number = mt_rand((int)$optionArray['Discount Lowest'],(int)$optionArray['Discount Highest']);

        //Generate coupon for cart and checkout
       if($random_number){
           $coupon = $this->generate_wc_coupon($random_number);//generate coupon now!  Don't delay generation until presentment of cart or checkout!
           update_option('dice_coupon_properties', $coupon);// tjs 2017May15
           update_option('coupon_amount', $random_number);
           update_option('coupon_generated', false);//check flag during cart or checkout whether to generate coupon
       }

        $userid = get_current_user_id();
        // temporarily disabled
        update_option($userid . '_' . 'dice_coupon_amount', $random_number); //tjs 2017April27
        update_option($userid . '_' . 'dice_thrower', $this->get_next_dice_thrower());// tjs 2017April27 primarily a way of distinguishing one dice thrower from another if all users are anonymous (0)
        //session id?  session_began flag?  session timestamp?

        /*if($userid ==0){


        }*/

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));

        $cart = array();

        if($use_woocommerce){

            /* Check if WooCommerce is active */
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

                //$product_ids = array();
                $product_ids = $_POST['product_id'];//get product ids, may be an array of ids for shop page for example

                $_pf = new WC_Product_Factory();
                $_product = array();//[];
                if (is_array($product_ids)) {
                    foreach ($product_ids as $product_id) {
                        $_products[] = $_pf->get_product($product_id);
                    }
                }

                global $product;   //tjs 2017 Dec 3
                global $woocommerce;

                foreach ($_products as $_product) {
                    $cart['product_price'][] = $this->apply_sale_price($_product, $random_number);//index 3 for simple product here. tjs 2017Jan24 2:36pm
                }

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    $percentage = $random_number . '%';
                    $cart['percentage']['spelled'] = $random_number . '%';
                    $cart['percentage']['decimal'] = ($random_number * .01);
                    update_option('coupon-percentage', $cart['percentage']);
                }
                else {
                    wp_redirect( get_permalink( $_REQUEST['random_number'] ) );//revisit
                    exit();
                }

                update_option('dice_thrown', $product_ids);
                //delete_option('dice_single_add_to_cart');
                update_option('dice_single_add_to_cart', false);

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                    if ( $price_html = $_product->get_price_html() ) {
                        $cart['new_price'] = '<span class="price">' . $price_html . '</span>';
                        $cart['dice_thrown'] = get_option('dice_thrown');
                    }

                    wp_send_json( $cart );
                }
            }
        }

        };
    }


    public function dice_get_sale_price(){

        //called after:
        // 1. variation change
        // 2.
        // 3.

        //check if dice thrown
        if(get_option('dice_thrown')){

            //delete_option('dice_single_add_to_cart');
            update_option('dice_single_add_to_cart', false);

            $_pf = new WC_Product_Factory();

            $product_id = $_POST['dice_product_id'];//does something need to be done with the dice_product_id someplace else?  tjs 2017April07
            $_product = $_pf->get_product($product_id);


            $variations = $_product->get_children(true);

            $dice_flag = $_POST['dice_flag'];

            if($dice_flag == 'select_saleprice'){
                rsort($variations);
            }


            $sale_prices = array();//[];
            $price = array();//[];

            if(!empty($variations)){

                foreach($variations as $variation){
                    $sale_prices['variation'][] = get_post_meta( $variation, 'dice_sale_price', true );// get from option instead?
                }

            }else{
                $sale_prices['variation'][] = get_post_meta( $product_id, 'dice_sale_price', true );
            }

            if($dice_flag == 'single-product'){
                sort($sale_prices['variation']);
            }


            $sale_prices['terminus']['min'] = $sale_prices['variation'][0];
            $sale_prices['terminus']['max'] = $sale_prices['variation'][count($sale_prices['variation'])-1];

            if($dice_flag == 'select_saleprice'){
                $index = $_POST['dice_index'];
                $sale_price = $sale_prices['variation'][$index];
            }


            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                $original_variation_price_selected = get_option('original_variation_price_selected');
                delete_option( 'original_variation_price_selected');

                $discounted_variation_price_selected = get_option('discounted_variation_price_selected');
                delete_option( 'discounted_variation_price_selected');


                if ( $price_html = $_product->get_price_html() ) {
                    $cart['new_price'] = '<span class="price">' . $price_html . '</span>';
                }

                $cart['percentage'] = get_option('coupon-percentage');
                $cart['product_id'] = $product_id;
                $cart['sale_prices'] = $sale_prices;
                $cart['sale_price_applied'] = !empty($sale_prices);
                $cart['dice_thrown'] = get_option('dice_thrown');//how to set this properly?
                $cart['dice_single_add_to_cart']= false;

                $cart['dice_variation_changed'] = get_option('dice_variation_changed');// tjs 2017 May 09
                update_option('dice_variation_changed', false);

                if($original_variation_price_selected){
                    $cart['original_variation_price_selected'] = $original_variation_price_selected;
                }

                if($discounted_variation_price_selected){
                    $cart['discounted_variation_price_selected'] = $discounted_variation_price_selected;
                }



                wp_send_json( $cart );//return url to product page here?  tjs 2017March26
            }


        } else {


            if(get_option('dice_variation_changed')){
                $cart['dice_variation_changed'] = get_option('dice_variation_changed');
                $cart['dice_selected_sale_price'] = get_option('dice_selected_sale_price');
                update_option('dice_variation_changed', false);
            };

            $cart['dice_single_add_to_cart'] = get_option('dice_single_add_to_cart');
            update_option('dice_single_add_to_cart', false);

            wp_send_json( $cart );//return url to product page here?  tjs 2017March26
        }
    }



    public function dice_reset_flags(){

        update_option('dice_thrown', false);
        update_option('dice_single_add_to_cart', false);
        update_option('dice_variation_changed', false);
        update_option('coupon_amount', false);
        Update_option('coupon_code', false);
        update_option('coupon-percentage', false);
        update_option('dice_coupon_properties', false);

        /*
        update_option('discount', false);
        update_option('tax_total', false);
        update_option('order_total', false);
        update_option('subtotal', false);
        */
        wp_die();

    }


    public function dice_get_coupon(){

        $random_number = get_option('coupon_amount');
        $percentage = $random_number . '%';
        $cart['percentage']['spelled'] = $random_number . '%';
        $cart['percentage']['decimal'] = ($random_number * .01);
        update_option('coupon-percentage', $cart['percentage']);

        $cart['coupon-percentage'] = get_option('coupon-percentage');
        $cart['coupon_code'] = get_option('coupon_code');
        $coupon_properties = get_option('dice_coupon_properties');
        $cart['discount'] = $coupon_properties['discount'];
        $cart['tax_total'] = $coupon_properties['tax_total'];
        $cart['order_total'] = $coupon_properties['order_total'];
        $cart['subtotal'] = $coupon_properties['subtotal'];

        wp_send_json( $cart );
    }



    public function dice_select_product(){

        $product_id = $_POST['dice_selected_product_id'];

        $product_page = $_POST['dice_product_page'];

        $the_selected_product_id = $product_id;


        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

            //echo $product_page;
        }

    }


    public function add_sale_price(){

    }



    public function _cast_die_for_coupon(){

        $optionArray=(array)get_option('TPI_Discount_Options');
        $random_number = mt_rand((int)$optionArray['Discount Lowest'],(int)$optionArray['Discount Highest']);
        add_option('coupon_amount', $random_number);

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));

        $cart = array();

        if($use_woocommerce){

            /* Check if WooCommerce is active */
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

                $product_id = get_option('dice_product_id');

                $_pf = new WC_Product_Factory();
                $_product = $_pf->get_product($product_id);

                if($random_number){
                    $coupon = $this->generate_wc_coupon($random_number);
                    update_option('coupon-details', $coupon);
                }

                global $product;   //tjs 2017 Dec 3
                global $woocommerce;

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    $percentage = $random_number . '%';
                    $cart['percentage']['spelled'] = $random_number . '%';
                    $cart['percentage']['decimal'] = ($random_number * .01);
                    update_option('coupon-percentage', $cart['percentage']);
                }
                else {
                    wp_redirect( get_permalink( $_REQUEST['random_number'] ) );//revisit
                    exit();
                }

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                    if ( $price_html = $_product->get_price_html() ) {
                        $cart['new_price'] = '<span class="price">' . $price_html . '</span>';
                    }

                    $percentage = $random_number . '%';

                    $cart['coupon-percentage'] = get_option('coupon-percentage');
                    $cart['coupon_code'] = get_option('coupon_code');
                    $coupon_properties = get_option('dice_coupon_properties');
                    $cart['discount'] = $coupon_properties['discount'];
                    $cart['tax_total'] = $coupon_properties['tax_total'];
                    $cart['order_total'] = $coupon_properties['order_total'];
                    $cart['subtotal'] = $coupon_properties['subtotal'];

                    wp_send_json( $cart );
                }
            }
        }
    }


    public function apply_sale_price($_product, $random_number){

        $regular_price = $_product->get_regular_price();// 2016Dec08

        $sale_price = null;
        $product_price = null;
        $product_id = $_product->get_id();

        if(!empty($regular_price)){// generate sale price

            $discount_price['single'] = $this->generate_discount_price($regular_price, $random_number);

            update_post_meta( $product_id, 'dice_sale_price', $discount_price['single'] );
            update_post_meta($product_id, 'dice_price', $discount_price['single']); // illegal string offset?  tjs 2017March20

            $sale_prices['variation'][] = $discount_price['single'];
            $sale_prices['terminus']['min'] = $sale_prices['variation'][0];
            $sale_prices['terminus']['max'] = $sale_prices['variation'][0];

            $product_price['discount_price'] = $sale_prices;

        }else {

            $variations = $_product->get_children(true);

            if(!empty($variations)){

                $regular_prices = $_product->get_variation_prices(true)['regular_price'];

                $sale_prices = [];
                foreach ($regular_prices as $variation_id => $regular_price) {
                    $sale_price = $this->generate_discount_price($regular_price, $random_number);

                    update_post_meta( $variation_id, 'dice_sale_price', $sale_price );
                    update_post_meta($variation_id, 'dice_price', $sale_price);
                    $sale_prices['variation'][] = $sale_price;
                }

                $min_regular_price = $_product->get_variation_regular_price('min');
                $sale_prices['terminus']['min'] = $this->generate_discount_price($min_regular_price, $random_number);

                $max_regular_price = $_product->get_variation_regular_price('max');
                $sale_prices['terminus']['max'] = $this->generate_discount_price($max_regular_price, $random_number);

                $product_price['discount_price'] = $sale_prices;


            }else{

                /*
                 * $regular_prices = $_product->get_variation_prices(true)['regular_price'];
                $sale_prices['terminus']['min'] = $sale_prices['terminus']['max'] = $sale_prices['variation'][] = $regular_prices;
                $product_price['discount_price'] = $sale_prices;
                */

            }

        }


        $selected_price = $_POST['selected_price'];//coming from tpi-widget.js  //dice_selected_product_id here?  tjs 2017March28
        if(!empty($selected_price)){
            $product_price['selected_variation_price'] = $this->generate_discount_price($selected_price, $random_number);
        }

        $products_on_sale = get_option( "_transient_wc_products_onsale" );
        if(!$products_on_sale){$products_on_sale =true;}
        update_option("_transient_wc_products_onsale", $products_on_sale);// update to true
        //for sale price only? ^

        $_POST['product-type'] = $_POST['product_type'];// tjs 2016Dec13

        if($_product->get_type() == 'variable'){
            $this->save_variations($_product);  //tjs 2016Dec12  sale logic
        }else{
            $simple_product = 'simple product';
        }

        return $product_price;
    }

    public function apply_discount($random_number, $_product){

        if($random_number){

            $coupon = $this->generate_wc_coupon($random_number);
        }

        $regular_price = $_product->get_regular_price();// 2016Dec08
        $discount_price = null;

        if(!empty($regular_price)){

            $discount_price['single'] = $this->generate_discount_price($regular_price, $random_number);
        }

        return $discount_price['single'];  //tjs 2017March21

    }


    public function cast_die() {

        //generate a random number here between 2 and 12 inclusive.

        $optionArray=(array)get_option('TPI_Discount_Options');
        $random_number = mt_rand((int)$optionArray['Discount Lowest'],(int)$optionArray['Discount Highest']);
        add_option('coupon_amount', $random_number);

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));

        $cart = array();

        if($use_woocommerce){

            /* Check if WooCommerce is active */
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

                if($random_number){
                    $cart = $this->generate_wc_coupon($random_number);
                    $coupon = $this->generate_wc_coupon($random_number);
                }

                global $product;   //tjs 2017 Dec 3
                global $woocommerce;

                $product_id = $_POST['product_id'];//get product ids, may be an array of ids for shop page for example

                $_pf = new WC_Product_Factory();
                $_product = $_pf->get_product($product_id);

                $regular_price = $_product->get_regular_price();// 2016Dec08
                $sale_price = null;

                if(!empty($regular_price)){

                    $discount_price['single'] = $this->generate_discount_price($regular_price, $random_number);
                    update_post_meta( $product_id, '_sale_price', $discount_price['single'] );
                    update_post_meta($product_id, '_price', $discount_price['single']);
                    $sale_price = $_product->get_sale_price();

                }else{

                    $variations = $_product->get_children(true);

                    if(!empty($variations)){

                        $regular_prices = $_product->get_variation_prices(true)['regular_price'];



                        $sale_prices = [];
                        foreach ($regular_prices as $variation_id => $regular_price) {
                            $sale_price = $this->generate_discount_price($regular_price, $random_number);
                            //update_post_meta( $variation_id, '_sale_price', $sale_price );
                            update_post_meta( $variation_id, 'dice_sale_price', $sale_price );
                            //update_post_meta($variation_id, '_price', $sale_price);
                            update_post_meta($variation_id, 'dice_price', $sale_price);// I may want to change this to option only tjs 2017May08
                            $sale_prices['variation'][] = $sale_price;
                        }

                        $min_regular_price = $_product->get_variation_regular_price('min');
                        $sale_prices['terminus']['min'] = $this->generate_discount_price($min_regular_price, $random_number);

                        $max_regular_price = $_product->get_variation_regular_price('max');
                        $sale_prices['terminus']['max'] = $this->generate_discount_price($max_regular_price, $random_number);

                        $product_price['discount_price'] = $sale_prices;

                        $selected_price = $_POST['selected_price'];//coming from tpi-widget.js
                        $product_price['selected_variation_price'] = $this->generate_discount_price($selected_price, $random_number);

                        $cart['product_price'] = $product_price;
                    }
                }

                $products_on_sale = get_option( "_transient_wc_products_onsale" );
                update_option("_transient_wc_products_onsale", $products_on_sale);

                $_POST['product-type'] = $_POST['product_type'];// tjs 2016Dec13

                update_option('dice_product_id', $_POST['product_id']);

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    $percentage = $random_number . '%';
                    $cart['percentage']['spelled'] = $random_number . '%';
                    $cart['percentage']['decimal'] = ($random_number * .01);
                    update_option('coupon-percentage', $cart['percentage']);
                }
                else {
                    wp_redirect( get_permalink( $_REQUEST['random_number'] ) );//revisit
                    exit();
                }

                $this->save_variations();  //tjs 2016Dec12  sale logic

                //global $product;

                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                    if ( $price_html = $_product->get_price_html() ) {
                        $cart['new_price'] = '<span class="price">' . $price_html . '</span>';
                    }

                    wp_send_json( $cart );
                }

            }

        }

    }

    public function retain_selection(){

        if($_POST['dice_single_add_to_cart']){


            if (defined('DOING_AJAX') && DOING_AJAX) {

                $cart['dice_thrown'] = get_option('dice_thrown');

                $cart['dice_single_add_to_cart'] = $_POST['dice_single_add_to_cart'];
                update_option('dice_single_add_to_cart', $_POST['dice_single_add_to_cart']);

                $cart['original_variation_price_selected'] = $_POST['original_variation_price_selected'];

                $cart['discounted_variation_price_selected'] = $_POST['discounted_variation_price_selected'];

                $cart['percentage'] = get_option('coupon-percentage');

                wp_send_json($cart);

                //die();
            } else {
                wp_redirect(get_permalink($_REQUEST['random_number']));//revisit
                exit();
            }

        }



    }

    public function get_sale_prices() {

        //generate a random number here between 2 and 12 inclusive.

        $optionArray=(array)get_option('TPI_Discount_Options');
        $random_number = mt_rand((int)$optionArray['Discount Lowest'],(int)$optionArray['Discount Highest']);

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));

        $cart = array();

        if($use_woocommerce){

            /**
             * Check if WooCommerce is active
             **/
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                // Put your plugin code here


                global $product;

                $current_price = $product->get_price();
                $current_price_html = $product->get_price_html();

                $prices = $product->get_variation_prices( true );

                $variation_ids = $product->children;

                       // move the internal pointer to the end of the array
                $last_variation_id =  end($variation_ids['visible']);
                $max_price = $product->get_variation_price('max');

                $discount_max_price = $this->generate_discount_price($max_price, $random_number);
                update_post_meta( $last_variation_id, '_sale_price', $discount_max_price );
                update_post_meta($last_variation_id, '_price', $discount_max_price);

                $first_variation_id = reset($variation_ids['visible']);
                $min_price = $product->get_variation_price('min');
                //create reduced price here
                $discount_min_price = $this->generate_discount_price($min_price, $random_number);
                update_post_meta( $first_variation_id, '_sale_price', $discount_min_price );
                update_post_meta($first_variation_id, '_price', $discount_min_price);

                $discount_price['min'] = $discount_min_price;
                $discount_price['max'] = $discount_max_price;

                $product_price['discount_price'][] = $discount_price;
                update_option('discount-price', $discount_price );

                $selected_prices = $_POST['selected_price'];//coming from tpi-widget.js
                update_option('selected-prices',$selected_prices);

                if($selected_prices){

                    foreach($selected_prices as $selected_price){/// what to do here below?  Maybe not set coupon since sale applied?  2017Jan03
                        $product_price['selected_variation_price'] = $this->generate_discount_price($selected_price, $random_number);
                    }
                }

                $cart['product_price'] = $product_price; // record discount applied
            }

                $percentage = $random_number . '%';
                $cart['percentage']['spelled'] = $random_number . '%';
                $cart['percentage']['decimal'] = ($random_number * .01);
                update_option('coupon-percentage', $cart['percentage']);

                return $cart;
        }

    }

    //sale price only, no coupon
    public function get_sale_prices_ajax() {

        //generate a random number here between 2 and 12 inclusive.

        $optionArray=(array)get_option('TPI_Discount_Options');
        $random_number = mt_rand((int)$optionArray['Discount Lowest'],(int)$optionArray['Discount Highest']);

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));

        $cart = array();

        if($use_woocommerce) {

            /**
             * Check if WooCommerce is active
             **/
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                // Put your plugin code here

                global $product; //WC Product object

                $product_id = $_POST['product_id'];//may be an array of ids for shop page for example

                $_pf = new WC_Product_Factory();
                $_product = $_pf->get_product($product_id);

                //can not check if product is on sale this way.  Plugin can not alter the integrity of the product.  Must check a flag, such as dice_onsale
                if ($_product->is_on_sale()) {

                    $current_price = $_product->get_price();
                    $current_price_html = $_product->get_price_html();

                    $prices = $_product->get_variation_prices(true);

                    $variation_ids = null;

                    if ($_product->has_child()) {
                        $variation_ids = $_product->get_children(true);//use method not private property
                    }

                    // move the internal pointer to the end of the array

                    $last_variation_id = end($variation_ids);// No need to identify the 'visible' array since get_children is to 'true' for visible only
                    $max_price = $_product->get_variation_price('max');

                    $first_variation_id = reset($variation_ids);
                    $min_price = $_product->get_variation_price('min');

                    if ($max_price != $min_price) {

                        //create reduced price here
                        $discount_max_price = $this->generate_discount_price($max_price, $random_number);
                        update_post_meta($last_variation_id, '_sale_price', $discount_max_price);
                        update_post_meta($last_variation_id, '_price', $discount_max_price);

                        //create reduced price here
                        $discount_min_price = $this->generate_discount_price($min_price, $random_number);
                        update_post_meta($first_variation_id, '_sale_price', $discount_min_price);
                        update_post_meta($first_variation_id, '_price', $discount_min_price);

                        $discount_price['min'] = $discount_min_price;
                        $discount_price['max'] = $discount_max_price;

                        $product_price['discount_price'][] = $discount_price;
                        update_option('discount-price', $discount_price);

                        $selected_prices = $_POST['selected_price'];//coming from tpi-widget.js
                        update_option('selected-prices', $selected_prices);

                    }


                    update_option('dice_product_id', $product_id);// tjs 2017Jan06

                    global $woocommerce;

                    if ($selected_prices && is_array($selected_prices)){

                        foreach ($selected_prices as $selected_price) {
                            if(!empty($selected_price))
                            $product_price['selected_variation_price'] = $this->generate_discount_price($selected_price, $random_number);
                        }
                    }

                    // record discount applied
                    $cart['product_price'] = $product_price;


                    if (defined('DOING_AJAX') && DOING_AJAX) {
                        $percentage = $random_number . '%';
                        $cart['percentage']['spelled'] = $random_number . '%';
                        $cart['percentage']['decimal'] = ($random_number * .01);
                        update_option('coupon-percentage', $cart['percentage']);

                        wp_send_json($cart);

                        die();// Is this needed tjs 2017May18
                    } else {
                        wp_redirect(get_permalink($_REQUEST['random_number']));//revisit
                        exit();
                    }
                }

            }
        }

    }


    function _toInt($str)
    {
        return (int)preg_replace("/([^0-9\\.])/i", "", $str);
    }

    private function generate_discount_price($variation_price, int $random_number){

        $price = floatval(preg_replace('/[\$,]/', '', $variation_price));
        $discount = ($price * $random_number * .01);

        $total_after_discount =  $price - $discount;
        $total_after_discount = money_format('%.2n', $total_after_discount);

        return $total_after_discount;
    }

    private function generate_wc_coupon(int $random_number){
        //Check and delete discount coupon
        global $woocommerce;

        $coupon_added = get_option('coupon_code');

        if($coupon_added){
            delete_option('coupon_code');
            // remove discount
            $woocommerce->cart->remove_coupon($coupon_added);
        }

        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $coupon_code = $f->format($random_number);
        $discount_type = 'percent'; // Type: fixed_cart, percent, fixed_product, percent_product
        $coupon_code .= $discount_type;

        $amount = $random_number;

        global $user_ID, $wpdb;

        $query = $wpdb->prepare(
            'SELECT ID FROM ' . $wpdb->posts . '
        WHERE post_title = %s
        AND post_type = \'shop_coupon\'',
            $coupon_code
        );
        $wpdb->query( $query );

        if ( $wpdb->num_rows ) {
            $post_id = $wpdb->get_var( $query );
            $meta = get_post_meta( $post_id, 'times', TRUE );
            $meta++;
            update_post_meta( $post_id, 'times', $meta );
        } else {

            $coupon = array(
                'post_title' => $coupon_code,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'shop_coupon'
            );

            $new_coupon_id = wp_insert_post($coupon);  //Check for existing coupon before creating new coupon.   tjs 2016Sept27

            // Add meta
            update_post_meta($new_coupon_id, 'discount_type', $discount_type);
            update_post_meta($new_coupon_id, 'coupon_amount', $amount);
            update_post_meta($new_coupon_id, 'individual_use', 'no');  //Can use with other coupons  tjs 2016Sept27
            update_post_meta($new_coupon_id, 'product_ids', '');
            update_post_meta($new_coupon_id, 'exclude_product_ids', '');
            update_post_meta($new_coupon_id, 'usage_limit', '');
            update_post_meta($new_coupon_id, 'expiry_date', '');
            //update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );  //Gives this options to merchant in admin page tjs 2016Sept12
            update_post_meta($new_coupon_id, 'apply_before_tax', (esc_attr(get_option('apply_before_tax')) ? esc_attr(get_option('apply_before_tax')) : 'yes'));
            update_post_meta($new_coupon_id, 'free_shipping', 'no');

        }

        //Coupon code already applied?
        if ( !$woocommerce->cart->has_discount( $coupon_code ) ) {
            $woocommerce->cart->add_discount($coupon_code);//tjs 2016Sept21
            update_option('coupon_code', $coupon_code );
        }

        //if there is a tpi discount then remove before adding new discount. 2016Oct19

        $discount = ($woocommerce->cart->subtotal * $amount * .01);
        $discount = money_format('%.2n', $discount);
        $total_after_discount =  $woocommerce->cart->subtotal - $discount - $woocommerce->cart->tax_total;//This discount includes the discount and tax.
        //How to account for additional discounts?  Do I need to account for additional discounts?  tjs 2016Setp27

        $cart = array(
            'coupon_code' => $coupon_code,
            'total' => $woocommerce->cart->total,
            'subtotal' => $woocommerce->cart->subtotal,
            'subtotal_ex_tax' => $woocommerce->cart->subtotal_ex_tax,
            'discount' => $discount,
            'order_total' => $total_after_discount ,
            'tax_total' => $woocommerce->cart->tax_total,
            );


        update_option ('dice_coupon_properties', $cart);

        return $cart;
    }



public function add_discounted_single_variation($content){

    $cart = $this->get_sale_prices();

    $min = $cart['product_price']['discount_price'][0]['min'];
    $max = $cart['product_price']['discount_price'][0]['max'];


    $currencySymbol = '<span class="woocommerce-Price-currencySymbol">$</span>';
    $price_seperator = '<span class="price-Seperator"> – </span>';

    $sale_price =   '<span class="price">' .
                    '<span class="reduced-Price first-Price amount">' .
                    $currencySymbol . $min .
                    '</span>' .
                    $price_seperator .
                    '<span class="reduced-Price first-Price amount">' .
                    $currencySymbol . $max .
                    '</span>' .
                    '</span>';



    return $sale_price . $content;
}




    /**
     * Save variations via AJAX.
     */
    private function save_variations($_product) {
        ob_start();

        check_ajax_referer( 'save-variations', 'security' );

        // Current code requires log in.  How to do without login?  tjs 2017Jan17
        // Check permissions again and make sure we have what we need

        /*
         * Temporarily disable 'edit products' check  2017Jan17
         */
        /*
        if(!current_user_can('edit_products')){
            $current_user = wp_get_current_user();
            $current_user->add_cap('edit_products');
            //$this->tpi_set_user_cap('edit_products');
        }
        */

        /*
        if ( ! current_user_can( 'edit_products' ) || empty( $_POST ) || empty( $_POST['product_id'] ) ) {
            die( -1 );
        }
        */

        if ( empty( $_POST ) || empty( $_POST['product_id'] ) ) {
            die( -1 );
        }

        // Remove previous meta box errors
        WC_Admin_Meta_Boxes::$meta_box_errors = array();

        //$product_id   = absint( $_POST['product_id'] );
        $product_id = $_product->get_id();

        //$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );
        $product_type = $_product->get_type();

        $product_type_terms = wp_get_object_terms( $product_id, 'product_type' );

        // If the product type hasn't been set or it has changed, update it before saving variations
        if ( empty( $product_type_terms ) || $product_type !== sanitize_title( current( $product_type_terms )->name ) ) {
            wp_set_object_terms( $product_id, $product_type, 'product_type' );
        }

        WC_Meta_Box_Product_Data::save_variations( $product_id, get_post( $product_id ) );

        do_action( 'woocommerce_ajax_save_product_variations', $product_id );

        // Clear cache/transients
        wc_delete_product_transients( $product_id );

        if ( $errors = WC_Admin_Meta_Boxes::$meta_box_errors ) {
            echo '<div class="error notice is-dismissible">';

            foreach ( $errors as $error ) {
                echo '<p>' . wp_kses_post( $error ) . '</p>';
            }

            echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'woocommerce' ) . '</span></button>';
            echo '</div>';

            delete_option( 'woocommerce_meta_box_errors' );
        }

        //die();
    }

    function tpi_create_temporary_dice_user(){

        $website = "http://example.com";//need to get current website
        $userdata = array(
            'user_login'  =>  'temp_dice_user',
            'user_url'    =>  $website,
            'user_pass'   =>  'password1'  // When creating an user, `user_pass` is expected. user password generator
        );

        $user_id = wp_insert_user( $userdata ) ;

        //add a role to the user

        $temp_user = new WP_User( $user_id );

        $temp_user->add_cap('edit_products');


        return $user_id;
    }
    /*
     * Get user's role
     *
     * If $user parameter is not provided, returns the current user's role.
     * Only returns the user's first role, even if they have more than one.
     * Returns false on failure.
     *
     * @param  mixed       $user User ID or object.
     * @return string|bool       The User's role, or false on failure.
     */
    function tpi_get_user_role( $user = null ) {
        $user = $user ? new WP_User( $user ) : wp_get_current_user();
        return $user->roles ? $user->roles[0] : false;
    }

    private function tpi_set_user_cap($capability_name){

        $roles =  $this->tpi_get_user_role();

        // get the the role object
        //$role_object = get_role( $role_name );

        // add $cap capability to this role object
        $roles->add_cap( $capability_name );

        // remove $cap capability from this role object
        //$role_object->remove_cap( $capability_name );

    }



    /**
     * Bulk action - Sale Schedule.
     * @access private
     * @used-by bulk_edit_variations
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_sale_schedule( $variations, $data ) {
        if ( ! isset( $data['date_from'] ) && ! isset( $data['date_to'] ) ) {
            return;
        }

        foreach ( $variations as $variation_id ) {
            // Price fields
            $regular_price = get_post_meta( $variation_id, '_regular_price', true );
            $sale_price    = get_post_meta( $variation_id, '_sale_price', true );

            // Date fields
            $date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
            $date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );

            if ( 'false' === $data['date_from'] ) {
                $date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
            } else {
                $date_from = $data['date_from'];
            }

            if ( 'false' === $data['date_to'] ) {
                $date_to = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';
            } else {
                $date_to = $data['date_to'];
            }

            _wc_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
        }
    }


    /**
     * Bulk action - Set Sale Prices.
     * @access private
     * @used-by bulk_edit_variations
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_sale_price( $variations, $data ) {
        if ( ! isset( $data['value'] ) ) {
            return;
        }

        foreach ( $variations as $variation_id ) {
            // Price fields
            $regular_price = get_post_meta( $variation_id, '_regular_price', true );
            $sale_price    = wc_clean( $data['value'] );

            // Date fields
            $date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
            $date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );
            $date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
            $date_to   = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';

            _wc_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
        }
    }


    public function _get_dice_saleprice()
    {

        if (get_option('dice_thrown')) {


            update_option('dice_single_add_to_cart', $_POST['dice_single_add_to_cart']);
            update_option('dice_variation_changed', $_POST['dice_variation_changed']);


            $_pf = new WC_Product_Factory();


            $product_id = $_POST['dice_product_id'];//does something need to be done with the dice_product_id someplace else?  tjs 2017April07

            $_product = $_pf->get_product($product_id);


            $variations = $_product->get_children(true);

            rsort($variations);

            $sale_prices = array();

            if (!empty($variations)) {

                foreach ($variations as $variation) {

                    $sale_prices['variation'][] = get_post_meta($variation, 'dice_sale_price', true);
                    // get original price variations as well?  tjs 2017May09

                }
            }


            // set up loop here

            //$sale_prices = get_post_meta('dice_sale_price');
            $index = $_POST['dice_index'] - 1;
            $sale_price = $sale_prices['variation'][$index];


            if (defined('DOING_AJAX') && DOING_AJAX) {


                $cart['dice_thrown'] = get_option('dice_thrown');

                $cart['dice_selected_sale_price'] = $sale_price;
                update_option('dice_selected_sale_price', $sale_price);//  discounted_variation_price_selected?

                $cart['discounted_variation_price_selected'] = $sale_price;
                update_option('discounted_variation_price_selected', $sale_price );

                wp_send_json($cart);//return url to product page here?  tjs 2017March26
            }
        }else {

            $cart['dice_selected_sale_price'] = null;
            $cart['discounted_variation_price_selected'] = null;//?
            $cart['dice_thrown'] = false;
            wp_send_json($cart);

        }


    }



} // end class



