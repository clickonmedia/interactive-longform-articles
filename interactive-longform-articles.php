<?php
/*
Plugin Name: Interactive Longform Articles
Description: Interactive, multimedia articles for longform journalism
Version:     1.2.13
Author:      CLICKON Media, jpylisela
Author URI:  http://www.clickon.co
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
require 'inc/options.php';

/*
	Utility function for checking for interactive article
*/
function is_interactive() {

    global $post;

    // ACF enabled
    if ( function_exists( 'get_field' ) ) {

    	// Enabled for the specific article
	    return is_single() && get_field( 'interactive-enable', $post->ID ) && !post_password_required();
    }

    return false;
}


/*
	JS scripts for the interactive article
*/
function interactive_enqueue_scripts( $hook ) {

    if ( is_interactive() ) {

	    $plugin_data = get_plugin_data( __FILE__ );

    	// Enqueue styles
    	wp_enqueue_style( 'interactive-longform-styles', plugins_url( '/css/style.css', __FILE__ ), false, $plugin_data['Version'] );

	    // Enqueue the component scripts
		wp_enqueue_script( 'jquery', plugins_url( '/js/jquery-3.3.1.min.js', __FILE__ ), array(), false, true );
	    wp_enqueue_script( 'lodash', plugins_url( '/js/lodash.min.js', __FILE__ ), array(), false, true );
		wp_enqueue_script( 'interactive-longform-script', plugins_url( '/js/main.js', __FILE__ ), array( 'jquery', 'lodash' ), false, true );

		// GA tracking options available via ajax_object
		wp_localize_script(
			'interactive-longform-script',
			'ajax_object',
		    array(
		    	'event_tracking' => get_option('int_option_google_analytics'),
		    	'tracker_name' => get_option('int_option_tracker_name'),
		    )
		);
    }
}

add_action( 'wp_enqueue_scripts', 'interactive_enqueue_scripts' );



/*
	HTML template for the interactive article
*/
function interactive_longform_custom_template( $single ) {

    if ( is_interactive() ) {

    	$template = dirname(__FILE__) . '/templates/article.php';

        if ( file_exists( $template ) ) {
            return $template;
        }
    }

    return $single;
}

add_filter( 'single_template', 'interactive_longform_custom_template' );


/*
	Add interactive article post type
*/
function interactive_create_article_post_type() {

	if( !empty( get_option('int_option_enable_post_type') ) ) {

		register_post_type( 'interactive_article',
			array(
				'labels' => array(
					'name' => __( 'Interactive' ),
					'singular_name' => __( 'Interactive Longform Article' )
				),
				'public' => true,
				'has_archive' => true,
				'menu_icon' => 'dashicons-admin-page',
				'rewrite' => array(
					'slug' => 'interactive'
				),
				'supports' => array('title')
			)
		);
	}
}
add_action( 'acf/init', 'interactive_create_article_post_type', 1 );



/*
	Rewrite flush
*/
function interactive_longform_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry,
    // when you add a post of this CPT.
    interactive_create_article_post_type();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'interactive_longform_rewrite_flush' );



/*
	Add interactive article ACF editor formats
*/
function interactive_tiny_mce_before_init( $settings ) {

	// ACF editors only
	if ( strpos( $settings["body_class"], 'acf_content' ) !== false ) {

		$style_formats = array(
		    array(
		        'title' => 'Intro Paragraph',
		        'block' => 'p',
		        'classes' => 'intro-para',
		        'exact' => true
		    ),
		    array(
		        'title' => 'Byline',
		        'block' => 'p',
		        'classes' => 'byline',
		        'exact' => true
		    ),
		    array(
		        'title' => 'Caption (left)',
		        'block' => 'div',
		        'classes' => 'caption-left',
		        'exact' => true
		    ),
		    array(
		        'title' => 'Caption (center)',
		        'block' => 'div',
		        'classes' => 'caption-center',
		        'exact' => true
		    ),
		    array(
		        'title' => 'Caption (right)',
		        'block' => 'div',
		        'classes' => 'caption-right',
		        'exact' => true
		    ),
		);

		$settings['style_formats'] = json_encode( $style_formats );

		$settings['block_formats'] = "Paragraph=p; Title=h2;";
	}

	return $settings;
}

