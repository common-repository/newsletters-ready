function stplCanvasElementsFabric() {
	this.elements = [];
}
stplCanvasElementsFabric.prototype.push = function(element) {
	return this.elements.push( element );
};
stplCanvasElementsFabric.prototype.clear = function() {
	if(this.elements && this.elements.length) {
		for(var i in this.elements) {
			this.elements[i].close();
		}
		this.elements = [];
	}
};
stplCanvasElementsFabric.prototype.clearById = function(id) {
	if(this.elements && this.elements[ id ]) {
		this.elements[ id ].close();
		this.elements.splice(id, 1);
	}
};
stplCanvasElementsFabric.prototype.outClick = function(event) {
	if(this.elements 
		&& this.elements.length 
		&& event 
		&& typeof(event.pageX) !== 'undefined'
		&& typeof(event.pageY) !== 'undefined'
	) {
		if(event.target && 
			(
			jQuery(event.target).attr('id') === 'insert-media-button'
			|| jQuery(event.target).parents('.media-modal').size()
			|| jQuery(event.target).parents('.ui-dialog').size()
			)
		) {
			return;
		}
		for(var i in this.elements) {
			var editableArea = this.elements[i].getEditableArea();
			if(editableArea) {
				var offset	= editableArea.offset()
				,	width	= editableArea.width()
				,	height	= editableArea.height();
				if(!(event.pageX > offset.left && event.pageX < (offset.left + width)
					&& event.pageY > offset.top && event.pageY < (offset.top + height))
				) {
					this.clearById( i );
				}
			}
		}
	}
};
stplCanvasElementsFabric.prototype.isAjaxType = function(type) {
	return toeInArraySub(type, ['stplCanvasElementStaticContent', 'stplCanvasElementNewContent']);
};
function stplCanvasElement(item, options) {
	options = options || {};
	this.item = item;
	this.opened = false;
	this.code = 'base';
	this.editableArea = false;
	// For elements that will have form or editor dialogs
	//this.form = null;
	this.editorDialog = null;
	this.editorDialogClosed = false;
	this.elementClassName = options.elementClassName;
	//this.init();
}
stplCanvasElement.prototype.getParentRowContent = function() {
	return this.item.parents('.subStplCanvasRowContent:first');
};
/**
 * Retrive min cell height from current row element
 */
