var subNewslettersPerPage = 10
,	subNewslettersTable = null
,	subNewslettersSendStatPerPage = 10
,	subNewslettersSendStatTable = null
,	subNewslettersCurrent = {};
jQuery(document).ready(function(){
	getNewslettersListSub();
	jQuery('#subAdminNewslettersBackButt').click(function(){
		getNewslettersListScreenSub();
		return false;
	});
	jQuery('#subAdminNewslettersEditForm').submit(function(){
		var clickedAction = jQuery(this).find('input[type=submit][clicked=true]').attr('name')
		,	appendData = {
			next_action: clickedAction
		};

		jQuery(this).sendFormSub({
			msgElID: 'subAdminNewslettersEditMsg'
		,	appendData: appendData
		,	onSuccess: function(res) {
				if(res.data.newsletter) {
					// fillNewsletterEditFormSub( res.data.newsletter );
					// Reload current newsletters page
					if(subNewslettersTable) {
						getNewslettersListSub();
					}
					getNewslettersEditSub( res.data.newsletter.id, res.data.newsletter );
				}
			}
		});
		return false;
	});
	jQuery('#subAdminNewslettersSendPreviewButt').click(function(){
		jQuery.sendFormSub({
			msgElID: 'subAdminNewslettersSendPreviewMsg'
		,	data: {
				page: 'newsletters'
			,	action: 'sendTest'
			,	reqType: 'ajax'
			,	email: jQuery('#subAdminNewslettersSendPreviewEmail').val()
			// Don't send ID - let's always send "fresh" version of template and not take it from database
			//,	stpl_id: jQuery('#subAdminNewslettersEditForm').find('input[name=stpl_id]').val()
			,	subject: jQuery('#subAdminNewslettersEditForm').find('input[name=subject]').val()
			,	id: jQuery('#subAdminNewslettersEditForm').find('input[name=id]').val()
			}
		,	onSuccess: function(res) {
				if(!res.error) {
					
				}
			}
		});
		return false;
	});
	jQuery('#subAdminNewslettersSaveTplForm').submit(function(){
		var nextAction = jQuery(this).find('input[type=submit][clicked=true]').attr('name')
		,	stplContent = stplCanvasGetCurrentContentSub();
		jQuery(this).sendFormSub({
			msgElID: 'subAdminNewslettersSaveTplMsg'
		,	appendData: {
				stpl_data: {
					rows:			stplContent.rows
				,	style_params:	stplContent.style_params
				,	id:				jQuery(this).find('[name=stpl_id]').val()
				}
			,	next_action: nextAction
			}
		,	onSuccess: function(res) {
				if(!res.error) {
					updateDataTableRow(subNewslettersTable, res.data.newsletterForTbl, res.data.newsletterForTbl[0]);
					if(nextAction !== 'save') {
						getNewslettersEditSub(res.data.newsletter.id, res.data.newsletter);
					}
					// Let's reload all list, just in case
					getNewslettersListSub();
				}
			}
		});
		return false;
	});
	jQuery('#subAdminNewslettersSendingTimeSelect').change(function(){
		switch(jQuery(this).val()) {
			case SUB_DATA.SUB_TIME_IMMEDIATELY:
				jQuery('#subAdminNewslettersSendingTimeParams').hide();
				break;
			default:
				jQuery('#subAdminNewslettersSendingTimeParams').show();
				break;
		}
	});
	jQuery('#subAdminNewslettersEditForm input[name=send_type]').change(function(){
		if(jQuery(this).val() === SUB_DATA.SUB_TYPE_NOW) {
			jQuery('#subAdminNewslettersSendingTimeShell').show();
		} else {
			jQuery('#subAdminNewslettersSendingTimeShell').hide();
		}
	});
	jQuery('#subAdminNewslettersResendButt').click(function(){
		if(subNewslettersCurrent.id) {
			jQuery.sendFormSub({
				msgElID: 'subAdminNewslettersResendMsg'
			,	data: {page: 'newsletters', action: 'resend', reqType: 'ajax', id: subNewslettersCurrent.id}
			});
		} else
			jQuery('#subAdminNewslettersResendMsg').html('Newsletter is not selected!');
		return false;
	});
	jQuery('#subAdminNewslettersBackToEditButt').click(function(){
		jQuery.sendFormSub({
			msgElID: 'subAdminNewslettersSendStatMsg'
		,	data: {page: 'newsletters', action: 'backToEdit', reqType: 'ajax', id: subNewslettersCurrent.id}
		,	onSuccess: function(res) {
				if(res.data.newsletter) {
					if(subNewslettersTable) {
						getNewslettersListSub();
					}
					getNewslettersEditSub( res.data.newsletter.id, res.data.newsletter );
				}
			}
		});
		return false;
	});
});
function toggleNewslettersFilterSub() {
	
}
function getNewslettersListSub(page) {
	this.page;	// Let's save page ID here, in static variable
	if(typeof(this.page) == 'undefined')
		this.page = 0;
	if(typeof(page) != 'undefined')
		this.page = page;

	var page = this.page;
	
	if(subNewslettersTable) {
		subNewslettersTable.fnDestroy();
	}
	var columns = [];
	if(subNewslettersColumns) {
		for(var key in subNewslettersColumns) {
			columns.push({
				mData: key, sClass: key
			});
		}
	}
	var dateInputs = {
			from: jQuery('<input type="text" size="12" placeholder="'+ toeLangSub('From')+ '" style="float: left; width: 90px;" />')
		,	to: jQuery('<input type="text" size="12" placeholder="'+ toeLangSub('To')+ '" style="float: left; width: 90px;" />')
		}
	,	dateShell = jQuery('<div />').css('float', 'left').css('width', '50%')
			.append('<span style="float: left; padding: 5px 5px 0px 0px;">Date Sent:</span>')
			.append(dateInputs.from)
			.append('<span style="float: left;padding-top: 5px;">/</span>')
			.append(dateInputs.to)
	,	listsSelect = jQuery('<select style="width: auto;" />')
	,	listsSelectShell = jQuery('<div style="float: left; padding-left: 10px;" />')
			.append( jQuery('<label>')
				.append('<span style="padding-top: 5px;">'+ toeLangSub('Have List')+ ':</span>')
				.append(listsSelect) )
	,	filterLink = jQuery('<a href="#"><i>'+ toeLangSub('add filter')+ '</i></a>')
	,	filterLinkShell = jQuery('<div class="toeNewslettersTblFilterLink" />').append( filterLink )
	,	filterShell = jQuery('<div style="display: none;" />').append(dateShell).append(listsSelectShell);
	subNewslettersTable = jQuery('#subAdminNewslettersListTbl').dataTable({
		bProcessing: true
	,	bServerSide: true
	,	sAjaxSource: createAjaxLinkSub({page: 'newsletters', action: 'getListForTable', reqType: 'ajax'})
	,	aoColumns: columns
	,	fnInitComplete: function() {
			var getSearchDateRange = function() {
				return dateInputs.from.val()+ '|'+  dateInputs.to.val();
			};
			jQuery('<button class="button button-primary" id="subAdminNewslettersAddButt">'+ toeLangSub('Create Nesletter')+ '</button>')
				.insertBefore('#subAdminNewslettersListTbl_length')
				.click(function(){
					getNewslettersEditSub();
					return false;
				});
			jQuery( filterLinkShell ).insertAfter('#subAdminNewslettersListTbl_filter');
			
			jQuery( filterShell ).insertAfter( filterLinkShell );
			filterLink.click(function(){
				filterShell.toggle();
				return false;
			});
			for(var key in dateInputs) {
				dateInputs[key].datepicker().change(function(){
					var settings = subNewslettersTable.fnSettings();
					if(settings && settings.aoPreSearchCols) {
						for(var i in columns) {
							if(columns[i].mData === 'date_sent_tbl') {
								settings.aoPreSearchCols[i].sSearch = getSearchDateRange();
								break;
							}
						}
						subNewslettersTable.fnDraw();
					}
				});
			}
			listsSelect.change(function(){
				var settings = subNewslettersTable.fnSettings();
				if(settings && settings.aoPreSearchCols) {
					for(var i in columns) {
						if(columns[i].mData === 'list_label_str') {
							settings.aoPreSearchCols[i].sSearch = jQuery(this).val();
							break;
						}
					}
					subNewslettersTable.fnDraw();
				}
			});
			jQuery('<br />').insertBefore( jQuery('#subAdminNewslettersListTbl') );
		}
	,	fnDrawCallback : function(settings) {
			if(settings && settings.jqXHR && settings.jqXHR.responseJSON && settings.jqXHR.responseJSON) {
				var allUsedLists = settings.jqXHR.responseJSON.allUsedLists
				,	currentValue = listsSelect.val();
				if(!currentValue) 
					currentValue = 0;	// Let it be Any selected by default
				listsSelect.find('option').remove();
				listsSelect.append('<option value="0">'+ toeLangSub('Any')+ '</option>');
				if(allUsedLists) {
					for(var i in allUsedLists) {
						listsSelect.append('<option value="'+ allUsedLists[i].id+ '">'+ allUsedLists[i].label+ '</option>');
					}
				}
				listsSelect.val( currentValue );
			}
		}
	});
}
function getNewslettersSendStatListSub(page, newsletterId) {
	this.page;	// Let's save page ID here, in static variable
	if(typeof(this.page) == 'undefined')
		this.page = 0;
	if(typeof(page) != 'undefined')
		this.page = page;

	var page = this.page;
	
	jQuery.sendFormSub({
		msgElID: 'subAdminNewslettersSendStatMsg'
	,	data: {page: 'newsletters', action: 'getSendStatList', reqType: 'ajax', limitFrom: page * subNewslettersSendStatPerPage, limitTo: subNewslettersSendStatPerPage, newsletterId: newsletterId}
	,	onSuccess: function(res) {
			if(!res.error) {
				if(page > 0 && res.data.count > 0 && res.data.list.length == 0) {	// No results on this page - 
					// Let's load next page
					getNewslettersSendStatListSub(page - 1);
				} else {
					subNewslettersSendStatTable = new toeListableSub({
						table: '#subAdminNewslettersSendStatTbl'
					,	paging: '#subAdminNewslettersSendStatPaging'
					,	list: res.data.list
					,	count: res.data.count
					,	perPage: subNewslettersSendStatPerPage
					,	page: page
					,	pagingCallback: getNewslettersSendStatListSub
					,	addPagingCallbackOpts: [newsletterId]	// this will pushed in callback when pressing next buttons
					});
				}
			}
		}
	});
}
function getNewslettersEditSub(id, newsletterData, forceStep) {
	subNewslettersCurrent = {};
	var stplLoadData = {
		toElement: '#subAdminNewslettersStplShell'
		}
	,	subListsData = {
		loadTo: '#subAdminNewslettersListsShell'
	};
	id = parseInt(id);
	if(!id)
		id = 0;
	var step = 0;
	
	jQuery('#subAdminNewslettersListShell').hide();
	jQuery('#subAdminNewslettersFormShell').hide();
	jQuery('#subAdminNewslettersSelectTplShell').hide();
	jQuery('#subAdminNewslettersTplSelectingShell').hide();
	jQuery('#subAdminNewslettersSendStatShell').hide();
	newslettersClearEditForm();
	jQuery('#subAdminNewslettersBackButt').show();

	if(id) {
		if(!newsletterData) 
			newsletterData = getDataTableRow(subNewslettersTable, id);//subNewslettersTable.getRowById(id);
		
		subNewslettersCurrent = newsletterData;
		if(newslettersIsStatus(newsletterData.status, 'new')) {
			if(parseInt(newsletterData.stpl_id))
				stplLoadData.id = newsletterData.stpl_id;
			step = 1;
		}
		if(newslettersIsStatus(newsletterData.status, 'tpl_selected')) {
			step = 2;
		}
		if(newslettersIsStatus(newsletterData.status, 'waiting')
			|| newslettersIsStatus(newsletterData.status, 'sent')
		) {
			step = 3;
		}
		if(newslettersIsStatus(newsletterData.status, 'first_step')) {
			step = 0;
		}
	}
	if(typeof(forceStep) !== 'undefined') {
		step = forceStep;
	}
	switch(step) {
		case 3:
			jQuery('#subAdminNewslettersSendStatShell').show();
			if(subNewslettersSendStatTable)
				subNewslettersSendStatTable.clear();
			getNewslettersSendStatListSub(0, newsletterData.id);
			break;
		case 2:
			jQuery('#subAdminNewslettersFormShell').show();
			fillNewsletterEditFormSub( newsletterData );
			break;
		case 1:
			jQuery('#subAdminNewslettersSelectTplShell').show();
			jQuery('#subAdminNewslettersSelectTplShell').find('input[name=stpl_id]').val( stplLoadData.id );
			jQuery('#subAdminNewslettersSelectTplShell').find('input[name=id]').val( id );
			if(newsletterData && newsletterData.subject) {
				stplLoadData.subject = newsletterData.subject;
			}
			loadStplSub( stplLoadData );
			break;
		case 0:
		default:
			jQuery('#subAdminNewslettersTplSelectingShell').find('input[name=id]').val('');
			jQuery('#subAdminNewslettersTplSelectingShell').find('input[name=subject]').val('');
			if(newsletterData && newsletterData.subject) {
				jQuery('#subAdminNewslettersTplSelectingShell').find('input[name=id]').val( newsletterData.id );
				// Leave clear for now
				//jQuery('#subAdminNewslettersTplSelectingShell').find('input[name=subject]').val( newsletterData.subject );
			}
			jQuery('#subAdminNewslettersTplSelectingShell').show();
			break;
	}
}

