var subSubersPerPage = 10
,	subSubersTable = null
,	subSubersListsPerPage = 20
,	subSubersListsTable = null
,	subSubersTotalSubscribers = 0
,	subSubersAllLists = []
,	subSubersAddForm = null
,	subSubersTblColumns = [];

jQuery(document).ready(function(){
	jQuery('#subSubAdminOptsForm').submit(function(){
		jQuery(this).sendFormSub({
			msgElID: 'subAdminSubOptionsMsg'
		});
		return false;
	});
	jQuery('#subSubscribersListsAddButt').click(function(){
		subSubscrbShowEditListForm();
		return false;
	});
	/*jQuery('#subSubscribersAddButt').click(function(){
		subSubscrbShowEditForm();
		return false;
	});*/
	/*jQuery('#subSubscribersFilterByListSel').change(function(){
		getSubersListSub();
	});*/
	getSubersListSub();
	getSubersListListsSub();
	//subSubscrbBuildListFilter();
});
function subSubscrbCloseAddForm(button) {
	jQuery(button).parents('form:first').dialog('close');
	return false;
}
function getSubersListSub(page) {
	this.page;	// Let's save page ID here, in static variable
	if(typeof(this.page) == 'undefined')
		this.page = 0;
	if(typeof(page) != 'undefined')
		this.page = page;
	if(subSubersTable) {
		subSubersTable.fnDestroy();
	}
	var page = this.page;

	var columns = [];
	if(subSubersTblColumns) {
		for(var key in subSubersTblColumns) {
			columns.push({
				mData: key, sClass: key
			});
		}
	}
	subSubersTable = jQuery('#subAdminSubersTable').dataTable({
		bProcessing: true
	,	bServerSide: true
	,	sAjaxSource: createAjaxLinkSub({page: 'subscribe', action: 'getListForTable', reqType: 'ajax'})
	,	aoColumns: columns
	,	fnInitComplete: function() {
			var	listsSelect = jQuery('<select style="width: auto;" id="subSubscribersFilterByListSel" />')
			,	listsSelectShell = jQuery('<div style="float: left; padding-left: 10px;" />')
					.append( jQuery('<label>')
						.append('<span style="padding-top: 5px;">'+ toeLangSub('Have List')+ ':</span>')
						.append(listsSelect) );
			jQuery('<button class="button" id="subSubscribersAddButt">'+ toeLangSub('Add New')+ '</button>')
				.insertBefore('#subAdminSubersTable_length')
				.click(function(){
					subSubscrbShowEditForm();
					return false;
				});
			jQuery( listsSelectShell ).insertAfter('#subAdminSubersTable_filter');
			
			subSubscrbBuildListFilter();
			listsSelect.change(function(){
				var settings = subSubersTable.fnSettings();
				if(settings && settings.aoPreSearchCols) {
					for(var i in columns) {
						if(columns[i].mData === 'list_label_str') {
							settings.aoPreSearchCols[i].sSearch = jQuery(this).val();
							break;
						}
					}
					subSubersTable.fnDraw();
				}
			});
		}
	,	fnDrawCallback : function(settings) {

		}
	});
}

