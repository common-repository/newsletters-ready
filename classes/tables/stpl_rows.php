<?php
class tableStpl_rowsSub extends tableSub {
    public function __construct() {
        $this->_table = '@__stpl_rows';
        $this->_id = 'id';
        $this->_alias = 'toe_stpl_rows';
        $this->_addField('stpl_id', 'text', 'int', '', 'stpl_id')
				->_addField('height', 'text', 'int', '', 'height')
				->_addField('background_color', 'text', 'varchar', '', 'background_color');
    }
}
