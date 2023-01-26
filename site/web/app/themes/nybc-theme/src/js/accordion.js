jQuery( function( $ ) {
	// accordion
	$( document ).on(
		'click',
		'.accordion:not(.employment-accord) .accordion-item .accordion-title',
		function() {
			if ( $( this ).hasClass( 'active' ) ) {
				$( this ).removeClass( 'active' ).next().slideUp();
				window.history.back();
			} else {
				$( this )
					.closest( '.accordion' )
					.find( '.accordion-title' )
					.not( this )
					.removeClass( 'active' )
					.next()
					.slideUp();
				$( this ).addClass( 'active' ).next().slideDown();
				var ac_id = $(this).closest('.accordion').attr('id');
				if (history.pushState) {
				    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '/#' + ac_id;
				    window.history.pushState({path:newurl},'',newurl);
				}
			}
		}
	);
	$( document ).ready(function() {
		var path = window.location.pathname;
		var ac_id = '';
		if (path.indexOf('#') > -1) {
			ac_id = path.split('#')[1];
		}else{
			return;
		}
		$(ac_id).addClass('active');
		if (history.pushState) {
		    var newurl = window.location.protocol + "//" + window.location.host + path.split('#')[0] + '/' + ac_id;
		    window.history.pushState({path:newurl},'',newurl);
		}
	}
} );
