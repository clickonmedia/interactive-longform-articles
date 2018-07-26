<?php


add_action( 'admin_head-post.php', 'metabox_switcher' );
add_action( 'admin_head-post-new.php', 'metabox_switcher' );

function metabox_switcher( $post ){

    #Isolate to your specific post type
    // if( $post->post_type === 'page' || $post->post_type === 'post' ){

        #Locate the ID of your metabox with Developer tools
        $metabox_selector_id = 'postbox-container-2';

        echo '
            <style type="text/css">
                /* Hide your metabox so there is no latency flash of your metabox before being hidden */
                #'.$metabox_selector_id.'{display:none;}
            </style>

            <script type="text/javascript">

            	var getUrlParameter = function getUrlParameter(sParam) {
				    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
				        sURLVariables = sPageURL.split("&"),
				        sParameterName,
				        i;

				    for (i = 0; i < sURLVariables.length; i++) {
				        sParameterName = sURLVariables[i].split("=");

				        if (sParameterName[0] === sParam) {
				            return sParameterName[1] === undefined ? true : sParameterName[1];
				        }
				    }
				};

                jQuery(document).ready( function( $ ) {

                	// Get the current post type
                	var post_type = getUrlParameter("post_type");

                	console.log( "post_type", post_type );

                    //You can find this in the value of the Page Template dropdown
                    var templateName = \'interactive-template.php\';

                    //Page template in the publishing options
                    var currentTemplate = $(\'#page_template\');

                    //Identify your metabox
                    var metabox = $(\'#'.$metabox_selector_id.'\');

                    //On DOM ready, check if your page template is selected
                    if(currentTemplate.val() === templateName || $( "body" ).hasClass( "post-type-interactive_article" ) ){
                        metabox.show();
                    }

                    //Bind a change event to make sure we show or hide the metabox based on user selection of a template
                    currentTemplate.change(function(e) {
                        if(currentTemplate.val() === templateName){
                            metabox.show();
                        }
                        else{
                            //You should clear out all metabox values here;
                            metabox.hide();
                        }
                    });

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

                    /*
                    	Toggle conditional field on click
                    */
                    $( "#int_article_cmb_box" ).on( "click", ".int-section-type input", function(e) {
                    	toggleType( $(e.currentTarget) );
                    });
                    $( "#int_article_cmb_box" ).on( "click", ".int-background-type input", function(e) {
                    	toggleBackground( $(e.currentTarget) );
                    });
                    $( "#int_article_cmb_box" ).on( "click", ".int-background-color input", function(e) {
                    	toggleColor( $(e.currentTarget) );
                    });

                    /*
                    	Toggle all conditional fields
                    */
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
                    $( "#int_article_cmb_box" ).on( "click", ".cmb-add-group-row", function(e) {
                    	toggleAllSections();
                    });

                    // On page ready
                    toggleAllSections();
                });
            </script>
        ';
    //}
}

class PageTemplater {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new PageTemplater();
		}

		return self::$instance;

	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_post_templates', array( $this, 'add_new_template' )
			);

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data',
			array( $this, 'register_project_templates' )
		);


		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path
		add_filter(
			'template_include',
			array( $this, 'view_project_template')
		);


		// Add your templates to this array.
		$this->templates = array(
			'interactive-template.php' => 'Interactive Article',
		);

	}

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doesn't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		// Return the search template if we're searching (instead of the template for the first result)
		if ( is_search() ) {
			return $template;
		}

		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta(
			$post->ID, '_wp_page_template', true
		)] ) ) {
			return $template;
		}

		// Allows filtering of file path
		$filepath = apply_filters( 'page_templater_plugin_dir_path', plugin_dir_path( __FILE__ ) );

		$file =  $filepath . get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

}
add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );