<?php
/*
Plugin Name: Interactive Longform Articles
Description: Interactive multimedia articles for longform journalism
Version:     2.0.0
Author:      CLICKON Media
Author URI:  https://www.clickon.co
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Initialize CMB2
 * https://github.com/CMB2/CMB2
 */
require_once __DIR__ . '/cmb2/init.php';

/**
 * Custom CMB field types
 */
require_once __DIR__ . '/inc/custom-field-types.php';

/**
 * Settings page
 */
require_once __DIR__ . '/inc/options.php';

/**
 * Adding the page templates
 */
require_once __DIR__ . '/pagetemplater.php';



function int_metabox_register() {


	// if ( 'foobar.php' == get_post_meta( $post->ID, '_wp_page_template', true ) ) {
	// }

		$prefix = 'int_';

        $cmb = new_cmb2_box( array(
            'id'            => $prefix . 'article_cmb_box',
            'title'         => __( 'Repeatable Section', 'your-text-domain' ),
            'object_types' => array( 'post', 'page', 'interactive_article' ), // post type
            //'show_on'      => array( 'key' => 'page-template', 'value' => 'page-test.php' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ));

        // Repeatable Section Group
        $group_sections = $cmb->add_field( array(
            'id'          => $prefix . 'article_sections',
            'classes'     => 'int-article-sections',
            'type'        => 'group',
            'options'     => array(
                'group_title'   => __( 'Section', 'your-text-domain' ) . ' {#}', // {#} gets replaced by row number
                'add_button'    => __( 'Add another Section', 'your-text-domain' ),
                'remove_button' => __( 'Remove Section', 'your-text-domain' ),
                'sortable'      => true, // beta
            ),
        ) );

        /*
        	Section type
        */
		$section_types = array(
			'cover' 	=> __( 'Cover page', 'your-text-domain' ),
			'large'   	=> __( 'Lead text', 'your-text-domain' ),
			'default'   => __( 'Body text', 'your-text-domain' ),
			'embed'     => __( 'Video embed', 'your-text-domain' ),
		);

		// Add downloads if the option is enabled
		if ( !empty( get_option( 'int_option_display_downloads' ) ) ) {
			$section_types['downloads'] = __( 'Downloads', 'your-text-domain' );
		}

		$cmb->add_group_field( $group_sections, array(
			'name'             => 'Section Type',
			'id'               => $prefix . 'section_type',
			'classes' 		   => 'int-section-type',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => $section_types,
			'default' 		   => 'default'
		));

        // Background Type
		$cmb->add_group_field( $group_sections, array(
			'name'             => 'Background Type',
			'id'               => $prefix . 'background_type',
			'classes' 		   => 'int-background-type',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => array(
				'color' => __( 'Color', 'your-text-domain' ),
				'image' => __( 'Image', 'your-text-domain' ),
				'video' => __( 'Video', 'your-text-domain' ),
			),
			'default' => 'color'
		));

		// Background Color
		$cmb->add_group_field( $group_sections, array(
			'name'             => 'Background Color',
			'id'               => $prefix . 'background_color',
			'classes' 		   => 'int-background-color',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => array(
				'black' => __( 'Black', 'your-text-domain' ),
				'white'     => __( 'White', 'your-text-domain' ),
			),
			'default' => 'black',
			'show_on_cb' => true,
		));

        // Background image
        $cmb->add_group_field( $group_sections, array(
			'name'    => 'Background image',
			'desc'    => 'JPEG image (16:9), at least 2600px (width) x 1465px (height)',
			'id'      => $prefix . 'background_image',
			'classes' => 'int-background-image',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Add Image' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				'type' => array(
				 	'image/gif',
				 	'image/jpeg',
				 	'image/png',
				),
			),
			'preview_size' => 'medium', // Image size to use when previewing in the admin.
			'show_on_cb' => true,
		) );

        // Mobile background image
        $cmb->add_group_field( $group_sections, array(
			'name'    => 'Mobile background image (optional)',
			'desc'    => 'JPEG image 9:16, at least 1300px (width) x 2310px (height)',
			'id'      => $prefix . 'background_image_mobile',
			'classes' => 'int-background-image-mobile',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Add Image' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				'type' => array(
				 	'image/gif',
				 	'image/jpeg',
				 	'image/png',
				),
			),
			'preview_size' => 'medium', // Image size to use when previewing in the admin.
			'show_on_cb' => true,
		) );

        // Background video
        $cmb->add_group_field( $group_sections, array(
			'name'    => 'Background video',
			'desc'    => 'Upload a video file',
			'id'      => $prefix . 'background_video',
			'classes' => 'int-background-video',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Add an MP4 video file' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				// 'type' => 'application/pdf', // Make library only display PDFs.
				// Or only video formats
				'type' => array(
				 	'video/mp4'
				),
			),
			'preview_size' => 'medium', // Image size to use when previewing in the admin.
			'show_on_cb' => true,
		) );

        // Video - mobile
        $cmb->add_group_field( $group_sections, array(
			'name'    => 'Mobile background video',
			'desc'    => 'Upload a GIF file',
			'id'      => $prefix . 'background_video_mobile',
			'classes' => 'int-background-video-mobile',
			'type'    => 'file',
			'options' => array(
				'url' => true,
			),
			'text'    => array(
				'add_upload_file_text' => 'Add a GIF file'
			),
			'query_args' => array(
				'type' => array(
				 	'image/gif',
				),
			),
			'preview_size' => 'medium',
			'show_on_cb' => true,
		) );

        // Video - screenshot/poster
        $cmb->add_group_field( $group_sections, array(
			'name'    => 'Screenshot for background video ',
			'desc'    => 'Upload a JPG screenshot image (max. 300 kb)',
			'id'      => $prefix . 'background_video_poster',
			'classes' => 'int-background-video-poster',
			'type'    => 'file',
			'options' => array(
				'url' => true,
			),
			'text'    => array(
				'add_upload_file_text' => 'Add a JPG screenshot image'
			),
			'query_args' => array(
				'type' => array(
				 	'image/jpeg',
				 	'image/png',
				),
			),
			'preview_size' => 'medium',
			'show_on_cb' => true,
		) );

        // Background Color
		$cmb->add_group_field( $group_sections, array(
			'name'             => 'Background Color',
			'id'               => $prefix . 'background_color',
			'classes' 		   => 'int-background-color',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => array(
				'black' => __( 'Black', 'your-text-domain' ),
				'white'     => __( 'White', 'your-text-domain' ),
			),
			'default' => 'black',
			'show_on_cb' => true,
		));

        // Opacity
		$cmb->add_group_field( $group_sections, array(
			'name'             => 'Brightness',
			'id'               => $prefix . 'background_opacity',
			'classes' 		   => 'int-background-opacity',
			'type'             => 'range_input',
			'desc'			   => '',
			'show_option_none' => false,
			'default' => '100',
			'show_on_cb' => true,
		));

        // Background alignment
		$cmb->add_group_field( $group_sections, array(
			'name'             => __( 'Background alignment', 'your-text-domain' ),
			'id'               => $prefix . 'background_align',
			'classes' 		   => 'int-background-align',
			'type'             => 'radio',
			'show_option_none' => false,
			'options' => array(
				'left center'	=>  __( 'left center', 'your-text-domain' ),
				'left top'	=>  __( 'left top', 'your-text-domain' ),
				'left bottom'	=>  __( 'left bottom', 'your-text-domain' ),
				'center center'	=>  __( 'center center', 'your-text-domain' ),
				'center top'	=>  __( 'center top', 'your-text-domain' ),
				'center bottom'	=>  __( 'center bottom', 'your-text-domain' ),
				'right center'	=>  __( 'right center', 'your-text-domain' ),
				'right top'	=>  __( 'right top', 'your-text-domain' ),
				'right bottom'	=>  __( 'right bottom', 'your-text-domain' ),
			),
			'default' => 'center center',
			'show_on_cb' => true,
		));

        /*
        	Default TinyMCE editor
        */
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

		$content_css = plugins_url( '/css/editor-style.css', __FILE__ ) . ', ' . plugins_url( '/css/editor-white.css', __FILE__ );

		$editor_args = array(
			'name'    => 'Text',
			'desc'    => '',
			'id'      => $prefix . 'text_default',
			'classes' => 'int-text-default',
			'type'    => 'wysiwyg',
			'options' => array(
			    'wpautop' => false, // use wpautop?
			    'media_buttons' => true, // show insert/upload button(s)
			    'textarea_name' => '', // set the textarea name to something different, square brackets [] can be used here
			    'textarea_rows' => get_option( 'default_post_edit_rows', 10 ), // rows="..."
			    'tabindex' => '',
			    'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
			    'editor_class' => 'white_whatever', // add extra class(es) to the editor textarea
			    'teeny' => false, // output the minimal editor config used in Press This
			    'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
			    'tinymce' => array(
			    	'style_formats' => json_encode( $style_formats ),
			    	'block_formats' => "Paragraph=p; Title=h2;",
			    	'content_css' => $content_css
			    ),  // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			    'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
			),
		);

        $cmb->add_group_field( $group_sections, $editor_args );

        /*
        	Video Embed
        */
		$cmb->add_group_field( $group_sections, array(
			'name' => 'Video Embed',
			'desc' => 'A simple video embed - If text is required, please use the text editor.',
			'id'      => $prefix . 'video_embed',
			'classes' => 'int-video-embed',
			'type' => 'oembed',
			'show_on_cb' => true,
		) );

        /*
        	Downloads
        */
		if ( !empty( get_option( 'int_option_display_downloads' ) ) ) {

			$cmb->add_group_field( $group_sections, array(
				'name' => 'Downloads',
				'desc' => 'The files uploaded here will be available in the downloads section.',
				'id'      => $prefix . 'downloads',
				'classes' => 'int-downloads',
				'type' => 'file_list',
				'show_on_cb' => true,
				'text' => array(
					'add_upload_files_text' => 'Add or Upload Files',
					'remove_image_text' => 'Remove Image',
					'file_text' => 'File:',
					'file_download_text' => 'Download',
					'remove_text' => 'Remove',
				),
			) );
		}

}

