
;(function( $ ) {

    $( document ).ready( function() {

		/**
		* Toggles the visibility of the WYSIWYG text editors. By default, the editor is shown
		* depending on the color selected by the background color input.
		*
		* @method toggleEditor
		* @param {jQuery element} 		$container 	The container element for the section
		* @param {String} 				color 		(Optional) To override default behaviour define editor color here.
		*											To hide all editors, set parameter as "none".
		*/
        var toggleEditor = function( $container, color ) {

        	// Hide all text editors by default
        	$container.find(".int-text").addClass( "hidden" );

        	if( typeof color === "undefined" ) {
        		// Display editor based on background color input
        		var color = $container.find(".int-background-color input:checked").val();
        		$container.find( ".int-text-" + color ).removeClass( "hidden" );

        	} else if ( typeof color === "string" ) {
        		// Display editor based on selected color
        		$container.find( ".int-text-" + color.toLowerCase() ).removeClass( "hidden" );
        	}
        }

		/**
		* Toggles the visibility of sections, depending on the clicked CMB2 input.
		*
		* @method toggleType
		* @param {jQuery element} 	$el 		The form input that has been clicked
		*/
        var toggleType = function( $el ) {

        	var $section = $el.parents(".cmb-repeatable-grouping");
        	var option = $el.val();

    		switch( option ) {
    			case "embed":
            		$section.find(".int-video-embed").removeClass( "hidden" );
            		$section.find(".int-downloads").addClass( "hidden" );
            		toggleEditor( $section, "none" );
            		break;
            	case "downloads":
            		$section.find(".int-downloads").removeClass( "hidden" );
            		$section.find(".int-video-embed").addClass( "hidden" );
            		toggleEditor( $section, "none" );
            		break;
            	default:
            		$section.find(".int-video-embed").addClass( "hidden" );
            		$section.find(".int-downloads").addClass( "hidden" );
            		toggleEditor( $section );
            }
        }

		/**
		* Toggles the visibility of sections, depending on the clicked CMB2 input.
		*
		* @method toggleBackground
		* @param {jQuery element} 	$el 		The form input that has been clicked
		*/
        var toggleBackground = function( $el ) {

        	var $section = $el.parents(".cmb-repeatable-grouping");
        	var option = $el.val();

    		// Hide by default
    		$section.find( ".int-background-color" ).addClass( "hidden" );
    		$section.find( ".int-background-image, .int-background-image-mobile" ).addClass( "hidden" );
    		$section.find( ".int-background-video, .int-background-video-mobile, .int-background-video-poster" ).addClass( "hidden" );
    		$section.find( ".int-background-opacity, .int-background-align" ).addClass( "hidden" );

    		// Toggle editor color only if an editor is already visible
    		var has_editor = !!$section.find( ".int-text:visible" ).length;

    		// Display fields based on option selected
    		switch( option ) {
    			case "color":
            		$section.find(".int-background-color").removeClass( "hidden" );
            		if ( has_editor ) {
            			toggleEditor( $section );
            		}
            		break;
            	case "image":
            		$section.find(".int-background-image, .int-background-image-mobile").removeClass("hidden" );
            		$section.find( ".int-background-opacity, .int-background-align" ).removeClass( "hidden" );
            		if ( has_editor ) {
            			toggleEditor( $section, "black" );
            		}
            		break;
            	case "video":
            		$section.find(".int-background-video, .int-background-video-mobile, .int-background-video-poster").removeClass("hidden" );
            		$section.find( ".int-background-opacity, .int-background-align" ).removeClass( "hidden" );
            		if ( has_editor ) {
            			toggleEditor( $section, "black" );
            		}
            }
        }

		/**
		* Toggles the visibility of sections, depending on the clicked CMB2 input.
		*
		* @method toggleColor
		* @param {jQuery element} 	$el 		The form input that has been clicked
		*/
        var toggleColor = function( $el ) {
        	var $section = $el.parents(".cmb-repeatable-grouping");
        	var option = $el.val();

        	// Toggle editor color only if an editor is already visible
        	if( $section.find( ".int-text:visible" ).length ) {
            	toggleEditor( $section, option );
        	}
        }

        // Meta box container
        var $metabox = $( '#int_article_cmb_box' );

        // Toggle conditional field on click
        $metabox.on( "click", ".int-section-type input", function(e) {
        	toggleType( $(e.currentTarget) );
        });
        $metabox.on( "click", ".int-background-type input", function(e) {
        	toggleBackground( $(e.currentTarget) );
        });
        $metabox.on( "click", ".int-background-color input", function(e) {
        	toggleColor( $(e.currentTarget) );
        });

        // Toggle all conditional fields
        var toggleAllSections = function() {

            $( ".int-section-type input:checked" ).each( function( i ) {
            	toggleType( $(this) );
            });

            $( ".int-background-type input:checked" ).each( function( i ) {
            	toggleBackground( $(this) );
            });

            $( ".int-background-color input:checked" ).each( function( i ) {
            	toggleColor( $(this) );
            });
        }

        // Add new section
        $metabox.on( "click", ".cmb-add-group-row", function(e) {
        	toggleAllSections();
        });

        // Move row up/down
        $metabox.on( "click", ".cmb-shift-rows", function(e) {
        	toggleAllSections();
        });

        // On page ready
        toggleAllSections();
    });

})( window.jQuery );


