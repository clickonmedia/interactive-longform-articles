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


/**
 * Create CMB2 metabox and related fields
 */
function int_metabox_register() {

	$prefix = 'int_';

    $cmb = new_cmb2_box( array(
        'id'            => $prefix . 'article_cmb_box',
        'title'         => __( 'Repeatable Section', 'your-text-domain' ),
        'object_types' => array( 'post', 'page', 'interactive_article' ),
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

    // Section type
	$section_types = array(
		'cover' 	=> __( 'Cover page', 'your-text-domain' ),
		'large'   	=> __( 'Lead text', 'your-text-domain' ),
		'default'   => __( 'Body text', 'your-text-domain' ),
		'embed'     => __( 'Video embed', 'your-text-domain' ),
	);

	// Add downloads if the option is enabled in settings
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
		'options' => array(
			'url' => true,
		),
		'text'    => array(
			'add_upload_file_text' => 'Add Image'
		),
		'query_args' => array(
			'type' => array(
			 	'image/gif',
			 	'image/jpeg',
			 	'image/png',
			),
		),
		'preview_size' => 'medium',
		'show_on_cb' => true,
	) );

    // Background video
    $cmb->add_group_field( $group_sections, array(
		'name'    => 'Background video',
		'desc'    => 'Upload a video file',
		'id'      => $prefix . 'background_video',
		'classes' => 'int-background-video',
		'type'    => 'file',
		'options' => array(
			'url' => true, // Hide the text input for the url
		),
		'text'    => array(
			'add_upload_file_text' => 'Add an MP4 video file'
		),
		'query_args' => array(
			'type' => array(
			 	'video/mp4'
			),
		),
		'preview_size' => 'medium',
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
		'name'    => __( 'Screenshot for background video', 'your-text-domain' ),
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
		'name'             => __( 'Background color', 'your-text-domain' ),
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
		'name'             => __( 'Brightness', 'your-text-domain' ),
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
    	TinyMCE editor (black background)
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

	$editor_css_black = plugins_url( '/css/editor-style.css', __FILE__ ) . ', ' . plugins_url( '/css/editor-black.css', __FILE__ );

	$editor_args = array(
		'name'    => 'Text',
		'desc'    => '',
		'id'      => $prefix . 'text_black',
		'classes' => 'int-text int-text-black',
		'type'    => 'wysiwyg',
		'options' => array(
		    'wpautop' => false, // use wpautop?
		    'media_buttons' => true, // show insert/upload button(s)
		    'textarea_name' => '', // set the textarea name to something different, square brackets [] can be used here
		    'textarea_rows' => get_option( 'default_post_edit_rows', 10 ), // rows="..."
		    'tabindex' => '',
		    'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
		    'editor_class' => '', // add extra class(es) to the editor textarea
		    'teeny' => false, // output the minimal editor config used in Press This
		    'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
		    'tinymce' => array(
		    	'style_formats' => json_encode( $style_formats ),
		    	'block_formats' => "Paragraph=p; Title=h2;",
		    	'content_css' => $editor_css_black,
		    	'toolbar' => 'styleselect'
		    ),  // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
		    'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
		),
	);

    $cmb->add_group_field( $group_sections, $editor_args );

    /*
    	TinyMCE editor (white background)
    */
	$editor_css_white = plugins_url( '/css/editor-style.css', __FILE__ ) . ', ' . plugins_url( '/css/editor-white.css', __FILE__ );

	$editor_args['id'] = $prefix . 'text_white';
	$editor_args['classes'] = 'int-text int-text-white';
	$editor_args['options']['tinymce']['content_css'] = $editor_css_white;

	$cmb->add_group_field( $group_sections, $editor_args );

    /*
    	Video Embed
    */
	$cmb->add_group_field( $group_sections, array(
		'name' => 'Video Embed',
		'desc' => 'A simple video URL embed - If text is required, please use the text editor.',
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
	Utility function for checking when the current post is interactive
*/
function is_interactive_article() {
	global $post;

	// Page template
	if ( 'interactive-template.php' == get_page_template_slug( $post->ID ) ) {
		return true;
	}

	// Custom post type
	if ( 'interactive_article' == $post->post_type ) {
		return true;
	}

    return false;
}


/*
	Utility function for checking when the post uses the interactive-list shortcode
*/
function interactive_has_shortcode() {
	global $post;

	$sections = get_post_meta( $post->ID, 'int_article_sections', true );

    return has_shortcode( serialize( $sections ), 'interactive-list' );
}


/*
	JS scripts for the interactive article
*/
function interactive_enqueue_scripts( $hook ) {

    if ( is_interactive_article() ) {

		$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );

    	// Enqueue styles
    	if( interactive_has_shortcode() ) {
    		wp_enqueue_style( 'flexslider', plugins_url( '/css/flexslider.css', __FILE__ ), false, $plugin_data['Version'] );
    	}
    	wp_enqueue_style( 'interactive-longform-styles', plugins_url( '/css/style.min.css', __FILE__ ), false, $plugin_data['Version'] );

	    // Enqueue scripts
		wp_enqueue_script( 'jquery', plugins_url( '/js/jquery-3.3.1.min.js', __FILE__ ), array(), false, true );
	    wp_enqueue_script( 'lodash', plugins_url( '/js/lodash.min.js', __FILE__ ), array(), false, true );

	    if( interactive_has_shortcode() ) {
    		wp_enqueue_script( 'flexslider', plugins_url( '/js/jquery.flexslider-min.js', __FILE__ ), array(), false, true );
    	}
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

		// Inline styles
        $color = get_option( 'int_option_progress_color' );
        $custom_css = "
			.interactive-section progress[value]::-webkit-progress-value {
				background-color: {$color};
			}

			.interactive-section progress[value]::-moz-progress-bar {
				background: {$color};
			}
                ";
        wp_add_inline_style( 'interactive-longform-styles', $custom_css );
    }
}

add_action( 'wp_enqueue_scripts', 'interactive_enqueue_scripts' );


/*
	JS scripts for WP admin
*/
function interactive_enqueue_admin_scripts($hook) {

    if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
    	wp_enqueue_script( 'interactive-admin-script', plugin_dir_url(__FILE__) . 'js/admin.js' );
    }

    return;
}

add_action('admin_enqueue_scripts', 'interactive_enqueue_admin_scripts');


/*
	Use custom template for interactive_article custom post type
*/
function interactive_article_custom_template( $single ) {

    global $wp_query, $post;

    // Checks for single template by post type
    if ( $post->post_type == 'interactive_article' ) {
        if ( file_exists( plugin_dir_path( __FILE__ ) . '/interactive-template.php' ) ) {
            return plugin_dir_path( __FILE__ ) . '/interactive-template.php';
        }
    }

    return $single;

}

add_filter( 'single_template', 'interactive_article_custom_template' );


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
add_action( 'init', 'interactive_create_article_post_type', 1 );


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


/**
 * Add interactive article custom image sizes
 */
function interactive_setup() {
	add_image_size( 'interactive-sm', 1280, 2272, false );
	add_image_size( 'interactive-xl', 2560, 1440, false );
}

add_action( 'after_setup_theme', 'interactive_setup' );


/**
 * Adds styleselect formatting option to wysiwyg wp_editor
 */
function interactive_tiny_mce_buttons_bottom( $buttons ) {

    if( is_interactive_article() && !in_array( 'styleselect', $buttons )) {
		array_unshift( $buttons, 'styleselect' );
    }

    return $buttons;
}

add_filter( 'mce_buttons_2', 'interactive_tiny_mce_buttons_bottom');


/**
 *  Add custom REST API endpoint for detecting an interactive article
 */
function interactive_add_to_rest_api_data(){

    register_rest_field( 'post',
        'interactive',
        array(
            'get_callback'    => 'int_rest_api_is_interactive',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

/**
 *  Check if post is interactive
 */
function int_rest_api_is_interactive ( $post ) {
	$slug = get_page_template_slug( $post['id'] );
	return $slug == 'interactive-template.php';
}

add_action( 'rest_api_init', 'interactive_add_to_rest_api_data' );


/**
 *  Shortcode for showing the latest interactive articles
 *
 *	For example: [interactive-list max="5"]
 */
function int_shortcode_interactive_list( $atts ) {

    $atts = shortcode_atts( array(
        'type' => 'carousel',
        'max' => 3,
    ), $atts, 'interactive-list' );

    $post_id = get_the_ID();

	$articles = new WP_Query( array(
		'post_type' => array( 'post', 'page', 'interactive_article' ),
		'posts_per_page' => $atts['max'],
		'offset' => 0,
		'ignore_sticky_posts' => true,
		'has_password' => false,
		'post__not_in' => array( $post_id ),
	    'meta_query' => array(
	        array(
	            'key' => 'int_article_sections',
	            'compare' => 'EXISTS'
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

