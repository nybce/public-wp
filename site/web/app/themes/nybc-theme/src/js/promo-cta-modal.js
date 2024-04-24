jQuery( function( $ ) {
	// Cookies
	function createCookie( name, value, days ) {
		let expires;

		if ( days ) {
			const date = new Date();
			date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
			expires = '; expires=' + date.toGMTString();
		} else {
			expires = '';
		}
		document.cookie =
      encodeURIComponent( name ) +
      '=' +
      encodeURIComponent( value ) +
      expires +
      '; path=/';
	}

	function readCookie( name ) {
		const nameEQ = encodeURIComponent( name ) + '=';
		const ca = document.cookie.split( ';' );
		for ( let i = 0; i < ca.length; i++ ) {
			let c = ca[ i ];
			while ( c.charAt( 0 ) === ' ' ) {
				c = c.substring( 1, c.length );
			}
			if ( c.indexOf( nameEQ ) === 0 ) {
				return decodeURIComponent( c.substring( nameEQ.length, c.length ) );
			}
		}
		return null;
	}

	// promo-cta-modal
	if ( ! readCookie( 'nybc-promo-showed' ) && $( '.promo-cta-modal' ).length ) {
		setTimeout( function() {
			$( '.promo-cta-modal' ).addClass( 'active' );
			createCookie( 'nybc-promo-showed', 1 );
		}, 4000 );
	}

	$( document ).on( 'click', '.promo-cta-modal .btn-close', function() {
		$( this ).parents( '.promo-cta-modal' ).removeClass( 'active' );
	} );
} );
