<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://transcent.tech
 * @since      1.0.0
 *
 * @package    Tpi
 * @subpackage Tpi/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tpi
 * @subpackage Tpi/includes
 * @author     Thomas J Sherlock <Tom@transcent.tech>
 */
class Tpi {




    /**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tpi_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $ecommerce_kit;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'tpi';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
        $this->load_widgets(); //tjs 2016Aug05

		add_action('admin_menu', array($this,'dice_coupon_admin_menu'));// add to menu tjs 2016Sept13
	}






	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tpi-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tpi-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tpi-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tpi-public.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/tpi-widget.php';//tjs 2016August04

        /**
         * The class responsible for ajax.
         *
         */
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/tpi-ajax.php';//tjs 2016August04


		$this->loader = new Tpi_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tpi_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tpi_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );



	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Tpi_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );


        //$this->loader->add_action('woocommerce_before_variations_form',$plugin_public, 'add_discounted_single_variation');//10,1  tjs 2016Nov17
        //$this->loader->add_action('woocommerce_single_variation',$plugin_public, 'add_discounted_selected_variation');//10,1  tjs 2016Nov17

		$this->loader->add_filter('sidebars_widgets', $plugin_public, 'hide_tpi_widget');//  tjs 2016October18 Added filter to hide/show widgets in sidebar.

		//$this->loader->add_filter('woocommerce_before_variations_form',$plugin_public, 'add_discounted_single_variation');//10,1  tjs 2016Nov17
		//$this->loader->add_filter('woocommerce_single_variation',$plugin_public, 'add_discounted_selected_variation');//10,1  tjs 2016Nov17

		//woocommerce_after_single_variation
		$this->loader->add_filter( 'woocommerce_locate_template', $this, 'custom_woocommerce_locate_template', 10, 3 );  //tj 2016Nov17
	}

    /**
     * Register all widgets
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_widgets() {

        $widget = new Tpi_Widget();

        /*
         *  Should this line remain here or be isolated in its own function:  load_tpi_widget?
         */
        $this->loader->add_action("widgets_init", $widget, "load_widget" );// tjs 2016August05 //"Tpi_Widget::load_widget"

    }

	public function dice_coupon_admin_menu(){

		//Admin Page
        $dice_image =  '/wp-content/plugins/tpi/public/images/tpi-die-with-four-corners-20x20.png';

		add_menu_page(
			'Dice Coupon Options',
			'Dice Coupon',
			'manage_options',
            'dice-coupon',
			array($this,'dice_coupon_options_page'),
            $dice_image,
			6
		);

		//call register settings function
		add_action( 'admin_init', array($this,'register_dice_coupon_settings') );
	}


	function register_dice_coupon_settings(){

		settings_fields( 'Dice-Coupon-Section' );// Must be set before register_setting tjs 2016Sept18


		register_setting('Dice-Coupon-Section', 'minimum_discount', array($this, 'Dice_Coupon_Field_Validation'));
		register_setting('Dice-Coupon-Section', 'maximum_discount', array($this, 'Dice_Coupon_Field_Validation'));

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			register_setting('Dice-Coupon-Section', 'use_woocommerce', array($this, 'Dice_Coupon_Field_Validation'));
			register_setting('Dice-Coupon-Section', 'apply_before_tax', array($this, 'Dice_Coupon_Field_Validation'));

			register_setting('Dice-Coupon-Section', 'tpi_shop_page', array($this, 'Dice_Coupon_Field_Validation'));
			register_setting('Dice-Coupon-Section', 'tpi_product_page', array($this, 'Dice_Coupon_Field_Validation'));
			register_setting('Dice-Coupon-Section', 'tpi_cart', array($this, 'Dice_Coupon_Field_Validation'));
			register_setting('Dice-Coupon-Section', 'tpi_checkout', array($this, 'Dice_Coupon_Field_Validation'));
		}
	}


	function Dice_Coupon_Field_Validation($input){
        return $input;
    }


    function dice_coupon_options_page() {
        ?>
        <div class="wrap">
            <h1>Dice Coupon</h1>

            <form method="post" action="options.php">
                <?php settings_fields( 'Dice-Coupon-Section' ); ?>
                <?php do_settings_sections( 'Dice-Coupon-Section' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Minimum Discount</th>
                        <td><input type="text" name="minimum_discount" value="<?php echo (esc_attr(get_option('minimum_discount')) ? esc_attr(get_option('minimum_discount')) : '2') ?>" />
							<label for="minimum_discount"> % </label> </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Maximum Discount</th>
                        <td><input type="text" name="maximum_discount" value="<?php echo (esc_attr(get_option('maximum_discount')) ? esc_attr(get_option('maximum_discount')) : '12') ?>" />
							<label for="maximum_discount"> % </label> </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">eCommerce</th>

                        <td><input type="checkbox" name="use_woocommerce" value="1" <?php echo checked(1, esc_attr( get_option('use_woocommerce') ) , false); ?>  />
							<label for="use_woocommerce"> Use WooCommerce </label> </td>
                    </tr>

					<tr valign="top" class="tpi_display">
						<th scope="row">Plugin Display</th>

						<td class ="tpi_shop_page"><input type="checkbox"  name="tpi_shop_page" value="1" <?php echo checked(1, esc_attr( get_option('tpi_shop_page') ) , false); ?>  />
							<label for="tpi_shop_page"> Shop Page </label> </td>


						<td class="tpi_product_page"><input type="checkbox" name="tpi_product_page" value="1" <?php echo checked(1, esc_attr( get_option('tpi_product_page') ) , false); ?>  />
							<label for="tpi_product_page"> Product Page </label> </td>


						<td class="tpi_cart"><input type="checkbox" name="tpi_cart" value="1" <?php echo checked(1, esc_attr( get_option('tpi_cart') ) , false); ?>  />
							<label for="tpi_cart"> Cart </label> </td>


						<td class="tpi_checkout"><input type="checkbox" name="tpi_checkout" value="1" <?php echo checked(1, esc_attr( get_option('tpi_checkout') ) , false); ?>  />
							<label for="tpi_checkout"> Checkout </label> </td>
					</tr>

                    <tr valign="top">
                        <th scope="row">Taxes</th>
						<td><input type="checkbox" name="apply_before_tax" value="1" <?php echo checked(1, esc_attr( get_option('apply_before_tax') ) , false); ?>  />
							<label for="apply_before_tax"> Apply before Taxes </label> </td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
    <?php }









	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}




	function get_plugin_path() {

		// gets the absolute path to this plugin directory

		return untrailingslashit( plugin_dir_path( __FILE__ ) );

	}


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





}


