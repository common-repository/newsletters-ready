<?php
class newslettersControllerSub extends controllerSub {
	public function getList() {
		$res = new responseSub();
		if($count = $this->getModel()->getCount()) {
			$list = $this->getModel()->getList(reqSub::get('post'));
			$res->addData('list', $list);
			$res->addData('count', $count);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	private function _convertDataForDatatable($list, $single = false) {
		$returnList = array();
		if($single) {
			$list = array($list);
		}
		foreach($list as $i => $newsletter) {
			$returnList[ $i ] = $newsletter;
			$returnList[ $i ]['list_subject_line'] = $this->getView()->getListSubjectLine( $newsletter );
			$returnList[ $i ]['modified'] = dateSub::fromDbWithTime( $newsletter['date_created'] );
			$returnList[ $i ]['date_sent_tbl'] = dateSub::fromDbWithTime( $newsletter['date_sent'] );
		}
		if($single) {
			return $returnList[0];
		}
		return $returnList;
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
			$listReqData['additionalCondition'] = 'subject LIKE "%'. $dbSearch. '%"';
		}
		foreach($displayColumnsKeys as $i => $colKey) {
			if($colKey == 'date_sent_tbl') {
				$dateRange = reqSub::getVar('sSearch_'. $i);
				if(!empty($dateRange)) {
					$dateConditionArr = array();
					$dateFromTo = explode('|', $dateRange);
					if(!empty($dateFromTo[0])) {
						$dateConditionArr[] = 'date_sent >= "'. dateSub::toDb( dbSub::escape($dateFromTo[0]) ).' 00:00:00"';
					}
					if(!empty($dateFromTo[1])) {
						$dateConditionArr[] = 'date_sent <= "'. dateSub::toDb( dbSub::escape($dateFromTo[1]) ).' 24:59:59"';
					}
					if(!empty($dateConditionArr)) {
						if(!empty($listReqData['additionalCondition']))
							$listReqData['additionalCondition'] .= ' AND ';
						$listReqData['additionalCondition'] .= implode(' AND ', $dateConditionArr);
					}
				}
			}
			if($colKey == 'list_label_str') {
				$listId = (int) reqSub::getVar('sSearch_'. $i);
				if($listId) {
					if(!empty($listReqData['additionalCondition']))
						$listReqData['additionalCondition'] .= ' AND ';
					$listReqData['additionalCondition'] .= 'id IN (SELECT newsletter_id FROM @__newsletters_to_lists WHERE subscriber_list_id = '. $listId. ')';
				}
			}
		}
		$list = $this->getModel()->getList( $listReqData );

		$res->addData('aaData', $this->_convertDataForDatatable($list));
		$res->addData('iTotalRecords', $count);
		$res->addData('iTotalDisplayRecords', $count);
		$res->addData('sEcho', reqSub::getVar('sEcho'));
		$res->addData('allUsedLists', $this->getModel()->getAllUsedSubscribeLists());
		$res->addMessage(__('Done'));
		return $res->ajaxExec();
	}
	public function getEditForm() {
		$res = new responseSub();
		if($html = $this->getView()->getEditForm(reqSub::get('post'))) {
			$res->setHtml($html);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getView()->getErrors());
		return $res->ajaxExec();
	}
	public function save() {
		$res = new responseSub();
		$saveData = reqSub::get('post');
		$nextAction = reqSub::getVar('next_action');
		if($nextAction == 'back') {
			$saveData['status'] = $this->getModule()->getStatusByKey('new');
		}
		if(($id = $this->getModel()->save($saveData))) {
			// Try to get errors in any case - they can be no critical, connected with stpl editor
			$errors = $this->getModel()->getErrors();
			if(!empty($errors))
				$res->pushError( $errors );
			$newsletter = $this->getModel()->getById($id);
			
			if($nextAction) {
				switch($nextAction) {
					case 'send':
						if(!$this->getModel()->startSend($newsletter)) {
							$res->pushError ($this->getModel()->getErrors());
							return $res->ajaxExec();
						}
						break;
				}
				// Get it with updated info
				$newsletter = $this->getModel()->getById($id);
			}
			$res->addData('newsletter', $newsletter);
			$res->addData('newsletterForTbl', $this->_convertDataForDatatable($newsletter, true));
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function backToEdit() {
		$res = new responseSub();
		if($this->getModel()->backToEdit(reqSub::get('post'))) {
			$newsletter = $this->getModel()->getById( reqSub::getVar('id') );
			$res->addData('newsletter', $newsletter);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function sendTest() {
		$res = new responseSub();
		if($this->getModel()->sendTest(reqSub::get('post'))) {
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
	public function duplicate() {
		$res = new responseSub();
		if($this->getModel()->duplicate(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function selectTemplate() {
		$res = new responseSub();
		if(($id = $this->getModel()->selectTemplate(reqSub::get('post')))) {
			$newsletter = $this->getModel()->getById($id);
			$res->addData('newsletter', $newsletter);
			$res->addData('newsletterForTbl', $this->_convertDataForDatatable($newsletter, true));
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function saveTemplate() {
		$res = new responseSub();
		if(($id = $this->getModel()->saveTemplate(reqSub::get('post')))) {
			$newsletter = $this->getModel()->getById($id);
			$res->addData('newsletter', $newsletter);
			$res->addData('newsletterForTbl', $this->_convertDataForDatatable($newsletter, true));
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getSendStatList() {
		$res = new responseSub();
		$newsletterId = (int) reqSub::getVar('newsletterId');
		if($count = $this->getModel()->getSendStatCount($newsletterId)) {
			$list = $this->getModel()->getSendStatList(reqSub::get('post'), $newsletterId);
			$res->addData('list', $list);
			$res->addData('count', $count);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function resend() {
		$res = new responseSub();
		if($this->getModel()->resend(reqSub::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SUB_USERLEVELS => array(
				SUB_ADMIN => array('getList', 'getEditForm', 'save', 'sendTest', 'remove', 'duplicate', 
					'selectTemplate', 'saveTemplate', 'getSendStatList', 'resend')
			),
		);
	}
}

