/* eslint-disable no-unused-vars, no-undef */
const _functions = {};

let winW, winH, winScr, isTouchScreen, isMac, isIE;

jQuery( function( $ ) {
	'use strict';

	/* function on page ready */
	isTouchScreen = navigator.userAgent.match( /Android/i ) || navigator.userAgent.match( /webOS/i ) || navigator.userAgent.match( /iPhone/i ) || navigator.userAgent.match( /iPad/i ) || navigator.userAgent.match( /iPod/i );
	isMac = navigator.platform.toUpperCase().indexOf( 'MAC' ) >= 0;
	isIE = /MSIE 9/i.test( navigator.userAgent ) || /rv:11.0/i.test( navigator.userAgent ) || /MSIE 10/i.test( navigator.userAgent );

	const $body = $( 'body' );

	$body.addClass( 'loaded' );

	if ( isTouchScreen ) {
		$( 'html' ).addClass( 'touch-screen' );
	}
	if ( isMac ) {
		$body.addClass( 'mac' );
	}
	if ( isIE ) {
		$body.addClass( 'ie' );
	}

	_functions.pageCalculations = function() {
		winW = $( window ).width();
		winH = $( window ).height();
	};

	_functions.pageCalculations();

	//images preload
	_functions.imagesLazyLoad = function() {
		/* images load */
		$( 'img[data-i-src]:not(.imgLoaded)' ).each( function( i, el ) {
			let loadImage = new Image();
			loadImage.src = $( el ).data( 'i-src' );

			loadImage.onload = function() {
				$( el ).attr( {
					src: $( el ).data( 'i-src' ),
				} ).addClass( 'imgLoaded' );
			};
			loadImage = null;
		} );

		$( 'iframe[data-i-src]:not(.imgLoaded)' ).each( function( i, el ) {
			$( el ).attr( {
				src: $( el ).data( 'i-src' ),
			} ).addClass( 'imgLoaded' );
		} );

		$( '[data-bg]:not(.bgLoaded)' ).each( function( i, el ) {
			let loadImage = new Image();
			loadImage.src = $( el ).data( 'bg' );

			loadImage.onload = function() {
				$( el )
					.css( {
						'background-image': 'url(' + $( el ).data( 'bg' ) + ')',
					} )
					.addClass( 'bgLoaded' );
			};
			loadImage = null;
		} );
	};

	//images preload
	setTimeout( function() {
		_functions.imagesLazyLoad();
	}, 100 );

	// _functions.pageScroll = function (current, headerHeight) {
	//   $('html, body').animate({
	//     scrollTop: current.offset().top - headerHeight
	//   }, 700);
	// }

	/* function on page scroll */
	$( window ).scroll( function() {
		_functions.scrollCall();
	} );

	let prevScroll = 0;
	_functions.scrollCall = function() {
		winScr = $( window ).scrollTop();
		if ( winScr > 10 ) {
			$( 'header' ).addClass( 'scrolled' );
		} else if ( winScr < 10 ) {
			$( 'header' ).removeClass( 'scrolled' );
			//TODO: what's the purpose of this?
			prevScroll; // eslint-disable-line no-unused-expressions
		}

		//show-hide header on scroll
		if ( winScr > prevScroll ) {
			$( 'header' ).addClass( 'hide-top' );
		} else {
			$( 'header' ).removeClass( 'hide-top' );
		}
		prevScroll = winScr;

		if ( winScr <= 10 ) {
			$( 'header' ).removeClass( 'hide-top' );
			prevScroll = 0;
		}

		if ( $( 'header' ).hasClass( 'hide-top' ) ) {
			$( '.sidebar' ).addClass( 'top' );
		} else {
			$( '.sidebar' ).removeClass( 'top' );
		}
	};

	setTimeout( _functions.scrollCall, 0 );

	/* function on page resize */
	_functions.resizeCall = function() {
		setTimeout( function() {
			_functions.pageCalculations();
		}, 100 );

		setTimeout( function() {
			_functions.heightImg();
		}, 200 );
	};

	if ( ! isTouchScreen ) {
		$( window ).resize( function() {
			_functions.resizeCall();
		} );
	} else {
		window.addEventListener( 'orientationchange', function() {
			_functions.resizeCall();
		}, false );
	}

	$( window ).resize( function() {
		_functions.heightImg();
	} );

	/* function on page resize */
	_functions.heightImg = function() {
		if ( $( '.about-swiper' ).length ) {
			$( '.about-swiper' ).each( function() {
				const heightSlide = parseInt( $( this ).find( '.swiper-slide .about-img' ).outerHeight() );
				$( this ).css( '--slider-height', heightSlide + 'px' );
			} );
		}
	};

	setTimeout( function() {
		_functions.heightImg();
	}, 200 );

	//popup
	let popupTop = 0;
	_functions.removeScroll = function() {
		popupTop = $( window ).scrollTop();
		$( 'html' ).css( {
			// "position": "fixed",
			top: -$( window ).scrollTop(),
			width: '100%',
		} ).addClass( 'overflow-hidden' );
	};
	_functions.addScroll = function() {
		$( 'html' ).css( {
			// "position": "static"
		} ).removeClass( 'overflow-hidden' );
		window.scroll( 0, popupTop );
	};

	_functions.openPopup = function( popup ) {
		$( '.popup-content' ).removeClass( 'active' );
		$( popup + ', .popup-wrapper' ).addClass( 'active' );
		_functions.removeScroll();
	};

	_functions.videoPopup = function( src ) {
		$( '#video-popup .embed-responsive' ).html( '<iframe src="' + src + '"></iframe>' );
		_functions.openPopup( '#video-popup' );
	};

	_functions.closePopup = function() {
		$( '.popup-wrapper, .popup-content' ).removeClass( 'active' );

		// $('.popup-iframe').html('');
		$( '#video-popup iframe' ).remove();

		_functions.addScroll();
	};

	_functions.textPopup = function( title, description ) {
		$( '#text-popup .text-popup-title' ).html( title );
		$( '#text-popup .text-popup-description' ).html( description );
		_functions.openPopup( '#text-popup' );
	};

	$( document ).on( 'click', '.video-popup', function( e ) {
		e.preventDefault();
		_functions.videoPopup( $( this ).data( 'src' ) );
	} );

	$( document ).on( 'click', '.open-popup', function( e ) {
		e.preventDefault();
		_functions.openPopup( '.popup-content[data-rel="' + $( this ).data( 'rel' ) + '"]' );
	} );

	$( document ).on( 'click', '.popup-wrapper .close-popup, .popup-wrapper .layer-close', function( e ) {
		e.preventDefault();
		_functions.closePopup();
	} );

	// detect if user is using keyboard tab-button to navigate
	// with 'keyboard-focus' class we add default css outlines
	function keyboardFocus( e ) {
		if ( e.keyCode !== 9 ) {
			return;
		}

		switch ( e.target.nodeName.toLowerCase() ) {
			case 'input':
			case 'select':
			case 'textarea':
				break;
			default:
				document.documentElement.classList.add( 'keyboard-focus' );
				document.removeEventListener( 'keydown', keyboardFocus, false );
		}
	}

	document.addEventListener( 'keydown', keyboardFocus, false );

	// Invalid Input
	$( '.input[required]' ).on( 'blur', function() {
		if ( $( this ).val().trim() ) {
			$( this ).removeClass( 'invalid' );
		} else {
			$( this ).addClass( 'invalid' );
		}
	} );

	// tag
	$( document ).on( 'click', '.tag', function() {
		$( this ).toggleClass( 'active' );
	} );

	// dots-select
	$( document ).on( 'click', 'li.dots', function() {
		$( this ).find( '.dots-select' ).toggleClass( 'active' );
	} );
} );
