<p>
	<label for="<?php echo $this->widget->get_field_id('sub_form_title')?>"><?php _e('Subscribe form title')?>:</label><br />
    <?php 
        echo htmlSub::text($this->widget->get_field_name('sub_form_title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('sub_form_title'). '"', 
            'value' => $this->data['sub_form_title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('sub_enter_email_msg')?>"><?php _e('"Enter Email" message for your subscribe form')?>:</label><br />
    <?php 
        echo htmlSub::text($this->widget->get_field_name('sub_enter_email_msg'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('sub_enter_email_msg'). '"', 
            'value' => $this->data['sub_enter_email_msg']));
    ?><br />
	<label for="<?php echo $this->widget->get_field_id('sub_success_msg')?>"><?php _e('Message that user will see after subscribe')?>:</label><br />
    <?php 
        echo htmlSub::text($this->widget->get_field_name('sub_success_msg'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('sub_success_msg'). '"', 
            'value' => $this->data['sub_success_msg']));
    ?><br />
	<label for="<?php echo $this->widget->get_field_id('list')?>"><?php _e('Lists where subscribers should be added')?>:</label><br />
    <?php 
		if(!empty($this->allLists)) {
			if(!isset($this->data['list']) || !$this->data['list'])
				$this->data['list'] = array();
			foreach($this->allLists as $list) {
				echo '<label>';
				echo htmlSub::checkbox($this->widget->get_field_name('list'). '[]', array(
					'value' => $list['id'],
					'checked' => in_array($list['id'], $this->data['list'])
				));
				echo '&nbsp;';
				echo $list['label'];
				echo '</label><br />';
			}
		}
    ?><br />
</p>