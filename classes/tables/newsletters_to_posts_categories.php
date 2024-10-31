<?php
class tableNewsletters_to_posts_categoriesSub extends tableSub {
    public function __construct() {
        $this->_table = '@__newsletters_to_posts_categories';
        $this->_id = 'newsletter_id';
        $this->_alias = 'toe_newsletters_to_posts_categories';
        $this->_addField('newsletter_id', 'text', 'int', '', 'newsletter_id')
				->_addField('cat_id', 'text', 'int', '', 'cat_id');
    }
}