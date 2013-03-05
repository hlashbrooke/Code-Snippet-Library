<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function csl_display_snippet( $id = 0 , $execute = false ) {
	global $csl;
	$csl->display_snippet( $id , $execute );
}