jQuery( document ).ready( function() {

    tinymce.create( 'tinymce.plugins.csl_shortcode', {
        createControl : function( id, controlManager ) {
            if (id == 'csl_button') {
                var img_url = jQuery( '#csl_plugin_dir' ).html() + 'assets/images/code_snippet.png';
                var button = controlManager.createButton( 'csl_button', {
                    title : 'Insert Snippet',
                    image : img_url,
                    onclick : function() {
                        var width = jQuery( window ).width(), H = jQuery( window ).height(), W = ( 720 < width ) ? 720 : width;
                        W = W - 80;
                        H = H - 84;
                        tb_show( 'Insert Snippet', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=csl_shortcode-form' );
                    }
                });
                return button;
            }
            return null;
        }
    });
    
    tinymce.PluginManager.add( 'csl_shortcode', tinymce.plugins.csl_shortcode );

    jQuery( function() {
        
        var request = jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: { action: 'csl_load_shortcode_form' },
            dataType: 'text'
        });

        request.done(function( response ) {

            var form = jQuery( response );
            
            var table = form.find( 'table' );
            form.appendTo( 'body' ).hide();
            
            form.find( '#csl_shortcode-submit' ).click(function(){
                var options = { 
                    'id'    : ''
                };
                var shortcode = '[snippet';
                
                for( var index in options) {
                    var value = table.find('#csl_shortcode-' + index).val();

                    if ( value !== options[index] )
                        shortcode += ' ' + index + '="' + value + '"';
                }
                
                shortcode += ']';

                tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
                
                tb_remove();
            });

        });
            
        
    });

});