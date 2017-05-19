<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://transcent.tech
 * @since      1.0.0
 *
 * @package    Tpi
 * @subpackage Tpi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tpi
 * @subpackage Tpi_Name/public
 * @author     Thomas J. Sherlock <Tom@transcent.tech>
 */
class Tpi_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tpi-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tpi-public.js', array( 'jquery' ), $this->version, false );
         /*wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'widget/js/tpi-widget.js', array( 'jquery' ), $this->version, false );*/

	}


    public function hide_tpi_widget($all_widgets) {

        //this should run on the homepage / frontpage only! you can use more conditions here
        if(is_front_page() || is_home())
        {
            //foreach ($all_widgets['primary-widget-area'] as $i => $inst)
            foreach ($all_widgets['sidebar-1'] as $i => $inst)
            {
                //check if the id for the archives widgets exists.
                $pos = strpos($inst, 'tpi-widget-3');

                if($pos !== false)
                {
                    //remove the archives widget by unsetting it's id
                    unset($all_widgets['sidebar-1'][$i]);
                }
            }
        } else {

            $current_post_type = get_post_type();

            if(is_shop() ) {

                    $tpi_shop_page_option = esc_attr( get_option('tpi_shop_page'));
                    if(!$tpi_shop_page_option){

                        foreach ($all_widgets['sidebar-1'] as $i => $inst)
                        {
                            //check if the id for the archives widgets exists.
                            $pos = strpos($inst, 'tpi-widget-3');

                            if($pos !== false)
                            {
                                //remove the archives widget by unsetting it's id
                                unset($all_widgets['sidebar-1'][$i]);
                            }
                        }
                    }
            }

            //if($current_post_type == 'single-product'){
            if(is_product()){

                $tpi_product_page_option = esc_attr( get_option('tpi_product_page'));

                if(!$tpi_product_page_option){

                    foreach ($all_widgets['sidebar-1'] as $i => $inst)
                    {
                        //check if the id for the archives widgets exists.
                        $pos = strpos($inst, 'tpi-widget-3');

                        if($pos !== false)
                        {
                            //remove the archives widget by unsetting it's id
                            unset($all_widgets['sidebar-1'][$i]);
                        }
                    }

                }

            }

            if(is_cart()){

                $tpi_cart_option = esc_attr( get_option('tpi_cart'));

                if(!$tpi_cart_option) {

                    foreach ($all_widgets['sidebar-1'] as $i => $inst)
                    {
                        //check if the id for the archives widgets exists.
                        $pos = strpos($inst, 'tpi-widget-3');

                        if($pos !== false)
                        {
                            //remove the archives widget by unsetting it's id
                            unset($all_widgets['sidebar-1'][$i]);
                        }
                    }
                }
            }

            if(is_checkout()){

                $tpi_checkout_option = esc_attr( get_option('tpi_checkout'));

                if(!$tpi_checkout_option) {

                    foreach ($all_widgets['sidebar-1'] as $i => $inst)
                    {
                        //check if the id for the archives widgets exists.
                        $pos = strpos($inst, 'tpi-widget-3');

                        if($pos !== false)
                        {
                            //remove the archives widget by unsetting it's id
                            unset($all_widgets['sidebar-1'][$i]);
                        }
                    }
                }
            }

        }//else

        //comment the following and tell me what happens!
        return $all_widgets;

    }

    private function generate_discount_price($variation_price, int $random_number){

        $price = preg_replace('/[\$,]/', '', $variation_price);
        $discount = ($price * $random_number * .01);

        $total_after_discount =  $price - $discount;
        $total_after_discount = money_format('%.2n', $total_after_discount);

        return $total_after_discount;
    }

    private function generate_wc_coupon(int $random_number)
    {
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

        return $cart;
    }

    private function get_sale_prices() {

        //generate a random number here between 2 and 12 inclusive.

        $optionArray=(array)get_option('TPI_Discount_Options');
        $random_number = mt_rand((int)$optionArray['Discount Lowest'],(int)$optionArray['Discount Highest']);
        add_option('coupon_amount', $random_number);

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));


        //create a switch
        /*switch ($ecommerce){
            case 'woocommerce': */

        $cart = array();

        if($use_woocommerce){


            /*
                        $prices = $this->get_variation_prices( true );

                        // No variations, or no active variation prices
                        if ( $this->get_price() === '' || empty( $prices['price'] ) ) {
                            $price = apply_filters( 'woocommerce_variable_empty_price_html', '', $this );
                        } else {
                            $min_price = current( $prices['price'] );
                            $max_price = end( $prices['price'] );
                            $price     = $min_price !== $max_price ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_price ), wc_price( $max_price ) ) : wc_price( $min_price );
                            $is_free   = $min_price == 0 && $max_price == 0;
            */


            /**
             * Check if WooCommerce is active
             **/
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                // Put your plugin code here

                if($random_number){
                    $cart = $this->generate_wc_coupon($random_number);
                }

                global $product;


                $current_price = $product->get_price();
                $current_price_html = $product->get_price_html();
                //$product->get_

                $prices = $product->get_variation_prices( true );

                $variation_ids = $product->children;

                // move the internal pointer to the end of the array
                $last_variation_id =  end($variation_ids['visible']);
                $max_price = $product->get_variation_price('max');
                //create reduced price here
                $discount_max_price = $this->generate_discount_price($max_price, $random_number);
                update_post_meta( $last_variation_id, '_sale_price', $discount_max_price );


                $first_variation_id = reset($variation_ids['visible']);
                $min_price = $product->get_variation_price('min');
                //create reduced price here
                $discount_min_price = $this->generate_discount_price($min_price, $random_number);
                update_post_meta( $first_variation_id, '_sale_price', $discount_min_price );


                $discount_price['min'] = $discount_min_price;
                $discount_price['max'] = $discount_max_price;


                $product_price['discount_price'][] = $discount_price;
                update_option('discount-price', $discount_price );


                $selected_prices = $_POST['selected_price'];//coming from tpi-widget.js
                update_option('selected-prices',$selected_prices);


                if($selected_prices){

                    foreach($selected_prices as $selected_price){
                        $product_price['selected_variation_price'] = $this->generate_discount_price($selected_price, $random_number);
                    }
                }

                // record discount applied
                $cart['product_price'] = $product_price;


                /*
                                $product_price = array();
                                $product_price = $_POST['product_price'];//coming from tpi-widget.js
                                if($product_price){

                                    foreach($product_price as $price){

                                        $product_price['original_price'][] = $price;

                                        $discount_price = array();
                                        foreach($price as $variation_price){
                                            //generate discount price per product per variation
                                            $discount_price[] = $this->generate_discount_price($variation_price, $random_number);
                                        }

                                        $product_price['discount_price'][] = $discount_price;
                                        update_option('discount-price', $discount_price );
                                    }

                                    $selected_prices = $_POST['selected_price'];//coming from tpi-widget.js
                                    update_option('selected-prices',$selected_prices);

                                    if($selected_prices){

                                        foreach($selected_prices as $selected_price){
                                            $product_price['selected_variation_price'] = $this->generate_discount_price($selected_price, $random_number);
                                        }
                                    }

                                    // record discount applied
                                    $cart['product_price'] = $product_price;
                                }

                                */


            }

            ///if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $percentage = $random_number . '%';
            $cart['percentage']['spelled'] = $random_number . '%';
            $cart['percentage']['decimal'] = ($random_number * .01);
            update_option('coupon-percentage', $cart['percentage']);

            //wp_send_json( $cart );


            return $cart;

            //die();
            //}
            /* else {
                 wp_redirect( get_permalink( $_REQUEST['random_number'] ) );//revisit
                 exit();
             }*/
        }

    }


    public function add_discounted_single_variation(){


        $cart = $this->get_sale_prices();

        //WC_Product_Variable $product_variable = WC_Product_Variable::class;

        $min = $cart['product_price']['discount_price'][0]['min'];
        $max = $cart['product_price']['discount_price'][0]['max'];




        $currencySymbol = '<span class="woocommerce-Price-currencySymbol">$</span>';
        $price_seperator = '<span class="price-Seperator"> – </span>';

        $price_seperator = $currencySymbol = $min = $max = ' ';

        $sale_price =   '<span class="price">' .
            '<span class="reduced-Price first-Price amount">' .
            $currencySymbol . $min .
            '</span>' .
            $price_seperator .
            '<span class="reduced-Price first-Price amount">' .
            $currencySymbol . $max .
            '</span>' .
            '</span>';


        /*
        jQuery('div[itemprop="offers"] > p.price').before(
            '<p class="price">' +
            '<span class="original-Price first-Price amount">' +
            currencySymbol + first_original_price +
            '</span>' +
            price_seperator +
            '  <span class="original-Price last-Price amount">' +
            currencySymbol + last_original_price +
            '</span>' +
            '</p>');// .price
        */

        echo $sale_price;

        //return $sale_price . $content;
    }

    public function add_discounted_selected_variation(){


        $random_number = get_option('coupon_amount');

        $use_woocommerce = esc_attr( get_option('use_woocommerce'));


        //create a switch
        /*switch ($ecommerce){
            case 'woocommerce': */

        $cart = array();

        if($use_woocommerce) {

        }


        $selected_prices = $_POST['selected_price'];//coming from tpi-widget.js
        update_option('selected-prices',$selected_prices);


        if($selected_prices){

            foreach($selected_prices as $selected_price){
                $product_price['selected_variation_price'] = $this->generate_discount_price($selected_price, $random_number);
            }
        }

        // record discount applied
        $cart['product_price'] = $product_price;



        $dollar = "$";
        $full_Price = null;


        if(empty($full_Price)){
            $dollar = $full_Price;
        }

        $currencySymbol = '<span class="woocommerce-Price-currencySymbol">' . $dollar . '</span>';
        $price_seperator = '<span class="price-Seperator"> – </span>';

        $selected_sale_price = '<span class="price">' .
                            '<span class="original-selected-Price amount">' .
                            $currencySymbol . $full_Price .
                            '</span>' .
                            '</span>';

        echo $selected_sale_price;
    }




    function get_plugin_path() {

        // gets the absolute path to this plugin directory

        return untrailingslashit( plugin_dir_path( __FILE__ ) );

    }






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

        $sought_file = $plugin_path . $template_name;

        //if ( ! $template && file_exists( $plugin_path . $template_name ) )
        if ( ! $template && file_exists( $sought_file ) )

            $template = $sought_file;



        // Use default template

        if ( ! $template )

            $template = $_template;



        // Return what we found

        return $template;

    }
*/

}
