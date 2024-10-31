var gShowBordersSub = false;	// Show borders for drops - usefull for testings
var dragMaster = (function() {
    var dragObject
	,	dropObjects = []
	,	transDropObjectsH = []	// List of horisontal trans drops
	,	transDropObjectsV = []	// List of vertical trans drops
    ,	mouseDownAt
	,	currentDropTarget
	,	transDelta = 20;

	function mouseDown(e) {
		e = fixEvent(e);
		if (e.which != 1) return;

 		mouseDownAt = {
			x: e.pageX
		,	y: e.pageY
		,	element: this
		}
		addDocumentEventHandlers();
		return false;
	}
	function mouseMove(e){
		e = fixEvent(e);
		if (mouseDownAt) {
			if (Math.abs(mouseDownAt.x - e.pageX) < 5 && Math.abs(mouseDownAt.y - e.pageY) < 5) {
				return false;
			}
			var elem  = mouseDownAt.element;
			dragObject = elem.dragObject;
			var mouseOffset = getMouseOffset(elem, mouseDownAt.x, mouseDownAt.y);
			mouseDownAt = null;
			dragObject.onDragStart(mouseOffset);
		}
		dragObject.onDragMove(e.pageX, e.pageY);
		var newTarget = getCurrentTarget(e);
		if (currentDropTarget != newTarget) {
			if (currentDropTarget) {
				currentDropTarget.onLeave();
			}
			if (newTarget) {
				newTarget.onEnter();
			}
			currentDropTarget = newTarget;
		}
		return false;
    }
    function mouseUp() {
		if (!dragObject) {
			mouseDownAt = null;
		} else {
			if (currentDropTarget) {
				currentDropTarget.accept(dragObject);
				dragObject.onDragSuccess(currentDropTarget);
			} else {
				dragObject.onDragFail();
			}
			dragObject = null
		}
		removeDocumentEventHandlers()
    }
	function getMouseOffset(target, x, y) {
		var docPos = getOffset(target);
		return {
			x: x - docPos.left
		,	y: y - docPos.top
		};
	}
	function getCurrentTarget(e) {
		if (navigator.userAgent.match('MSIE')/* || navigator.userAgent.match('Gecko')*/) {
			var x = e.clientX, y = e.clientY;
		} else {
			var x = e.pageX, y = e.pageY;
		}
		// Horizontal trans drops is 1 priority - we will create new rows is this is possible
		if(transDropObjectsH.length) {
			for(var i in transDropObjectsH) {
				if(transDropObjectsH[i].isPointInside(x, y)) {
					return transDropObjectsH[i];
				}
			}
		}
		if(transDropObjectsV.length) {
			for(var i in transDropObjectsV) {
				if(transDropObjectsV[i].isPointInside(x, y)) {
					return transDropObjectsV[i];
				}
			}
		}
		if(dropObjects.length) {
			for(var i in dropObjects) {
				if(dropObjects[i].dropTarget.isPointInside(x, y))
					return dropObjects[i].dropTarget;
			}
		}
		return null;
	}
	function addDocumentEventHandlers() {
		document.onmousemove = mouseMove;
		document.onmouseup = mouseUp;
		document.ondragstart = document.body.onselectstart = function() {
			return false;
		};
	}
	function removeDocumentEventHandlers() {
		document.onmousemove = document.onmouseup = document.ondragstart = document.body.onselectstart = null;
	}
	function recalcTransDropObjects() {
		if(gShowBordersSub) {
			if(transDropObjectsH && transDropObjectsH.length) {
				for(var i in transDropObjectsH) {
					transDropObjectsH[i].remove();
				}
			}
			if(transDropObjectsV && transDropObjectsV.length) {
				for(var i in transDropObjectsV) {
					transDropObjectsV[i].remove();
				}
			}
		}
		transDropObjectsH = [];
		transDropObjectsV = [];
		if(dropObjects && dropObjects.length) {
			var rows = {}
			,	maxY = 0
			,	maxYId = 0
			,	minX = 9999999999
			,	maxX = 0;
			for(var i in dropObjects) {
				var x = dropObjects[i].dropTarget.x()
				,	y = dropObjects[i].dropTarget.y()
				,	x2 = dropObjects[i].dropTarget.x2()
				,	y2 = dropObjects[i].dropTarget.y2()
				,	yInt = parseInt(y);
				if(!rows[ yInt ])
					rows[ yInt ] = {maxId: i, maxX: x};
				else if(x > rows[ yInt ].maxX) {
					rows[ yInt ].maxX = x;
					rows[ yInt ].maxId = i;
				}
				transDropObjectsV.push( new TransDropTarget(
					x, 
					y, 
					x, 
					y2, 
					transDelta,
					dropObjects[i]) );
				transDropObjectsH.push( new TransDropTarget(
					x, 
					y, 
					x2, 
					y, 
					transDelta,
					dropObjects[i]) );
				if(y2 > maxY) {
					maxY = y2;
					maxYId = i;
				}
				minX = x < minX ? x : minX;
				maxX = x2 > maxX ? x2 : maxX;
			}
			for(var i in rows) {
				var x2 = dropObjects[ rows[i].maxId ].dropTarget.x2()
				,	y = dropObjects[ rows[i].maxId ].dropTarget.y()
				,	y2 = dropObjects[ rows[i].maxId ].dropTarget.y2();
				
				transDropObjectsV.push( new TransDropTarget(
					x2, 
					y, 
					x2, 
					y2, 
					transDelta,
					dropObjects[ rows[i].maxId ], true) );	// true - this is last possible cell in current row
			}
			if(maxYId) {
				transDropObjectsH.push( new TransDropTarget(
					minX, 
					maxY, 
					maxX, 
					maxY, 
					transDelta,
					dropObjects[ maxYId ], true) );	// true - this is last possible row in canvas
			}
		}
	}
	
    return {
		makeDraggable: function(element){
			element.onmousedown = mouseDown;
		}
	,	makeDroppable: function(element) {
			return dropObjects.push( element ) - 1;
		}
	,	recalcDropObjects: function() {
			if(dropObjects && dropObjects.length) {
				for(var i in dropObjects) {
					dropObjects[i].dropTarget.calcRect();
				}
			}
			recalcTransDropObjects();
		}
	,	removeDropObject: function(id) {
			if(dropObjects && dropObjects[id]) {
				dropObjects[id].dropTarget.remove();
				dropObjects.splice(id, 1);
			}
		}
    };
}());

