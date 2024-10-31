<?php
class optionsControllerSub extends controllerSub {
    public function save() {
		$res = new responseSub();
		if($this->getModel()->save(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
			if(reqSub::getVar('code') == 'template') {
				$plTemplate = $this->getModel()->get('template');		// Current plugin template
				$tplMod = ($plTemplate && frameSub::_()->getModule($plTemplate)) ? frameSub::_()->getModule($plTemplate) : false;
				$tplName = '';
				if($tplMod) {
					$tplName = $tplMod->getLabel();
					$defOptions = $tplMod->getDefOptions();
					if(!empty($defOptions))
						$res->addData('def_options', $defOptions);
				}
				$res->addData('new_name', $tplName);
			}
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
    }
	public function saveGroup() {
		$res = new responseSub();
		if($this->getModel()->saveGroup(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function saveBgImg() {
		$res = new responseSub();
		if($this->getModel()->saveBgImg(reqSub::get('files'))) {
			$res->addData(array('imgPath' => frameSub::_()->getModule('options')->getBgImgFullPath()));
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function saveLogoImg() {
		$res = new responseSub();
		if($this->getModel()->saveLogoImg(reqSub::get('files'))) {
			$res->addData(array('imgPath' => frameSub::_()->getModule('options')->getLogoImgFullPath()));
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	/**
	 * Will save main options and detect - whether or not sub mode enabled/disabled to trigger appropriate actions
	 */
	public function saveMainGroup() {
		$res = new responseSub();
		$oldMode = $this->getModel()->get('mode');
		if($this->getModel()->saveGroup(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
			$newMode = $this->getModel()->get('mode');
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	/**
	 * Will save subscriptions options as usual options + try to re-saive email templates from this part
	 */
	public function saveSubscriptionGroup() {
		$res = new responseSub();
		if($this->getModel()->saveGroup(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
			$emailTplData = reqSub::getVar('email_tpl');
			if(!empty($emailTplData) && is_array($emailTplData)) {
				foreach($emailTplData as $id => $tData) {
					frameSub::_()->getModule('messenger')->getController()->getModel('email_templates')->save(array(
						'id'		=> $id, 
						'subject'	=> $tData['subject'],
						'body'		=> $tData['body'],
					));
				}
			}
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function setTplDefaultList() {
		
	}
	public function setTplDefault() {
		$res = new responseSub();
		$newOptValue = $this->getModel()->setTplAnyDefault(reqSub::get('post'));
		if($newOptValue !== false) {
			$res->addMessage(__('Done'));
			$res->addData('newOptValue', $newOptValue);
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function removeBgImg() {
		$res = new responseSub();
		if($this->getModel()->removeBgImg(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function removeLogoImg() {
		$res = new responseSub();
		if($this->getModel()->removeLogoImg(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function activatePlugin() {
		$res = new responseSub();
		if($this->getModel('modules')->activatePlugin(reqSub::get('post'))) {
			$res->addMessage(lang::_('Plugin was activated'));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function activateUpdate() {
		$res = new responseSub();
		if($this->getModel('modules')->activateUpdate(reqSub::get('post'))) {
			$res->addMessage(lang::_('Very good! Now plugin will be updated.'));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SUB_USERLEVELS => array(
				SUB_ADMIN => array('save', 'saveGroup', 'saveBgImg', 'saveLogoImg', 
					'saveMainGroup', 'saveSubscriptionGroup', 'setTplDefault', 
					'removeBgImg', 'removeLogoImg',
					'activatePlugin', 'activateUpdate')
			),
		);
	}
}

