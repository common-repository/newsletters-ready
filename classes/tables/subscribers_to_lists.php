<?php
class tableSubscribers_to_listsSub extends tableSub {
    public function __construct() {
        $this->_table = '@__subscribers_to_lists';
        $this->_id = 'subscriber_id';
        $this->_alias = 'toe_subscr_to_list';
        $this->_addField('subscriber_id', 'text', 'int', '', __('subscriber_id'))
				->_addField('subscriber_list_id', 'text', 'int', '', __('subscriber_list_id'));
    }
}
