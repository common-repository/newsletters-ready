<?php
class logViewSub extends viewSub {
    public function getList() {
        $this->assign('logs', frameSub::_()->getModule('logSub')->getModel()->getSorted());
        $this->assign('logTypes', frameSub::_()->getModule('logSub')->getModel()->getTypes());
        parent::display('logList');
    }
}