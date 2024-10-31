<?php
class optionsSub extends moduleSub {
	protected $_uploadDir = 'sub';
	protected $_bgImgSubDir = 'bg_img';
	protected $_bgLogoImgSubDir = 'logo_img';

    /**
     * Method to trigger the database update
     */
    public function init(){
        parent::init();
        /*$add_option = array(
            'add_checkbox' => __('Add Checkbox'),
            'add_radiobutton' => __('Add Radio Button'),
            'add_item' => __('Add Item'),
        );
        frameSub::_()->addJSVar('adminOptions', 'TOE_LANG', $add_option);*/
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tabSub(__('General'), $this->getCode());
        $tab->setView('optionTab');
        $tab->setSortOrder(-99);
        $tabs[] = $tab;
        return $tabs;
    }
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($d = array()) {
        return $this->getController()->getModel()->get($d);
    }
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($d = array()) {
		return $this->getController()->getModel()->isEmpty($d);
	}
	
	public function getUploadDir() {
		return $this->_uploadDir;
	}
	public function getBgImgDir() {
		return $this->_uploadDir. DS. $this->_bgImgSubDir;
	}
	public function getBgImgFullDir() {
		return utilsSub::getUploadsDir(). DS. $this->getBgImgDir(). DS. $this->get('bg_image');
	}
	public function getBgImgFullPath() {
		return utilsSub::getUploadsPath(). '/'. $this->_uploadDir. '/'. $this->_bgImgSubDir. '/'. $this->get('bg_image');
	}
	
	public function getLogoImgDir() {
		return $this->_uploadDir. DS. $this->_bgLogoImgSubDir;
	}
	public function getLogoImgFullDir() {
		return utilsSub::getUploadsDir(). DS. $this->getLogoImgDir(). DS. $this->get('logo_image');
	}
	public function getLogoImgFullPath() {
		return utilsSub::getUploadsPath(). '/'. $this->_uploadDir. '/'. $this->_bgLogoImgSubDir. '/'. $this->get('logo_image');
	}
	public function getAllowedPublicOptions() {
		$res = array();
		if(is_admin()) {
			$alowedForPublic = array('default_from_name', 'default_from_email', 'default_reply_name', 'default_reply_email');
		} else {
			$alowedForPublic = array();
		}
		if(!empty($alowedForPublic)) {
			$allOptions = $this->getModel()->getByCode();
			foreach($alowedForPublic as $code) {
				if(isset($allOptions[ $code ]))
					$res[ $code ] = $allOptions[ $code ];
			}
		}
		return $res;
	}
}

