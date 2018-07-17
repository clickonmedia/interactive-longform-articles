<?php

/*
	Settings page
*/
function int_longform_options_page() {
?>
	<div class="wrap">
		<h2><?php _e( 'Interactive Longform Articles', 'interactive-longform-articles' ) ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'interactive' ); ?>
			<?php do_settings_sections( 'interactive' ); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', 'interactive-longform-articles' ); ?>" class="button button-primary" />
		</form>
	</div>
<?php
}

function int_admin_add_page() {
	add_options_page(
		__( 'Interactive Longform Article Options', 'interactive-longform-articles' ),
		__( 'Interactive Longform Articles', 'interactive-longform-articles' ),
		'publish_posts',
		'interactive',
		'int_longform_options_page'
	);
}

add_action( 'admin_menu', 'int_admin_add_page' );

/*
	Settings sections and fields
*/
function interactive_admin_init(){

	// Page
	$page = 'interactive';

	// Section
	$section = 'int_display_section';
	add_settings_section( $section, '', '', $page );

	// Field
	add_settings_field( 'int_option_enable_for_post_types', __( 'Enable for post types:', 'interactive-longform-articles' ), 'int_enable_for_post_types', $page, $section );

	add_settings_field( 'int_option_enable_post_type', __( 'Enable interactive articles as a post type', 'interactive-longform-articles' ), 'int_display_as_post_type', $page, $section );

	add_settings_field( 'int_option_display_downloads', __( 'Display downloads section', 'interactive-longform-articles' ), 'int_display_downloads_section', $page, $section );

	add_settings_field( 'int_option_google_analytics', __( 'Enable Google Analytics event tracking', 'interactive-longform-articles' ), 'int_enable_google_analytics', $page, $section );

	add_settings_field( 'int_option_tracker_name', __( 'Name of the Google Analytics tracker', 'interactive-longform-articles' ), 'int_tracker_name', $page, $section );

	// Register settings
	register_setting( $page, 'int_option_enable_for_posts' );
	register_setting( $page, 'int_option_enable_for_projects' );
	register_setting( $page, 'int_option_enable_for_casestudies' );
	register_setting( $page, 'int_option_enable_post_type' );
	register_setting( $page, 'int_option_display_downloads' );
	register_setting( $page, 'int_option_google_analytics' );
	register_setting( $page, 'int_option_tracker_name' );

	// Register settings for specific post types
	$post_types = array_keys( get_post_types() );

	foreach( $post_types as $post_type ) {
		register_setting( $page, 'int_option_enable_for_' . $post_type );
	}
}
add_action( 'admin_init', 'interactive_admin_init' );

/*
	Post type selection
*/
function int_enable_for_post_types() {

	$post_types = array_keys( get_post_types() );

	foreach( $post_types as $post_type ) {

		$option = 'int_option_enable_for_' . $post_type;

		$field = get_option( $option );
		$checked = empty( $field ) ? '' : 'checked';

		echo '<input type="checkbox" id="' . $option . '" name="' . $option . '" value="1" ' . $checked . ' /> ' . $post_type . '<br>';
	}
}

/*
	Interactive post type
*/
function int_display_as_post_type() {
	$field = get_option('int_option_enable_post_type');
	$checked = empty( $field ) ? '' : 'checked';

	echo '<input type="checkbox" id="int_option_enable_post_type" name="int_option_enable_post_type" value="1" ' . $checked . ' />';
}

/*
	Downloads
*/
function int_display_downloads_section() {
	$field = get_option('int_option_display_downloads');
	$checked = empty( $field ) ? '' : 'checked';

	echo '<input type="checkbox" id="int_option_display_downloads" name="int_option_display_downloads" value="1" ' . $checked . ' />';
}

/*
	Google Analytics
*/
function int_enable_google_analytics() {
	$field = get_option('int_option_google_analytics');
	$checked = empty( $field ) ? '' : 'checked';

	echo '<input type="checkbox" id="int_option_google_analytics" name="int_option_google_analytics" value="1" ' . $checked . ' />';
}

/*
	Google Analytics Tracker
*/
function int_tracker_name() {
	$name = sanitize_text_field( get_option('int_option_tracker_name') );

	echo '<input type="text" id="int_option_tracker_name" name="int_option_tracker_name" value="' . $name . '"  /> (Leave empty if none exists).<br>';
}

