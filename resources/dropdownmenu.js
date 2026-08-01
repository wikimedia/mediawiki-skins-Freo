const viewport = document.body.getBoundingClientRect();
for ( const freoMenu of document.querySelectorAll( '.skin-freo-menu' ) ) {

	// Dynamically add the alignment class to menus that are too close to the right.
	const menuRect = freoMenu.querySelector( 'menu' ).getBoundingClientRect();
	const rightOrLeft = document.documentElement.dir === 'rtl' ? 'left' : 'right';
	if ( menuRect[ rightOrLeft ] > viewport[ rightOrLeft ] ) {
		freoMenu.classList.add( 'skin-freo-menu-end' );
	}

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
