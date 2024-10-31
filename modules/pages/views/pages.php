<?php
class pagesViewSub extends viewSub {
    public function displayDeactivatePage() {
        $this->assign('GET', reqSub::get('get'));
        $this->assign('POST', reqSub::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqSub::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqSub::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

