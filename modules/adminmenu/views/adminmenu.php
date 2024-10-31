<?php
class adminmenuViewSub extends viewSub {
    protected $_mainSlug = 'ready-newsletters-subscribe';
    public function initMenu() {
		$mainSlug = dispatcherSub::applyFilters('adminMenuMainSlug', $this->_mainSlug);
		$mainMenuPageOptions = array(
			'page_title' => SUB_WP_PLUGIN_NAME, 
			'menu_title' => SUB_WP_PLUGIN_NAME, 
			'capability' => 'manage_options',
			'menu_slug' => $mainSlug,
			'function' => array(frameSub::_()->getModule('options')->getView(), 'getAdminPage'));
		$mainMenuPageOptions = dispatcherSub::applyFilters('adminMenuMainOption', $mainMenuPageOptions);
        add_menu_page($mainMenuPageOptions['page_title'], $mainMenuPageOptions['menu_title'], $mainMenuPageOptions['capability'], $mainMenuPageOptions['menu_slug'], $mainMenuPageOptions['function']);
    }
	public function getMainSlug() {
		return $this->_mainSlug;
	}
}