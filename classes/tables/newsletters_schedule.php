<?php
class tableNewsletters_scheduleSub extends tableSub {
    public function __construct() {
        $this->_table = '@__newsletters_schedule';
        $this->_id = 'id';
        $this->_alias = 'toe_newsletters_schedule';
        $this->_addField('newsletter_id', 'text', 'int', '', 'newsletter_id')
			->_addField('year', 'text', 'int', '', 'year')
			->_addField('month', 'text', 'int', '', 'month')
			->_addField('day', 'text', 'int', '', 'day')
			->_addField('hour', 'text', 'int', '', 'hour')
			->_addField('one_time', 'text', 'int', '', 'one_time');
    }
}

