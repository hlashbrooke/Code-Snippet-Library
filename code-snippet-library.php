<?php
/*
 * Plugin Name: Code Snippet Library
 * Version: 1.1
 * Plugin URI: http://www.hughlashbrooke.com/plugins
 * Description: Store a library of reusable code snippets that you can add to any post. Supports 61 programming languages.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 3.8
 * Tested up to: 4.0
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'classes/class-code-snippet-library.php' );
require_once( 'classes/class-code-snippet-library-settings.php' );

global $csl;
$csl = new Code_Snippet_Library( __FILE__ );
$csl_settings = new Code_Snippet_Library_Settings( __FILE__ );