jQuery( function( $ ) {
	$( document ).ready( function() {
		$( 'a.play-video' ).click( function() {
			$( this ).parents( '.video-container' ).find( 'video' ).trigger( 'play' );
			$( this ).hide();
			$( this ).next( 'a.pause-video' ).show();
			// IF VIDEO HAS COVER
			const hasitcover = $( this ).parents( '.video-container' ).find( '.video-cover' );
			if ( hasitcover.length > 0 ) {
				hasitcover.fadeOut();
			}
		} );
		$( 'a.pause-video' ).click( function() {
			$( this ).parents( '.video-container' ).find( 'video' ).trigger( 'pause' );
			$( this ).hide();
			$( this ).prev( 'a.play-video' ).show();
		} );
		$( '.video-item video' ).on( 'ended', function() {
			const hasitcovertwo = $( this ).parents( '.video-container' ).find( '.video-cover' );
			if ( hasitcovertwo.length > 0 ) {
				hasitcovertwo.fadeIn();
			}
			$( this ).parents( '.video-container' ).find( 'a.play-video' ).show();
			$( this ).parents( '.video-container' ).find( 'a.pause-video' ).hide();
		} );
	} );
} );
