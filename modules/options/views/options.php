<?php
class optionsViewSub extends viewSub {
    public function getAdminPage() {
		$tabsData = array(
			'subMainOptions'		=> array('title' => 'Main',		'content' => $this->getMainOptionsTab()),
		);
		$tabsData = dispatcherSub::applyFilters('adminOptionsTabs', $tabsData);
		$this->assign('tabsData', $tabsData);
        parent::display('optionsAdminPage');
    }
	public function getMainOptionsTab() {
		$generalOptions = $this->getModel()->getByCategories('General');
		if(!isset($this->optModel))
			$this->assign('optModel', $this->getModel());
		$this->assign('allOptions', $generalOptions['opts']);
		$this->assign('subscribeSettings', frameSub::_()->getModule('subscribe')->getView()->getAdminOptions());
		return parent::getContent('mainOptionsTab');
	}
}