add_filter( 'tiny_mce_before_init', 'interactive_tiny_mce_before_init', 10 );



/*
	Add interactive article ACF fields
*/
function interactive_acf_add_fields() {

	if( function_exists( 'acf_add_local_field_group' ) ) {

		$location = array();

		$post_types = array_keys( get_post_types() );

		foreach( $post_types as $post_type ) {
			$option = 'int_option_enable_for_' . $post_type;

			if ( !empty( get_option( $option ) ) ) {
				array_push( $location, array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => $post_type,
					),
				));
			}
		}

		acf_add_local_field_group( array(
			'key' => 'group_sections',
			'title' => 'Interactive sections',
			'instruction_placement' => 'label',
			'position' => 'normal',
			'label_placement' => 'top',
			'style' => 'seamless',
			'fields' => array (),
			'location' => $location
		));
	}

	if( function_exists( 'acf_add_local_field' ) ) {

		acf_add_local_field( array(
			'key' => 'field_enable',
			'label' => 'Interactive longform article',
			'name' => 'interactive-enable',
			'type' => 'checkbox',
			'collapsed' => 1,
			'parent' => 'group_sections',
			'layout' => 'row',
			'choices' => array(
				'enabled'	=> 'Enable interactive article',
			),
			'required' => 0
		));

		acf_add_local_field( array(
			'key' => 'field_repeater',
			'label' => 'Interactive sections',
			'name' => 'interactive-section',
			'type' => 'repeater',
			'collapsed' => 1,
			'parent' => 'group_sections',
			'instructions' => 'Add section backgrounds and text content',
			'layout' => 'row',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_enable',
						'operator' => '==',
						'value' => 'enabled',
					),
				),
			),
			'required' => 0
		));

		$choices = array(
			'cover'	=> 'Cover page (the first section)',
			'large'	=> 'Lead text',
			'default' => 'Body text',
			'embed' => 'Video Embed',
		);

		// Add downloads if the option is enabled
		if ( !empty( get_option( 'int_option_display_downloads' ) ) ) {
			$choices['downloads'] = 'Downloads';
		}

		acf_add_local_field( array(
			'key' => 'field_section_style',
			'label' => 'Section type',
			'name' => 'section-style',
			'type' => 'radio',
			'choices' => $choices,
			'parent' => 'field_repeater',
			'default_value' => 'default',
			'instructions' => ''
		));

		acf_add_local_field( array(
			'key' => 'field_bg_type',
			'label' => 'Background type',
			'name' => 'background-type',
			'type' => 'radio',
			'choices' => array(
				'color'	=> 'Color',
				'image'	=> 'Image',
				'video'	=> 'Video',
			),
			'parent' => 'field_repeater',
			'default_value' => 'color',
			'instructions' => 'Select background type'
		));

		acf_add_local_field( array(
			'key' => 'field_bg_image',
			'label' => 'Background image',
			'name' => 'background-image',
			'type' => 'image',
			'return_format' => 'array',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'image',
					),
				),
			),
			'parent' => 'field_repeater',
			'instructions' => 'JPEG image (16:9), at least 2600px (width) x 1465px (height)'
		));

		acf_add_local_field( array(
			'key' => 'field_bg_image_mobile',
			'label' => 'Mobile background image',
			'name' => 'background-image-mobile',
			'type' => 'image',
			'return_format' => 'array',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'image',
					),
				),
			),
			'parent' => 'field_repeater',
			'instructions' => 'Optional (JPEG image, 9:16), at least 1300px (width) x 2310px (height).'
		));

		acf_add_local_field( array(
			'key' => 'field_bg_opacity',
			'label' => 'Brightness',
			'name' => 'background-opacity',
			'type' => 'range',
			'return_format' => 'array',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'image',
					),
				),
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'video',
					),
				),
			),
			'parent' => 'field_repeater',
			'default_value' => 100,
			'instructions' => ''
		));

		acf_add_local_field( array(
			'key' => 'field_bg_align',
			'label' => 'Background alignment',
			'name' => 'background-align',
			'type' => 'radio',
			'choices' => array(
				'left center'	=> 'left center',
				'left top'	=> 'left top',
				'left bottom'	=> 'left bottom',
				'center center'	=> 'center center',
				'center top'	=> 'center top',
				'center bottom'	=> 'center bottom',
				'right center'	=> 'right center',
				'right top'	=> 'right top',
				'right bottom'	=> 'right bottom',
			),
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'image',
					),
				),
			),
			'parent' => 'field_repeater',
			'default_value' => 'center center',
		));

		acf_add_local_field( array(
			'key' => 'field_bg_color',
			'label' => 'Background color',
			'name' => 'background-color',
			'type' => 'radio',
			'choices' => array(
				'black'	=> 'Black',
				'white'	=> 'White',
			),
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'color',
					),
				),
			),
			'parent' => 'field_repeater',
			'default_value' => 'black',
			'instructions' => ''
		));

		acf_add_local_field( array(
			'key' => 'field_bg_poster',
			'label' => 'Video screenshot',
			'name' => 'background-poster',
			'type' => 'image',
			'return_format' => 'array',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'video',
					),
				),
			),
			'parent' => 'field_repeater',
			'instructions' => 'JPEG image, 16:9 (max 200kB). Is displayed as a placeholder image while video is loading.'
		));

		acf_add_local_field( array(
			'key' => 'field_bg_gif',
			'label' => 'Mobile video',
			'name' => 'background-gif',
			'type' => 'file',
			'return_format' => 'url',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'video',
					),
				),
			),
			'parent' => 'field_repeater',
			'instructions' => 'GIF image (max 3MB)'
		));

		acf_add_local_field( array(
			'key' => 'field_bg_video_md',
			'label' => 'Medium-sized video (16:9)',
			'name' => 'background-video-md',
			'type' => 'file',
			'return_format' => 'url',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'video',
					),
				),
			),
			'parent' => 'field_repeater',
			'instructions' => 'MP4, 16:9, 10-15 sec (max 5MB)'
		));

		acf_add_local_field( array(
			'key' => 'field_bg_video_xl',
			'label' => 'Large video (16:9)',
			'name' => 'background-video-xl',
			'type' => 'file',
			'return_format' => 'url',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_type',
						'operator' => '==',
						'value' => 'video',
					),
				),
			),
			'parent' => 'field_repeater',
			'instructions' => 'MP4, 16:9, 10-15 sec (max 5MB)'
		));

		acf_add_local_field( array(
			'key' => 'field_video_oembed',
			'label' => 'Video Embed',
			'name' => 'interactive-video-embed',
			'type' => 'oembed',
			'parent' => 'field_repeater',
			'instructions' => 'Add download',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_section_style',
						'operator' => '==',
						'value' => 'embed',
					),
				),
			),
			'required' => 0,
			'instructions' => 'A simple video embed - If text is required, please use the text editor.'
		));

		acf_add_local_field( array(
			'key' => 'field_download_repeater',
			'label' => 'Downloads',
			'name' => 'interactive-download',
			'type' => 'repeater',
			'collapsed' => 1,
			'parent' => 'field_repeater',
			'instructions' => 'Add download',
			'layout' => 'row',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_section_style',
						'operator' => '==',
						'value' => 'downloads',
					),
				),
			),
			'required' => 0,
			'instructions' => 'The files uploaded here will be available in the downloads section.'
		));

		acf_add_local_field( array(
			'key' => 'field_download_repeater_image',
			'label' => 'File',
			'name' => 'interactive-download-file',
			'type' => 'file',
			'return_format' => 'array',
			'parent' => 'field_download_repeater',
			'instructions' => 'PDF format'
		));

		acf_add_local_field( array(
			'key' => 'field_text_black',
			'label' => 'Text',
			'name' => 'text-black',
			'type' => 'wysiwyg',
			'parent' => 'field_repeater',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_color',
						'operator' => '==',
						'value' => 'black',
					),
					array (
						'field' => 'field_bg_type',
						'operator' => '!=',
						'value' => 'none',
					),
					array (
						'field' => 'field_section_style',
						'operator' => '!=',
						'value' => 'embed',
					),
					array (
						'field' => 'field_section_style',
						'operator' => '!=',
						'value' => 'downloads',
					),
				),
			),
			'instructions' => ''
		));

		acf_add_local_field( array(
			'key' => 'field_text_white',
			'label' => 'Text',
			'name' => 'text-white',
			'type' => 'wysiwyg',
			'parent' => 'field_repeater',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_bg_color',
						'operator' => '==',
						'value' => 'white',
					),
					array (
						'field' => 'field_bg_type',
						'operator' => '!=',
						'value' => 'none',
					),
					array (
						'field' => 'field_section_style',
						'operator' => '!=',
						'value' => 'embed',
					),
					array (
						'field' => 'field_section_style',
						'operator' => '!=',
						'value' => 'downloads',
					),
				),
			),
			'instructions' => ''
		));
	}
}

