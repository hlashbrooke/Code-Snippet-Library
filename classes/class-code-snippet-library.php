<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Code_Snippet_Library {

    private $dir;
    private $file;
    private $assets_dir;
    private $assets_url;
    private $token;
    private $snippet;

    public function __construct( $file ) {
        $this->dir = dirname( $file );
        $this->file = $file;
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
        $this->token = 'snippet';
        $this->snippet = false;

        $this->load_plugin_textdomain();
        add_action( 'init', array( &$this, 'load_localisation' ), 0 );

        add_action('init', array( &$this , 'register_post_type' ) );
        add_action('init', array( &$this , 'register_taxonomy' ) );

        add_shortcode( 'snippet' , array( &$this , 'shortcode' ) );

        if( is_admin() ) {
            add_filter( 'manage_edit-code_snippets_columns' , array( &$this , 'edit_taxonomy_columns' ) );
            add_filter( 'manage_code_snippets_custom_column' , array( &$this , 'add_taxonomy_columns' ) , 1 , 3 );

            add_action( 'admin_enqueue_scripts' , array( &$this , 'admin_load_scripts' ) );

            add_action( 'code_snippets_add_form_fields' , array( &$this , 'add_taxonomy_fields' ) , 1 , 1 );
            add_action( 'code_snippets_edit_form_fields' , array( &$this , 'edit_taxonomy_fields' ) , 1 , 1 );

            add_action( 'edited_code_snippets' , array( &$this , 'save_taxonomy_fields' ) , 10 , 2 );
            add_action( 'created_code_snippets' , array( &$this , 'save_taxonomy_fields' ) , 10 , 2 );

            add_action('send_headers', array( &$this , 'page_redirect' ) );
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
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => false,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array( 'title' ),
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

        $columns['snippet_shortcode'] = __( 'Shortcode' , 'code_snippet' );
        $columns['snippet_id'] = __( 'ID' , 'code_snippet' );
        
        return $columns;
    }

    public function add_taxonomy_columns( $column_data , $column_name , $term_id ) {

        $term = get_term( $term_id , 'code_snippets' );

        switch ( $column_name ) {
            case 'snippet_shortcode':
                $column_data = '[snippet id="' . $term->term_id . '"]';
            break;
            case 'snippet_id':
                $column_data = $term->term_id;
            break;
        }

        return $column_data; 
    }

    public function add_taxonomy_fields( $taxonomy ) {

        $theme = get_option('code_snippet_admin_theme');

        if( ! $theme || strlen( $theme ) == 0 || $theme == '' ) {
            $theme = 'chrome';
        }
        
        ?>
        <div class="form-field">
            <label for="language"><?php _e( 'Language', 'code_snippet' ); ?></label>
            <select id="language" name="snippet_data[language]"><?php echo $this->get_language_options(); ?></select>
        </div>
        <div class="form-field" id="wcs-edit">
            <label for="snippet"><?php _e( 'Snippet', 'code_snippet' ); ?></label>
            <pre id="editor" style="display:block;position:relative;width:100%;height:300px;"></pre>
            <textarea id="snippet" name="snippet_data[snippet]" rows="1" cols="1" style="display:none;"></textarea>
            <script type="text/javascript">
                jQuery( 'input#tag-slug' ).closest( '.form-field' ).remove();
                jQuery( 'textarea#tag-description' ).closest( '.form-field' ).remove();

                var editor = ace.edit( 'editor' );
                editor.setTheme( 'ace/theme/<?php echo $theme; ?>' );
                editor.getSession().setMode( 'ace/mode/php' );
                editor.setShowPrintMargin( false );

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

        $theme = get_option('code_snippet_admin_theme');

        if( ! $theme || strlen( $theme ) == 0 || $theme == '' ) {
            $theme = 'chrome';
        }

        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="language"><?php _e( 'Language', 'code_snippet' ); ?></label></th>
            <td><select id="language" name="snippet_data[language]"><?php echo $this->get_language_options( $lang ); ?></select></td>
        </tr>
        <tr class="form-field" id="wcs-edit">
            <th scope="row" valign="top"><label for="snippet"><?php _e( 'Snippet', 'code_snippet' ); ?></label></th>
            <td>
                <pre id="editor" style="display:block;position:relative;width:100%;height:500px;"><?php echo $this->decode_snippet( $term_meta['snippet'] ); ?></pre>
                <textarea id="snippet" name="snippet_data[snippet]" rows="1" cols="1" style="display:none;"></textarea>
                
                <script type="text/javascript">
                    jQuery( 'input#slug' ).closest( '.form-field' ).remove();
                    jQuery( 'textarea#description' ).closest( '.form-field' ).remove();

                    var editor = ace.edit( 'editor' );
                    editor.setTheme( 'ace/theme/<?php echo $theme; ?>' );
                    editor.getSession().setMode( 'ace/mode/<?php echo $lang; ?>' );
                    editor.setShowPrintMargin( false );

                    jQuery( 'select#language' ).on( 'change' , function() {
                        editor.getSession().setMode( 'ace/mode/' + this.value );
                    });

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
                if ( isset( $_POST['snippet_data'][$key] ) ) {
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

    public function shortcode( $args ) {

        extract( shortcode_atts( array(
            'id' => 0,
            'execute' => 'no'
        ), $args ) );

        if( $execute && $execute == 'yes' ) {
            $execute = true;
        } else {
            $execute = false;
        }

        $html = $this->display_snippet( $id , $execute );

        return $html;

    }

    private function display_snippet( $id = 0 , $execute = false ) {

        $this->snippet = get_option( 'code_snippet_' . $id );

        $html = '';

        if( $this->snippet && is_array( $this->snippet ) ) {

            if( $execute && in_array( $this->snippet['language'] , array( 'php' , 'html' , 'css' , 'javascript' ) ) ) {
                $executable = $this->decode_snippet( $this->snippet['snippet'] , true );
                switch( $this->snippet['language'] ) {
                    case 'php':
                        ob_start();
                        eval( $executable );
                        $html = ob_get_clean();
                    break;
                    case 'html': $html = $executable; break;
                    case 'css': $html = '<style type="text/css">' . $executable . '</style>'; break;
                    case 'javascript': $html = '<script type="text/javascript">' . $executable . '</script>'; break;
                }
            } else {
                $html = '<pre id="code_snippet" style="position:relative;width:100%;border:0;padding:0;">' . $this->decode_snippet( $this->snippet['snippet'] ) . '</pre>';

                add_action( 'wp_footer' , array( &$this , 'trigger_ace' ) );
            }

        }

        return $html;

    }

    public function admin_load_scripts() {
        $this->load_ace();

        wp_register_style( 'csl_admin' , esc_url( $this->assets_url . 'css/admin.css' ) );
        wp_enqueue_style( 'csl_admin' );
    }

    public function load_scripts() {
        $this->load_ace();
    }

    private function load_ace() {
        wp_register_script( 'ace' , esc_url( 'http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js' ) );
        wp_enqueue_script( 'ace' );
    }

    public function trigger_ace() {

        if( $this->snippet ) {

            $theme = get_option('code_snippet_display_theme');

            if( ! $theme || strlen( $theme ) == 0 || $theme == '' ) {
                $theme = 'chrome';
            }
            
            $html = "<script type='text/javascript'>
                        var editor = ace.edit( 'code_snippet' );
                        var session = editor.getSession();
                        
                        editor.setTheme( 'ace/theme/" . $theme . "' );
                        session.setMode( 'ace/mode/" . $this->snippet['language'] . "' );
                        
                        editor.setReadOnly( true );
                        editor.setHighlightActiveLine( false );
                        editor.setShowPrintMargin( false );
                        editor.setHighlightGutterLine( false );

                        var doc = session.getDocument();
                        var lines = parseInt( doc.getLength() );
                        var line_height = 20;
                        var editor_height = lines * line_height;
                        jQuery( '#code_snippet' ).height( editor_height + 'px' );
                        
                        
                        jQuery( window ).load( function(e) {
                            var doc = session.getDocument();
                            var lines = parseInt( doc.getLength() );
                            var line_height = jQuery( '.ace_line' ).height();

                            var editor_height = lines * line_height;
                            jQuery( '#code_snippet' ).height( editor_height + 'px' );
                            jQuery( '.ace_scroller' ).height( editor_height + 'px' );
                            jQuery( '.ace_gutter' ).height( editor_height + 'px' );

                            var content_height = editor_height + ( line_height * 2 );
                            jQuery( '.ace_content' ).height( content_height + 'px' );
                        });
                        

                    </script>";

            echo $html;
        }

    }

    private function encode_snippet( $snippet = '' ) {

        if( '' != $snippet ) {
            $snippet = base64_encode( stripslashes( $snippet ) );
        }

        return $snippet;

    }

    private function decode_snippet( $snippet = '' , $execute = false ) {

        if( '' != $snippet ) {
            $snippet = base64_decode( $snippet );
            if( ! $execute ) {
                $snippet = htmlspecialchars( $snippet );
            }
        }

        return $snippet;

    }

    private function get_language_options( $selected = 'php' ) {

        $html = '<option value="abap" ' . selected( 'abap' , $selected , false ) . '>ABAP</option>
                 <option value="asciidoc" ' . selected( 'asciidoc' , $selected , false ) . '>AsciiDoc</option>
                 <option value="c9search" ' . selected( 'c9search' , $selected , false ) . '>C9Search</option>
                 <option value="coffee" ' . selected( 'coffee' , $selected , false ) . '>CoffeeScript</option>
                 <option value="coldfusion" ' . selected( 'coldfusion' , $selected , false ) . '>ColdFusion</option>
                 <option value="csharp" ' . selected( 'csharp' , $selected , false ) . '>C#</option>
                 <option value="css" ' . selected( 'css' , $selected , false ) . '>CSS</option>
                 <option value="curly" ' . selected( 'curly' , $selected , false ) . '>Curly</option>
                 <option value="dart" ' . selected( 'dart' , $selected , false ) . '>Dart</option>
                 <option value="diff" ' . selected( 'diff' , $selected , false ) . '>Diff</option
                 ><option value="dot" ' . selected( 'dot' , $selected , false ) . '>Dot</option>
                 <option value="glsl" ' . selected( 'glsl' , $selected , false ) . '>Glsl</option>
                 <option value="golang" ' . selected( 'golang' , $selected , false ) . '>Go</option>
                 <option value="groovy" ' . selected( 'groovy' , $selected , false ) . '>Groovy</option>
                 <option value="haxe" ' . selected( 'haxe' , $selected , false ) . '>haXe</option>
                 <option value="haml" ' . selected( 'haml' , $selected , false ) . '>HAML</option>
                 <option value="html" ' . selected( 'html' , $selected , false ) . '>HTML</option>
                 <option value="c_cpp" ' . selected( 'c_cpp' , $selected , false ) . '>C/C++</option>
                 <option value="clojure" ' . selected( 'clojure' , $selected , false ) . '>Clojure</option>
                 <option value="jade" ' . selected( 'jade' , $selected , false ) . '>Jade</option>
                 <option value="java" ' . selected( 'java' , $selected , false ) . '>Java</option>
                 <option value="jsp" ' . selected( 'jsp' , $selected , false ) . '>JSP</option>
                 <option value="javascript" ' . selected( 'javascript' , $selected , false ) . '>JavaScript</option>
                 <option value="json" ' . selected( 'json' , $selected , false ) . '>JSON</option>
                 <option value="jsx" ' . selected( 'jsx' , $selected , false ) . '>JSX</option>
                 <option value="latex" ' . selected( 'latex' , $selected , false ) . '>LaTeX</option>
                 <option value="less" ' . selected( 'less' , $selected , false ) . '>LESS</option>
                 <option value="lisp" ' . selected( 'lisp' , $selected , false ) . '>Lisp</option>
                 <option value="liquid" ' . selected( 'liquid' , $selected , false ) . '>Liquid</option>
                 <option value="lua" ' . selected( 'lua' , $selected , false ) . '>Lua</option>
                 <option value="luapage" ' . selected( 'luapage' , $selected , false ) . '>LuaPage</option>
                 <option value="lucene" ' . selected( 'lucene' , $selected , false ) . '>Lucene</option>
                 <option value="makefile" ' . selected( 'makefile' , $selected , false ) . '>Makefile</option>
                 <option value="markdown" ' . selected( 'markdown' , $selected , false ) . '>Markdown</option>
                 <option value="objectivec" ' . selected( 'objectivec' , $selected , false ) . '>Objective-C</option>
                 <option value="ocaml" ' . selected( 'ocaml' , $selected , false ) . '>OCaml</option>
                 <option value="perl" ' . selected( 'perl' , $selected , false ) . '>Perl</option>
                 <option value="pgsql" ' . selected( 'pgsql' , $selected , false ) . '>pgSQL</option>
                 <option value="php" ' . selected( 'php' , $selected , false ) . '>PHP</option>
                 <option value="powershell" ' . selected( 'powershell' , $selected , false ) . '>Powershell</option>
                 <option value="python" ' . selected( 'python' , $selected , false ) . '>Python</option>
                 <option value="r" ' . selected( 'r' , $selected , false ) . '>R</option>
                 <option value="rdoc" ' . selected( 'rdoc' , $selected , false ) . '>RDoc</option>
                 <option value="rhtml" ' . selected( 'rhtml' , $selected , false ) . '>RHTML</option>
                 <option value="ruby" ' . selected( 'ruby' , $selected , false ) . '>Ruby</option>
                 <option value="scad" ' . selected( 'scad' , $selected , false ) . '>OpenSCAD</option>
                 <option value="scala" ' . selected( 'scala' , $selected , false ) . '>Scala</option>
                 <option value="scss" ' . selected( 'scss' , $selected , false ) . '>SCSS</option>
                 <option value="sh" ' . selected( 'sh' , $selected , false ) . '>SH</option>
                 <option value="sql" ' . selected( 'sql' , $selected , false ) . '>SQL</option>
                 <option value="stylus" ' . selected( 'stylus' , $selected , false ) . '>Stylus</option>
                 <option value="svg" ' . selected( 'svg' , $selected , false ) . '>SVG</option>
                 <option value="tcl" ' . selected( 'tcl' , $selected , false ) . '>Tcl</option>
                 <option value="tex" ' . selected( 'tex' , $selected , false ) . '>Tex</option>
                 <option value="text" ' . selected( 'text' , $selected , false ) . '>Text</option>
                 <option value="textile" ' . selected( 'textile' , $selected , false ) . '>Textile</option>
                 <option value="typescript" ' . selected( 'typescript' , $selected , false ) . '>Typescript</option>
                 <option value="vbscript" ' . selected( 'vbscript' , $selected , false ) . '>VBScript</option>
                 <option value="xml" ' . selected( 'xml' , $selected , false ) . '>XML</option>
                 <option value="xquery" ' . selected( 'xquery' , $selected , false ) . '>XQuery</option>
                 <option value="yaml" ' . selected( 'yaml' , $selected , false ) . '>YAML</option>';

        return $html;
    }

    public function page_redirect() {
        global $pagenow, $typenow;
        
        $do_redirect = false;
        if( $pagenow == 'edit.php' && $typenow == 'snippet' ) {
            $do_redirect = true;
            if( isset( $_GET['page'] ) && $_GET['page'] == 'code_snippet_settings' ) {
                $do_redirect = false;
            }
        }

        if( $do_redirect ) {
            wp_safe_redirect( admin_url( 'edit-tags.php?taxonomy=code_snippets&post_type=' . $this->token ) );
            exit;
        }
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