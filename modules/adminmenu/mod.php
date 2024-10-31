<?php
class adminmenuSub extends moduleSub {
    public function init() {
        parent::init();
		add_action('admin_menu', array($this, 'initMenu'), 9);
        //$this->getController()->getView('adminmenu')->init();
		$plugName = plugin_basename(SUB_DIR. SUB_MAIN_FILE);
		add_filter('plugin_action_links_'. $plugName, array($this, 'addSettingsLinkForPlug') );
    }
	public function addSettingsLinkForPlug($links) {
		array_unshift($links, '<a href="'. uriSub::_(array('baseUrl' => admin_url('admin.php'), 'page' => plugin_basename(frameSub::_()->getModule('adminmenu')->getView()->getMainSlug()))). '">'. __('Settings'). '</a>');
		return $links;
	}
	public function initMenu() {
		$this->getView()->initMenu();
	}
}

