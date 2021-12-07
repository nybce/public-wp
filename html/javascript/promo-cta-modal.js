jQuery( function( $ ) {
	// promo-cta-modal
	setTimeout( function() {
		$( '.promo-cta-modal' ).addClass( 'active' );
	}, 4000 );

	$( document ).on( 'click', '.promo-cta-modal .btn-close', function() {
		$( '.promo-cta-modal' ).removeClass( 'active' );
	} );
} );
