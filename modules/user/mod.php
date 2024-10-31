<?php
class userSub extends moduleSub {
    public function loadUserData() {
        return $this->getCurrent();
    }
    public function isAdmin() {
		if(!function_exists('wp_get_current_user')) {
			frameSub::_()->loadPlugins();
		}
        return current_user_can('administrator');
    }
	public function getCurrentUserPosition() {
		if($this->isAdmin())
			return SUB_ADMIN;
		else if($this->getCurrentID())
			return SUB_LOGGED;
		else 
			return SUB_GUEST;
	}
    public function getCurrent() {
        return $this->getController()->getModel('user')->get();
    }

    public function getCurrentID() {
        return $this->getController()->getModel()->getCurrentID();
    }
}

