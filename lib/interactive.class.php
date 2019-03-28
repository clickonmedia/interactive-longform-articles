<?php
class Interactive {

	const POST_TYPE = 'interactive_article';
	const TEXT_DOMAIN = 'interactive-longform-articles';
	const SHORTCODE = 'interactive-list';
	const SLUG    = 'interactive';

	/**
	 * Singleton instance
	 */
	private static $instance;

	/**
	 * Plugin folder path
	 */
	private $path;

	/**
	 * Plugin folder URL
	 */
	private $url;

	public function  __construct() {
		$this->path = trailingslashit( dirname( dirname( __FILE__ )         ) );
		$this->url  = trailingslashit( dirname( plugins_url( '', __FILE__ ) ) );

		$this->setup_metaboxes();
		$this->settings();
		$this->page_templater();
		$this->enqueue_scripts();
		$this->register_shortcode();
		$this->setup_frontend();
		$this->setup_backend();

		add_action( 'init', array( $this, 'create_article_post_type' ) );
		add_action( 'after_setup_theme', array( $this, 'interactive_image_sizes' ) );

		register_activation_hook( __FILE__, array( $this, 'longform_rewrite_flush' ) );
	}

	/**
	 * Setup frontend features
	 */
	public function setup_frontend() {
		add_action( 'rest_api_init', array( $this, 'setup_rest_api_data' ) );
		add_filter( 'single_template', array( $this, 'article_custom_template' ) );
	}

	/**
	 * Setup backend features
	 */
	public function setup_backend() {
		add_filter( 'mce_buttons_2', array( $this, 'tiny_mce_buttons_bottom') );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Utility function for checking when the current post is interactive
	 */
	public function is_interactive_article() {
		global $post;

		// Page template
		if ( 'interactive-template.php' == get_page_template_slug( $post->ID ) ) {
			return true;
		}

		// Custom post type
		if ( $this::POST_TYPE == $post->post_type ) {
			return true;
		}

	    return false;
	}

	/**
	 * Utility function for checking when the post uses the interactive-list shortcode
	 */
	public function has_shortcode() {
		global $post;

		$sections = get_post_meta( $post->ID, 'int_article_sections', true );

	    return has_shortcode( serialize( $sections ), $this::SHORTCODE );
	}

	/**
	 * Enqueue frontend scripts and styles for the interactive article
	 */
	public function enqueue_frontend_scripts( $hook ) {

	    if ( $this->is_interactive_article() ) {

			$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );

	    	// Enqueue styles
	    	if( $this->has_shortcode() ) {
	    		wp_enqueue_style( 'flexslider', $this->url . '/css/flexslider.css', false, $plugin_data['Version'] );
	    	}
	    	wp_enqueue_style( 'interactive-longform-styles', $this->url . '/css/style.min.css', false, $plugin_data['Version'] );

		    // Enqueue scripts
			wp_enqueue_script( 'jquery', $this->url . '/js/jquery-3.3.1.min.js', array(), false, true );
		    wp_enqueue_script( 'lodash', $this->url . '/js/lodash.min.js', array(), false, true );

		    if( $this->has_shortcode() ) {
	    		wp_enqueue_script( 'flexslider', $this->url . '/js/jquery.flexslider-min.js', array(), false, true );
	    	}
			wp_enqueue_script( 'interactive-longform-script', $this->url . '/js/main.js', array( 'jquery', 'lodash' ), false, true );

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

	/**
	 * Enqueue JS scripts for WP admin
	 */
	public function enqueue_admin_scripts($hook) {

	    if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
	    	wp_enqueue_script( 'interactive-admin-script', $this->url . 'js/admin.js' );
	    }

	    return;
	}

	/**
	 * Page templater to enable selecting the "interactive article" page template
	 */
	public function page_templater() {
		require_once __DIR__ . '/page-templater.php';
		Page_Templater::instance();
	}

	/**
	 * 	Setup CMB2 metaboxes for storing section data
	 *
	 * 	https://github.com/CMB2/CMB2/wiki
	 */
	public function setup_metaboxes() {
		require_once $this->path . '/lib/cmb2/init.php';


		add_action(
			'cmb2_init',
			array( $this, 'cmb2_metabox_register')
		);

		add_action(
			'cmb2_render_range_input',
			array( $this, 'cmb2_render_range_input_field_type' ),
			10,
			5
		);

		add_filter(
			'cmb2_sanitize_range_input',
			array( $this, 'cmb2_sanitize_range_input_callback' ),
			10,
			2
		);
	}

	/**
	 * 	Adds a custom CMB2 field type for range input
	 *
	 * 	@param  object $field             The CMB2_Field type object.
	 * 	@param  string $value             The saved (and escaped) value.
	 * 	@param  int    $object_id         The current post ID.
	 * 	@param  string $object_type       The current object type.
	 * 	@param  object $field_type_object The CMB2_Types object.
	 *
	 * 	@return void
	 */
	public function cmb2_render_range_input_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

		$value = empty( $escaped_value ) ? '100' : $escaped_value;

		$html = '<input type="range" id="' . $field->args['_id'] . '" name="' . $field->args['_name'] . '" min="0" max="100" value="' . $value . '" />';
		$html .= $field_type_object->_desc( true );

		echo $html;
	}

