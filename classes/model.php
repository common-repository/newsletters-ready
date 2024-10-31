<?php
abstract class modelSub extends baseObjectSub {
    protected $_data = array();
	protected $_code = '';
    
    public function init() {

    }
    public function get($d = array()) {

    }
    public function put($d = array()) {

    }
    public function post($d = array()) {

    }
    public function delete($d = array()) {

    }
    public function store($d = array()) {
        
    }
	public function setCode($code) {
        $this->_code = $code;
    }
    public function getCode() {
        return $this->_code;
    }
	public function getModule() {
		return frameSub::_()->getModule( $this->_code );
	}
}
