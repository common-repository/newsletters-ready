<?php
class tableStpl_colsSub extends tableSub {
    public function __construct() {
        $this->_table = '@__stpl_cols';
        $this->_id = 'id';
        $this->_alias = 'toe_stpl_cols';
        $this->_addField('stpl_row_id', 'text', 'int', '', 'stpl_row_id')
			->_addField('width', 'text', 'int', '', 'width')
			->_addField('content', 'text', 'text', '', 'content')
			->_addField('element_class', 'text', 'text', '', 'element_class');
    }
}