function DragObject(element) {
	var rememberPosition
	,	mouseOffset
	,	moveObject;
	
	element.dragObject = this;
	dragMaster.makeDraggable(element);
	this.onDragStart = function(offset) {
		moveObject = jQuery(element).clone().appendTo('body').get(0);
		
		var s = moveObject.style;
		rememberPosition = {
			top: s.top
		,	left: s.left
		,	position: s.position
		};
		s.position = 'absolute';
		s.zIndex = 999999;
		mouseOffset = offset;
	};
	this.hide = function() {
		moveObject.style.display = 'none' ;
	};
	this.show = function() {
		moveObject.style.display = '' ;
	};
	this.onDragMove = function(x, y) {
		moveObject.style.top =  y - mouseOffset.y +'px'
		moveObject.style.left = x - mouseOffset.x +'px'
	};
	this.onDragSuccess = function(dropTarget) {
		this.removeMovedObject();
	};
	this.getDropName = function() {
		return jQuery(element).data('element');
	};
	this.onDragFail = function() {
		this.removeMovedObject();
	};
	this.removeMovedObject = function() {
		jQuery(moveObject).remove();
	};
}
function DropTarget(element) {
	var _x, _y, _x2, _y2, _width, _height;
	element.dropTarget = this;
	var _dropId = dragMaster.makeDroppable( element );
	var _testRect;
	this.calcRect = function() {
		var parentCell = jQuery(element).parents('.subStplCanvasCol:first')
		,	offset = jQuery(parentCell).offset();
		_x = offset.left;
		_y = offset.top;
		_width = jQuery(parentCell).width();
		_height = jQuery(parentCell).height();
		_x2 = _x + _width;
		_y2 = _y + _height;
		if(gShowBordersSub) {
			if(_testRect)
				_testRect.remove();
			_testRect = jQuery('<div style="border: 2px solid black; position: absolute; z-index: 99999;"/>').css({
				left: _x
			,	top: _y
			,	width: _width
			,	height: _height
			}).appendTo('body');
		}
	};
	this.accept = function(dragObject) {
		this.onLeave();
		var dropClassName = dragObject.getDropName();
		stplCanvasStartEdit(dropClassName, element);
	};
	this.onLeave = function() {
		jQuery(element).parents('.subStplCanvasCol:first, .subStplCanvasRowContent:first').removeClass('subStplHover');
	};
	this.onEnter = function() {
		jQuery(element).parents('.subStplCanvasCol:first, .subStplCanvasRowContent:first').addClass('subStplHover');
	};
	this.isPointInside = function(x, y) {
		return isPointInsideRectSub(_x, _y, _x2, _y2, x, y);
	};
	this.getDropId = function() {
		return _dropId;
	};
	this.remove = function() {
		if(gShowBordersSub) {
			if(_testRect)
				_testRect.remove();
		}
	};
	this.x = function() {
		return _x;
	};
	this.y = function() {
		return _y;
	};
	this.x2 = function() {
		return _x2;
	};
	this.y2 = function() {
		return _y2;
	};
	this.width = function() {
		return _width;
	};
	this.height = function() {
		return _height;
	};
	this.calcRect();
}
function TransDropTarget(x1, y1, x2, y2, delta, nearObj, isLast) {
	var _x, _y, _x2, _y2, _type, _testRect, _nearObj, _transElement, _isLast, _animationSpeed, _animationEffect, _fullWidth, _fullHeight, _nearObjPrevWidth, _nearColsPrevWidth;
	_nearObj = nearObj;
	_isLast = isLast;
	_animationSpeed = 500;
	_animationEffect = 'easeOutBounce';
	_fullWidth = 110;
	_fullHeight = 100;
	this.calcRect = function() {
		_type = x1 === x2 ? 'vertical' : 'horizontal';
		switch(_type) {
			case 'horizontal':
				_y = y1 - delta;
				_y2 = y1 + delta;
				_x = x1 - delta;
				_x2 = x2 + delta;
				break;
			case 'vertical':
				_x = x1 - delta;
				_x2 = x1 + delta;
				_y = y1;
				_y2 = y2;
				break;
		}
		if(gShowBordersSub) {
			if(_testRect)
				_testRect.remove();
			_testRect = jQuery('<div style="border: 2px solid yellowgreen; position: absolute; z-index: 99999;"/>').css({
				left: _x
			,	top: _y
			,	width: _x2 - _x
			,	height: _y2 - _y
			}).appendTo('body').html( _type );
		}
	};
	this.remove = function() {
		if(gShowBordersSub) {
			if(_testRect)
				_testRect.remove();
		}
	};
	this.isPointInside = function(x, y) {
		return isPointInsideRectSub(_x, _y, _x2, _y2, x, y);
	};
	this.nearObj = function() {
		return _nearObj;
	};
	this.type = function() {
		return _type;
	};
	this.x = function() {
		return _x;
	};
	this.y = function() {
		return _y;
	};
	this.x2 = function() {
		return _x2;
	};
	this.y2 = function() {
		return _y2;
	};
	this.onEnter = function() {
		switch(_type) {
			case 'horizontal':
				var nearObjRow = jQuery(_nearObj).parents('.subStplCanvasRow:first');
				if(_isLast) {
					_transElement = jQuery('<div class="subStplCanvasRow" style="border: 1px dashed #000000; height: 0px;"/>')
						.insertAfter( nearObjRow );
				} else {
					_transElement = jQuery('<div class="subStplCanvasRow" style="border: 1px dashed #000000; height: 0px;"/>')
						.insertBefore( nearObjRow );
				}
				// Show trans element with some animation
				_transElement.animate({
					'height': _fullHeight+ 'px'
				}, _animationSpeed, _animationEffect);
				break;
			case 'vertical':
				var nearObjCol = jQuery(_nearObj).parents('.subStplCanvasCol:first');
				if(_isLast) {
					_transElement = jQuery('<div class="subStplCanvasCol subStplCanvasTransDropTarget" style="border: 1px dashed #000000; width: 0px;"/>')
						.insertAfter( nearObjCol );
				} else {
					_transElement = jQuery('<div class="subStplCanvasCol subStplCanvasTransDropTarget" style="border: 1px dashed #000000; width: 0px;"/>')
						.insertBefore( nearObjCol );
				}
				_nearColsPrevWidth = [];
				var nearRowContent =  jQuery(_nearObj).parents('.subStplCanvasRowContent:first')
				,	nearCols = nearRowContent.find('.subStplCanvasCol')
				,	tmpAvarageWidth = stplCanvasCalcAvarageColsWidth(nearRowContent);
				nearCols.each(function(){
					_nearColsPrevWidth.push({
						col: jQuery(this)
					,	width: jQuery(this).width()
					});
				});
				// We will need to decrease width of near cell - to insert trans one, we will return original cell width in this.onLeave()
				//_nearObjPrevWidth = nearObjCol.width();
				//nearObjCol.width( (_nearObjPrevWidth - _fullWidth)+ 'px' );
				nearCols.not('.subStplCanvasTransDropTarget').width( tmpAvarageWidth+ 'px' );
				// Show trans element with some animation
				_transElement.animate({
					'width': tmpAvarageWidth+ 'px'
				}, _animationSpeed, _animationEffect);
				break;
		}
	};
	this.onLeave = function() {
		if(_transElement) {
			_transElement.remove();
			_transElement = null;
		}
		if(_type === 'vertical' && _nearColsPrevWidth && _nearColsPrevWidth.length) {
			for(var i in _nearColsPrevWidth) {
				_nearColsPrevWidth[i].col.width( _nearColsPrevWidth[i].width );
			}
			//jQuery(_nearObj).parents('.subStplCanvasCol:first').width( _nearObjPrevWidth );
			_nearObjPrevWidth = [];
		}
	};
	this.accept = function(dragObject) {
		this.onLeave();
		var dropToElement;
		switch(_type) {
			case 'horizontal':
				var addRowOpts = {}
				,	nearObjRow = jQuery(_nearObj).parents('.subStplCanvasRow:first');
				if(_isLast) {
					addRowOpts.insertAfter = nearObjRow;
				} else {
					addRowOpts.insertBefore = nearObjRow;
				}
				var newRowContent = stplCanvasAddRowSub(_nearObj, addRowOpts);
				dropToElement = newRowContent.find('.subStplCanvasColContent:first');
				break;
			case 'vertical':
				var addColOpts = {}
				,	nearObjRowContent = jQuery(_nearObj).parents('.subStplCanvasRowContent:first')
				,	nearObjCol = jQuery(_nearObj).parents('.subStplCanvasCol:first');;
				if(_isLast) {
					addColOpts.insertAfter = nearObjCol;
				} else {
					addColOpts.insertBefore = nearObjCol;
				}
				var newColContent = stplCanvasAddColSub(nearObjRowContent, addColOpts);
				dropToElement = newColContent;
				dragMaster.recalcDropObjects();
				_nearColsPrevWidth = [];
				break;
		}
		var dropClassName = dragObject.getDropName();
		stplCanvasStartEdit(dropClassName, dropToElement);
	};
	this.calcRect();
}