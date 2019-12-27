<?php
/*
Plugin Name: WP Select Primary Category
Plugin URI: https://wordpress.org/plugins/wp-select-primary-category/
Description: Custom plugin to select primary category to show content based on that categroy.
Author: Mobeen Abdullah
Version: 1.0.6
Author URI: https://github.com/mobeenabdullah
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// define plugin path directory
if ( ! defined( 'SPC_PLUGIN_PATH' ) ) {
    define( 'SPC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}


// including class file
include SPC_PLUGIN_PATH . 'includes/class-select-primary-category.php';

// initializing the class
$spc = new Select_Primary_Category();