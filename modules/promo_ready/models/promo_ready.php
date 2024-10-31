<?php
class promo_readyModelSub extends modelSub {
	private $_apiUrl = '';
	public function __construct() {
		//$this->_apiUrl = implode('', array('ht','tp:','/','/r','ea','dy','sh','opp','i','n','gc','ar','t','.','c','o','m/'));
	}
	private function _getApiUrl() {
		if(empty($this->_apiUrl)) {
			$this->_apiUrl = 'http://54.68.191.217/';
		}
		return $this->_apiUrl;
	}
	public function welcomePageSaveInfo($d = array()) {
		$reqUrl = $this->_getApiUrl(). '?mod=options&action=saveWelcomePageInquirer&pl=rcs';
		$d['where_find_us'] = (int) $d['where_find_us'];
		$desc = '';
		if(in_array($d['where_find_us'], array(4, 5))) {
			$desc = $d['where_find_us'] == 4 ? $d['find_on_web_url'] : $d['other_way_desc'];
		}
		wp_remote_post($reqUrl, array(
			'body' => array(
				'site_url' => get_bloginfo('wpurl'),
				'site_name' => get_bloginfo('name'),
				'where_find_us' => $d['where_find_us'],
				'desc' => $desc,
				'plugin_code' => SUB_CODE,
			)
		));
		// In any case - give user posibility to move futher
		return true;
	}
}
