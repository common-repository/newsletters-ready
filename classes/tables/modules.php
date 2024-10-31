<?php
class tableModulesSub extends tableSub {
    public function __construct() {
        $this->_table = '@__modules';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_m';
        $this->_addField('label', 'text', 'varchar', 0, __('Label'), 128)
                ->_addField('type_id', 'selectbox', 'smallint', 0, __('Type'))
                ->_addField('active', 'checkbox', 'tinyint', 0, __('Active'))
                ->_addField('params', 'textarea', 'text', 0, __('Params'))
                ->_addField('has_tab', 'checkbox', 'tinyint', 0, __('Has Tab'))
                ->_addField('description', 'textarea', 'text', 0, __('Description'), 128)
                ->_addField('code', 'hidden', 'varchar', '', __('Code'), 64)
                ->_addField('ex_plug_dir', 'hidden', 'varchar', '', __('External plugin directory'), 255);
    }
}
?>
