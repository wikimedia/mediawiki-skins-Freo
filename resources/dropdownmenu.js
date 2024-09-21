for ( const freoMenu of document.querySelectorAll( '.skin-freo-menu' ) ) {
	freoMenu.querySelector( 'button' ).addEventListener( 'click', () => {
		freoMenu.toggleAttribute( 'data-skin-freo-menu-open' );
	} );
	document.addEventListener( 'click', ( event ) => {
		if ( !freoMenu.contains( event.target ) ) {
			freoMenu.toggleAttribute( 'data-skin-freo-menu-open', false );
		}
	} );
}