add_action( 'cmb2_init', 'int_metabox_register' );


/*
	Utility function for checking for interactive article
*/
function is_interactive() {

    global $post;

	$meta = get_post_meta( get_the_ID(), 'int_article_sections', true );

	// Interactive articles are enabled for this article
    return !empty( $meta );
}


/*
	JS scripts for the interactive article
*/
function interactive_enqueue_scripts( $hook ) {

    if ( is_interactive() ) {

	    $plugin_data = get_plugin_data( __FILE__ );

    	// Enqueue styles
    	wp_enqueue_style( 'flexslider', plugins_url( '/css/flexslider.css', __FILE__ ), false, $plugin_data['Version'] );
    	wp_enqueue_style( 'interactive-longform-styles', plugins_url( '/css/style.min.css', __FILE__ ), false, $plugin_data['Version'] );

	    // Enqueue the component scripts
		wp_enqueue_script( 'jquery', plugins_url( '/js/jquery-3.3.1.min.js', __FILE__ ), array(), false, true );
	    wp_enqueue_script( 'lodash', plugins_url( '/js/lodash.min.js', __FILE__ ), array(), false, true );
	    wp_enqueue_script( 'flexslider', plugins_url( '/js/jquery.flexslider-min.js', __FILE__ ), array(), false, true );
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




function interactive_longform_custom_template( $single ) {

    global $wp_query, $post;

    // Checks for single template by post type
    if ( $post->post_type == 'interactive_article' ) {
        if ( file_exists( plugin_dir_path( __FILE__ ) . '/interactive-template.php' ) ) {
            return plugin_dir_path( __FILE__ ) . '/interactive-template.php';
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
				'supports' => array('title', 'thumbnail'),
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
			'related' => 'Related articles',
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
			'label' => 'Mobile background image (optional)',
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
			'instructions' => 'JPEG image 9:16, at least 1300px (width) x 2310px (height).'
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
			'instructions' => 'GIF image (max 2MB)'
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
			'instructions' => 'MP4, 16:9, 10-15 sec (max 3MB)'
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
			'instructions' => 'MP4, 16:9, 10-15 sec (max 3MB)'
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
			'instructions' => 'Paste the video URL here - If text is required, please use a section type that has a text editor.'
		));

		/*
			Downloads
		*/
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

		/*
			Related articles
		*/
		acf_add_local_field( array(
			'key' => 'field_related_repeater',
			'label' => 'Related articles',
			'name' => 'interactive-related',
			'type' => 'repeater',
			'collapsed' => 1,
			'parent' => 'field_repeater',
			'instructions' => 'Add related article',
			'layout' => 'row',
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_section_style',
						'operator' => '==',
						'value' => 'related',
					),
				),
			),
			'required' => 0,
			'instructions' => 'These articles will be listed in a Related articles section.'
		));

		acf_add_local_field( array(
			'key' => 'field_related_repeater_image',
			'label' => 'Image',
			'name' => 'interactive-related-image',
			'type' => 'file',
			'return_format' => 'array',
			'parent' => 'field_related_repeater',
			'instructions' => ''
		));

		acf_add_local_field( array(
			'key' => 'field_related_repeater_title',
			'label' => 'Title',
			'name' => 'interactive-related-title',
			'type' => 'text',
			'parent' => 'field_related_repeater',
			'instructions' => ''
		));

		acf_add_local_field( array(
			'key' => 'field_related_repeater_link',
			'label' => 'URL',
			'name' => 'interactive-related-link',
			'type' => 'link',
			'return_format' => 'array',
			'parent' => 'field_related_repeater',
			'instructions' => ''
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
					array (
						'field' => 'field_section_style',
						'operator' => '!=',
						'value' => 'related',
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
	$slug = get_page_template_slug( $post['id'] );
	return function_exists( 'get_field' ) && ( $slug == 'interactive-template.php' );
}

add_action( 'rest_api_init', 'interactive_add_to_rest_api_data' );



/**
 *  Shortcode for showing the latest interactive articles
 */
// [interactive-list max="5"]
function int_shortcode_interactive_list( $atts ) {

    $atts = shortcode_atts( array(
        'type' => 'strip',
        'max' => 2,
    ), $atts, 'interactive-list' );

	$articles = new WP_Query( array(
		'post_type' => 'interactive_article',
		'posts_per_page' => $atts['max'],
		'offset' => 0,
		'ignore_sticky_posts' => true,
		'has_password' => false,
		'post__not_in' => array( get_the_ID() ),
	    'meta_query' => array(
	        array(
	            'key' => 'interactive-enable',
	            'value' => 'enabled',
	            'compare' => 'LIKE'
	        )
	    )
	));

	$html = '<div class="flexslider"><ul class="slides">';

	if ( $articles->have_posts() ) : ?>

		<?php while ( $articles->have_posts() ) : $articles->the_post(); ?>

			<?php
				global $post;
				$html .= '<li>
							<a href="' . esc_url( get_permalink() ) . '" class="flex-item" style="background-image: url(' . get_the_post_thumbnail_url( $post->ID, 'interactive-sm' ) . ')">
							    <p class="flex-caption">' . get_the_title() . '</p>
						  	</a>
						  </li>';
			?>

		<?php endwhile; ?>
	<?php endif; ?>

	<?php
	$html .= '</ul></div>';

    return $html;
}

add_shortcode( 'interactive-list', 'int_shortcode_interactive_list' );

