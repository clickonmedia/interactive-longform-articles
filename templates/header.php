<?php
/**
 * Default header template
 *
 * @package understrap
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
	// Site branding
	if( get_theme_mod( 'custom_logo' ) ) {
		$logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
		$brand = '<img src="' . $logo[0] . '" alt="" />';
	} else {
		$brand = '<div class="title">' . get_bloginfo( 'name' ) . '</div>';
	}
?>

<div class="interactive-header">
	<a href="/" class="brand"><?php echo $brand; ?></a>

	<div class="social-share">
		<a class="link ia-icon twitter ia-icon-twitter"
			href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_the_permalink() ); ?>&text=<?php echo urlencode( wp_title() ); ?>"
			target="_blank">
		</a>

		<a class="link ia-icon facebook ia-icon-facebook-official"
			href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_the_permalink() ); ?>"
			target="_blank" >
		</a>
	</div>
</div>