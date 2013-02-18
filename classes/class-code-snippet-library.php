<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Code_Snippet_Library {

    protected $dir;
    protected $file;
    protected $assets_dir;
    protected $assets_url;
    protected $token;

    public function __construct( $file ) {
        $this->dir = dirname( $file );
        $this->file = $file;
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
        $this->token = 'snippet';

        $this->load_plugin_textdomain();
        add_action( 'init', array( &$this, 'load_localisation' ), 0 );

        add_action('init', array( &$this , 'register_post_type' ) );
        add_action('init', array( &$this , 'register_taxonomy' ) );

        if( is_admin() ) {
            $csl_settings = new Code_Snippet_Library_Settings();
            add_action('admin_menu', array( &$csl_settings , 'add_menu_pages' ) );

            add_filter( 'manage_edit-code_snippets_columns' , array( &$this , 'edit_taxonomy_columns' ) );
            add_filter( 'manage_code_snippets_custom_column' , array( &$this , 'add_taxonomy_columns' ) , 1 , 3 );

            add_action( 'admin_enqueue_scripts' , array( &$this , 'admin_load_scripts' ) );

            add_action( 'code_snippets_add_form_fields' , array( &$this , 'add_taxonomy_fields' ) , 1 , 1 );
            add_action( 'code_snippets_edit_form_fields' , array( &$this , 'edit_taxonomy_fields' ) , 1 , 1 );

            add_action( 'edited_code_snippets' , array( &$this , 'save_taxonomy_fields' ) , 10 , 2 );
            add_action( 'created_code_snippets' , array( &$this , 'save_taxonomy_fields' ) , 10 , 2 );
        } else {
            add_action( 'wp_enqueue_scripts' , array( &$this , 'load_scripts' ) );
        }
    }

    public function register_post_type() {
 
        $labels = array(
            'name' => _x( 'Code Snippets', 'post type general name' , 'code_snippet' ),
            'singular_name' => _x( 'Code Snippet', 'post type singular name' , 'code_snippet' ),
            'add_new' => _x( 'Add New', $this->token , 'code_snippet' ),
            'add_new_item' => sprintf( __( 'Add New %s' , 'code_snippet' ), __( 'Snippet' , 'code_snippet' ) ),
            'edit_item' => sprintf( __( 'Edit %s' , 'code_snippet' ), __( 'Snippet' , 'code_snippet' ) ),
            'new_item' => sprintf( __( 'New %s' , 'code_snippet' ), __( 'Snippet' , 'code_snippet' ) ),
            'all_items' => sprintf( __( 'All %s' , 'code_snippet' ), __( 'Snippets' , 'code_snippet' ) ),
            'view_item' => sprintf( __( 'View %s' , 'code_snippet' ), __( 'Snippet' , 'code_snippet' ) ),
            'search_items' => sprintf( __( 'Search %a' , 'code_snippet' ), __( 'Snippets' , 'code_snippet' ) ),
            'not_found' =>  sprintf( __( 'No %s Found' , 'code_snippet' ), __( 'Snippets' , 'code_snippet' ) ),
            'not_found_in_trash' => sprintf( __( 'No %s Found In Trash' , 'code_snippet' ), __( 'Snippets' , 'code_snippet' ) ),
            'parent_item_colon' => '',
            'menu_name' => __( 'Code Snippets' , 'code_snippet' )

        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'snippets' , 'feeds' => true ),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'supports' => array( ),
            'menu_position' => 5,
            'menu_icon' => ''
        );

        register_post_type( $this->token, $args );
    }

    public function register_taxonomy() {

        $labels = array(
            'name' => __( 'Code Snippets' , 'woocommerce' ),
            'singular_name' => __( 'Code Snippet', 'woocommerce' ),
            'search_items' =>  __( 'Search Snippets' ),
            'all_items' => __( 'All Snippets' , 'woocommerce' ),
            'parent_item' => __( 'Parent Snippet' ),
            'parent_item_colon' => __( 'Parent Snippet:' ),
            'edit_item' => __( 'Edit Snippet' , 'woocommerce' ),
            'update_item' => __( 'Update Snippet' ),
            'add_new_item' => __( 'Add New Snippet' ),
            'new_item_name' => __( 'New Snippet Name' ),
            'menu_name' => __( 'Code Snippets' ),
        );

        $args = array(
            'public' => true,
            'hierarchical' => false,
            'rewrite' => true,
            'labels' => $labels
        );

        register_taxonomy( 'code_snippets' , $this->token , $args );
    }

    public function edit_taxonomy_columns( $columns ) {

        unset( $columns['description'] );
        unset( $columns['slug'] );
        unset( $columns['posts'] );

        $columns['snippet_id'] = __( 'ID' , 'code_snippet' );
        
        return $columns;
    }

    public function add_taxonomy_columns( $column_data , $column_name , $term_id ) {

        $term = get_term( $term_id , 'code_snippets' );

        switch ( $column_name ) {
            case 'snippet_id':
                $column_data = $term->term_id;
            break;
        }

        return $column_data; 
    }

    public function add_taxonomy_fields( $taxonomy ) {
        
        ?>
        <div class="form-field">
            <label for="language"><?php _e( 'Language', 'code_snippet' ); ?></label>
            <select id="language" name="snippet_data[language]"><?php echo $this->get_language_options(); ?></select>
        </div>
        <div class="form-field" id="wcs-edit">
            <label for="snippet"><?php _e( 'Snippet', 'code_snippet' ); ?></label>
            <div id="editor" style="position:relative;width:100%;height:300px;"></div>
            <textarea id="snippet" name="snippet_data[snippet]" rows="1" cols="1" style="display:none;"></textarea>
            <script type="text/javascript">
                jQuery( 'input#tag-slug' ).closest( '.form-field' ).remove();
                jQuery( 'textarea#tag-description' ).closest( '.form-field' ).remove();

                var editor = ace.edit( 'editor' );
                editor.setTheme( 'ace/theme/solarized_light' );
                editor.getSession().setMode( 'ace/mode/php' );

                jQuery( 'select#language' ).on( 'change' , function() {
                    editor.getSession().setMode( 'ace/mode/' + this.value );
                });

                jQuery( '#editor textarea' ).on( 'blur' , function() {
                    jQuery( 'textarea#snippet' ).val( editor.getValue() );
                });
            </script>
        </div>
        <?php   
    }

    public function edit_taxonomy_fields( $term ) {

        $term_id = $term->term_id;
        $term_meta = get_option( 'code_snippet_' . $term_id );

        if( isset( $term_meta['language'] ) && strlen( $term_meta['language'] ) > 0 ) {
            $lang = $term_meta['language'];
        } else {
            $lang = 'php';
        }

        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="language"><?php _e( 'Language', 'code_snippet' ); ?></label></th>
            <td><select id="language" name="snippet_data[language]"><?php echo $this->get_language_options( $lang ); ?></select></td>
        </tr>
        <tr class="form-field" id="wcs-edit">
            <th scope="row" valign="top"><label for="snippet"><?php _e( 'Snippet', 'code_snippet' ); ?></label></th>
            <td>
                <div id="editor" style="position:relative;width:100%;height:500px;"><?php echo $this->decode_snippet( $term_meta['snippet'] ); ?></div>
                <textarea id="snippet" name="snippet_data[snippet]" rows="1" cols="1" style="display:none;"></textarea>
                
                <script type="text/javascript">
                    jQuery( 'input#slug' ).closest( '.form-field' ).remove();
                    jQuery( 'textarea#description' ).closest( '.form-field' ).remove();

                    var editor = ace.edit( 'editor' );
                    editor.setTheme( 'ace/theme/solarized_light' );
                    editor.getSession().setMode( 'ace/mode/<?php echo $lang; ?>' );

                    jQuery( 'select#language' ).on( 'change' , function() {
                        editor.getSession().setMode( 'ace/mode/' + this.value );
                    });

                    /* For front-end */
                    // editor.setReadOnly(true);
                    // editor.setHighlightActiveLine(false);

                    jQuery( '#editor textarea' ).on( 'blur' , function() {
                        jQuery( 'textarea#snippet' ).val( editor.getValue() );
                    });
                </script>
            </td>
        </tr>
        <?php
    }

    public function save_taxonomy_fields( $term_id ) {
        if ( isset( $_POST['snippet_data'] ) ) {
            $snippet_data = get_option( 'code_snippet_' . $term_id );
            $keys = array_keys( $_POST['snippet_data'] );
                foreach ( $keys as $key ){
                if ( isset( $_POST['snippet_data'][$key] ) ){
                    if( $key == 'snippet' ) {
                        $snippet_data[$key] = $this->encode_snippet( $_POST['snippet_data'][$key] );
                    } else {
                        $snippet_data[$key] = $_POST['snippet_data'][$key];
                    }
                }
            }
            
            update_option( 'code_snippet_' . $term_id , $snippet_data );
        }
    }

    private function get_language_options( $current ) {

        $languages = array(
            'php' => 'PHP',
            'javascript' => 'Javascript'
        );

        $html = '';
        foreach( $languages as $k => $lang ) {
            $selected = '';
            if( $k == $current ) {
                $selected = " selected='selected'";
            }
            $html .= '<option value="' . $k . '"' . $selected . '>' . $lang . '</option>';
        }

        return $html;

    }

    public function admin_load_scripts() {
        wp_register_script( 'ace' , esc_url( 'http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js' ) );
        wp_enqueue_script( 'ace' );
    }

    public function load_scripts() {

        wp_register_script( 'ace' , esc_url( 'http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js' ) );
        wp_enqueue_script( 'ace' );
    }

    private function encode_snippet( $snippet = '' ) {

        if( '' != $snippet ) {
            $snippet = base64_encode( stripslashes( $snippet ) );
        }

        return $snippet;

    }

    private function decode_snippet( $snippet = '' ) {

        if( '' != $snippet ) {
            $snippet = base64_decode( $snippet );
        }

        return $snippet;

    }

    public function load_localisation () {
        load_plugin_textdomain( 'code_snippet', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    }

    public function load_plugin_textdomain () {
        $domain = 'code_snippet';
        
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
     
        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    }

}









