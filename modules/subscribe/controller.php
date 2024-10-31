<?php
class subscribeControllerSub extends controllerSub {
	public function create() {
		$res = new responseSub();
		$data = reqSub::get('post');
		$data['withoutConfirm'] = false;	// DO THIS FROM OPTIONS !!!!!!!!
		
		if($this->getModel()->create($data)) {
			$res->addMessage(__(frameSub::_()->getModule('options')->get('sub_success_msg')));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function saveAdmin() {
		$res = new responseSub();
		$data = reqSub::get('post');
		$data['withoutConfirm'] = true;
		 if(($id = $this->getModel()->save($data))) {
			 $subscriber = $this->getModel()->getById($id);
			 $res->addData('subscriber', $subscriber);
			 $res->addMessage(__('Done'));
		 } else
			 $res->pushError ($this->getModel()->getErrors());
		 return $res->ajaxExec();
	}
	public function confirm() {
		$res = new responseSub();
		if($this->getModel()->confirm(reqSub::get('get'))) {
			$res->addMessage(__('Your subscription was activated!'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res;
	}
	public function confirmLead() {
		if(($subId = $this->getModel()->confirm(reqSub::get('get')))) {
			$this->getView()->displaySubscribeSuccess(array(
				'subId' => $subId,
			));
		} else {
			$this->getView()->displaySubscribeErrors(array(
				'errors' => $this->getModel()->getErrors()
			));
		}
		exit();
	}
	public function unsubscribeLead() {
		if(($subId = $this->getModel()->unsubscribe(reqSub::get('get')))) {
			$this->getView()->displayUnsubscribeSuccess(array(
				'subId' => $subId,
			));
		} else {
			$this->getView()->displayUnsubscribeErrors(array(
				'errors' => $this->getModel()->getErrors()
			));
		}
		exit();
	}
	
	/**
	 * Get list of subscribers lists
	 */
	public function getListLists() {
		$res = new responseSub();
		if($count = $this->getModel()->getCountLists()) {
			$list = $this->getModel()->getListLists(reqSub::get('post'));
			$res->addData('list', $list);
			$res->addData('count', $count);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function saveList() {
		$res = new responseSub();
		 if(($id = $this->getModel()->saveList(reqSub::get('post')))) {
			 $list = $this->getModel()->getListById( $id );
			 $res->addData('list', $list);
			 $res->addMessage(__('Saved'));
		 } else
			 $res->pushError ($this->getModel()->getErrors());
		 return $res->ajaxExec();
	}
	public function removeList() {
		$res = new responseSub();
		 if($this->getModel()->removeList(reqSub::get('post'))) {
			 $res->addMessage(__('Done'));
		 } else
			 $res->pushError ($this->getModel()->getErrors());
		 return $res->ajaxExec();
	}
	public function changeStatus() {
		$res = new responseSub();
		if($this->getModel()->changeStatus(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function remove() {
		$res = new responseSub();
		if($this->getModel()->remove(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getList() {
		$res = new responseSub();
		if($count = $this->getModel()->getCount(reqSub::get('post'))) {
			$list = $this->getModel()->getList(reqSub::get('post'));
			$res->addData('list', $list);
			$res->addData('count', $count);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getListForTable() {
		$res = new responseSub();
		$res->ignoreShellData();
		
		$count = $this->getModel()->getCount();
		$listReqData = array(
			'limitFrom' => reqSub::getVar('iDisplayStart'),
			'limitTo' => reqSub::getVar('iDisplayLength'),
		);
		$displayColumns = $this->getView()->getDisplayColumns();
		$displayColumnsKeys = array_keys($displayColumns);
		$iSortCol = reqSub::getVar('iSortCol_0');
		if(!is_null($iSortCol) && is_numeric($iSortCol)) {
			$listReqData['orderBy'] = $displayColumns[ $displayColumnsKeys[ $iSortCol ] ]['db'];
			$iSortDir = reqSub::getVar('sSortDir_0');
			if(!is_null($iSortDir)) {
				$listReqData['orderBy'] .= ' '. strtoupper($iSortDir);
			}
		}
		$search = reqSub::getVar('sSearch');
		if(!is_null($search) && !empty($search)) {
			$dbSearch = dbSub::escape($search);
			$listReqData['additionalCondition'] = 'email LIKE "%'. $dbSearch. '%" OR name LIKE "%'. $dbSearch. '%"';
		}
		foreach($displayColumnsKeys as $i => $colKey) {
			if($colKey == 'list_label_str') {
				$listId = (int) reqSub::getVar('sSearch_'. $i);
				if($listId) {
					$listReqData['filterListId'] = $listId;
				}
			}
		}
		$list = $this->getModel()->getList( $listReqData );

		$res->addData('aaData', $this->_convertDataForDatatable($list));
		$res->addData('iTotalRecords', $count);
		$res->addData('iTotalDisplayRecords', $count);
		$res->addData('sEcho', reqSub::getVar('sEcho'));
		$res->addMessage(__('Done'));
		return $res->ajaxExec();
	}
	private function _convertDataForDatatable($list, $single = false) {
		$returnList = array();
		if($single) {
			$list = array($list);
		}
		foreach($list as $i => $subscriber) {
			$returnList[ $i ] = $subscriber;
			$returnList[ $i ]['list_email'] = $this->getView()->getListEmail( $subscriber );
		}
		if($single) {
			return $returnList[0];
		}
		return $returnList;
	}
	public function getPermissions() {
		return array(
			SUB_USERLEVELS => array(
				SUB_ADMIN => array('getList', 'changeStatus', 'remove', 'saveList', 'getListLists', 'removeList', 'saveAdmin', 'getListForTable')
			),
		);
	}
}

