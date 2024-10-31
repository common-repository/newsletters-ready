<?php
class tableSubscribers_listsSub extends tableSub {
    public function __construct() {
        $this->_table = '@__subscribers_lists';
        $this->_id = 'id';
        $this->_alias = 'toe_subscr_list';
        $this->_addField('label', 'text', 'varchar', '', __('label'))
			->_addField('description', 'text', 'text', '', __('description'))
			->_addField('protected', 'text', 'int', '', __('protected'));
    }
}
