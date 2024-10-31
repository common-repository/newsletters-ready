<?php
class tableHtmltypeSub extends tableSub {
    public function __construct() {
        $this->_table = '@__htmltype';
        $this->_id = 'id';     
        $this->_alias = 'toe_htmlt';
        $this->_addField('id', 'hidden', 'int', 0, __('ID'))
            ->_addField('label', 'text', 'varchar', 0, __('Method'), 32)
            ->_addField('description', 'text', 'varchar', 0, __('Description'), 255);
    }
}
?>
