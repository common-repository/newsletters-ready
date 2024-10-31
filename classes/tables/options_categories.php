<?php
class tableOptions_categoriesSub extends tableSub {
    public function __construct() {
        $this->_table = '@__options_categories';
        $this->_id = 'id';     
        $this->_alias = 'toe_opt_cats';
        $this->_addField('id', 'hidden', 'int', 0, __('ID'))
            ->_addField('label', 'text', 'varchar', 0, __('Method'), 128);
    }
}
?>
