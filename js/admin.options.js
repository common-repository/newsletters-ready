var subAdminFormChanged = [];
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(subAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	jQuery('#subAdminOptionsTabs').tabs().addClass('ui-tabs-vertical-res ui-helper-clearfix');
    jQuery('#subAdminOptionsTabs li').removeClass('ui-corner-top').addClass('ui-corner-left');
	
	jQuery('form input[type=submit]').click(function() {
		jQuery('input[type=submit]', jQuery(this).parents('form')).removeAttr('clicked');
		jQuery(this).attr('clicked', 'true');
	});
	
	jQuery('#subAdminOptionsForm').submit(function(){
		jQuery(this).sendFormSub({
			msgElID: 'subAdminMainOptsMsg'
		});
		return false;
	});
	// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
	var formsPreventLeave = ['subAdminOptionsForm', 'subSubAdminOptsForm', 'subAdminNewslettersSaveTplForm', 'subAdminNewslettersEditForm'];
	jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
		var formId = jQuery(this).parents('form:first').attr('id');
		changeAdminFormSub(formId);
	});
	jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
		var formId = jQuery(this).parents('form:first').attr('id');
		changeAdminFormSub(formId);
	});
	jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
		if(subAdminFormChanged.length) {
			var id = jQuery(this).attr('id');
			for(var i in subAdminFormChanged) {
				if(subAdminFormChanged[i] == id) {
					subAdminFormChanged.pop(i);
				}
			}
		}
	});
});
function toeShowModuleActivationPopupSub(plugName, action, goto) {
	action = action ? action : 'activatePlugin';
	goto = goto ? goto : '';
	jQuery('#toeModActivationPopupFormSub').find('input[name=plugName]').val(plugName);
	jQuery('#toeModActivationPopupFormSub').find('input[name=action]').val(action);
	jQuery('#toeModActivationPopupFormSub').find('input[name=goto]').val(goto);
	
	tb_show(toeLangSub('Activate plugin'), '#TB_inline?width=710&height=220&inlineId=toeModActivationPopupShellSub', false);
	var popupWidth = jQuery('#TB_ajaxContent').width()
	,	docWidth = jQuery(document).width();
	// Here I tried to fix usual wordpress popup displace to right side
	jQuery('#TB_window').css({'left': Math.round((docWidth - popupWidth)/2)+ 'px', 'margin-left': '0'});
}
function changeAdminFormSub(formId) {
	if(jQuery.inArray(formId, subAdminFormChanged) == -1)
		subAdminFormChanged.push(formId);
}

function toeShowDialogCustomized(element, options) {
	options = jQuery.extend({
		resizable: false
	,	width: 500
	,	height: 300
	,	closeOnEscape: true
	,	open: function(event, ui) {
			jQuery('.ui-dialog-titlebar').css({
				'background-color': '#222222'
			,	'background-image': 'none'
			,	'border': 'none'
			,	'margin': '0'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'color': '#CFCFCF'
			,	'height': '27px'
			});
			jQuery('.ui-dialog-titlebar-close').css({
				'background': 'url("'+ SUB_DATA.cssPath+ 'img/tb-close.png") no-repeat scroll 0 0 transparent'
			,	'border': '0'
			,	'width': '15px'
			,	'height': '15px'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'margin': '6px 6px 0'
			,	'float': 'right'
			}).html('');
			jQuery('.ui-dialog').css({
				'border-radius': '3px'
			,	'background-color': '#FFFFFF'
			,	'background-image': 'none'
			,	'padding': '1px'
			,	'z-index': '300000'
			});
			jQuery('.ui-dialog-buttonpane').css({
				'background-color': '#FFFFFF'
			});
			jQuery('.ui-dialog-title').css({
				'color': '#CFCFCF'
			,	'font': '12px sans-serif'
			,	'padding': '6px 10px 0'
			});
			jQuery('.ui-widget-overlay').css({
				'z-index': jQuery( event.target ).parents('.ui-dialog:first').css('z-index') - 1
			,	'background-image': 'none'
			,	'position': 'fixed'
			});
			if(options.openCallback && typeof(options.openCallback) == 'function') {
				options.openCallback(event, ui);
			}
			if(options.modal && options.closeOnBg) {
				jQuery('.ui-widget-overlay').unbind('click').bind('click', function() {
					jQuery( element ).dialog('close');
				});
			}
		}
	}, options);
	return jQuery(element).dialog(options);
}
