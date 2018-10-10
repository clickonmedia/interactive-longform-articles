
;(function( $ ) {

	var $window = $( window );
	var $document = $( document );
	var $header = $( '.interactive-header' );
	var $section = $( '.interactive-section' );

	// Initial first screen
	var index = Math.floor( $window.scrollTop() / $window.height() );
	var progressIndex = 0;
	var active, breakpoint, prev, next, start;

	var showCurrentBackground = function() {

		breakpoint = $window.scrollTop() + $window.height() / 2;
		prev = (index > 0) ? index - 1 : 0;
		next = index + 1;

		// Detect scroll to next section
		if( $section.eq( next ).length ) {
			if( breakpoint > $section.eq( next ).position().top ) {
				index = next;
			}
		}

		// Detect scroll to previous section
		if( ( breakpoint -  $section.eq( prev ).height() ) < $section.eq( prev ).position().top ) {
			index = prev;
		}

		// Display section background
		if ( index !== active ) {

			active = index;
			$section.addClass( 'transparent' ).eq( index ).removeClass( 'transparent' );

			/*
				GA Event Tracking
				Tracks progress of the article, when a user scrolls to a further slide
			*/
			if( !!ajax_object.event_tracking && typeof ga !== 'undefined' ) {

				if ( index === progressIndex ) {

					var tracker = ajax_object.tracker_name || false;
					var send = tracker ? tracker + '.send' : 'send';
					var set = tracker ? tracker + '.set' : 'set';

					// seconds since page load
					var time = Math.round( (Date.now() - start) / 1000 );

					// increase section index
					progressIndex++;

					// send tracking
					ga( send, 'event', {
						'eventCategory': 'Interactive',
						'eventAction': 'Scroll',
						'eventLabel': progressIndex,
						'eventValue': time
					});

					ga( set, 'nonInteraction', true );
				}
			}
		}
	}

	var showHeader = function() {

		// Fixed after midpoint
		var midpoint = $window.scrollTop() - ( $document.height() / 2 ) > 0;
		$header.toggleClass( 'fixed', midpoint );

		// End zone needs to be slightly earlier for mobile
		var end_zone = $window.width() < 768 ? ($window.height() * 1.25) : ($window.height() * 1.05);

		var end_of_article = $document.height() - $window.scrollTop() < end_zone;
		var start_of_article = $window.scrollTop() < $window.height();

		// Show header at both ends of article
		var show_header = start_of_article || end_of_article;
		$header.toggleClass( 'opaque', show_header );
	}

	var setupBackgroundVideo = function() {
		var screen = $window.width();

		$( '.interactive-background video' ).each( function() {
			var $this = $( this );
			var mobile = screen <= 768;

			// Display animated GIF on mobile
			if( mobile ) {
				$this.addClass('hidden').closest('.interactive-background').addClass('gif').css( 'background-image', 'url(' + $this.data( 'src-xs' ) + ')' );

			// Display appropriate sized video on desktop
			} else {
				var size = ( screen < 1280 ) ? 'src-md' : 'src-xl';
				$this.attr( 'src', $this.data( size ) );
			}
		});
	}

	var setupBackgroundImage = function() {
		var screen = $window.width();

		$( '.interactive-background' ).each( function() {
			var $this = $( this );
			var size;

			if ( $this.data('bg-xl') ) {

				switch( true ) {
					case ( screen < 350 ):
						size = 'bg-xs';
						break;
					case ( screen < 768 ):
						size = 'bg-sm';
						break;
					case ( screen < 992 ):
						size = 'bg-md';
						break;
					default:
						size = 'bg-xl';
				}

				// Display the size-appropriate background image
				$this.css( 'background-image', 'url(' + $this.data( size ) + ')' );
			}
		});
	}

	// Section progress bar
	var setSectionProgress = function() {

		var $current = $section.eq( index );

		if ( $current.find( 'progress' ).length ) {

		    var getMaxValue = function() {
		        return $current.height();
		    }

		    var getProgressValue = function() {
		        return $( window ).scrollTop() - $current.position().top + ($current.height() / 2);
		    }

		    // Check browser support
		    if ( 'max' in document.createElement('progress') ) {
		        $current.find( 'progress' ).attr({ max: getMaxValue(), value: getProgressValue() });
		    }
		}
	}

	$( document ).ready( function() {

		// Set the start time for GA tracking
		start = Date.now();

		$( 'html' ).addClass( 'interactive' );

		// Display scroll icon
		$( '.js-scroll-icon' ).removeClass( 'transparent' );

		// Flexslider - Related articles list
		if ( $( '.flexslider' ).length > 0 ) {

			$( '.flexslider' ).flexslider({
				animation: "slide",
			    itemWidth: 260,
			    itemMargin: 5,
			    startAt: 0
			});
		}

		// Initialize
		showHeader();
		showCurrentBackground();
		setupBackgroundVideo();
		setupBackgroundImage();
		setSectionProgress();

		var throttleBackground = _.throttle( showCurrentBackground, 100 );
		var throttleHeader = _.throttle( showHeader, 100 );
		var throttleProgress = _.throttle( setSectionProgress, 10 );

		// Scroll actions
		$window.on( 'scroll touchmove', function() {
			throttleBackground();
			throttleHeader();
			throttleProgress();
		});

		var throttleResize = _.throttle( function() {
			setupBackgroundVideo();
			setSectionProgress();

			// needed for returning from fullscreen vimeo/youube
			setTimeout( function() {
				showCurrentBackground();
			}, 500);

		}, 750 );

		// Resize actions
		$( window ).resize( function() {
			throttleResize();
		});
	});

})( window.jQuery );
