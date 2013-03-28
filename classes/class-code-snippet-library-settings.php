<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Code_Snippet_Library_Settings {
	private $dir;
    private $file;
    private $assets_dir;
    private $assets_url;
    private $token;

	public function __construct( $file ) {
		$this->dir = dirname( $file );
        $this->file = $file;
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token = 'snippet';

		add_action( 'admin_init' , array( &$this , 'register_settings' ) );
		add_action('admin_menu', array( &$this , 'add_menu_pages' ) );

	}

	public function add_menu_pages() {
        add_submenu_page( 'edit.php?post_type=' . $this->token , 'Code Snippet Library Settings' , 'Settings' , 'manage_options' , 'code_snippet_settings' , array( &$this , 'settings_page' ) );
    }

	public function add_settings_link( $links ) {
		$settings_link = '<a href="edit.php?post_type=snippet&page=code_snippet_settings">Settings</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	public function register_settings() {
		
		// Add settings section
		add_settings_section( 'snippet_theme_settings' , __( 'Modify your code snippet theme' , 'code_snippet' ) , array( &$this , 'main_settings' ) , 'code_snippet_settings' );

		// Add settings fields
		add_settings_field( 'code_snippet_admin_theme' , __( 'Theme for snippet editor:' , 'code_snippet' ) , array( &$this , 'admin_theme' )  , 'code_snippet_settings' , 'snippet_theme_settings' );
		add_settings_field( 'code_snippet_display_theme' , __( 'Theme for front-end snippet display:' , 'code_snippet' ) , array( &$this , 'display_theme' )  , 'code_snippet_settings' , 'snippet_theme_settings' );

		// Register settings fields
		register_setting( 'code_snippet_settings' , 'code_snippet_admin_theme' );
		register_setting( 'code_snippet_settings' , 'code_snippet_display_theme' );

	}

	public function main_settings() { echo '<p>' . sprintf( __( 'Set the theme (colour scheme) used for your code snippets. See %s for examples of what each theme looks like (use the \'Mode\' drop down).' , 'code_snippet' ), '<a href="http://ace.ajax.org/build/kitchen-sink.html" target="_blank">' . __( 'the Ace demo' , 'code_snippet' ) . '</a>' ) . '</p>'; }

	private function theme_options( $selected = '' ) {

		$html = '<optgroup label="Bright">
		            <option value="chrome" ' . selected( 'chrome' , $selected , false ) . '>Chrome</option>
		            <option value="clouds" ' . selected( 'clouds' , $selected , false ) . '>Clouds</option>
		            <option value="crimson_editor" ' . selected( 'crimson_editor' , $selected , false ) . '>Crimson Editor</option>
		            <option value="dawn" ' . selected( 'dawn' , $selected , false ) . '>Dawn</option>
		            <option value="dreamweaver" ' . selected( 'dreamweaver' , $selected , false ) . '>Dreamweaver</option>
		            <option value="eclipse" ' . selected( 'eclipse' , $selected , false ) . '>Eclipse</option>
		            <option value="github" ' . selected( 'github' , $selected , false ) . '>GitHub</option>
		            <option value="solarized_light" ' . selected( 'solarized_light' , $selected , false ) . '>Solarized Light</option>
		            <option value="textmate" ' . selected( 'textmate' , $selected , false ) . '>TextMate</option>
		            <option value="tomorrow" ' . selected( 'tomorrow' , $selected , false ) . '>Tomorrow</option>
		            <option value="xcode" ' . selected( 'xcode' , $selected , false ) . '>XCode</option>
		          </optgroup>
		          <optgroup label="Dark">
		            <option value="ambiance" ' . selected( 'ambiance' , $selected , false ) . '>Ambiance</option>
		            <option value="chaos" ' . selected( 'chaos' , $selected , false ) . '>Chaos</option>
		            <option value="clouds_midnight" ' . selected( 'clouds_midnight' , $selected , false ) . '>Clouds Midnight</option>
		            <option value="cobalt" ' . selected( 'cobalt' , $selected , false ) . '>Cobalt</option>
		            <option value="idle_fingers" ' . selected( 'idle_fingers' , $selected , false ) . '>idleFingers</option>
		            <option value="kr_theme" ' . selected( 'kr_theme' , $selected , false ) . '>krTheme</option>
		            <option value="merbivore" ' . selected( 'merbivore' , $selected , false ) . '>Merbivore</option>
		            <option value="merbivore_soft" ' . selected( 'merbivore_soft' , $selected , false ) . '>Merbivore Soft</option>
		            <option value="mono_industrial" ' . selected( 'mono_industrial' , $selected , false ) . '>Mono Industrial</option>
		            <option value="monokai" ' . selected( 'monokai' , $selected , false ) . '>Monokai</option>
		            <option value="pastel_on_dark" ' . selected( 'pastel_on_dark' , $selected , false ) . '>Pastel on dark</option>
		            <option value="solarized_dark" ' . selected( 'solarized_dark' , $selected , false ) . '>Solarized Dark</option>
		            <option value="twilight" ' . selected( 'twilight' , $selected , false ) . '>Twilight</option>
		            <option value="tomorrow_night" ' . selected( 'tomorrow_night' , $selected , false ) . '>Tomorrow Night</option>
		            <option value="tomorrow_night_blue" ' . selected( 'tomorrow_night_blue' , $selected , false ) . '>Tomorrow Night Blue</option>
		            <option value="tomorrow_night_bright" ' . selected( 'tomorrow_night_bright' , $selected , false ) . '>Tomorrow Night Bright</option>
		            <option value="tomorrow_night_eighties" ' . selected( 'tomorrow_night_eighties' , $selected , false ) . '>Tomorrow Night 80s</option>
		            <option value="vibrant_ink" ' . selected( 'vibrant_ink' , $selected , false ) . '>Vibrant Ink</option>
		          </optgroup>';

    	return $html;
	}

	public function admin_theme() {

		$option = get_option('code_snippet_admin_theme');

		$data = '';
		if( $option && strlen( $option ) > 0 && $option != '' ) {
			$data = $option;
		}

		echo '<select name="code_snippet_admin_theme" id="code_snippet_admin_theme">' . $this->theme_options( $data ) . '</select>
				<label for="code_snippet_admin_theme"><span class="description">' . __( 'Theme for the code editor when adding snippets in the WordPress dashboard.' , 'code_snippet' ) . '</span></label>';
	}

	public function display_theme() {

		$option = get_option('code_snippet_display_theme');

		$data = '';
		if( $option && strlen( $option ) > 0 && $option != '' ) {
			$data = $option;
		}

		echo '<select name="code_snippet_display_theme" id="code_snippet_display_theme">' . $this->theme_options( $data ) . '</select>
				<label for="code_snippet_display_theme"><span class="description">' . __( 'Theme for the code as it is displayed to users on the front-end.' , 'code_snippet' ) . '</span></label>';
	}

	public function settings_page() {

		echo '<div class="wrap">
				<div class="icon32" id="csl-icon"><br/></div>
				<h2>Code Snippet Library Settings</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">';

				settings_fields( 'code_snippet_settings' );
				do_settings_sections( 'code_snippet_settings' );

			  echo '<h3>How to use your snippets</h3>
			  		<p>
			  			' . sprintf( __( 'To display your snippet on any post or page, simply copy the supplied shortcode and paste it wherever you want the snippet to appear. You can also insert your shortcodes using the toolbar button in the editor.%1$sIf you would like to execute the code in the snippet on the page (only works for PHP, HTML, CSS &amp; Javascript) simply add \'execute="yes"\' as a paremter to the shortcode.' ), '<br/>' ) . '
			  		</p>
		  			<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'code_snippet' ) ) . '" />
					</p>
				</form>
			  </div>';
	}

}