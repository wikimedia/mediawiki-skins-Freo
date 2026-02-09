<?php

use MediaWiki\Html\Html;
use MediaWiki\Title\Title;

/**
 * @ingroup Skins
 */
class SkinFreo extends SkinMustache {

	private string $skinConfigName = 'skin-freo-config.json';

	/** @var mixed[] Skin config read from the skin-freo-config.json message. */
	private $skinConfig;

	/**
	 * @return mixed[]
	 */
	private function getSkinConfig(): array {
		if ( !$this->skinConfig ) {
			$this->skinConfig = json_decode( $this->msg( $this->skinConfigName )->text(), true );
			if ( !is_array( $this->skinConfig ) ) {
				$this->skinConfig = [];
			}
		}
		return $this->skinConfig;
	}

	/**
	 * @inheritDoc
	 */
	public function getTemplateData() {
		$out = parent::getTemplateData();
		$config = $this->getSkinConfig();

		// Wiki configurable menus.
		$out['html-freo-menus'] = '';
		foreach ( $config['menus'] ?? [] as $menuConfig ) {
			$label = isset( $menuConfig['label-msg'] )
				? $this->msg( $menuConfig['label-msg'] )->text()
				: ( $menuConfig['label'] ?? '' );
			$out['html-freo-menus'] .= $this->getMenu( $label, $menuConfig['items'] ?? [] );
		}

		// Page actions menu.
		$portletsRest = $out['data-portlets-sidebar']['array-portlets-rest'] ?? [];
		$toolbox = array_values( array_filter( $portletsRest, static function ( $item ) {
			return $item['id'] === 'p-tb';
		} ) );
		$toolsPage = [];
		foreach ( $toolbox[0]['array-items'] ?? [] as $tool ) {
			if ( !in_array( $tool['id'], [ 't-upload', 't-specialpages' ] ) ) {
				$toolsPage[] = $tool;
			}
		}
		$actionsAll = array_merge(
			$out['data-portlets']['data-views']['array-items'],
			$out['data-portlets']['data-actions']['array-items'],
			$toolsPage
		);
		$actions = '';
		foreach ( $actionsAll as $item ) {
			if ( in_array( $item['name'], [ 'talk', 'specialpages' ] )
				|| ( isset( $item['id'] )
					&& ( in_array( $item['id'], [ 'ca-view', 'ca-talk', 't-upload' ] )
						|| str_starts_with( $item['id'], 'ca-nstab-' )
					)
				)
			) {
				continue;
			}
			$actions .= $item['html-item'];
		}
		$out['html-freo-page-actions'] = $actions;

		// Site menu.
		$siteMenu = [
			[ 'page' => $this->msg( 'aboutpage' )->text(), 'label-msg' => 'about' ],
			[ 'page' => 'Special:RecentChanges', 'label-msg' => 'recentchanges' ],
			[ 'page' => 'Special:Upload', 'label-msg' => 'upload' ],
			[ 'page' => $this->msg( 'randompage-url' )->text(), 'label-msg' => 'randompage' ],
			[ 'page' => 'Special:SpecialPages', 'label-msg' => 'specialpages' ],
		];
		$out['html-freo-menu-site'] = $this->getMenu( $this->msg( 'skin-freo-menu-site' ), $siteMenu );

		// Associated pages menu.
		$out['html-freo-associated-pages'] = '';
		foreach ( $out['data-portlets']['data-associated-pages']['array-items'] as $assocPage ) {
			if ( in_array( $assocPage['id'], [ 'ca-nstab-main', 'ca-talk' ] ) ) {
				continue;
			}
			$out['html-freo-associated-pages'] .= $assocPage['html-item'];
		}
		if ( !$out['html-freo-associated-pages'] ) {
			unset( $out['html-freo-associated-pages'] );
		}

		// Only link the page title if we're not currently viewing the page.
		$isView = Action::getActionName( $this->getContext() ) === 'view';
		$diff = $this->getContext()->getRequest()->getVal( 'diff' );
		$oldid = $this->getContext()->getRequest()->getVal( 'oldid' );
		$out['page-title-url'] = $isView && !$diff && !$oldid
			? false
			: $this->getTitle()->getLocalURL();
		$out['page-title-text'] = $this->getTitle()->getText();
		$out['page-title-ns'] = $this->getTitle()->getNamespace() !== NS_MAIN
			? trim( str_replace( '_', ' ', $this->getTitle()->getNsText() ) . $this->msg( 'colon-separator' )->text() )
			: false;

		// @HACK There's no way to get the display title, so instead we check that it's different.
		$displayTitle = $this->getOutput()->getDisplayTitle();
		$out['page-title-displaytitle'] = $displayTitle !== $this->getTitle()->getPrefixedText()
			? $displayTitle
			: false;

		// Get subtitle without subpage breadcrumbs.
		$out['html-subtitleonly'] = $this->getOutput()->getSubtitle();

		$out['html-footer-blurb'] = $this->msg( 'skin-freo-footer-blurb' )->parse();

		return $out;
	}

	private function getMenu( string $label, array $items ): string {
		$menuItems = '';
		foreach ( $items as $item ) {
			$menuItems .= $this->getMenuItem( $item );
		}
		$label = Html::element( 'button', [ 'type' => 'button' ], $label );
		$menu = Html::rawElement( 'menu', [], $menuItems );
		return Html::rawElement( 'div', [ 'class' => 'skin-freo-menu' ], $label . $menu );
	}

	/**
	 * @param array $menuItem
	 * @return string
	 */
	public function getMenuItem( array $menuItem ): string {
		$menuItem = array_filter( $menuItem );
		$aParams = [];

		// title
		if ( isset( $menuItem['title'] ) ) {
			$aParams['title'] = $menuItem['title'];
		}

		// href
		$title = Title::newFromText( $menuItem['page'] );
		if ( $title ) {
			$aParams['href'] = $title->getLinkURL();
			if ( !isset( $menuItem['title'] ) ) {
				$aParams['title'] = $title->getFullText();
			}
		}

		// contents
		$contents = '';
		if ( isset( $menuItem['label-msg'] ) ) {
			$contents = $this->msg( $menuItem['label-msg'] )->text();
		} elseif ( isset( $menuItem['label'] ) ) {
			$contents = $menuItem['label'];
		} elseif ( $title instanceof Title ) {
			$contents = $title->getText();
		}

		$class = $menuItem['class'] ?? '';

		return Html::rawElement(
			'li',
			[ 'class' => trim( $class ) ],
			Html::rawElement( 'a', $aParams, $contents )
		);
	}
}
