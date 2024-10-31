<?php
class tableEmail_sentSub extends tableSub {
    public function __construct() {
        $this->_table = '@__email_sent';
        $this->_id = 'id';
        $this->_alias = 'toe_email_sent';
        $this->_addField('subscriber_id', 'text', 'int', '', __('subscriber_id'))
				->_addField('newsletter_id', 'text', 'int', '', __('newsletter_id'))
				->_addField('date_sent', 'text', 'int', '', __('date_sent'))
				->_addField('date_opened', 'text', 'int', '', __('date_opened'))
				->_addField('status', 'text', 'int', '', __('status'))
				->_addField('error_msg', 'text', 'text', '', __('error_msg'));
    }
}