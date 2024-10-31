<?php
class subscribeViewSub extends viewSub {
	private $_displayColumns = array();
	public function getAdminOptions() {
		$subOptions = frameSub::_()->getModule('options')->getModel()->getByCategories('Subscribe');
		$emailEditTpls = array();
		$emailTpls = frameSub::_()->getModule('messenger')->getController()->getModel('email_templates')->get(array('module' => 'subscribe'));
		if(!empty($emailTpls)) {
			foreach($emailTpls as $tpl) {
				$emailEditTpls[] = array(
					'label' => $tpl['label'], 
					'content' => frameSub::_()->getModule('messenger')->getController()->getView()->getOneEmailTplEditor(array('tplData' => $tpl)),
				);
			}
		}
		$this->assign('subOptions', $subOptions['opts']);
		$this->assign('optModel',	frameSub::_()->getModule('options')->getModel());
		$this->assign('emailEditTpls', $emailEditTpls);
		return parent::getContent('subscribeAdminOptions');
	}
	public function getAdminSubscribersOptions() {
		$this->assign('allLists', $this->getModel()->getListLists());
		$this->assign('totalSubscribers', $this->getModel()->getCount());
		$this->assign('displayColumns', $this->getDisplayColumns());
		return parent::getContent('subscribeAdminSubscribersOptions');
	}
	public function getDisplayColumns() {
		if(empty($this->_displayColumns)) {
			$this->_displayColumns = array(
				'id'				=> array('label' => __('ID'), 'db' => 'id'),
				'list_email'		=> array('label' => __('Email'), 'db' => 'email'),
				'list_label_str'	=> array('label' => __('Lists'), 'db' => 'list_label_str'),
				'status'			=> array('label' => __('Status'), 'db' => 'status'),
			);
		}
		return $this->_displayColumns;
	}
	public function getAdminListsOptions() {
		return parent::getContent('subscribeAdminListsOptions');
	}
	public function displaySubscribeWidget($instance) {
		$instance = $this->preFillWidgetFormOptions($instance);
		$this->assign('instance', $instance);
		$this->assign('uniqueId', 'subSubscribeForm_'. mt_rand(1, 999999));
		parent::display('subscribeWidget');
	}
	public function displaySubscribeSetupForm($data, $widget) {
		$data = $this->preFillWidgetFormOptions($data);
		$this->assign('data', $data);
		$this->assign('widget', $widget);
		$this->assign('allLists', $this->getModel()->getListLists());
		parent::display('subscribeSetupForm');
	}
	public function preFillWidgetFormOptions($data) {
		$preFillKeys = array('sub_form_title', 'sub_enter_email_msg', 'sub_success_msg');
		foreach($preFillKeys as $key) {
			$data[ $key ] = isset($data[ $key ]) 
				? $data[ $key ] 
				: frameSub::_()->getModule('options')->get( $key );
		}
		return $data;
	}
	public function displaySubscribeSuccess($d = array()) {
		parent::display('subscribeSuccess');
	}
	public function displaySubscribeErrors($d = array()) {
		$this->assign('errors', $d['errors']);
		parent::display('subscribeErrors');
	}
	public function displayUnsubscribeSuccess($d = array()) {
		parent::display('unsubscribeSuccess');
	}
	public function displayUnsubscribeErrors($d = array()) {
		$this->assign('errors', $d['errors']);
		parent::display('unsubscribeErrors');
	}
	public function getListEmail($subscriber) {
		$this->assign('subscriber', $subscriber);
		return parent::getContent('subscribeListEmail');
	}
}