	/**
	 * Use custom template for interactive_article custom post type
	 */
	public function article_custom_template( $single ) {

	    global $wp_query, $post;

	    // Checks for single template by post type
	    if ( $this::POST_TYPE == $post->post_type ) {
	        if ( file_exists( $this->path . '/interactive-template.php' ) ) {
	            return $this->path . '/interactive-template.php';
	        }
	    }

	    return $single;

	}

	/**
	 * Add custom REST API endpoint for detecting an interactive article
	 */
	public function setup_rest_api_data(){

	    register_rest_field( 'post',
	        $this::SLUG,
	        array(
	            'get_callback'    => array( $this, 'rest_api_is_interactive' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	}

	/**
	 * Add interactive article custom image sizes
	 */
	public function interactive_image_sizes() {
		add_image_size( 'interactive-sm', 1280, 2272, false );
		add_image_size( 'interactive-xl', 2560, 1440, false );
	}


	/**
	 *  Check if post is interactive
	 */
	public function rest_api_is_interactive ( $post ) {
		return 'interactive-template.php' == get_page_template_slug( $post['id'] );
	}

	/**
	 * Create CMB2 metabox and related fields
	 */
	public function cmb2_metabox_register() {

		$prefix = 'int_';

	    $cmb = new_cmb2_box( array(
	        'id'            => $prefix . 'article_cmb_box',
	        'title'         => __( 'Repeatable Section', $this::TEXT_DOMAIN ),
	        'object_types' => array( 'post', 'page', $this::POST_TYPE ),
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
	            'group_title'   => __( 'Section', $this::TEXT_DOMAIN ) . ' {#}', // {#} gets replaced by row number
	            'add_button'    => __( 'Add another Section', $this::TEXT_DOMAIN ),
	            'remove_button' => __( 'Remove Section', $this::TEXT_DOMAIN ),
	            'sortable'      => true, // beta
	        ),
	    ) );

	    // Section type
		$section_types = array(
			'cover' 	=> __( 'Cover page', $this::TEXT_DOMAIN ),
			'large'   	=> __( 'Lead text', $this::TEXT_DOMAIN ),
			'default'   => __( 'Body text', $this::TEXT_DOMAIN ),
			'embed'     => __( 'Video embed', $this::TEXT_DOMAIN ),
		);

		// Add downloads if the option is enabled in settings
		if ( ! empty( get_option( 'int_option_display_downloads' ) ) ) {
			$section_types['downloads'] = __( 'Downloads', $this::TEXT_DOMAIN );
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
				'color' => __( 'Color', $this::TEXT_DOMAIN ),
				'image' => __( 'Image', $this::TEXT_DOMAIN ),
				'video' => __( 'Video', $this::TEXT_DOMAIN ),
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
				'black' => __( 'Black', $this::TEXT_DOMAIN ),
				'white'     => __( 'White', $this::TEXT_DOMAIN ),
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
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Add Image' // Default text: "Add or Upload File"
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
			'name'    => __( 'Screenshot for background video', $this::TEXT_DOMAIN ),
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
			'name'             => __( 'Background color', $this::TEXT_DOMAIN ),
			'id'               => $prefix . 'background_color',
			'classes' 		   => 'int-background-color',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => array(
				'black' => __( 'Black', $this::TEXT_DOMAIN ),
				'white'     => __( 'White', $this::TEXT_DOMAIN ),
			),
			'default' => 'black',
			'show_on_cb' => true,
		));

	    // Opacity
		$cmb->add_group_field( $group_sections, array(
			'name'             => __( 'Brightness', $this::TEXT_DOMAIN ),
			'id'               => $prefix . 'background_opacity',
			'classes' 		   => 'int-background-opacity',
			'type'             => 'range_input',
			'desc'			   => '',
			'show_option_none' => false,
			'show_on_cb' => true,
		));

	    // Background alignment
		$cmb->add_group_field( $group_sections, array(
			'name'             => __( 'Background alignment', $this::TEXT_DOMAIN ),
			'id'               => $prefix . 'background_align',
			'classes' 		   => 'int-background-align',
			'type'             => 'radio',
			'show_option_none' => false,
			'options' => array(
				'left center'	=>  __( 'left center', $this::TEXT_DOMAIN ),
				'left top'	=>  __( 'left top', $this::TEXT_DOMAIN ),
				'left bottom'	=>  __( 'left bottom', $this::TEXT_DOMAIN ),
				'center center'	=>  __( 'center center', $this::TEXT_DOMAIN ),
				'center top'	=>  __( 'center top', $this::TEXT_DOMAIN ),
				'center bottom'	=>  __( 'center bottom', $this::TEXT_DOMAIN ),
				'right center'	=>  __( 'right center', $this::TEXT_DOMAIN ),
				'right top'	=>  __( 'right top', $this::TEXT_DOMAIN ),
				'right bottom'	=>  __( 'right bottom', $this::TEXT_DOMAIN ),
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

		$editor_css_black = $this->url . '/css/editor-style.css' . ', ' . $this->url . '/css/editor-black.css';

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
			    	'block_formats' => 'Paragraph=p; Title=h2;',
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
		$editor_css_white = $this->url . '/css/editor-style.css' . ', ' . $this->url . '/css/editor-white.css';

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
		if ( ! empty( get_option( 'int_option_display_downloads' ) ) ) {

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

	/**
	 * Sanitize the selected value.
	 */
	public function cmb2_sanitize_range_input_callback( $override_value, $value ) {

		if ( ! is_numeric( $value ) ) {
			return '100';
		}

		return;
	}

	/**
	 * Add settings page
	 */
	public function settings() {
		require_once __DIR__ . '/interactive-options.php';
		$options = Interactive_Options::instance();
	}

	/**
	 * Interactive article list shortcode
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function display_shortcode( $atts ) {

	    $atts = shortcode_atts( array(
	        'type' => 'carousel',
	        'max' => 3,
	    ), $atts, $this::SHORTCODE );

	    $post_id = get_the_ID();

		$articles = new WP_Query( array(
			'post_type' => array( 'post', 'page', $this::POST_TYPE ),
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

	/**
	 * Get the today's day name depending on the WP setting.
	 * To adjust your timezone go to Settings->General
	 *
	 * @return array [day_number int]day_name string
	 */
	public function get_day_using_timezone() {

		$timestamp = $this->get_timestamp_using_timezone();

		$arr = array( strtolower( gmdate( 'w', $timestamp ) ) => ucwords( date_i18n( 'l', $timestamp ) ) );

		return $arr;
	}

	/**
	 * Get the current timestamp in the timezone set up in Settings->General
	 * @return int
	 */
	public function get_timestamp_using_timezone() {

		$timezone_string = get_option( 'timezone_string' );

		if ( ! empty( $timezone_string ) ) {
			$zone      = new DateTimeZone( $timezone_string );
			$datetime  = new DateTime( 'now', $zone );
			$timestamp = time() + $datetime->getOffset();
		} else {
			$offset    = get_option( 'gmt_offset' );
			$offset    = $offset * HOUR_IN_SECONDS;
			$timestamp = time() + $offset;
		}

		return $timestamp;
	}

	/**
	 * Add interactive article post type
	 */
	public function create_article_post_type() {

		if( ! empty( get_option('int_option_enable_post_type') ) ) {

			register_post_type( $this::POST_TYPE,
				array(
					'labels' => array(
						'name' => __( 'Interactive' ),
						'singular_name' => __( 'Interactive Longform Article' )
					),
					'public' => true,
					'has_archive' => true,
					'menu_icon' => 'dashicons-admin-page',
					'rewrite' => array(
						'slug' => $this::SLUG
					),
					'supports' => array('title', 'thumbnail'),
				)
			);
		}
	}

	/**
	 * 	Registers the shortcodes
	 */
	public function register_shortcode() {
		add_shortcode( $this::SHORTCODE, array( $this, 'display_shortcode' ) );
	}

	/**
	 * 	Adds styleselect formatting option to wysiwyg wp_editor
	 */
	public function tiny_mce_buttons_bottom( $buttons ) {

	    if( $this->is_interactive_article() && ! in_array( 'styleselect', $buttons )) {
			array_unshift( $buttons, 'styleselect' );
	    }

	    return $buttons;
	}

	/**
	 * 	Rewrite flush
	 */
	public function longform_rewrite_flush() {
	    // First, we "add" the custom post type via the above written function.
	    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
	    // They are only referenced in the post_type column with a post entry,
	    // when you add a post of this CPT.
	    $this->create_article_post_type();

	    // ATTENTION: This is *only* done during plugin activation hook in this example!
	    // You should *NEVER EVER* do this on every page load!!
	    flush_rewrite_rules();
	}

	/**
	 * Returns a singleton instance for the class
	 *
	 * @static
	 * @return Interactive
	 */
	public static function instance() {

		if ( !isset( self::$instance ) ) {
			self::$instance = new Interactive();
		}

		return self::$instance;
	}
}
