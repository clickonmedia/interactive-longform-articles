<?php
/**
 * The template for Interactive Article
 *
 * @package understrap
 */
	// Get header template
	if( file_exists( get_stylesheet_directory() . '/interactive-longform-articles/header.php' ) ) {
		include( get_stylesheet_directory() . '/interactive-longform-articles/header.php' );
	} else {
		include( 'header.php' );
	}
?>

	<?php

		$sections = get_post_meta( get_the_ID(), 'int_article_sections', true );

		if ( ! empty( $sections ) ) {

			foreach ( $sections as $section ) {

				// Define section content
				$content = '';

				// Define section background
				$background = '';
				$progress = '';
				$color = '';
				$type = empty( $section['int_section_type'] ) ? 'default' : $section['int_section_type'];

				/*
					Section type
				*/
				switch( $section['int_section_type'] ) {

					case 'cover':
						$content = ( $section['int_background_color'] == 'black' ) ? $section['int_text_black'] : $section['int_text_white'];
						$content .= '<i class="ia-icon ia-icon-keyboard_arrow_down scroll-icon js-scroll-icon transparent"></i>';
						break;

					case 'embed':
						$content = wp_oembed_get( $section['int_video_embed'], array( 'width' => 640, 'height' => 360 ) );
						break;

					// Downloads section
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

				/*
					Background type
				*/
				switch( $section['int_background_type'] ) {
					case 'image':

						$opacity = round( intval( $section['int_background_opacity'] ) / 100, 2 );
						$align = $section['int_background_align'];

						// Desktop BG image
						$xl = wp_get_attachment_image_url( $section['int_background_image_id'], 'interactive-xl' );

						// Tablet BG image
						$md = wp_get_attachment_image_url( $section['int_background_image_id'], 'large' );

						// Mobile BG image
						if( empty( $section['int_background_image_mobile_id'] ) ) {
							$xs = $md;
							$sm = $md;
						} else {
							$xs = wp_get_attachment_image_url( $section['int_background_image_mobile_id'], 'interactive-sm' );
							$sm = $xs;
						}

						// Markup
						$background = "<div class='interactive-background' data-bg-xs='" . $xs . "' data-bg-sm='" . $sm . "' data-bg-md='" . $md . "' data-bg-xl='" . $xl . "' style='background-position: " . $align . "; opacity: " . $opacity . "'></div>";
						break;

					case 'color':
						$color = 'style-' . $section['int_background_color'];
						$background = "<div class='interactive-background'></div>";
						break;

					case 'video':
						$opacity = round( intval( $section['int_background_opacity'] ) / 100, 2 );
						$align = $section['int_background_align'];

						$background = "<div class='interactive-background' style='background-position: " . $align . "; opacity: " . $opacity . "'>";

						$poster = empty( $section['int_background_video_poster'] ) ? '' : $section['int_background_video_poster'];
						$video = empty( $section['int_background_video'] ) ? '' : $section['int_background_video'];
						$mobile = empty( $section['int_background_video_mobile'] ) ? '' : $section['int_background_video_mobile'];

						$background .= '<video poster="' . $poster . '" data-src-xs="' . $mobile . '" data-src-md="' . $video . '" data-src-xl="' . $video . '" preload="auto" width="16" height="9" autoplay loop muted></video>';

						$background .= "</div>";
						break;
				}

				if( empty( $content ) ) {
					$progress = '<progress value="0"></progress>';
				}

				?>

					<div class="interactive-section <?php echo $type; ?> <?php echo $color; ?> transparent">
						<?php echo $progress; ?>
						<?php echo $background; ?>
						<div class="interactive-text">
							<div class="content">
								<?php echo $content; ?>
							</div>
						</div>
					</div>

				<?php
			}
		}
	?>

<?php
	// Get header template
	if( file_exists( get_stylesheet_directory() . '/interactive-longform-articles/footer.php' ) ) {
		include( get_stylesheet_directory() . '/interactive-longform-articles/footer.php' );
	} else {
		include('footer.php');
	}
?>
