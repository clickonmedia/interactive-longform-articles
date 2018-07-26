<?php

/**
 * CMB2 Range input field type
 * @package CMB2 Range Input Field Type
 */
/**
 * Adds a custom field type for range input
 * @param  object $field             The CMB2_Field type object.
 * @param  string $value             The saved (and escaped) value.
 * @param  int    $object_id         The current post ID.
 * @param  string $object_type       The current object type.
 * @param  object $field_type_object The CMB2_Types object.
 * @return void
 */
function cmb2_render_range_input_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$value = empty( $escaped_value ) ? '100' : $escaped_value;

	$html = '<input type="range" id="' . $field->args['_id'] . '" name="' . $field->args['_name'] . '" min="0" max="100" value="' . $value . '" />';
	$html .= $field_type_object->_desc( true );

	echo $html; // WPCS: XSS ok.
}
add_action( 'cmb2_render_range_input', 'cmb2_render_range_input_field_type', 10, 5 );

/**
 * Sanitize the selected value.
 */
function cmb2_sanitize_range_input_callback( $override_value, $value ) {

	if ( ! is_numeric( $value ) ) {
		return '100';
	}

	return;
}
add_filter( 'cmb2_sanitize_range_input', 'cmb2_sanitize_range_input_callback', 10, 2 );



