<?php
class promo_readyControllerSub extends controllerSub {
    public function welcomePageSaveInfo() {
		$res = new responseSub();
		if($this->getModel()->welcomePageSaveInfo(reqSub::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!'));
			$originalPage = reqSub::getVar('original_page');
			$returnArr = explode('|', $originalPage);
			$return = $this->getModule()->decodeSlug(str_replace('return=', '', $returnArr[1]));
			$return = admin_url( strpos($return, '?') ? $return : 'admin.php?page='. $return);
			$res->addData('redirect', $return);
			installerSub::setUsed();
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			SUB_USERLEVELS => array(
				SUB_ADMIN => array('welcomePageSaveInfo')
			),
		);
	}
}