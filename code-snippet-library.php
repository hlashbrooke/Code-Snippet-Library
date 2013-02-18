<?php
/*
Plugin Name: Code Snippet Library
Plugin URI: http://www.hughlashbrooke.com
Description: Store a library of code snippets that you can add to posts.
Author: Hugh Lashbrooke
Version: 1.0.0
Author URI: http://www.hughlashbrooke.com
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'classes/class-code-snippet-library.php' );
require_once( 'classes/class-code-snippet-library-settings.php' );

global $csl;
$csl = new Code_Snippet_Library( __FILE__ );