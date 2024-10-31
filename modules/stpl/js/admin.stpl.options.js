var subSortGlobalClone = null;
var stplEditorSettings = {};
var stplColPadding = 3;	// 2 for margin and 1 - for border
var stplColMinWidth = 15;	// Min width for cols
var stplElements = new stplCanvasElementsFabric();
var stplFontStyleKeys = ['font-family', 'font-size', 'color'];

function loadStplSub(options) {
	options = options || {};
	if(options.toElement) {
		options.toElement = jQuery(options.toElement);
		options.id = parseInt(options.id);
		jQuery.sendFormSub({
			msgElID: options.toElement
		,	data: {page: 'stpl', action: 'load', reqType: 'ajax', id: options.id}
		,	onSuccess: function(res) {
				if(!res.error) {
					console.time('tpl load');
					options.toElement.html( res.html );
					stplCanvasInitSub( options.toElement, res.data.stpl );
					
					if(res.data.stpl)
						stplCanvasFillWithContent(res.data.stpl, options);
					console.timeEnd('tpl load');
				}
			}
		});
	} else
		console.log('loadStplSub error - no element found!');
}

function stplCanvasAddRowSub(butt, options) {
	options = options || {};
	
	var canvasShell = jQuery(butt).hasClass('subStplCanvasShell') ? jQuery(butt) : jQuery(butt).parents('.subStplCanvasShell:first')
	,	canvas = canvasShell.find('.subStplCanvas:first')
	,	row = jQuery('<div class="subStplCanvasRow" />')
	,	rowContent = jQuery('<div class="subStplCanvasRowContent" />')
	,	rowSettings = canvasShell.find('.subStplCanvasRowSettings.subExample').clone().removeClass('subExample')
	,	colsNumBox = canvasShell.find('.subStplCanvasRowColumnsNumShell.subExample').clone().removeClass('subExample')
	,	rowBgColorPicker = rowSettings.find('.subStplCanvasRowIconBgColor');

	colsNumBox.hide();
	// Compose full row data
	row.append( rowContent.append(rowSettings).append(colsNumBox) );
	if(options.insertBefore) {
		row.insertBefore( options.insertBefore );
	} else if(options.insertAfter) {
		row.insertAfter( options.insertAfter );
	} else
		canvas.append( row );

	if(options.background_color && options.background_color !== '') {
		rowBgColorPicker.val( options.background_color );
		stplCanvasSetBgRowColorChange(rowContent, options.background_color);
	}
	rowBgColorPicker.wpColorPicker({
		change: function(event) {
			stplCanvasSetBgRowColorChange(rowContent, jQuery(event.target).val());
		}
	});
	rowBgColorPicker.change();

	// Cols num input manipulations
	rowSettings.find('.subStplCanvasRowIconColumns').click(function(){
		colsNumBox.toggle();
		// Recalc columns num if we toggled box on
		if(colsNumBox.is(':visible')) {
			colsNumBox.find('.subStplCanvasRowColumnsNumText').val( rowContent.find('.subStplCanvasCol').size() );
		}
		return false;
	});
	// TODO: make hide of colsNumBox on out click, bellow code will not work - it will hide this item when we click on it inner elements
	/*jQuery('html').click(function(){
		colsNumBox.hide();
	});*/
	colsNumBox.find('.subStplCanvasRowColumnsNumButt').click(function(){
		var newColsNum = parseInt(colsNumBox.find('.subStplCanvasRowColumnsNumText').val())
		,	oldColsNum = parseInt(rowContent.find('.subStplCanvasCol').size());
		if(newColsNum && newColsNum !== oldColsNum) {
			if(newColsNum > oldColsNum) {
				for(var i = oldColsNum; i < newColsNum; i++) {
					stplCanvasAddColSub(rowContent);
				}
			} else {
				for(var i = oldColsNum; i > newColsNum; i--) {
					stplCanvasRemoveColSub(rowContent);
				}
			}
		}
		colsNumBox.hide();
		dragMaster.recalcDropObjects();
		return false;
	});
	// Remove row setting
	rowSettings.find('.subStplCanvasRowIconRemove').click(function(){
		stplCanvasRemoveRowSub(row);
		return false;
	});
	
	rowContent.resizable({
		handles: 's'
	,	stop: function() {
			dragMaster.recalcDropObjects();
		}
	}).sortable({
		items:			'.subStplCanvasCol'
	,	handle:			'.subStplCanvasCellIconMove'
	,	placeholder: {
            element: function(item, ui) {
                return jQuery('<div class="subStplCanvasColSortHelper" />').css({
					'height': item.css('height')
				,	'width': item.css('width')
				}).html( item.html() );
            },
            update: function() {
                return;
            }
        }
	,	sort: function(event, ui) {
			var inserted = false;
			var i = 0;
			rowContent.find('.subStplCanvasCol:not(.ui-sortable-helper, .subStplCanvasColSortHelper)').each(function(){
				if(!inserted && ui.position.left < jQuery(this).position().left) {
					ui.placeholder.insertBefore( this );
					inserted = true;
				}
				i++;
			});
			if(!inserted) {
				ui.placeholder.insertAfter( rowContent.find('.subStplCanvasCol:not(.ui-sortable-helper, .subStplCanvasColSortHelper):last') );
			}
		}
	});
	stplCanvasAddColSub(rowContent);
	
	if(options.height) {
		rowContent.height( options.height );
	}
	return rowContent;
}
function stplCanvasExcludeFromSortingSub() {
	return ':not(.subStplCanvasCellSettings, .subStplCanvasCellSettings *, .subStplCanvasRowSettings, .subStplCanvasRowSettings *)';
}
function stplCanvasColsStopResize(element, ui, rowContent) {
	var parentOriginalWidth = rowContent.attr('original_width')
	,	innerColsSize = 0
	,	innerColsWidth = 0;

	rowContent.find('.subStplCanvasCol').each(function(){
		innerColsWidth += jQuery(this).width();
		innerColsSize++;
	});

	var colsWithPaddingWidth = innerColsWidth + 2 * innerColsSize * stplColPadding;
	if(colsWithPaddingWidth > parentOriginalWidth) {
		rowContent.width( parentOriginalWidth )
		jQuery(element)
			.width( jQuery(element).width() - (colsWithPaddingWidth - parentOriginalWidth) )
			.resizable('widget').trigger('mouseup');
		return true;
	}
	return false;
}
function stplCanvasAddColSub(rowContent, options) {
	options = options || {};
	var col = jQuery('<div class="subStplCanvasCol" />')
	,	colContent = jQuery('<div class="subStplCanvasColContent">')
	,	colSettings = rowContent.parents('.subStplCanvasShell:first').find('.subStplCanvasCellSettings.subExample').clone().removeClass('subExample')
	//,	canvasContentSettings = rowContent.parents('.subStplCanvasShell').next('.subStplCanvasSettings').find('.subStplCanvasSettingsContent')
	,	randContentId = getRandElIdSub('stplColContent_')
	,	randColId = getRandElIdSub('stplCol_')
	,	elementClass = options.element_class ? options.element_class : 'stplCanvasElementText';	// Text element by default
	// Conpose full cell data
	col.append(colSettings).append(colContent);
	if(options.insertBefore) {
		col.insertBefore( options.insertBefore );
	} else if(options.insertAfter) {
		col.insertAfter( options.insertAfter );
	} else
		rowContent.append( col );
	
	
	colContent.attr('id', randContentId).attr('data-element', elementClass);
	col.attr('id', randColId);
	
	rowContent.find('.subStplCanvasRowEnd').remove();
	rowContent.append( jQuery('<div class="subStplCanvasRowEnd" />') );
	
	col.resizable({
		handles: 'e'
	,	resize: function(event, ui) {
			var nextElement = ui.element.next();
			if(nextElement.size() && nextElement.hasClass('subStplCanvasCol')) {
				var originalWidth = parseInt(nextElement.attr('original_width'));
				if(!originalWidth) {
					originalWidth = nextElement.width();
					nextElement.attr('original_width', originalWidth);
				}
				var newWidth = (originalWidth - (ui.size.width - ui.originalSize.width));
				if(newWidth > stplColMinWidth)
					nextElement.width( newWidth+ 'px' );
				else {
					jQuery(this).resizable('widget').trigger('mouseup');
				}
			} else {
				var prevElement = ui.element.prev();
				if(prevElement.size()) {
					var originalWidth = parseInt(prevElement.attr('original_width'));
					if(!originalWidth) {
						originalWidth = prevElement.width();
						prevElement.attr('original_width', originalWidth);
					}
					prevElement.width( (originalWidth - (ui.position.left - ui.originalPosition.left))+ 'px' );
				}
			}
			stplCanvasColsStopResize(this, ui, rowContent);
		}
	,	stop: function() {
			dragMaster.recalcDropObjects();
		}
	,	start: function(event, ui) {
			if(!rowContent.attr('original_width'))
				rowContent.attr('original_width', rowContent.width());
			stplCanvasColsStopResize(this, ui, rowContent);
		}
	});
	// Edit setting
	colSettings.find('.subStplCanvasCellIconEdit').click(function(){
		var editClass = colContent.data('element');
		stplCanvasStartEdit( editClass, colContent );
		return false;
	});
	// Remove setting
	colSettings.find('.subStplCanvasCellIconRemove').click(function(){
		stplCanvasRemoveColSub(rowContent, col);
		return false;
	});
	
	if(options.width) {
		col.width( options.width );
	} else {
		stplCanvasUpdateColsWidth( rowContent );
	}
	if(options.content) {
		colContent.html( options.content );
	}
	var dropElement = new DropTarget( colContent.get(0) );
	colContent.attr('data-dropid', dropElement.getDropId());
	
	return colContent;
}
function stplCanvasRemoveRowSub(row) {
	row.remove();
}
function stplCanvasRemoveColSub(rowContent, col) {
	if(!col) {
		col = rowContent.find('.subStplCanvasCol:last');
	}
	dragMaster.removeDropObject( col.find('.subStplCanvasColContent:first').data('dropid') );
	col.remove();
	stplCanvasUpdateColsWidth(rowContent);
}
function stplCanvasCalcAvarageColsWidth(rowContent, colsNum) {
	colsNum = colsNum ? colsNum : rowContent.find('.subStplCanvasCol').size();
	return colsNum 
		? (rowContent.width() - 2 * stplColPadding - 2 * (colsNum - 1) * stplColPadding - 2 * 3 /*2*3 - don't know why for now*/) / colsNum 
		: 0;
}
function stplCanvasUpdateColsWidth(rowContent) {
	var newWidth = stplCanvasCalcAvarageColsWidth(rowContent);
	rowContent.find('.subStplCanvasCol').width(newWidth+ 'px');
	dragMaster.recalcDropObjects();
}
function stplCanvasGetCanvasFromSetElement(settingElement) {
	return jQuery(settingElement).parents('.subStplCanvasSettings:first').parent().find('.subStplCanvas:first');
}
function stplCanvasGetCanvasFromElement(element) {
	return jQuery(element).parents('.subStplCanvasShell:first').find('.subStplCanvas:first');
}
function stplCanvasGetSettingsFromSetElement(element) {
	return jQuery(element).parents('.subStplCanvasSettings:first');
}
function stplCanvasInitSub(toElement) {
	var canvas = jQuery( toElement ).find('.subStplCanvas')
	,	canvasSettings = jQuery( toElement ).find('.subStplCanvasSettings');
	// Generate unique ID for each canvas element
	canvas.attr('id', getRandElIdSub('stplCanvas_'));
	canvas.sortable({
		items: '.subStplCanvasRow'
	,	handle:	'.subStplCanvasRowIconMove'
	,	placeholder: {
            element: function(item, ui) {
                return jQuery('<div class="subStplCanvasRowSortHelper" />').css({
					'height': item.css('height')
				});
            },
            update: function() {
                return;
            }
        }
	});
	canvasSettings.tabs();
	canvasSettings.find('.subStplCanvasSettingBgTypeRadio input[type=radio]').change(function(){
		var shell = jQuery(this).parents('.subStplCanvasSettingBgTypeShell:first');
		shell.find('.subStplCanvasSettingBgTypeContainer').hide();
		shell.find('#'+ jQuery(this).attr('id')+ 'Container').show();
		switch(jQuery(this).val()) {
			case 'none':
				var canvas = stplCanvasGetCanvasFromSetElement(this);
				canvas.css({
					'background-image': 'none'
				,	'background-color': 'inherit'
				});
				break;
			case 'color':
				stplCanvasSetBgColorChange({
					target: canvasSettings.find('input[name=background_color]')
				});
				break;
			case 'image':
				canvasSettings.find('input[name=background_image]').trigger('change');
				canvasSettings.find('input[name=background_img_pos]:checked').trigger('change');
				break;
		}
	});
	canvasSettings.find('.subStplCanvasSettingBgTypeRadio, .subStplCanvasSettingBgImgPosRadio').buttonset();
	
	var _custom_media = true
	,	_orig_send_attachment = wp.media.editor.send.attachment;
	canvasSettings.find('.subStplCanvasSettingImageUploaderContainer .button').click(function(){
		var button = jQuery(this);
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				button.parents('.subStplCanvasSettingImageUploaderContainer:first').find('input[type=hidden]').val( attachment.url ).trigger('change');
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		};
		wp.media.editor.open(button);
		return false;
	});
	canvasSettings.find('.subStplCanvasSettingImageUploaderContainer .subStplCanvasSettingImageUploaderValue').change(function(){
		var imgUrl = jQuery(this).val();
		jQuery(this)
			.parents('.subStplCanvasSettingImageUploaderContainer:first')
			.find('.subStplCanvasSettingImageUploaderExample')
			.attr('src', imgUrl)
			.show();
		if(jQuery(this).parents('.subStplCanvasSettingBgTypeShell:first').find('input[name=background_type]:checked').val() === 'image') {	// If bg type - image - let's set it
			var canvas = stplCanvasGetCanvasFromSetElement(this);
			canvas.css({
				'background-image': 'url('+ imgUrl+ ')'
			});
		}
	});
	canvasSettings.find('.subStplCanvasSettingBgImgPosRadio input[type=radio]').change(function(){
		var canvas = stplCanvasGetCanvasFromSetElement(this);
		canvas
			.removeClass('subStplCanvasBgImgStretch')
			.removeClass('subStplCanvasBgImgCenter')
			.removeClass('subStplCanvasBgImgTile')
			.css('background-color', 'inherit');
		switch(jQuery(this).val()) {
			case 'stretch':
				canvas.addClass('subStplCanvasBgImgStretch');
				break;
			case 'tile':
				canvas.addClass('subStplCanvasBgImgTile');
				break;
			case 'center':
				canvas.addClass('subStplCanvasBgImgCenter');
				break;
		}
	});
	jQuery('body').on('click', '.add_media', function(){
		_custom_media = false;
	});
	
	jQuery('.subStplCanvasContentElementOriginal').each(function(){
		new DragObject(this);
	});
	stplCanvasSettingsPosition( canvas, canvasSettings, toElement );
	stplCanvasApplyAllFontStyles( canvas, canvasSettings );
	stplCanvasShowGrid( canvas );
}
function stplCanvasOnFontStyleChange(element) {
	if(typeof(element) === 'object') {
		// element.target is for changed colorpicker input - it return event, all other - just html object
		var changedElement = jQuery(element.target ? element.target : element)
		,	canvas = stplCanvasGetCanvasFromSetElement(changedElement)
		,	canvasSettings = stplCanvasGetSettingsFromSetElement(changedElement);
		stplCanvasApplyAllFontStyles(canvas, canvasSettings);
	}
}
function stplCanvasGetFontStyles(canvasSettings) {
	var stylesParsed = parseStr(canvasSettings.find('.subStplCanvasSettingStylesShell').serializeAnything());
	return stylesParsed && stylesParsed.font_style ? stylesParsed.font_style : false;
}
function stplCanvasApplyAllFontStyles(canvas, canvasSettings) {
	var styles = stplCanvasGetFontStyles(canvasSettings);
	if(styles) {
		var canvasId = canvas.attr('id')
		,	styleSheetId = canvasId+ '_styles'
		,	styleSheetDataArr = [];
		for(var key in styles) {
			switch(key) {
				default:
					var elStyleArr = []
					,	elSelector = '#'+ canvasId+ ' '+ styles[ key ].selector;
					for(var i in stplFontStyleKeys) {
						elStyleArr.push(stplFontStyleKeys[i]+ ':'+ styles[ key ][ stplFontStyleKeys[i] ]+ (styles[ key ].selector === '*' ? '' : '')+ ';');
					}
					if(inArray(styles[ key ].selector, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
						elStyleArr.push('font-weight:bold !important;');
					}
					styleSheetDataArr.push(elSelector+ ':not([class^="mce-"]):not([class^="wp"]):not([id^="mce"]):not(.button):not(.ed_button){'+ elStyleArr.join('')+ '}');
					break;
			}
		}
		jQuery('head').find('#'+ styleSheetId).remove();
		jQuery('head').append(
			jQuery('<style/>', {
				id:		styleSheetId
			,	html:	styleSheetDataArr.join(' ')
			})
		);
	}
}

function stplCanvasSettingsPosition(canvas, canvasSettings, toElement) {
	var toElementPosition = toElement.position();
	canvasSettings.css({
		'top': toElementPosition.top+ 'px'
	,	'left': (toElementPosition.left + canvas.width() + 80)+ 'px'
	});
}
function stplGetEditorOuterElement() {
	if(jQuery('#subStplCanvasEditorOuter').size())
		return jQuery('#subStplCanvasEditorOuter');
	return jQuery('<div id="subStplCanvasEditorOuter" >').appendTo('body');
}
function stplCanvasGetCurrentContentSub() {
	stplElements.clear();
	var result	= {}
	,	rows	= []
	,	row_i	= 0
	,	col_i	= 0
	,	styleParams = {};
	if(jQuery('.subStplCanvas').size()) {
		jQuery('.subStplCanvas').find('.subStplCanvasRow').each(function(){
			var rowBackgroundColor = jQuery(this).find('.subStplCanvasRowContent:first').css('background-color');
			if(rowBackgroundColor === 'transparent')
				rowBackgroundColor = '';
			rows[ row_i ] = {
				cols:	[]
			,	height: jQuery(this).height()
			,	background_color: rowBackgroundColor
			};
			col_i = 0;
			jQuery(this).find('.subStplCanvasCol').each(function(){
				var clonedContent = jQuery(this).find('.subStplCanvasColContent').clone()
				,	saveHtml = ''
				,	elementClass = clonedContent.data('element');
				clonedContent.find('img').show();
				
				if(stplElements.isAjaxType(elementClass)) {
					saveHtml = clonedContent.find('.stplCanvasCellAjaxHideData').html();
				} else {
					saveHtml = clonedContent.html();
				}
				rows[ row_i ].cols[ col_i ] = {
					width:			jQuery(this).width()
				,	content:		saveHtml
				,	element_class:	elementClass
				};
				col_i++;
			});
			row_i++;
		});
		if(jQuery('#subStplCanvasSettings').size()) {
			jQuery('#subStplCanvasSettings').find('input, select, textarea').each(function(){
				if(jQuery(this).attr('type') === 'radio' && !jQuery(this).attr('checked')) return;
				var inputName = jQuery(this).attr('name');
				if(!inputName) return;
				// Collect font styles in other way
				if(strpos(inputName, 'font_style[') === 0) return;
				styleParams[ inputName ] = jQuery(this).val();
			});
			styleParams['font_style'] = stplCanvasGetFontStyles( jQuery('#subStplCanvasSettings') );
		}
	}
	result = {
		rows:			rows
	,	style_params:	styleParams
	};
	return result;
}
function stplCanvasFillWithContent(stpl, options) {
	options = options || {};
	if(stpl.rows) {
		var canvasShell = jQuery( options.toElement ).find('.subStplCanvasShell');
		for(var i in stpl.rows) {
			var rowContent = stplCanvasAddRowSub(canvasShell, {
				height: stpl.rows[i].height
			,	background_color: stpl.rows[i].background_color
			});
			if(stpl.rows[i].cols) {
				stplCanvasRemoveColSub( rowContent );
				for(var j in stpl.rows[i].cols) {
					var colContent = stplCanvasAddColSub(rowContent, {
						width:			stpl.rows[i].cols[j].width
					,	content:		stpl.rows[i].cols[j].content
					,	element_class:	stpl.rows[i].cols[j].element_class
					});
					if(stplElements.isAjaxType(stpl.rows[i].cols[j].element_class)) {
						var newEditElement = new window[ stpl.rows[i].cols[j].element_class ]( jQuery(colContent), {
							elementClassName: stpl.rows[i].cols[j].element_class
						});
						newEditElement.applyAjax( stpl.rows[i].cols[j].content );
					}
				}
			}
		}
		
		// Try to fill in subject line
		var subjectInput = jQuery( options.toElement ).parents('form:first').find('[name=subject]');
		if(subjectInput && subjectInput.size()) {
			subjectInput.val( options.subject ? options.subject : '' );
		}
	}
	if(stpl.style_params) {
		var backgroundImgPos = stpl.style_params.background_img_pos ? stpl.style_params.background_img_pos : 'stretch'
		,	backgroundType = stpl.style_params.background_type ? stpl.style_params.background_type : 'none'
		,	canvasSettings = jQuery( options.toElement ).find('.subStplCanvasSettings');
		canvasSettings.find('input[name=background_type]').removeAttr('checked');
		canvasSettings.find('input[name=background_type][value="'+ backgroundType+ '"]').attr('checked', 'checked').trigger('change');
		canvasSettings.find('input[name=background_color]').wpColorPicker( 'color', stpl.style_params.background_color );
		canvasSettings.find('input[name=background_image]').val( stpl.style_params.background_image ).trigger('change');
		canvasSettings.find('input[name=background_img_pos]').removeAttr('checked');
		canvasSettings.find('input[name=background_img_pos][value="'+ backgroundImgPos+ '"]').attr('checked', 'checked').trigger('change');

		switch(backgroundType) {
			case 'color':
				stplCanvasSetBgColorChange({
					target: canvasSettings.find('input[name=background_color]')
				});
				break;
		}
		if(stpl.style_params.font_style) {
			for(var key in stpl.style_params.font_style) {
				for(var i in stplFontStyleKeys) {
					var fontStyleSetElement = canvasSettings.find('[name="font_style['+ key+ ']['+ stplFontStyleKeys[i]+ ']"]')
					,	fontStyleSetValue = stpl.style_params.font_style[ key ][ stplFontStyleKeys[i] ];
					if(stplFontStyleKeys[i] === 'color') {	// Special for colorpicker
						fontStyleSetElement.wpColorPicker( 'color', fontStyleSetValue );
					} else {
						fontStyleSetElement.val( fontStyleSetValue );
					}
				}
			}
		}
	}
	dragMaster.recalcDropObjects();
}
function stplCanvasPreviewInBrowserLinkClick(link) {
	var parentForm = jQuery(link).parents('form:first')
	,	idElement = parentForm ? parentForm.find('input[name=stpl_id]') : false
	,	id = idElement && idElement.size() ? idElement.val() : 0
	,	subject = parentForm ? parentForm.find('input[name=subject]').val() : ''
	,	msgEl = '';
	
	if(jQuery(link).parent().find('.stplCanvasTmpPrevMsgEl').size()) {
		msgEl = jQuery(link).parent().find('.stplCanvasTmpPrevMsgEl');
	} else {
		msgEl = jQuery('<div class="stplCanvasTmpPrevMsgEl"/>');
		jQuery(msgEl).insertAfter(link);
	}
	stplCanvasPreviewInBrowserWidthSave({
		idElement:	idElement
	,	id:			id
	,	msgEl:		msgEl
	,	subject:	subject
	});
}
function stplCanvasPreviewInBrowserWidthSave(options) {
	options = options || {};
	var stplContent = stplCanvasGetCurrentContentSub()
	,	saveData = {
		id:				options.id
	,	rows:			stplContent.rows
	,	style_params:	stplContent.style_params
	};
	// Let's save it from first
	jQuery.sendFormSub({
		msgElID: options.msgEl ? options.msgEl : ''
	,	data: {page: 'stpl', action: 'save', reqType: 'ajax', stpl: saveData}
	,	onSuccess: function(res) {
			if(!res.error) {
				if(options.idElement) {
					options.idElement.val( res.data.stpl.id );
				}
				stplCanvasPreviewInBrowser(res.data.stpl.id, options);
			}
		}
	});
	
}
function stplCanvasPreviewInBrowser(id, options) {
	options = options || {};
	var openUrl = 'admin.php?pl='+ SUB_DATA.SUB_CODE+ '&page=stpl&action=preview&id='+ id;
	if(options.subject)
		openUrl += '&subject='+ escape(options.subject);
	window.open( openUrl );
}

function stplCanvasSwitchImagesButtClick(butt) {
	var canvas = jQuery(butt).parents('.subStplCanvasShell:first').find('.subStplCanvas:first');
	jQuery(butt).attr('checked')
		? stplCanvasShowImages(canvas)
		: stplCanvasHideImages(canvas);
}
function stplCanvasSwitchGridButtClick(butt) {
	var canvas = stplCanvasGetCanvasFromElement(butt);
	jQuery(butt).attr('checked')
		? stplCanvasShowGrid(canvas)
		: stplCanvasHideGrid(canvas);
}
function stplCanvasHideImages(canvas) {
	jQuery(canvas).find('img').hide();
}
function stplCanvasShowImages(canvas) {
	jQuery(canvas).find('img').show();
}
function stplCanvasShowGrid(canvas) {
	jQuery('head').append(
		jQuery('<style/>', {
			id: 'stplCanvasGridStyles'
		,	html: '.subStplCanvasCol { border: 1px grey dashed; margin: 2px; border-radius: 5px; } .subStplCanvasRowContent { border: 1px dashed #000000; margin: 0px; border-radius: 5px; }'
		})
	);
}
function stplCanvasHideGrid(canvas) {
	jQuery('#stplCanvasGridStyles').remove();
}
function stplCanvasSetBgColorChange(event) {
	var canvas = stplCanvasGetCanvasFromSetElement(event.target);
	canvas.css({
		'background-color': jQuery(event.target).val()
	,	'background-image': 'none'
	});
}
function stplCanvasSetBgRowColorChange(rowContent, color) {
	rowContent.css({
		'background-color': color
	});	
}
jQuery(document).ready(function(){
	jQuery('html').mousedown(function(event){
		stplElements.outClick(event);
	});
});
function stplCanvasStartEdit(editElementClass, cellElement) {
	stplElements.clear();
	var newEditElement = new window[ editElementClass ]( jQuery(cellElement), {
		elementClassName: editElementClass
	});
	newEditElement.init();
	stplElements.push(newEditElement);
}
function stplCanvasGetTotalHeight(content) {
	var	heightCalcElement = jQuery('<div />').html( content )	// We will use this tmp dummy element to calc height of new content
	,	newContentTotalHeight = 0;
	jQuery('body').append( heightCalcElement );
	// Calc new total height included all children height and margins
	heightCalcElement.children().each(function(){
		newContentTotalHeight += jQuery(this).outerHeight(true); // true = include margins
	});
	heightCalcElement.remove();
	return newContentTotalHeight;
}
function stplCanvasParseShortcode(shortcode, codename) {
	if(!shortcode)
		return false;
	var res = {}
	,	parseExpression = /[\w-]+="[^"]*"/
	,	nameValStr = null;
	if((nameValStr = shortcode.match(parseExpression))) {
		while(nameValStr) {
			nameValStr = nameValStr[0];
			shortcode = str_replace(shortcode, nameValStr, '');
			var nameValArr = str_replace(nameValStr, '"', '').split('=');
			if(typeof(nameValArr[0]) !== 'undefined' && typeof(nameValArr[1]) !== 'undefined') {
				res[ nameValArr[0] ] = nameValArr[1];
			}
			nameValStr = shortcode.match(parseExpression)
		}
		return res;
	}
	return false;
}