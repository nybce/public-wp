import './lib/jquery-ui-datepicker.min';

jQuery( function( $ ) {
	$( function() {
		$( '.year-selected' ).val( new Date().getFullYear() );
	} );

	$( function() {
		for ( let i = 0; i < 12; i++ ) {
			const date = new Date( 0, i, 1 );
			const month = date.toLocaleString( 'en', { month: 'short' } );
			$( '.months' ).append( '<a class="month" data-month=' + ( '0' + ( i + 1 ) ).slice( -2 ) + '>' + month + '</a>' );
		}
	} );

	function switchSelectedYear( thisObj ) {
		const oldContent = thisObj.parent().parent().find( '.calendar .input-calendar p' ).text();
		const newContent = oldContent.slice( 0, 3 ) + thisObj.parent().parent().find( '.date_selector .year-selected' ).val();
		thisObj.parent().parent().find( '.calendar .input-calendar p' ).text( newContent );
	}

	$( '.input-calendar' ).click( function() {
		let open = null;

		if ( $( this ).parent().hasClass( 'date_pick' ) ) {
			open = ( $( '.date_selector.active' ).length > 0 && $( this ).parent().find( '.date_selector' ).length > 0 && ( $( '.date_selector.active' )[ 0 ] === $( this ).parent().find( '.date_selector' )[ 0 ] ) );
		} else if ( $( this ).parent().hasClass( 'select_box' ) ) {
			open = ( $( '.box_selector.active' ).length > 0 && $( this ).parent().find( '.box_selector' ).length > 0 && ( $( '.box_selector.active' )[ 0 ] === $( this ).parent().find( '.box_selector' )[ 0 ] ) );
		}

		$( '.date_selector.active' ).removeClass( 'active' );
		$( '.box_selector.active' ).removeClass( 'active' );
		$( '.select_box.active' ).removeClass( 'active' );
		$( '.date_pick.active' ).removeClass( 'active' );

		if ( ! open ) {
			$( this ).parent().find( '.date_selector' ).toggleClass( 'active' );
			$( this ).parent().find( '.box_selector' ).toggleClass( 'active' );
			$( this ).parent().toggleClass( 'active' );
		}
	} );

	$( document ).on( 'click', function( e ) {
		if ( $( e.target ).is( '.date_selector.active a, .input-calendar, .input-calendar p, .calendar .input-calendar img, .date_selector.active .picker-select-arrow, .years, .years .year-selected, .date_selector.active div.months, .picker-arrow' ) === false ) {
			$( '.date_selector.active' ).removeClass( 'active' );
			$( '.box_selector.active' ).removeClass( 'active' );
			$( '.select_box.active' ).removeClass( 'active' );
			$( '.date_pick.active' ).removeClass( 'active' );
		}
	} );

	window.picker_date = function() {
		$( '.picker-left' ).unbind( 'click' );
		$( '.picker-right' ).unbind( 'click' );

		$( '.picker-left' ).click( function( ) {
			const value = ( parseInt( ( $( this ).parent().parent().find( '.year-selected' ).val() ) ) - 1 );
			$( this ).parent().find( '.year-selected' ).val( value );

			switchSelectedYear( $( this ) );
		} );

		$( '.picker-right' ).click( function( ) {
			const value = ( parseInt( ( $( this ).parent().parent().find( '.year-selected' ).val() ) ) + 1 );
			$( this ).parent().find( '.year-selected' ).val( value );

			switchSelectedYear( $( this ) );
		} );
	};
	window.picker_date();

	$( '.months' ).on( 'click', '.month', function( ) {
		$( this ).parent().find( 'a' ).not( $( this ) ).removeClass( 'active' );
		$( this ).addClass( 'active' );
		const month = $( this ).attr( 'data-month' );
		let year = $( this ).parent().parent().find( '.years .year-selected' ).val();
		year = year.trim();
		$( this ).parent().parent().parent().find( '.picker_date' ).val( month + '/' + year );
		$( this ).parent().parent().parent().find( '.input-calendar p' ).text( month + '/' + year );
		$( this ).parent().parent().find( '.years .year-selected' ).val( month + '/' + year );
	} );

	$( '.calendar .picker_date' ).keypress( function( event ) {
		const keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode === '13' ) {
			event.preventDefault();

			$( this ).parent().click();
		}
	} );

	$( '.date_selector .year-selected' ).keypress( function( event ) {
		const keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode === '13' ) {
			event.preventDefault();

			$( this ).parent().parent().parent().click();
		}
	} );

	$( '.date_selector .year-selected' ).on( 'input', function( e ) {
		switchSelectedYear( e );
	} );
} );
