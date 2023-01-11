jQuery( function( $ ) {
	//menu
	$( '.mobile-button' ).on( 'click', function() {
		$( this ).toggleClass( 'active' );
		$( '.dropdown-menu' ).removeClass( 'active' );
		$( '.dropdown-toggle' ).removeClass( 'active' );
		$( 'html' ).toggleClass( 'overflow-menu' );
		$( this ).parents( 'header' ).find( '.toggle-block' ).toggleClass( 'open' );
	} );

	// mobile menu
	$( document ).on( 'click', '.dropdown-btn', function() {
		if ( $( window ).width() < 1199 ) {
			$( this ).parent().addClass( 'active' );
			$( this ).parents().find( '.toggle-block.open' ).addClass( 'remove-overflow' );
		}
	} );

	$( '.dropdown-close' ).on( 'click', function() {
		if ( $( window ).width() < 1199 ) {
			$( this ).parents( '.dropdown-item' ).removeClass( 'active' );
			$( this )
				.parents()
				.find( '.toggle-block.open' )
				.removeClass( 'remove-overflow' );
		}
	} );

	$( '.dropdown-v2-item.has-children' ).on( 'mouseenter', function() {
		var menuid = $(this).data('menuid');
		var target_submenu = '#submenu-' + menuid;
		console.log('hit');
		console.log(menuid);
		console.log(target_submenu);
		$(".sub-dropdown").removeClass('active');
		$(target_submenu).addClass('active');

	})
} );
