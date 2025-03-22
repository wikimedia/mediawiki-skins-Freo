for ( const freoMenu of document.querySelectorAll( '.skin-freo-menu' ) ) {
	freoMenu.querySelector( 'button' ).addEventListener( 'click', () => {
		freoMenu.toggleAttribute( 'data-skin-freo-menu-open' );
	} );
	document.addEventListener( 'click', ( event ) => {
		if ( !freoMenu.contains( event.target ) ) {
			freoMenu.toggleAttribute( 'data-skin-freo-menu-open', false );
		}
	} );

	// Close the menu when clicking the VE edit link, because it doesn't reload
	// the page (unlike all other links in the menu).
	const veEdit = freoMenu.querySelector( '#ca-ve-edit' );
	if ( veEdit ) {
		veEdit.addEventListener( 'click', () => {
			freoMenu.toggleAttribute( 'data-skin-freo-menu-open', false );
		} );
	}
}
