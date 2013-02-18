<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Code_Snippet_Library_Settings {
	private $token;

	public function __construct() {
		$this->token = 'snippet';

	}

	public function add_menu_pages() {
        add_submenu_page( 'edit.php?post_type=' . $this->token , 'Code Snippet Library Settings' , 'Settings' , 'manage_options' , 'snippet_settings' , array( &$this , 'settings_page' ) );
    }

	public function add_settings_link( $links ) {
		$settings_link = '<a href="edit.php?post_type=snippet&page=settings">Settings</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	public function register_settings() {
		
		// Add settings section
		add_settings_section( 'snippet_main_settings' , __( 'Configure your code snippets' , 'code_snippet' ) , array( &$this , 'main_settings' ) , 'snippet_settings' );

		// Add settings fields
		add_settings_field( 'code_snippet_admin_theme' , __( 'Theme for snippet editor:' , 'code_snippet' ) , array( &$this , 'admin_theme' )  , 'code_snippet' , 'snippet_main_settings' );
		// add_settings_field( 'code_snippet_display_theme' , __( 'Theme for front-end snippet display:' , 'code_snippet' ) , array( &$this , 'display_theme' )  , 'code_snippet' , 'snippet_main_settings' );

		// Register settings fields
		register_setting( 'code_snippet' , 'code_snippet_admin_theme' );
		// register_setting( 'code_snippet' , 'code_snippet_display_theme' );

	}

	public function main_settings() { echo '<p>' . __( 'These are a few simple settings to make your podcast work the way you want it to work.' , 'code_snippet' ) . '</p>'; }

	public function admin_theme() {

		// $option = get_option('code_snippet_admin_theme');

		// $data = '';
		// if( $option && strlen( $option ) > 0 && $option != '' ) {
		// 	$data = $option;
		// }

		echo '<input id="use_templates" type="checkbox" name="ss_podcasting_use_templates" />
				<label for="use_templates"><span class="description">' . sprintf( __( 'Select this to use the built-in templates for the podcast archive and single pages. If you leave this disabled then your theme\'s default post templates will be used unless you %1$screate your own%2$s' , 'code_snippet' ) , '<a href="' . esc_url( 'http://codex.wordpress.org/Post_Type_Templates' ) . '" target="' . esc_attr( '_blank' ) . '">' , '</a>' ) . '.</span></label>';
	}

	public function display_theme() {

		$option = get_option('code_snippet_admin_theme');

		$data = '';
		if( $option && strlen( $option ) > 0 && $option != '' ) {
			$data = $option;
		}

		// echo '<input id="use_templates" type="checkbox" name="ss_podcasting_use_templates" ' . $checked . '/>
		// 		<label for="use_templates"><span class="description">' . sprintf( __( 'Select this to use the built-in templates for the podcast archive and single pages. If you leave this disabled then your theme\'s default post templates will be used unless you %1$screate your own%2$s' , 'code_snippet' ) , '<a href="' . esc_url( 'http://codex.wordpress.org/Post_Type_Templates' ) . '" target="' . esc_attr( '_blank' ) . '">' , '</a>' ) . '.</span></label>';
	}

	public function settings_page() {

		echo '<div class="wrap">
				<div class="icon32" id="csl-icon"><br/></div>
				<h2>Code Snippet Library Settings</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">';

				settings_fields( 'code_snippet' );
				do_settings_sections( 'snippet_settings' );

			  echo '<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'code_snippet' ) ) . '" />
					</p>
				</form>
			  </div>';
	}

}