function getSubersListListsSub(page) {
	this.page;	// Let's save page ID here, in static variable
	if(typeof(this.page) == 'undefined')
		this.page = 0;
	if(typeof(page) != 'undefined')
		this.page = page;
	
	var page = this.page;
	
	jQuery.sendFormSub({
		msgElID: 'subAdminSubersListsMsg'
	,	data: {page: 'subscribe', action: 'getListLists', reqType: 'ajax', limitFrom: page * subSubersListsPerPage, limitTo: subSubersListsPerPage}
	,	onSuccess: function(res) {
			if(!res.error) {
				if(page > 0 && res.data.count > 0 && res.data.list.length == 0) {	// No results on this page - 
					// Let's load next page
					getSubersListListsSub(page - 1);
				} else {
					subSubersListsTable = new toeListableSub({
						table: '#subAdminSubersListsTable'
					,	paging: '#subAdminSubersListsPaging'
					,	list: res.data.list
					,	count: res.data.count
					,	perPage: subSubersListsPerPage
					,	page: page
					,	pagingCallback: getSubersListListsSub
					});
					if(res.data.list) {
						for(var i in res.data.list) {
							if(parseInt(res.data.list[i].protected)) {
								subSubersListsTable.makeRowUneditable( res.data.list[i].id );
							}
						}
					}
				}
			}
		}
	});
}
function subSubscrbChangeStatus(link) {
	var id = getRowIdValSub(link);
	if(id) {
		jQuery.sendFormSub({
			msgElID: 'subAdminSubersMsg'
		,	data: {page: 'subscribe', action: 'changeStatus', reqType: 'ajax', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					if(jQuery(link).hasClass('active')) {
						jQuery(link).removeClass('active').addClass('disabled').html(toeLangSub('Activate')).parents('tr:first').find('.status').html(toeLangSub('disabled'));
					} else {
						jQuery(link).removeClass('disabled').addClass('active').html(toeLangSub('Deactivate')).parents('tr:first').find('.status').html(toeLangSub('active'));
					}
				}
			}
		});
	}
}
function subSubscrbRemove(link) {
	if(confirm(toeLangSub('Are you sure?'))) {
		var id = getRowIdValSub(link);
		if(id) {
			jQuery.sendFormSub({
				msgElID: 'subAdminSubersMsg'
			,	data: {page: 'subscribe', action: 'remove', reqType: 'ajax', id: id}
			,	onSuccess: function(res) {
					if(!res.error) {
						getSubersListSub();
					}
				}
			});
		}
	}
}
function subSubscrbListRemove(link) {
	var id = parseInt(jQuery(link).parents('tr').find('.id').val());
	if(confirm(toeLangSub('Are you sure want to delete list?'))) {
		if(id) {
			var msgEl = jQuery('<div />');
			jQuery(link).parents('td:first').append( msgEl );
			jQuery.sendFormSub({
				msgElID: msgEl
			,	data: {page: 'subscribe', action: 'removeList', reqType: 'ajax', id: id}
			,	onSuccess: function(res) {
					if(!res.error) {
						subSubscrbRemoveListFromAll(id);
						subSubersListsTable.removeRowById(id);
						getSubersListListsSub();
					}
				}
			});
		}
	}
}
function subSubscrbFillInListForm(data, form) {
	form.find('[name=id]').val( data.id );
	form.find('[name=label]').val( data.label );
	form.find('[name=description]').val( data.description );
}
function subSubscrbShowEditListForm(element) {
	var form = jQuery('#subAdminSubersListsForm').clone().removeAttr('id').show()
	,	msgEl = form.find('.subAdminSubersListsFormMsg:first');
	toeShowDialogCustomized(form, {
		height: 'auto'
	,	modal: true
	,	closeOnBg: true
	});
	if(element) {
		var id = parseInt(jQuery(element).parents('tr:first').find('.id').val());
		if(id) {
			var listData = subSubersListsTable.getRowById(id);
			if(listData) {
				subSubscrbFillInListForm(listData, form);
			}
		}			
	}
	form.submit(function(){
		jQuery(this).sendFormSub({
			msgElID: msgEl
		,	onSuccess: function(res) {
				if(!res.error) {
					getSubersListListsSub();
					if(res.data.list) {
						if(!element)	// Add action - add it to list filter
							subSubscrbAddListToAll( res.data.list );
						subSubscrbFillInListForm(res.data.list, form);
					}
				}
			}
		});
		return false;
	});
	subSubersAddForm = form;
}
function subSubscrbFillInForm(data, form) {
	form.find('[name=id]').val( data.id );
	form.find('[name=email]').val( data.email );
}
function subSubersListsFulList(options) {
	this.loaded;
	options = options || {};
	if(options.reset) {
		this.loaded = false;
		return false;
	}
	if(options.loadTo) {
		options.loadTo = jQuery( options.loadTo );
	}
	var loadToHtmlCallback = function(listsData){
		if(options.loadTo) {
			options.loadTo
				.html('')						// Clear it from prev. html
				.removeClass('subSuccessMsg');	// It can stay from ajax request as here was ajax responce
			for(var i in listsData) {
				var checkedStr = '';
				if(options.selectedLists && inArray(listsData[i].id, options.selectedLists))
					checkedStr = 'checked="checked"';
				options.loadTo.append('<label><input type="checkbox" name="list[]" value="'+ listsData[i].id+ '" '+ checkedStr+ ' />&nbsp;'+ listsData[i].label+ '</label><br />');
			}
		}
	};

	loadToHtmlCallback( subSubersAllLists );

	return subSubersAllLists;
}
function subSubscrbShowEditForm(element) {
	var form = jQuery('#subAdminSubersForm').clone().removeAttr('id').show()
	,	msgEl = form.find('.subAdminSubersFormMsg:first')
	,	selectedLists = [];
	toeShowDialogCustomized(form, {
		height: 'auto'
	,	modal: true
	,	closeOnBg: true
	});
	
	if(element) {
		var id = getRowIdValSub(element);
		if(id) {
			var subscriberData = getDataTableRow(subSubersTable, id);
			if(subscriberData) {
				subSubscrbFillInForm(subscriberData, form);
				if(subscriberData.list) {
					for(var i in subscriberData.list) {
						selectedLists.push( subscriberData.list[i] );
					}
				}
			}
		}
	}
	subSubersListsFulList({
		loadTo: form.find('.subAdminSubersFormListsShell:first')
	,	selectedLists: selectedLists
	});
	form.submit(function(){
		jQuery(this).sendFormSub({
			msgElID: msgEl
		,	onSuccess: function(res) {
				if(!res.error) {
					getSubersListSub();
					if(res.data.subscriber) {
						subSubscrbFillInForm(res.data.subscriber, form);
					}
				}
			}
		});
		return false;
	});
}
function subSubscrbBuildListFilter() {
	if(jQuery('#subSubscribersFilterByListSel').size()) {
		var currentSelectedOption = parseInt(jQuery('#subSubscribersFilterByListSel').val());
		if(!currentSelectedOption)
			currentSelectedOption = 0;

		jQuery('#subSubscribersFilterByListSel').find('option').remove();
		if(subSubersAllLists && subSubersAllLists.length) {
			for(var i in subSubersAllLists) {
				var subscribersCount = parseInt(subSubersAllLists[i].subscribers_count);
				if(!subscribersCount)
					subscribersCount = 0;
				jQuery('#subSubscribersFilterByListSel').append('<option value="'+ subSubersAllLists[i].id+ '">'+ subSubersAllLists[i].label+ ' ('+ subscribersCount+ ')</option>');
			}
		}
		jQuery('#subSubscribersFilterByListSel')
			.prepend('<option value="0">'+ toeLangSub('All')+' ('+ subSubersTotalSubscribers+ ')</option>')
			.find('option[value='+ currentSelectedOption+ ']')
			.attr('selected', 'selected');
	} else
		console.log('subSubscrbBuildListFilter CALLED BEFORE LIST WAS BUILD');
}
function subSubscrbRemoveListFromAll(listId) {
	// For some case if we do not find this ID in current set - don't reload select box
	var found = false;
	for(var i in subSubersAllLists) {
		if(subSubersAllLists[i].id == listId) {
			subSubersAllLists.splice(i, 1);
			found = true;
			break;
		}
	}
	if(found)
		subSubscrbBuildListFilter();
}
function subSubscrbAddListToAll(list) {
	subSubersAllLists.push( list );
	subSubscrbBuildListFilter();
}