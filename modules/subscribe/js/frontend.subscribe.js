function subSubscribeFormSend(form) {
	jQuery(form).sendFormSub({
		msgElID: jQuery(form).find('.subSubscribeFormMsg:first')
	,	onSuccess: function(res) {
			if(!res.error) {
				jQuery(form).find('*:not(.subSubscribeFormSuccess)').remove();
				jQuery(form).find('.subSubscribeFormSuccess').show();
			}
		}
	});
}