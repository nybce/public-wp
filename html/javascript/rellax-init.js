/* eslint-disable no-unused-vars, no-undef */
jQuery( function( $ ) {
	//rellax
	setTimeout( function() {
		if ( ! isIE && $( '.rellax' ).length && $( window ).width() > 1199 ) {
			const rellax = new Rellax( '.rellax', {
				center: true,
			} );
		}
	}, 0 );
} );
