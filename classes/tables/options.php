<?php
class tableOptionsSub extends tableSub {
     public function __construct() {
        $this->_table = '@__options';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_opt';
        $this->_addField('id', 'text', 'int', 0, __('ID'))->
                _addField('code', 'text', 'varchar', '', __('Code'), 64)->
                _addField('value', 'text', 'varchar', '', __('Value'), 134217728)->
                _addField('label', 'text', 'varchar', '', __('Label'), 255)->
                _addField('description', 'text', 'text', '', __('Description'))->
                _addField('htmltype_id', 'selectbox', 'text', '', __('Type'))->
				_addField('cat_id', 'hidden', 'int', '', __('Category ID'))->
				_addField('sort_order', 'hidden', 'int', '', __('Sort Order'))->
				_addField('value_type', 'hidden', 'varchar', '', __('Value Type'));;
    }
}
?>
