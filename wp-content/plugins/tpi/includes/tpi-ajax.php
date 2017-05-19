<?php

/**
 * The ajax file
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
 * Plugin Name:       Transcent Plugin Ajax
 * Plugin URI:        http://transcent.tech/tpi/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Thomas J. Sherlock
 * Author URI:        http://transcent.tech/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tpi
 * Domain Path:       /languages
 * Date: 8/11/16
 * Time: 10:05 AM
 */



/*
 * enqueue js?  enqueue json? whatelse?
 * tjs 2016Aug11
 */




function tpi_ajax_request() {

    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {

        $fruit = $_REQUEST['fruit'];

        // Let's take the data that was sent and do something with it
        if ( $fruit == 'Banana' ) {
            $fruit = 'Apple';
        }

        // Now we'll return it to the javascript function
        // Anything outputted will be returned in the response
        echo $fruit;

        // If you're debugging, it might be useful to see what was sent in the $_REQUEST
        // print_r($_REQUEST);

    }

    // Always die in functions echoing ajax content
    die();
}

add_action( 'wp_ajax_example_ajax_request', 'tpi_ajax_request' );

// If you wanted to also use the function for non-logged in users (in a theme for example)
// add_action( 'wp_ajax_nopriv_example_ajax_request', 'example_ajax_request' );