add_action( 'acf/init', 'interactive_acf_add_fields', 10 );



/*
	Add interactive article image size
*/
function interactive_setup() {
	add_image_size( 'interactive-sm', 1280, 2272, false );
	add_image_size( 'interactive-xl', 2560, 1440, false );
}

add_action( 'after_setup_theme', 'interactive_setup' );



/*
	Add black and white WYSIWYG editor styles
*/
function interactive_editor_admin_footer() {
	$styles = plugins_url( '/css/editor-style.css', __FILE__ );
	$black_styles = plugins_url( '/css/editor-black.css', __FILE__ );
	$white_styles = plugins_url( '/css/editor-white.css', __FILE__ );
?>

<script type="text/javascript">
(function($) {

	if ( acf ) {

		acf.add_filter( 'wysiwyg_tinymce_settings', function( mceInit, id, $field ) {

			console.log( 'ACF field', $field );
			console.log( 'mceInit', mceInit );

			if ( $field.hasClass( 'acf-field' ) ) {
				 mceInit['content_css'] += ',' + '<?php echo $styles; ?>';
			}

			if ( $field.hasClass( 'acf-field-text-black' ) ) {
				 mceInit['content_css'] += ',' + '<?php echo $black_styles; ?>';
			}

			if ( $field.hasClass( 'acf-field-text-white' ) ) {
				 mceInit['content_css'] += ',' + '<?php echo $white_styles; ?>';
			}

			return mceInit;
		});
	}

})( jQuery );
</script>

<?php
}

