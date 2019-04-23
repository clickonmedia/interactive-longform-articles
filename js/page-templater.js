
(function( window, document, $ ) {

	var getUrlParameter = function( sParam ) {
	    var sPageURL = decodeURIComponent( window.location.search.substring(1) );
	    var sURLVariables = sPageURL.split( '&' );
	    var sParameterName;
	    var i;

	    for ( i = 0; i < sURLVariables.length; i++ ) {
	        sParameterName = sURLVariables[i].split( '=' );

	        if ( sParameterName[0] === sParam ) {
	            return sParameterName[1] === undefined ? true : sParameterName[1];
	        }
	    }
	};

    $( document ).ready( function() {

    	// Get the current post type
    	var post_type = getUrlParameter( 'post_type' );

        // You can find this in the value of the Page Template dropdown
        var template_name = 'interactive-template.php';

        // Page template in the publishing options
        var $template = $( '#page_template' );

        // Identify your metabox
        var $metabox = $( '#int_article_cmb_box' );

        // On DOM ready, check if your page template is selected
        if( $template.val() === template_name || $( 'body' ).hasClass( 'post-type-interactive_article' ) ) {
            $metabox.show();
        }

        // Bind a change event to make sure we show or hide the metabox based on user selection of a template
        $template.change( function( e ) {
            if( $template.val() === template_name ) {
                $metabox.show();
            } else {
                //You should clear out all metabox values here;
                $metabox.hide();
            }
        });
    });

})( window, document, window.jQuery );
