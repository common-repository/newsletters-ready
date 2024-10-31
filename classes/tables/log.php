<?php
class tableLogSub extends tableSub {
    public function __construct() {
        $this->_table = '@__log';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_log';
        $this->_addField('id', 'text', 'int', 0, __('ID'), 11)
                ->_addField('type', 'text', 'varchar', '', __('Type'), 64)
                ->_addField('data', 'text', 'text', '', __('Data'))
                ->_addField('date_created', 'text', 'int', '', __('Date created'))
				->_addField('uid', 'text', 'int', 0, __('User ID'))
				->_addField('oid', 'text', 'int', 0, __('Order ID'));
    }
}