add_action('acf/input/admin_footer', 'interactive_editor_admin_footer');



/*
 * 	Check for plugin dependencies
 */
if ( ! function_exists( 'interactive_theme_dependencies' ) ) {

	function interactive_theme_dependencies() {

		if( ! function_exists('acf_add_local_field') ) {
			echo '<div class="error"><p>' . __( 'Warning: The Interactive Longform Articles requires Advanced Custom Fields Pro plugin to function', 'streampress' ) . '</p></div>';
		}
	}
}

add_action( 'admin_notices', 'interactive_theme_dependencies' );



/**
 * Removes buttons from the editor first row
 */
add_filter( 'mce_buttons', 'interactive_remove_tiny_mce_buttons_top');
function interactive_remove_tiny_mce_buttons_top( $buttons ) {
    $remove_buttons = array(
        'wp_more', // read more link
        'spellchecker',
        'dfw', // distraction free writing mode
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }
    return $buttons;
}


/**
 * Removes buttons from the editor second row
 */
add_filter( 'mce_buttons_2', 'interactive_remove_tiny_mce_buttons_bottom');

function interactive_remove_tiny_mce_buttons_bottom( $buttons ) {
    $remove_buttons = array(
        'outdent',
        'indent',
        'removeformat', // clear formatting
        'charmap', // special characters
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }

    if( ! in_array( 'styleselect', $buttons )) {

		//Add style selector to the beginning of the toolbar
		array_unshift( $buttons, 'styleselect' );
    }

    return $buttons;
}


/**
 *  Add custom REST API endpoint for detecting an interactive article
 */
function interactive_add_to_rest_api_data(){

    register_rest_field( 'post',
        'interactive',
        array(
            'get_callback'    => 'int_post_is_interactive',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

function int_post_is_interactive ( $post ) {
	return function_exists( 'get_field' ) && get_field( 'interactive-enable', $post['id'] );
}

add_action( 'rest_api_init', 'interactive_add_to_rest_api_data' );


