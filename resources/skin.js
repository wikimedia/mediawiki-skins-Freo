require( './dropdownmenu.js' );
const { App, restSearchClient, urlGenerator } = require( 'mediawiki.skinning.typeaheadSearch' );
const Vue = require( 'vue' );

const searchForm = document.querySelector( '.skin-freo-searchform-wrapper form' );
const searchInput = document.querySelector( '.skin-freo-searchform-wrapper input[name="search"]' );
const urlGeneratorInstance = urlGenerator( mw.config.get( 'wgScript' ) );
const restClient = restSearchClient( mw.config.get( 'wgScriptPath' ) + '/rest.php', urlGeneratorInstance );
const props = {
	prefixClass: 'skin-freo-',
	id: searchForm.id,
	autocapitalizeValue: searchInput.getAttribute( 'autocapitalize' ),
	autofocusInput: searchInput === document.activeElement,
	action: searchForm.getAttribute( 'action' ),
	searchAccessKey: searchInput.getAttribute( 'accessKey' ),
	showThumbnail: true,
	restClient,
	urlGenerator: urlGeneratorInstance,
	searchTitle: searchInput.getAttribute( 'title' ),
	searchPlaceholder: searchInput.getAttribute( 'placeholder' ),
	searchQuery: searchInput.value,
	showEmptySearchRecommendations: true,
	showDescription: true
};
Vue.createMwApp( App, props ).mount( '.skin-freo-searchform-wrapper' );
