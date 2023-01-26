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
				console.log('acc open');
				var ac_id = $(this).closest('.accordion-item').attr('id');
				console.log(ac_id);
				    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '#' + ac_id;
				    console.log(newurl);
				    window.history.pushState({path:newurl},'',newurl);
				
			}
		}
	);
	$( document ).ready(function() {
		console.log('acc open on ready');
		var href = window.location.href;
		console.log(href);
		var ac_id = '';
		if (href.indexOf('#') > -1) {
			ac_id = '#' + href.split('#')[1];
			console.log(ac_id);
		}else{
			return;
		}
		$(ac_id).find('.accordion-title').addClass('active');
		    var newurl = window.location.protocol + "//" + window.location.host + href.split('#')[0] + '/' + ac_id;
		    window.history.pushState({path:newurl},'',newurl);
		
	} );
} );
