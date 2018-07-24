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

                    //You can find this in the value of the Page Template dropdown
                    var templateName = \'interactive-template.php\';

                    //Page template in the publishing options
                    var currentTemplate = $(\'#page_template\');

                    //Identify your metabox
                    var metabox = $(\'#'.$metabox_selector_id.'\');

                    //On DOM ready, check if your page template is selected
                    if(currentTemplate.val() === templateName || post_type === "interactive_article" ){
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

                    var toggleSection = function( $el ) {

                    	var $section = $el.parents(".cmb-repeatable-grouping");
                    	var option = $el.val();
                    	var name = $el.attr("name");

                    	// Section type
                    	if( name.indexOf("int_section_type") !== -1 ) {

                    		console.log("section", option);

                    		switch( option ) {
                    			case "embed":
		                    		$section.find(".int-video-embed").removeClass( "hidden" );
		                    		$section.find(".int-text-default").addClass( "hidden" );
		                    		$section.find(".int-downloads").addClass( "hidden" );
		                    		break;
		                    	case "downloads":
		                    		$section.find(".int-downloads").removeClass( "hidden" );
		                    		$section.find(".int-text-default").addClass( "hidden" );
		                    		$section.find(".int-video-embed").addClass( "hidden" );
		                    		break;
		                    	default:
		                    		$section.find(".int-text-default").removeClass( "hidden" );
		                    		$section.find(".int-downloads").addClass( "hidden" );
		                    		$section.find(".int-video-embed").addClass( "hidden" );
		                    }
                    	}

                    	// Background type
                    	if( name.indexOf("int_background_type") !== -1 ) {

                    		console.log("background", option);

                    		// Background color
                    		var show_color = (option === "color");
                    		$section.find(".int-background-color").toggleClass( "hidden", !show_color );
                    		$section.find(".int-background-opacity").toggleClass( "hidden", show_color );
                    		$section.find(".int-background-align").toggleClass( "hidden", show_color );

	                    	// Background image
	                		var show_image = (option === "image");
	                		$section.find(".int-background-image").toggleClass("hidden", !show_image );
	                		$section.find(".int-background-image-mobile").toggleClass("hidden", !show_image );

	                    	// Background video
	                 		var show_video = (option === "video");
	                		$section.find(".int-background-video").toggleClass("hidden", !show_video );
	                		$section.find(".int-background-video-mobile").toggleClass("hidden", !show_video );
	                		$section.find(".int-background-video-poster").toggleClass("hidden", !show_video );
                    	}
                    }

                    /*
                    	Toggle conditional field on click
                    */
                    $( "#int_article_cmb_box" ).on( "click", ".int-section-type input", function(e) {
                    	toggleSection( $(e.currentTarget) );
                    });
                    $( "#int_article_cmb_box" ).on( "click", ".int-background-type input", function(e) {
                    	toggleSection( $(e.currentTarget) );
                    });

                    /*
                    	Toggle all conditional fields
                    */
                    var toggleAllSections = function() {
                    	console.log( "toggle all" );

	                    $( ".int-section-type input:checked" ).each( function( i ) {
	                    	toggleSection( $(this) );
	                    });

	                    $( ".int-background-type input:checked" ).each( function( i ) {
	                    	toggleSection( $(this) );
	                    });
                    }

                    // Add new row
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
			'goodtobebad-template.php' => 'Good To Be Bad',
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