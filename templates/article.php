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
				// $content = ( $section['background-color'] == 'black' ) ? $section['text-black'] : $section['text-white'];
				// $content = !empty( $section['int_text_default'] ) ? $section['int_text_default'] : '';

				// Define section background
				$background = '';
				$progress = '';
				$color = '';
				// $style = empty( $section['section-style'] ) ? 'default' : $section['section-style'];
				$style = 'default';
				$opacity = '1';
				$align = 'center center';

				/*
					Section type
				*/
				switch( $section['int_section_type'] ) {

					case 'cover':
						// $content = ( $section['background-color'] == 'black' ) ? $section['text-black'] : $section['text-white'];
						$content = $section['int_text_default'];
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
						$content = !empty( $section['int_text_default'] ) ? $section['int_text_default'] : '';
				}

				/*
					Background type
				*/
				switch( $section['int_background_type'] ) {
					case 'image':

						$opacity = round( intval( $section['int_background_opacity'] ) / 100, 2 );

						/*
						// Desktop BG image
						$xl = isset( $section['background-image']['sizes']['interactive-xl'] ) ? $section['background-image']['sizes']['interactive-xl'] : $section['background-image']['large'];

						// Tablet BG image
						$md = $section['background-image']['sizes']['large'];

						// Mobile BG image
						if( empty( $section['background-image-mobile'] ) ) {
							$xs = $section['background-image']['sizes']['large'];
							$sm = $section['background-image']['sizes']['large'];
						} else {
							$xs = isset( $section['background-image-mobile']['sizes']['interactive-sm'] ) ? $section['background-image-mobile']['sizes']['interactive-sm'] : $section['background-image-mobile']['large'];
							$sm = $xs;
						}
						*/

						$xs = empty( $section['int_background_image_mobile'] ) ? $section['int_background_image'] : $section['int_background_image_mobile'];
						$sm = $section['int_background_image'];
						$md = $section['int_background_image'];
						$xl = $section['int_background_image'];

						// Markup
						$background = "<div class='interactive-background' data-bg-xs='" . $xs . "' data-bg-sm='" . $sm . "' data-bg-md='" . $md . "' data-bg-xl='" . $xl . "' style='background-position: " . $align . "; opacity: " . $opacity . "'></div>";
						break;

					case 'color':
						// $color = 'style-' . $section['background-color'];
						$color = 'style-' . $section['int_background_color'];
						$background = "<div class='interactive-background'></div>";
						break;

					case 'video':

						$opacity = round( intval( $section['int_background_opacity'] ) / 100, 2 );

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

					<div class="interactive-section <?php echo $style; ?> <?php echo $color; ?> transparent">
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
		/*
		if( function_exists( 'get_field' ) ) {

			$sections = get_field( 'interactive-section' );

			if ( ! empty( $sections ) ) {

				foreach ( $sections as $section ) {

					$content = '';

					// Define section content
					switch( $section['section-style'] ) {

						case 'cover':
							$content = ( $section['background-color'] == 'black' ) ? $section['text-black'] : $section['text-white'];
							$content .= '<i class="ia-icon ia-icon-keyboard_arrow_down scroll-icon js-scroll-icon transparent"></i>';
							break;

						case 'embed':
							$content = $section['interactive-video-embed'];
							break;

						// Related articles section
						case 'related':

							// Title row
							$content = '<h2 class="title">Related articles</h2>';
							$content .= '<div class="flexslider"><ul class="slides">';

							// Related articles list
							$related = $section['interactive-related'];

							if ( ! empty( $related ) ) {

								foreach ( $related as $rel ) {
									$image = isset($rel['interactive-related-image']) ? $rel['interactive-related-image']['sizes']['large'] : '';
									$title = $rel['interactive-related-title'];
									$link = $rel['interactive-related-link'];

									if ( !empty($image) && !empty($title) && !empty($link) ) {
										$content .= '<li>';

										$content .= '<a href="' . $link['url'] . '" class="flex-item" style="background-image: url(' . $image . ')">
													    <p class="flex-caption">' . $title . '</p>
												  	</a>';

										$content .= '</li>';
									}
								}
							}

							$content .= '</ul></div>';
							break;

						// Downloads section
						case 'downloads':
							$content = '<div class="container-fluid">';
							$content .= '<div class="row justify-content-center"><div class="col-12 col-sm-6">';

							// Downloads title row
							$content .= '<div class="row"><div class="col-12"><h2 class="title">Downloads</h2></div></div>';

							// Downloads list
							$downloads = $section['interactive-download'];

							if ( ! empty( $downloads ) ) {

								foreach ( $downloads as $download ) {
									$file = $download['interactive-download-file'];

									if ( $file ) {
										$content .= '<div class="row file">';
										$content .= '<div class="col-6">' . $file['title'] . '</div>';
										$content .= '<div class="col-6"><a href="'.$file['url'].'" target="_blank">Download</a></div>';
										$content .= '</div>';
									}
								}
							}

							$content .= '</div></div>';
							$content .= '</div>';
							break;

						default:
							$content = ( $section['background-color'] == 'black' ) ? $section['text-black'] : $section['text-white'];
					}

					// Define section background
					$background = '';
					$progress = '';
					$style = empty( $section['section-style'] ) ? 'default' : $section['section-style'];
					$color = '';

					switch( $section['background-type'] ) {
						case 'image':

							$opacity = round( intval( $section['background-opacity'] ) / 100, 2 );

							// Desktop BG image
							$xl = isset( $section['background-image']['sizes']['interactive-xl'] ) ? $section['background-image']['sizes']['interactive-xl'] : $section['background-image']['large'];

							// Tablet BG image
							$md = $section['background-image']['sizes']['large'];

							// Mobile BG image
							if( empty( $section['background-image-mobile'] ) ) {
								$xs = $section['background-image']['sizes']['large'];
								$sm = $section['background-image']['sizes']['large'];
							} else {
								$xs = isset( $section['background-image-mobile']['sizes']['interactive-sm'] ) ? $section['background-image-mobile']['sizes']['interactive-sm'] : $section['background-image-mobile']['large'];
								$sm = $xs;
							}

							// Markup
							$background = "<div class='interactive-background' data-bg-xs='" . $xs . "' data-bg-sm='" . $sm . "' data-bg-md='" . $md . "' data-bg-xl='" . $xl . "' style='background-position: " . $section['background-align'] . "; opacity: " . $opacity . "'></div>";
							break;

						case 'color':
							$color = 'style-' . $section['background-color'];
							$background = "<div class='interactive-background'></div>";
							break;

						case 'video':

							$opacity = round( intval( $section['background-opacity'] ) / 100, 2 );

							$background = "<div class='interactive-background' style='background-position: " . $section['background-align'] . "; opacity: " . $opacity . "'>";

							$poster = $section['background-poster']['sizes']['large'];

							$background .= '<video poster="' . $poster . '" data-src-xs="' . $section['background-gif'] . '" data-src-md="' . $section['background-video-md'] . '" data-src-xl="' . $section['background-video-xl'] . '" preload="auto" width="16" height="9" autoplay loop muted></video>';

							$background .= "</div>";
							break;
					}

					if( empty($content) ) {
						$progress = '<progress value="0"></progress>';
					}

					?>

						<div class="interactive-section <?php echo $style; ?> <?php echo $color; ?> transparent">
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
		}
		*/
	?>

<?php
	// Get header template
	if( file_exists( get_stylesheet_directory() . '/interactive-longform-articles/footer.php' ) ) {
		include( get_stylesheet_directory() . '/interactive-longform-articles/footer.php' );
	} else {
		include('footer.php');
	}
?>
