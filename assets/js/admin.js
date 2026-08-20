/* Zlaark Deals - wires the WP media library into the Deal Details meta box. */
( function ( $ ) {
	'use strict';

	var frame = null;

	$( document ).on( 'click', '.zlaark-select-image', function ( e ) {
		e.preventDefault();

		var $box = $( this ).closest( '.zlaark-image-box' );

		// Reusing a single frame keeps the modal snappy across repeated opens.
		if ( frame ) {
			frame.off( 'select' );
		} else {
			frame = wp.media( {
				title: ZlaarkDealsAdmin.chooseImage,
				button: { text: ZlaarkDealsAdmin.useImage },
				library: { type: 'image' },
				multiple: false
			} );
		}

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON(),
				url = attachment.sizes && attachment.sizes.thumbnail
					? attachment.sizes.thumbnail.url
					: attachment.url;

			$box.find( '.zlaark-image-id' ).val( attachment.id );
			$box.find( '.zlaark-image-preview' )
				.removeClass( 'is-empty' )
				.html( $( '<img>' ).attr( { src: url, alt: '' } ) );
			$box.find( '.zlaark-remove-image' ).show();
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.zlaark-remove-image', function ( e ) {
		e.preventDefault();

		var $box = $( this ).closest( '.zlaark-image-box' );

		$box.find( '.zlaark-image-id' ).val( '' );
		$box.find( '.zlaark-image-preview' )
			.addClass( 'is-empty' )
			.html( $( '<span>' ).text( ZlaarkDealsAdmin.noImage ) );
		$( this ).hide();
	} );
} )( jQuery );
