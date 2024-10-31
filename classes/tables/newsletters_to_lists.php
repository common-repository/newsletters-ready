<?php
class tableNewsletters_to_listsSub extends tableSub {
    public function __construct() {
        $this->_table = '@__newsletters_to_lists';
        $this->_id = 'newsletter_id';
        $this->_alias = 'toe_newsletters_to_list';
        $this->_addField('newsletter_id', 'text', 'int', '', 'newsletter_id')
				->_addField('subscriber_list_id', 'text', 'int', '', 'subscriber_list_id');
    }
}