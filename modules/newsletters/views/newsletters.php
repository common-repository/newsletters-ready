<?php
class newslettersViewSub extends viewSub {
	// We will fill this in return method
	private $_displayColumns = array();
	
	public function getAdminTab() {
		$this->assign('statusesByKeys', $this->getModule()->getStatusesByKeys());
		$this->assign('tplSelecting', $this->getTplSelecting());
		$this->assign('selectTpl', $this->getSelectTpl());
		$this->assign('editForm', $this->getEditForm());
		$this->assign('sendStat', $this->getSendStat());
		
		$this->assign('displayColumns', $this->getDisplayColumns());
		return parent::getContent('newslettersAdminTab');
	}
	public function getEditForm() {
		$categoriesList = array(0 => SUB_ANY);
		$categories = utilsSub::getCategories(array('hide_empty' => 0));
		if(!empty($categories)) {
			foreach($categories as $cat) {
				$categoriesList[ $cat->cat_ID ] = $cat->name;
			}
		}
		$monthes = array(0 => __('Every month'));
		foreach(utilsSub::getMonthesArray() as $mId => $mName) {
			$monthes[ (int)$mId ] = $mName;
		}
		$days = array(0 => __('Every day'));
		foreach(utilsSub::getDaysArray() as $dId => $dName) {
			$days[ $this->getModule()->getDayNum($dName) ] = $dName;
		}
		for($i = 1; $i <= 31; $i++) {
			$days[ $i ] = $i. 'th';
		}
		$hours = array(0 => __('Every hour'));
		for($i = 0; $i <= 23; $i++) {
			$hours[ ($i == 0 ? 24 : $i) ] = $i. ':00';
		}
		$sendingTimeOptions = array(
			SUB_TIME_IMMEDIATELY => __('Immediately'),
			SUB_TIME_APPOINTED => __('At the Appointed Time'),
		);
		
		$this->assign('categoriesList', $categoriesList);
		$this->assign('optModel', frameSub::_()->getModule('options')->getModel());
		$this->assign('monthes', $monthes);
		$this->assign('days', $days);
		$this->assign('hours', $hours);
		$this->assign('sendingTimeOptions', $sendingTimeOptions);
		return parent::getContent('newslettersEditForm');
	}
	public function getSendStat() {
		return parent::getContent('newslettersSendStat');
	}
	public function getSelectTpl() {
		return parent::getContent('newslettersSelectTpl');
	}
	public function getTplSelecting() {
		$this->assign('templates', frameSub::_()->getModule('stpl')->getModel()->getList(array('protected' => 1)));
		return parent::getContent('newslettersTplSelecting');
	}
	public function getDisplayColumns() {
		if(empty($this->_displayColumns)) {
			$this->_displayColumns = array(
				'id'				=> array('label' => __('ID'), 'db' => 'id'),
				'list_subject_line' => array('label' => __('Subject'), 'db' => 'subject'),
				'status_label'		=> array('label' => __('Status'), 'db' => 'status'),
				'list_label_str'	=> array('label' => __('Lists'), 'db' => 'list_label_str'),
				'modified'			=> array('label' => __('Modified'), 'db' => 'date_created'),
				'date_sent_tbl'		=> array('label' => __('Date Sent'), 'db' => 'date_sent'),
			);
		}
		return $this->_displayColumns;
	}
	public function getListSubjectLine($newsletter) {
		$this->assign('newsletter', $newsletter);
		return parent::getContent('newslettersListSubjectLine');
	}
}
