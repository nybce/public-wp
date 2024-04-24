import { isIE } from './global';
import Rellax from './lib/rellax.min';

jQuery( function( $ ) {
	//rellax

	setTimeout( function() {
		if ( ! isIE && $( '.rellax' ).length && $( window ).width() > 1199 ) {
			new Rellax( '.rellax', {
				center: true,
			} );
		}
	}, 0 );
} );
