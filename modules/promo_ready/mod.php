<?php
class promo_readySub extends moduleSub {
	private $_specSymbols = array(
		'from'	=> array('?', '&'),
		'to'	=> array('%', '^'),
	);
	
	public function init() {
		parent::init();
		add_action('admin_footer', array($this, 'displayAdminFooter'), 9);
		dispatcherSub::addFilter('adminMenuMainOption', array($this, 'addWelcomePageToMainMenu'), 99);
		dispatcherSub::addFilter('adminMenuMainSlug', array($this, 'modifyMainAdminSlug'), 99);
	}
	// We used such methods - _encodeSlug() and _decodeSlug() - as in slug wp don't understand urlencode() functions
	private function _encodeSlug($slug) {
		return str_replace($this->_specSymbols['from'], $this->_specSymbols['to'], $slug);
	}
	private function _decodeSlug($slug) {
		return str_replace($this->_specSymbols['to'], $this->_specSymbols['from'], $slug);
	}
	public function decodeSlug($slug) {
		return $this->_decodeSlug($slug);
	}
	public function modifyMainAdminSlug($mainSlug) {
		$firstTimeLookedToPlugin = !installerSub::isUsed();
		if($firstTimeLookedToPlugin) {
			$mainSlug = $this->_getNewAdminMenuSlug($mainSlug);
		}
		return $mainSlug;
	}
	private function _getWelcomMessageMenuData($option, $modifySlug = true) {
		return array_merge($option, array(
			'page_title'	=> __('Welcome to Ready! Ecommerce'),
			'menu_slug'		=> ($modifySlug ? $this->_getNewAdminMenuSlug( $option['menu_slug'] ) : $option['menu_slug'] ),
			'function'		=> array($this, 'showWelcomePage'),
		));
	}
	private function _getNewAdminMenuSlug($menuSlug) {
		// We can't use "&" symbol in slug - so we used "|" symbol
		return 'welcome-to-'. $menuSlug. '|return='. $this->_encodeSlug($menuSlug);
	}
	public function addWelcomePageToMainMenu($option) {
		$firstTimeLookedToPlugin = !installerSub::isUsed();
		if($firstTimeLookedToPlugin) {
			$option = $this->_getWelcomMessageMenuData($option, false);
		}
		return $option;
	}
	public function showWelcomePage() {
		$this->getView()->showWelcomePage();
	}
	public function displayAdminFooter() {
		if(frameSub::_()->isAdminPlugPage())
			$this->getView()->displayAdminFooter();
	}
	private function _preparePromoLink($link) {
		$link .= '?ref=user';
		return $link;
	}
	/**
	 * Public shell for private method
	 */
	public function preparePromoLink($link) {
		return $this->_preparePromoLink($link);
	}
}