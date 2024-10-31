<?php
class tableNewsletters_to_tagsSub extends tableSub {
    public function __construct() {
        $this->_table = '@__newsletters_to_tags';
        $this->_id = 'newsletter_id';
        $this->_alias = 'toe_newsletters_to_tags';
        $this->_addField('newsletter_id', 'text', 'int', '', 'newsletter_id')
				->_addField('tag', 'text', 'varchar', '', 'tag');
    }
}