function newslettersLoadStplPrevList(options) {
	options = options || {};
	jQuery( options.toElement ).html('');
	var selectionList = jQuery('#subAdminNewslettersTplSelectingShell')
		.clone()
		.removeAttr('id')
		.show()
		.appendTo( options.toElement );
}

function getNewslettersListScreenSub() {
	jQuery('#subAdminNewslettersListShell').show();
	jQuery('#subAdminNewslettersFormShell').hide();
	jQuery('#subAdminNewslettersSelectTplShell').hide();
	jQuery('#subAdminNewslettersCreateNavShell').hide();
	jQuery('#subAdminNewslettersTplSelectingShell').hide();
	jQuery('#subAdminNewslettersBackButt').hide();
	jQuery('#subAdminNewslettersSendStatShell').hide();
}
function fillNewsletterEditFormSub(data) {
	data = data || {};
	var form = jQuery('#subAdminNewslettersEditForm');
	subSubersListsFulList({
		loadTo: '#subAdminNewslettersListsShell'
	,	selectedLists: data.list
	});
	var textFillIn = ['id', 'stpl_id', 'subject', 'from_name', 'from_email', 'reply_name', 'reply_email']
	,	haveDefault = ['from_name', 'from_email', 'reply_name', 'reply_email'];
	for(var i in textFillIn) {
		var value = data[ textFillIn[i] ];
		form.find('[name="'+ textFillIn[i]+ '"]').val( value );
		if(!value && inArray(textFillIn[i], haveDefault)) {
			var defaultName = 'default_'+ textFillIn[i]
			,	defaultValue = toeOptionSub(defaultName);
			if(defaultValue)
				form.find('[name="'+ textFillIn[i]+ '"]').val(defaultValue);
		}
	}
	data.send_params = data.send_params || {};
	data.send_params.new_content = typeof(data.send_params.new_content) === 'undefined' ? {} : data.send_params.new_content;
	data.send_params.schedule = data.send_params.schedule || {};
	data.send_params.sending_time = data.send_params.sending_time || {};
	
	// Send Type
	var sendType = data.send_type ? data.send_type : SUB_DATA.SUB_TYPE_NOW;
	form.find('[name="send_type"]').removeAttr('checked');
	form.find('[name="send_type"][value="'+ sendType+ '"]').attr('checked', 'checked').change();
	// New content send params
	form.find('[name="send_params[new_content][more_then]"]').val( data.send_params.new_content.more_then 
		? data.send_params.new_content.more_then 
		: 1 );	// 1 by default
	form.find('[name="send_params[new_content][tags]"]').val( data.send_params.new_content.tags 
		? data.send_params.new_content.tags 
		: SUB_DATA.SUB_ANY );	// any by default
	form.find('[name="send_params[new_content][categories][]"]').val( data.send_params.new_content.categories 
		? data.send_params.new_content.categories 
		: 0 );	// 0 is ANY id
	// Schedule send params
	form.find('[name="send_params[schedule][month]"]').val( data.send_params.schedule.month 
		? data.send_params.schedule.month  
		: 0 );	// 0 is Every id
	form.find('[name="send_params[schedule][days]"]').val( data.send_params.schedule.days 
		? data.send_params.schedule.days  
		: 0 );	// 0 is Every id
	form.find('[name="send_params[schedule][hours]"]').val( data.send_params.schedule.hours 
		? data.send_params.schedule.hours  
		: 0 );	// 0 is Every id
	// Send Time params
	form.find('[name="send_params[sending_time][type]"]').val( data.send_params.sending_time.type 
		? data.send_params.sending_time.type 
		: SUB_DATA.SUB_TIME_IMMEDIATELY ).trigger('change');	// immediately by default
	form.find('[name="send_params[sending_time][date]"]').val( data.send_params.sending_time.date 
		? data.send_params.sending_time.date 
		: '');
	form.find('[name="send_params[sending_time][time]"]').val( data.send_params.sending_time.time 
		? data.send_params.sending_time.time 
		: '');
	// Only for new users checkbox
	parseInt(data.send_params.send_only_new_users) 
		? form.find('[name="send_params[send_only_new_users]"]').attr('checked', 'checked')
		: form.find('[name="send_params[send_only_new_users]"]').removeAttr('checked');
}
function newslettersClearEditForm() {
	// Just fill it with empty values for now
	fillNewsletterEditFormSub();
	jQuery('#subAdminNewslettersTplSelectionSubject').val('');
	jQuery('#subAdminNewslettersTplSelectingShell').find('input[name=id]').val('')
}
function newslettersPreviewLinkSub(link) {
	var id = getRowIdValSub(link);
	if(id) {
		var newsletterData = getDataTableRow(subNewslettersTable, id);
		if(newsletterData) {
			stplCanvasPreviewInBrowser( newsletterData.stpl_id, {subject: newsletterData.subject} );
		}
	} else
		console.log('Can not edit empty ID!');
}
function newslettersEditLinkSub(link) {
	var id = getRowIdValSub(link);
	if(id) {
		getNewslettersEditSub(id);
	} else
		console.log('Can not edit empty ID!');
}
function newslettersDuplicateLinkSub(link) {
	var id = getRowIdValSub(link);
	if(id) {
		var msgEl = null;
		if(jQuery(link).parent().find('.subNewsletterDuplicateTmpMsg').size()) {
			msgEl = jQuery(link).parent().find('.subNewsletterDuplicateTmpMsg');
		} else {
			msgEl = jQuery('<div class="subNewsletterDuplicateTmpMsg"/>').insertAfter( link );
		}
		newslettersDuplicateSub({
			id: id
		,	msgEl: msgEl
		});
	}
}
function newslettersDeleteLinkSub(link) {
	var id = getRowIdValSub(link);
	if(id) {
		var newsletterData = getDataTableRow(subNewslettersTable, id);
		if(newsletterData) {
			var msgEl = jQuery('<div />').insertAfter( link );
			newslettersDeleteSub({
				id: id
			,	msgEl: msgEl
			});
		}
	}
}
function newslettersDuplicateSub(params) {
	params = params || {};
	jQuery.sendFormSub({
		msgElID: params.msgEl
	,	data: {page: 'newsletters', action: 'duplicate', reqType: 'ajax', id: params.id}
	,	onSuccess: function(res) {
			if(!res.error) {
				getNewslettersListSub();
			}
		}
	});
}
function newslettersDeleteSub(params) {
	params = params || {};
	if(confirm('Are you sure want to delete this Newsletter?')) {
		jQuery.sendFormSub({
			msgElID: params.msgEl
		,	data: {page: 'newsletters', action: 'remove', reqType: 'ajax', id: params.id}
		,	onSuccess: function(res) {
				if(!res.error) {
					removeDataTableRow(subNewslettersTable, params.id);
					getNewslettersListSub();
				}
			}
		});
	}
}
function newslettersIsStatus(checkStatusId, checkStatusKey) {
	// subNewslettersStatuses showl be defined in newslettersAdminTab.php
	return (parseInt(checkStatusId) === parseInt(subNewslettersStatuses[checkStatusKey]));
}
function newslettersSelectTplSub(clickedElement) {
	var tplCell = jQuery(clickedElement);
	var stpl_id = parseInt(tplCell.attr('stpl_id'));
	if(stpl_id) {
		// Clear all prev. messages
		jQuery('#subAdminNewslettersTplSelectingShell').find('.subAdminNewslettersTplSelectionMsg .subAdminNewslettersTplSelectionMsgTxt').html('');
		jQuery('#subAdminNewslettersTplSelectingShell').find('h2,img').css('opacity', '1');
		jQuery('#subAdminNewslettersTplSelectingShell').find('[name=subject]').removeClass('subInputError');
		
		var msgEl = jQuery(clickedElement).find('.subAdminNewslettersTplSelectionMsg .subAdminNewslettersTplSelectionMsgTxt');
		jQuery(clickedElement).find('h2,img').css('opacity', '0.5');
		jQuery.sendFormSub({
			msgElID: msgEl
		,	data: {
				page: 'newsletters'
			,	action: 'selectTemplate'
			,	reqType: 'ajax'
			,	id: jQuery('#subAdminNewslettersTplSelectingShell').find('input[name=id]').val()
			,	subject: tplCell.find('input[name=subject]').val()
			,	stpl_id: stpl_id
			}
		,	onSuccess: function(res) {
				if(res.error) {
					if(res.errors.subject) {
						tplCell.find('input[name=subject]').addClass('subInputError');
					}
				} else {
					tplCell.find('h2,img').css('opacity', '1');
					//newsletterForTbl
					updateDataTableRow(subNewslettersTable, res.data.newsletterForTbl, res.data.newsletterForTbl[0]);
					getNewslettersEditSub(res.data.newsletter.id, res.data.newsletter);
					// Let's reload all list, just in case
					getNewslettersListSub();
				}
			}
		});
	}
}