stplCanvasElement.prototype.getMaxColHeight = function() {
	var rowContent = this.getParentRowContent()
	,	maxColHeight = 0;
	rowContent.find('.subStplCanvasColContent').not(this.item).each(function(){
		var currentHeight = stplCanvasGetTotalHeight( jQuery(this).html() );
		if(currentHeight > maxColHeight)
			maxColHeight = currentHeight;
	});
	return maxColHeight;
};
stplCanvasElement.prototype.isOpened = function() {
	return this.opened;
};
stplCanvasElement.prototype.init = function() {
	this.opened = true;
};
stplCanvasElement.prototype.close = function() {
	this.opened = false;
	this.item.attr('data-element', this.elementClassName);
};
stplCanvasElement.prototype.getItem = function() {
	return this.item;
};
stplCanvasElement.prototype.getEditableArea = function() {
	return this.editableArea;
};
stplCanvasElement.prototype.makeAjaxContent = function(sendData, hideData) {
	var cellHideData = jQuery('<div class="stplCanvasCellAjaxHideData" />')
	,	cellAjaxData = jQuery('<div class="stplCanvasCellAjaxData" />');
	if(hideData)
		cellHideData.html( hideData );
	this.item.html('').append( cellHideData ).append( cellAjaxData );
	sendData.reqType = 'ajax';
	jQuery.sendFormSub({
		msgElID: cellAjaxData
	,	data: sendData
	,	onSuccess: function(res) {
			if(!res.error) {
				cellAjaxData.html( res.html );
			}
		}
	});
	
};
// Text editor
function stplCanvasElementText(item, options) {
	stplCanvasElementText.superclass.constructor.apply(this, arguments);
}
extendSub(stplCanvasElementText, stplCanvasElement);
stplCanvasElementText.prototype.init = function() {
	stplCanvasElementText.superclass.init.apply(this, arguments);
	var initialHtml = this.item.html()
	,	editorShell = jQuery('#stplCanvasTextEditorShell')
	,	maxEditorTollbarWidth = 0
	,	cellWidth = this.item.width()
	,	cellHeight = this.item.height()
	,	allToolbarButtons = []
	,	editor = tinyMCE.get('stplCanvasTextEditor')

	if(cellHeight < 220) {
		this.item.parents('.subStplCanvasRowContent:first').height( 220 );
		cellHeight = 220;
	}
	// Show shell with text editor
	editorShell.show();
	// Make sure that it switched to tinyMCE
	switchEditors.switchto( jQuery('#stplCanvasTextEditor-tmce').get(0) );
	// Find max tollbar width for current set
	editorShell.find('.mceToolbar .Enabled').each(function(){
		var toolbarWidth = jQuery(this).width();
		maxEditorTollbarWidth = toolbarWidth > maxEditorTollbarWidth ? toolbarWidth : maxEditorTollbarWidth;
	});
	// Find all buttons names in editor toolbars
	var i = 0;
	while(1) {
		i++;
		if(!editor.settings['theme_advanced_buttons'+ i] || editor.settings['theme_advanced_buttons'+ i] === '')
			break;
		var buttonsLine = editor.settings['theme_advanced_buttons'+ i].split(',');
		if(buttonsLine && buttonsLine.length) {
			allToolbarButtons = allToolbarButtons.concat(buttonsLine);
		} else
			break;
	}

	var j = 1
	,	currentApplyButtons = []
	,	currentButtonsWidth = 0
	,	toolbarRowsNum = 1;

	for(var i in allToolbarButtons) {
		var currentButtonWidth = allToolbarButtons[i] === '|' ? 0 : jQuery('#stplCanvasTextEditor_'+ allToolbarButtons[i]).parents('td:first').width();
		currentButtonsWidth += currentButtonWidth;
		if((currentButtonsWidth + 12) < cellWidth) {	// 12px - is margin for toolbar table row
			currentApplyButtons.push( allToolbarButtons[i] );
		} else {
			editor.settings['theme_advanced_buttons'+ j] = currentApplyButtons.join(',');
			currentApplyButtons = [ allToolbarButtons[i] ];
			currentButtonsWidth = currentButtonWidth;
			j++;
		}
	}
	if(currentApplyButtons.length) {
		editor.settings['theme_advanced_buttons'+ j] = currentApplyButtons.join(',');
		j++;
	}
	toolbarRowsNum = j;
	while(editor.settings['theme_advanced_buttons'+ j] && editor.settings['theme_advanced_buttons'+ j] !== '') {
		editor.settings['theme_advanced_buttons'+ j] = '';
		j++;
	}
	editor.setContent( initialHtml );
	jQuery('#stplCanvasTextEditor').height('100%');
	tinyMCE.execCommand('mceRemoveEditor', false, 'stplCanvasTextEditor');
	this.item.html('').append( editorShell );
	tinyMCE.execCommand('mceAddEditor', false, 'stplCanvasTextEditor');

	var mceIfrHeight = cellHeight 
			- jQuery('#wp-stplCanvasTextEditor-editor-tools').height() 
			- jQuery('#stplCanvasTextEditor_toolbargroup').height() 
			- jQuery('#stplCanvasTextEditor_path_row').height()
			- toolbarRowsNum * 15;
	if(mceIfrHeight < 60)	// Min editor height
		mceIfrHeight = 60;
	tinyMCE.DOM.setStyle(tinyMCE.DOM.get("stplCanvasTextEditor" + '_ifr'), 'height', mceIfrHeight+ 'px');
	
	this.editableArea = editorShell;
};
stplCanvasElementText.prototype.close = function() {
	var editor = tinyMCE.get('stplCanvasTextEditor')
	,	newContent = editor.getContent()
	,	newContentTotalHeight = stplCanvasGetTotalHeight( newContent )
	,	maxColHeight = this.getMaxColHeight();
	// Hide editor
	jQuery('body').append( jQuery('#stplCanvasTextEditorShell').hide() );
	// Insert content into cell
	this.item.html( newContent );
	if(newContentTotalHeight > maxColHeight) {
		// Minimum height of each cell - 20px
		if(newContentTotalHeight < 20)
			newContentTotalHeight = 20;
		this.item.parents('.subStplCanvasRowContent:first').height( newContentTotalHeight );
	}
	stplCanvasElementText.superclass.close.apply(this, arguments);
};
// New content
function stplCanvasElementNewContent(item, options) {
	stplCanvasElementNewContent.superclass.constructor.apply(this, arguments);
}
extendSub(stplCanvasElementNewContent, stplCanvasElement);
stplCanvasElementNewContent.prototype.init = function() {
	stplCanvasElementNewContent.superclass.init.apply(this, arguments);
	this.editorDialogClosed = false;
	var self = this
	,	doneBtn = jQuery('<input type="button" value="Done" class="button-primary stplCanvasNewContentDoneBtn" />').click(function(){
		self.editorDialog.dialog('close');
		return false;
	})
	//,	currentContent = this.item.html()
	,	shortcodeData = stplCanvasParseShortcode(this.item.find('.stplCanvasCellAjaxHideData').html())
	,	contentShell = jQuery('.subStplCanvasNewContentShell:first').show();

	if(!contentShell.find('.stplCanvasNewContentDoneBtn').size()) {
		contentShell.append('<br />').append(doneBtn);
		var categoriesSelect = contentShell.find('[name=category]');
		buildAjaxSelect(categoriesSelect, {page: 'stpl', action: 'getPostsCategoriesListForSelect'}, {
			selectTxt: 'Any'
		,	itemsKey: 'categories'
		,	idNameKeys: {id: 'cat_ID', name: 'name'}
		,	selectedValue: shortcodeData ? shortcodeData.category : 0
		,	titlePrepareCallback: function(title, item) {
				title += ' ('+ item.category_count+')';
				return title;
			}
		});
		contentShell.find('[name=category]');
	}
	if(shortcodeData) {
		for(var key in shortcodeData) {
			contentShell.find('[name="'+ key+ '"]').val( shortcodeData[key] );
		}
	}
	this.editorDialog = toeShowDialogCustomized(contentShell, {
		height:		'auto'
	,	modal:		true
	,	closeOnBg:	true
	,	close: function() {
			self.editorDialogClosed = true;
			self.close();
		}
	});
};
stplCanvasElementNewContent.prototype.close = function() {
	var shortcode = '';
	if(this.editorDialog && this.editorDialogClosed) {
		var keysForCode = ['title_style', 'title_align', 'show_content', 'posts_num', 'category', 'content_styles', 'image_align', 'read_more_text', 'read_more_styles'];
		shortcode = '[new_content_ready';
		for(var i in keysForCode) {
			shortcode += ' '+ keysForCode[i]+ '="'+ this.editorDialog.find('[name="'+ keysForCode[i]+ '"]').val()+ '"';
		}
		/*if(subNewslettersCurrent && subNewslettersCurrent.id) {
			shortcode += ' newsletter_id="'+ subNewslettersCurrent.id+ '"';
		}*/
		shortcode += ']';
		if(shortcode && shortcode !== '')
			this.applyAjax( shortcode );
		if(this.editorDialog && !this.editorDialogClosed) {
			this.editorDialog.dialog('close');
		}
		this.editorDialogClosed = false;
	}
	stplCanvasElementNewContent.superclass.close.apply(this, arguments);
};
stplCanvasElementNewContent.prototype.applyAjax = function(content) {
	this.makeAjaxContent({
		mod: 'stpl'
	,	action: 'getShortcodeHtml'
	,	shortcode: content
	}, content);
};
// Images
function stplCanvasElementImage(item, options) {
	stplCanvasElementImage.superclass.constructor.apply(this, arguments);
}
extendSub(stplCanvasElementImage, stplCanvasElement);
stplCanvasElementImage.prototype.init = function() {
	stplCanvasElementImage.superclass.init.apply(this, arguments);
	var _custom_media = true
	,	_orig_send_attachment = wp.media.editor.send.attachment
	,	self = this;
	wp.media.editor.send.attachment = function(props, attachment){
		if ( _custom_media ) {
			var imgUrl = (attachment.sizes && attachment.sizes[ props.size ]) ? attachment.sizes[ props.size ].url : attachment.url
			,	imgItem = new Image()
			,	maxColHeight = self.getMaxColHeight()
			,	imgHeight = (attachment.sizes && attachment.sizes[ props.size ]) ? attachment.sizes[ props.size ].height : 0
			,	imgInsertItem = jQuery(props.link === 'none' ? '<div />' : '<a />');
			imgItem.src = imgUrl;
			imgItem.alt = attachment.alt;
			imgItem.title = attachment.title;
			switch(props.link) {
				case 'file':
					imgInsertItem.attr('href', attachment.url);
					break;
				case 'post':
					imgInsertItem.attr('href', attachment.link);
					break;
				case 'custom':
					imgInsertItem.attr('href', props.linkUrl);
					break;
			}
			switch(props.align) {
				case 'left':
				case 'right':
					imgInsertItem.css( 'float', props.align );
					break;
				case 'center':
					jQuery(imgItem).css({
						'display': 'block'
					,	'margin-left': 'auto'
					,	'margin-right': 'auto'
					})
					break;
			}

			self.item.html( imgInsertItem.html( imgItem ) );

			if(imgHeight > maxColHeight) {
				if(!imgHeight)
					imgHeight = imgItem.height;
				self.item.parents('.subStplCanvasRowContent:first').height( imgHeight );
			}
			self.close();
		} else {
			return _orig_send_attachment.apply( this, [props, attachment] );
		};
	};
	wp.media.editor.open(false);

	jQuery('body').on('click', '.add_media', function(){
		_custom_media = false;
	});
};
// Dividers
function stplCanvasElementDivider(item, options) {
	stplCanvasElementDivider.superclass.constructor.apply(this, arguments);
}
extendSub(stplCanvasElementDivider, stplCanvasElement);
stplCanvasElementDivider.prototype.init = function() {
	stplCanvasElementDivider.superclass.init.apply(this, arguments);
	var self = this
	,	dividersShell = jQuery('.subStplCanvasDividersShell:first').clone().show();
	
	dividersShell.find('.subStplCanvasDividerOriginal').click(function(){
		var imgDivider = jQuery(this).find('img:first').clone()
		,	parentRow = self.item.parents('.subStplCanvasRowContent:first');
		
		parentRow.height( 'auto' );
		self.item.html( imgDivider );
		parentRow.height( parentRow.height() );
		self.close();
		return false;
	});
	this.editorDialog = toeShowDialogCustomized(dividersShell, {
		height:		'auto'
	,	modal:		true
	,	closeOnBg:	true
	,	close: function() {
			self.close();
		}
	});
};
stplCanvasElementDivider.prototype.close = function() {
	if(this.editorDialog)
		this.editorDialog.dialog('close');
	stplCanvasElementDivider.superclass.close.apply(this, arguments);
};
// Social icons
function stplCanvasElementSocial(item, options) {
	stplCanvasElementSocial.superclass.constructor.apply(this, arguments);
}
extendSub(stplCanvasElementSocial, stplCanvasElement);
stplCanvasElementSocial.prototype.init = function() {
	stplCanvasElementSocial.superclass.init.apply(this, arguments);
	var self = this
	,	socialShell = jQuery('.subStplCanvasSocialShell:first').show()
	,	doneBtn = jQuery('<input type="button" value="Done" class="button-primary stplCanvasSocDoneBtn" />').click(function(){
		self.close();
		return false;
	})
	,	selectedDesId = 1
	,	designsShell = socialShell.find('.subStplCanvasSocialDesignsShell')
	,	linksShell = socialShell.find('.subStplCanvasSocialLinksShell');
	// Clear all links from prev. usage
	linksShell.find('input[name^="link_"]').val('');
	// If there was before set of soc. icons - let's restore them in new edit window
	if(this.item.find('.stplCanvasSocSet').size()) {
		var currentSetShell = this.item.find('.stplCanvasSocSet');
		selectedDesId = parseInt(currentSetShell.find('.stplCanvasSocSetId').val());
		if(!selectedDesId)
			selectedDesId = 1;
		currentSetShell.find('a').each(function(){
			linksShell.find('input[name="link_'+ jQuery(this).attr('title')+ '"]').val( jQuery(this).attr('href') );
		});
	}
	function rebuildDesigns(desId) {
		var presentationShell = designsShell.find('.subStplCanvasSocialDesignPresentation');
		presentationShell.html('');
		linksShell.find('input[type=text][name^="link_"]').each(function(){
			var link = jQuery(this).val()
			,	socName = str_replace(jQuery(this).attr('name'), 'link_', '');
			presentationShell.append('<a href="'+ link+ '" target="_blank" title="'+ socName+ '"><img style="padding: 10px 10px 10px 0px;" src="'+ SUB_DATA.stplModPath+'img/soc_icons/'+ socName+ '-'+ desId+ '.png" /></a>');
		});
	}
	// First init
	if(!socialShell.find('.stplCanvasSocDoneBtn').size()) {
		socialShell.append('<br />').append(doneBtn);
		designsShell.buttonset()
		.find('input[name=social_design]').change(function(){
			var desId = jQuery(this).val();
			rebuildDesigns(desId);
		});
		linksShell.find('input[type=text][name^="link_"]').change(function(){
			var desId = designsShell.find('input[name=social_design]:checked').val();
			rebuildDesigns(desId);
		});
	}
	designsShell.find('input[name=social_design][value="'+ selectedDesId+ '"]').attr('checked', 'checked').change();

	this.editorDialog = toeShowDialogCustomized(socialShell, {
		height:		'auto'
	,	modal:		true
	,	closeOnBg:	true
	,	close: function() {
			self.close();
		}
	});
};
stplCanvasElementSocial.prototype.close = function() {
	var newIconsSet = this.editorDialog.find('.subStplCanvasSocialDesignPresentation').clone()
	,	desId = this.editorDialog.find('input[name=social_design]:checked').val()
	,	newHtml = jQuery('<div class="stplCanvasSocSet" style="text-align: center;" />');
	newIconsSet.find('a').each(function(){
		var href = jQuery(this).attr('href');
		if(!href || href === '')
			jQuery(this).remove();
	});
	newHtml.append( newIconsSet.html() )
		.append('<input type="hidden" class="stplCanvasSocSetId" value="'+ desId+ '" />');
	this.item.html( newHtml) ;
	
	if(this.editorDialog)
		this.editorDialog.dialog('close');
	stplCanvasElementSocial.superclass.close.apply(this, arguments);
};
// Static worpdress content
function stplCanvasElementStaticContent(item, options) {
	stplCanvasElementStaticContent.superclass.constructor.apply(this, arguments);
}
extendSub(stplCanvasElementStaticContent, stplCanvasElement);
stplCanvasElementStaticContent.prototype.init = function() {
	stplCanvasElementStaticContent.superclass.init.apply(this, arguments);
	this.editorDialogClosed = false;
	var self = this
	,	staticContentShell = jQuery('.subStplCanvasStaticContentShell:first').show()
	,	doneBtn = jQuery('<input type="button" value="Done" class="button-primary stplCanvasStaticContentDoneBtn" />').click(function(){
		self.editorDialog.dialog('close');
		return false;
	})
	,	shortcodeData = stplCanvasParseShortcode(this.item.find('.stplCanvasCellAjaxHideData').html());

	// First init
	if(!staticContentShell.find('.stplCanvasStaticContentDoneBtn').size()) {
		var postSelect = staticContentShell.find('[name=static_content_post]')
		,	pageSelect = staticContentShell.find('[name=static_content_page]');
		staticContentShell.append('<br />').append(doneBtn);
		staticContentShell.find('[name=static_content_post],[name=static_content_page]').change(function(){
			if(jQuery(this).attr('name') === 'static_content_post') {
				pageSelect.val('0');
			} else {
				postSelect.val('0');
			}
		});
		buildAjaxSelect(postSelect, {page: 'stpl', action: 'getPostsListForSelect'}, {
			selectTxt: 'Select Post'
		,	itemsKey: 'posts'
		,	idNameKeys: {id: 'ID', name: 'post_title'}
		,	selectedValue: shortcodeData ? shortcodeData.static_content_post : 0
		,	titlePrepareCallback: function(title, item) {
				if(item.post_status !== 'publish') {
					title += ' ('+ item.post_status+')';
				}
				return title;
			}
		});
		buildAjaxSelect(pageSelect, {page: 'stpl', action: 'getPagesListForSelect'}, {
			selectTxt: 'Select Page'
		,	itemsKey: 'posts'
		,	idNameKeys: {id: 'ID', name: 'post_title'}
		,	selectedValue: shortcodeData ? shortcodeData.static_content_page : 0
		,	titlePrepareCallback: function(title, item) {
				if(item.post_status !== 'publish') {
					title += ' ('+ item.post_status+')';
				}
				return title;
			}
		});
	}
	this.editorDialog = toeShowDialogCustomized(staticContentShell, {
		height:		'auto'
	,	modal:		true
	,	closeOnBg:	true
	,	close: function() {
			self.editorDialogClosed = true;
			self.close();
		}
	});
	// If there was before set of static cntent - let's restore it in new edit window
	if(shortcodeData) {
		for(var key in shortcodeData) {
			this.editorDialog.find('[name="'+ key+ '"]').val( shortcodeData[key] );
		}
	}
};
stplCanvasElementStaticContent.prototype.close = function() {
	if(this.editorDialog && this.editorDialogClosed) {
		var shortcode = ''
		,	keysForCode = ['static_content_post', 'static_content_page', 'static_title_style', 'static_title_align', 'static_show_content', 'content_styles', 'image_align', 'read_more_text', 'read_more_styles'];
		shortcode = '[static_content_ready';
		for(var i in keysForCode) {
			shortcode += ' '+ keysForCode[i]+ '="'+ this.editorDialog.find('[name="'+ keysForCode[i]+ '"]').val()+ '"';
		}
		shortcode += ']';
		this.applyAjax( shortcode );
		if(!this.editorDialogClosed) {
			this.editorDialog.dialog('close');
		}
		this.editorDialogClosed = false;
	}
	stplCanvasElementStaticContent.superclass.close.apply(this, arguments);
};
stplCanvasElementStaticContent.prototype.applyAjax = function(content) {
	this.makeAjaxContent({
		mod: 'stpl'
	,	action: 'getShortcodeHtml'
	,	shortcode: content
	}, content);
};