<?php
/*
Plugin Name: Interactive Longform Articles
Description: Interactive multimedia articles for longform journalism
Version:     2.1.5
Author:      CLICKON Media
Author URI:  https://www.clickon.co
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

require 'lib/interactive.class.php';

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

function interactive_articles_init() {
	Interactive::instance();
}

add_action( 'plugins_loaded', 'interactive_articles_init' );
