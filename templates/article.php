<?php
/**
 * The template for Interactive Article
 *
 * @package understrap
 */

	// Get header template
	$custom_header = get_stylesheet_directory() . '/interactive/header.php';

	if( file_exists( $custom_header ) ) {
		include( $custom_header );
	} else {
		include( 'header.php' );
	}
?>

<?php

    /*
        Interactive articles data
    */
    $data = [
        'sections' => [],
    	'brand' => array(
	        'name' => get_bloginfo( 'name' )
	    ),
    ];

    if ( get_theme_mod( 'custom_logo' ) ) {
        $logo = esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ) );
        $data['brand']['logo'] = $logo[0];
    }
?>


<?php

	// Add interactive article sections to the HTML output
	$sections = get_post_meta( get_the_ID(), 'int_article_sections', true );

	if ( ! empty( $sections ) ) {

		foreach ( $sections as $section ) {

			// Section content
			$content = '';

			// Section background
			$background = [];
			$progress = '';
			$color = '';
			$type = empty( $section['int_section_type'] ) ? 'default' : $section['int_section_type'];

			// Section type
			switch ( $section['int_section_type'] ) {

				// Cover page
				case 'cover':
					$content = ( $section['int_background_color'] == 'black' ) ? $section['int_text_black'] : $section['int_text_white'];
					$content .= '<i class="ia-icon ia-icon-keyboard_arrow_down scroll-icon js-scroll-icon transparent"></i>';
					break;

				// oEmbed
				case 'embed':
					$content = wp_oembed_get( $section['int_video_embed'], array( 'width' => 640, 'height' => 360 ) );
					break;

				// Downloads
				case 'downloads':
					$content = '<div class="interactive-container">';
					$content .= '<div class="interactive-row justify-content-center"><div class="interactive-col-100 interactive-col-sm-50">';

					// Downloads title row
					$content .= '<div class="interactive-row"><div class="interactive-col-100"><h2 class="title">Downloads</h2></div></div>';

					// Downloads list
					$downloads = $section['int_downloads'];

					if ( ! empty( $downloads ) ) {
						foreach ( $downloads as $att_id => $att_url ) {
							if ( $att_url ) {
								$content .= '<div class="interactive-row file">';
								$content .= '<div class="interactive-col-50">' . get_the_title( $att_id ) . '</div>';
								$content .= '<div class="interactive-col-50"><a href="' . $att_url . '" target="_blank">Download</a></div>';
								$content .= '</div>';
							}
						}
					}

					$content .= '</div></div>';
					$content .= '</div>';
					break;

				default:
					$content = ( $section['int_background_color'] == 'black' ) ? $section['int_text_black'] : $section['int_text_white'];
			}

			// Background type
			switch ( $section['int_background_type'] ) {

				// Image background
				case 'image':

					$opacity = round( intval( $section['int_background_opacity'] ) / 100, 2 );
					$align = $section['int_background_align'];

					// Desktop BG image
					$xl = esc_url( wp_get_attachment_image_url( $section['int_background_image_id'], 'interactive-xl' ) );

					// Tablet BG image
					$md = esc_url( wp_get_attachment_image_url( $section['int_background_image_id'], 'large' ) );

					// Mobile BG image
					if( empty( $section['int_background_image_mobile_id'] ) ) {
						$xs = $md;
						$sm = $md;
					} else {
						$xs = esc_url( wp_get_attachment_image_url( $section['int_background_image_mobile_id'], 'interactive-sm' ) );
						$sm = $xs;
					}

					$background = [
						'xs' => $xs,
						'sm' => $sm,
						'md' => $md,
						'xl' => $xl,
						'align' => $align,
						'opacity' => $opacity
					];

					break;

				// Color background
				case 'color':
					$color = 'style-' . $section['int_background_color'];
					break;

				// Video background
				case 'video':
					$opacity = round( intval( $section['int_background_opacity'] ) / 100, 2 );
					$align = $section['int_background_align'];

					$poster = empty( $section['int_background_video_poster'] ) ? '' : $section['int_background_video_poster'];
					$video = empty( $section['int_background_video'] ) ? '' : $section['int_background_video'];
					$mobile = empty( $section['int_background_video_mobile'] ) ? '' : $section['int_background_video_mobile'];

					$background = [
						'poster' => $poster,
						'mobile' => $mobile,
						'video' => $video,
						'align' => $align,
						'opacity' => $opacity
					];

					break;
			}

			// Display progress indicator on pages without content
			if( empty( $content ) ) {
				$progress = '<progress value="0"></progress>';
			}

			// Apply the_content filters for the section content
			$content = apply_filters( 'the_content', $content );

            $section['type'] = $type;
            $section['color'] = $color;
            $section['progress'] = $progress;
            $section['background'] = $background;
            $section['content'] = $content;

            array_push( $data['sections'], $section );
		}
	}
?>

<script>
    var interactive_article_data = <?php echo json_encode( $data ); ?>;
</script>


<?php
	// Get footer template
	$custom_footer = get_stylesheet_directory() . '/interactive/footer.php';

	if( file_exists( $custom_footer ) ) {
		include( $custom_footer );
	} else {
		include( 'footer.php' );
	}
?>
