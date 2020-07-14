<?php

Class Interactive_Options {

	const SLUG = 'interactive';
	const TEXT_DOMAIN = 'interactive-longform-articles';

	/**
	 *	A reference to an instance of this class.
	 */
	private static $instance;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'init_options_page' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 *	Initialize settings page
	 */
	public function init_options_page() {

		// Page
		$page = $this::SLUG;

		// Section
		$section = 'int_display_section';
		add_settings_section( $section, '', '', $page );

		// Field
		add_settings_field( 'int_option_enable_post_type', __( 'Enable interactive articles as a post type', $this::TEXT_DOMAIN ), array( $this, 'display_as_post_type' ), $page, $section );

        add_settings_field( 'int_option_display_downloads', __( 'Display downloads section', $this::TEXT_DOMAIN ), array( $this, 'display_downloads_section' ), $page, $section );

        add_settings_field( 'int_option_allow_iframes', __( 'Allow iframes in text editor', $this::TEXT_DOMAIN ), array( $this, 'allow_iframes_section' ), $page, $section );

		add_settings_field( 'int_option_google_analytics', __( 'Enable Google Analytics event tracking', $this::TEXT_DOMAIN ), array( $this, 'enable_google_analytics' ), $page, $section );

		add_settings_field( 'int_option_tracker_name', __( 'Name of the Google Analytics tracker', $this::TEXT_DOMAIN ), array( $this, 'tracker_name' ), $page, $section );

		add_settings_field( 'int_option_progress_color', __( 'Progress indicator color', $this::TEXT_DOMAIN ), array( $this, 'progress_color' ), $page, $section );

		// Register settings
		register_setting( $page, 'int_option_enable_for_posts' );
		register_setting( $page, 'int_option_enable_for_projects' );
		register_setting( $page, 'int_option_enable_for_casestudies' );
		register_setting( $page, 'int_option_enable_post_type' );
        register_setting( $page, 'int_option_display_downloads' );
        register_setting( $page, 'int_option_allow_iframes' );
		register_setting( $page, 'int_option_google_analytics' );
		register_setting( $page, 'int_option_tracker_name' );
		register_setting( $page, 'int_option_progress_color' );

		// Register settings for specific post types
		$post_types = array_keys( get_post_types() );

		foreach( $post_types as $post_type ) {
			register_setting( $page, 'int_option_enable_for_' . $post_type );
		}
	}

	/**
	 * 	Interactive post type option
	 */
	public function display_as_post_type() {
		$field = get_option( 'int_option_enable_post_type' );
		$checked = empty( $field ) ? '' : 'checked';

		echo '<input type="checkbox" id="int_option_enable_post_type" name="int_option_enable_post_type" value="1" ' . $checked . ' />';
	}

    /**
     *  Downloads option
     */
    public function display_downloads_section() {
        $field = get_option( 'int_option_display_downloads' );
        $checked = empty( $field ) ? '' : 'checked';

        echo '<input type="checkbox" id="int_option_display_downloads" name="int_option_display_downloads" value="1" ' . $checked . ' />';
    }

    /**
     *  Allow iframes
     */
    public function allow_iframes_section() {
        $field = get_option( 'int_option_allow_iframes' );
        $checked = empty( $field ) ? '' : 'checked';

        echo '<input type="checkbox" id="int_option_allow_iframes" name="int_option_allow_iframes" value="1" ' . $checked . ' />';
    }

	/**
	 * 	Google Analytics option
	 */
	public function enable_google_analytics() {
		$field = get_option( 'int_option_google_analytics' );
		$checked = empty( $field ) ? '' : 'checked';

		echo '<input type="checkbox" id="int_option_google_analytics" name="int_option_google_analytics" value="1" ' . $checked . ' />';
	}

	/**
	 * 	Google Analytics Tracker
	 */
	public function tracker_name() {
		$name = sanitize_text_field( get_option( 'int_option_tracker_name' ) );

		echo '<input type="text" id="int_option_tracker_name" name="int_option_tracker_name" value="' . $name . '"  /> (Leave empty if none exists).<br>';
	}

	/**
	 * 	Progress indicator color
	 */
	public function progress_color() {
		$color = sanitize_text_field( get_option( 'int_option_progress_color' ) );
		$color = empty( $color ) ? '#333' : $color;

		echo '<input type="text" id="int_option_progress_color" name="int_option_progress_color" value="' . $color . '"  /> (Shown on sections that have no content)';
	}

	/**
	 * 	Add settings page
	 */
	public function add_options_page() {
		add_options_page(
			__( 'Interactive Longform Article Options', $this::TEXT_DOMAIN ),
			__( 'Interactive Longform Articles', $this::TEXT_DOMAIN ),
			'publish_posts',
			$this::SLUG,
			array( $this, 'display_options_page' )
		);
	}

	/**
	 * 	Settings page markup
	 */
	public function display_options_page() {
	?>
		<div class="wrap">
			<h2><?php _e( 'Interactive Longform Articles', $this::TEXT_DOMAIN ) ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( $this::SLUG ); ?>
				<?php do_settings_sections( $this::SLUG ); ?>
				<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', $this::TEXT_DOMAIN ); ?>" class="button button-primary" />
			</form>
		</div>
	<?php
	}

	/**
	 * Returns a singleton instance for the class
	 *
	 * @static
	 * @return Interactive_Options
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Interactive_Options();
		}

		return self::$instance;
	}
}
