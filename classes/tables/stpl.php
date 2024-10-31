<?php
class tableStplSub extends tableSub {
    public function __construct() {
        $this->_table = '@__stpl';
        $this->_id = 'id';
        $this->_alias = 'toe_stpl';
        $this->_addField('protected', 'text', 'int', '', 'protected')
				->_addField('category_id', 'text', 'int', '', 'category_id')
				->_addField('date_created', 'text', 'date', '', 'date_created')
				->_addField('style_params', 'text', 'text', '', 'style_params')
				->_addField('preview_img', 'text', 'text', '', 'preview_img')
				->_addField('label', 'text', 'text', '', 'label')
				->_addField('parent_id', 'text', 'int', '', 'parent_id');
    }
}