<?php
class tableNewslettersSub extends tableSub {
    public function __construct() {
        $this->_table = '@__newsletters';
        $this->_id = 'id';
        $this->_alias = 'toe_subscr';
        $this->_addField('subject', 'text', 'varchar', '', 'subject')
				->_addField('active', 'text', 'tinyint', '', 'active')
				->_addField('status', 'text', 'tinyint', '', 'status')
				->_addField('stpl_id', 'text', 'int', '', 'stpl_id')
				->_addField('date_created', 'text', 'date', '', 'date_created')
				->_addField('send_type', 'text', 'text', '', 'send_type')
				->_addField('send_params', 'text', 'text', '', 'send_params')
				->_addField('from_name', 'text', 'varchar', '', 'from_name')
				->_addField('from_email', 'text', 'varchar', '', 'from_email')
				->_addField('reply_name', 'text', 'varchar', '', 'reply_name')
				->_addField('reply_email', 'text', 'varchar', '', 'reply_email')
				->_addField('date_sent', 'text', 'date', '', 'date_sent');
    }
}