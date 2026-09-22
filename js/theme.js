( function( $ ) {
	'use strict';

	$( function() {
		var $menuToggle = $( '#menu_icon' );
		var $primaryMenu = $( '#primary-menu' );

		if ( ! $primaryMenu.length ) {
			$primaryMenu = $( '#site-navigation ul' ).first();
		}

		// Mobile menu toggling. The existing CSS classes are preserved.
		$menuToggle.on( 'click', function() {
			var expanded = 'true' === $menuToggle.attr( 'aria-expanded' );

			$primaryMenu.toggleClass( 'show_menu' );
			$menuToggle.toggleClass( 'close_menu' );
			$menuToggle.attr( 'aria-expanded', String( ! expanded ) );
		} );

		// Contact page map centering. No-op on pages without #map.
		var $map = $( '#map' );
		if ( $map.length ) {
			var headerWidth = $( 'header' ).width() + 50;
			var mapWidth = $map.width();
			var windowHeight = $( window ).height();

			$map.css( {
				'max-width': mapWidth,
				height: windowHeight,
				'margin-left': headerWidth
			} );
		}

		// Preserve Briite's existing visual tooltips while avoiding duplicate nodes.
		$( document )
			.on( 'mouseenter focusin', 'a[data-title]', function() {
				var $link = $( this );
				var title = $link.attr( 'data-title' );

				if ( ! title || $link.next( '.tooltip' ).length ) {
					return;
				}

				$link.after( '<span class="tooltip" role="tooltip"></span>' );

				var $tooltip = $link.next( '.tooltip' );
				$tooltip.text( title );

				var tipWidth = $tooltip.outerWidth();
				var linkWidth = $link.width();
				var linkHeight = $link.height() + 7;

				if ( tipWidth < linkWidth ) {
					tipWidth = linkWidth;
					$tooltip.outerWidth( tipWidth );
				}

				$tooltip.css( {
					left: '-' + ( ( tipWidth - linkWidth ) / 2 ) + 'px',
					bottom: linkHeight + 'px'
				} ).stop().animate( { opacity: 1 }, 200 );
			} )
			.on( 'mouseleave focusout', 'a[data-title]', function() {
				$( this ).next( '.tooltip' ).remove();
			} );

		// Dictionary list interaction.
		$( 'dl' ).on( 'click', 'dt', function() {
			$( this ).next().toggleClass( 'expand' );
			$( this ).toggleClass( 'expand' );
		} );
	} );
}( jQuery ) );
