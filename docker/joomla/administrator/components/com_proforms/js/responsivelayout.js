// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// @license: Mad4Media Javascipt License - copyright Mad4Media - Fahrettin Kutyol - All rights reserved    ++
// (re-) publishing or forking for any purpose of commercial or non-commercial use is not allowed.		   ++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


var RLayout = {
		
		data: {
			slotIndex: 0,
			slotCount: 0,
			layoutWidth: '100',
			layoutWidthUnit: '%',
			rows: [],
			orderToSlot: []
		},
		layoutWidth: '100',
		layoutWidthUnit: '%',
		lastClear: null,
		gridSize: 24,
		slotIndex: 0,
		rowIndex: 0,
		rows: [],
		globalMouse: {x:0, y:0},
		draggingRuler: null,
		dragStartPosition: {x:0, y:0},
		chunkWidth: 0,
		hidePane: null,
		animPane: null,
		editPane: null,
		currentSection: null,
		switchUseFieldset: null,
		switchArrangement: null,
		init: function(){
			console.log("Init");
			window.addWorkAreaExpandCallback(function(width){RLayout.workAreaExpand(width);});
			this.lastClear = dojo.byId("lastClear");
			
			this.hidePane = dojo.byId("hidePane");
			document.body.appendChild(this.hidePane);
			
			this.animPane = dojo.byId("editAnimPane");
			document.body.appendChild(this.animPane);
			
			this.editPane = dojo.byId("editPane");
			document.body.appendChild(this.editPane);
			
			parseInfoTips(this.editPane);
			
			
			this.switchUseFieldset = dojo.query("#wrapUseFieldset div")[0];
			this.switchArrangement = dojo.query("#wrapArrangement div")[0];
			
			
			document.onselectstart = function() { return false; };
			
			document.onmousemove =  function(e){
				 e = e || window.event;
			    var cursor = {x:0, y:0};
			    if (e.pageX || e.pageY) {
			      cursor.x = e.pageX;
			      cursor.y = e.pageY;
			    } 
			    else {
			      cursor.x = e.clientX + 
			        (document.documentElement.scrollLeft || 
			         document.body.scrollLeft) - 
			         document.documentElement.clientLeft;
			      cursor.y = e.clientY + 
			        (document.documentElement.scrollTop ||
			         document.body.scrollTop) - 
			         document.documentElement.clientTop;
			    }
			    RLayout.globalMouse = cursor;
			    if(RLayout.draggingRuler !== null){
			    	RLayout.processRuler();
			    }
			};
			
			document.onmouseup = function(e){
						if(RLayout.draggingRuler !== null){
							RLayout.draggingRuler.section.span =  parseInt(RLayout.draggingRuler.section.getAttribute("data-span"));
							RLayout.dragStartPosition = null;
							RLayout.draggingRuler = null;
							document.body.style.cursor = "";
						}
			};
			
			this.getChunkWidth();
			
			/**
			 * Edit Parsing
			 */
			this.parseData();
			console.log(this.data);
		},
		
		parseData: function(){
			var json  = dojo.byId('json').value;
			console.log("Pasring from json: ", json);
			if(json && json != "null"){
				this.data = dojo.fromJson(json);
				
				dojo.byId("layoutWidth").value = this.data.layoutWidth || 100;
				dojo.byId("layoutWidthUnit").value = this.data.layoutWidthUnit || '%';
				this.setLayoutWidth();
				
				dojo.forEach(this.data.rows, dojo.hitch(this,function(row){
					
					this.createRow(0, row);					
				}));
				
				this.slotIndex = this.data.slotIndex;
				dojo.byId('json').value = null;
			}else{
				this.createRow();
			}
		},
		
		processRuler: function(){
			document.body.style.cursor = "col-resize";
//			console.log("START", this.dragStartPosition, " CURRENT MOUSE:",this.globalMouse);
			var diff = this.globalMouse.x - this.dragStartPosition.x;
			var diffSpan = Math.floor( diff / this.chunkWidth );
			var section = this.draggingRuler.section;
			var newSpan = section.span + diffSpan;
			if(newSpan < 3) newSpan = 2;
			var emptySpan = section.row.emptySpan;
			if( ( ! emptySpan || emptySpan < 0 ) && diffSpan > 0 )return;
			var currSpan = parseInt(section.getAttribute("data-span"));
			section.row.emptySpan -= (newSpan - currSpan);
			section.setAttribute("data-span", newSpan);
			
		},
		
		getChunkWidth: function(){
			var containerCoords = dojo.coords("layoutContainer");
			this.chunkWidth = containerCoords.w / this.gridSize;
			console.log("chunkWidth",this.chunkWidth);
		},
		
		workAreaExpand: function(width){
			this.getChunkWidth();
		},
		createRow: function(size, data){
			var row = this.createElement("DIV", {"class": "_row" }),
				sections = [], 
				span = Math.floor( this.gridSize );
			
			data = data || null;
			var rowIndex = ++this.rowIndex;
			
			this.addRowButtons(row, rowIndex);
			
			if(! data){
				row.span = span;
				row.emptySpan = this.gridSize - span;
				var sec = this.createSection(span, row);
				sections.push(sec);
				
			}else{
				row.span = Math.floor( this.gridSize / data.length );
				
				row.emptySpan = this.gridSize;
				
				dojo.forEach(data, dojo.hitch(this,function(sec){
					row.emptySpan -= parseInt(sec.span);
					var sec = this.createSection(sec.span, row, sec);	
					sections.push(sec);				
				}));
			}
			
			var last = sections.pop();
			dojo.addClass(last,"last");
			sections.push(last);
			row.sections = sections;
			dojo.forEach(sections, function(sec){
				row.appendChild(sec);
			});
			
			this.rows.push(row);
			
			dojo.place(row, this.lastClear, "before");
		},
		
		addRowButtons: function(row, rowIndex){
			
			var wrap = this.createElement("DIV", {"class": "_rowButtons", "id": "_rowButtons"+rowIndex});
			var up = this.createElement("IMG", {"src": "components/com_proforms/images/up.png", "id": "_up"+rowIndex, "style": "margin-bottom: 2px;"},
						{"onclick": function(){RLayout.moveRow(this,1);}});
			
			var down = this.createElement("IMG", {"src": "components/com_proforms/images/down.png", "id": "_down"+rowIndex, "style": "margin-bottom: 10px;"},
					{"onclick": function(){RLayout.moveRow(this,0);}});
			
			var remove = this.createElement("IMG", {"src": "components/com_proforms/images/remove.png", "id": "_remove"+rowIndex},
					{"onclick": function(){RLayout.askRemoveRow(this);}});

			up.row = row; down.row = row; remove.row = row;
			
			wrap.appendChild(up);
			wrap.appendChild(down);
			wrap.appendChild(remove);
			
			row.appendChild(wrap);
		},
		
		
		createSection: function(span, row, values){
			span = span || 2;			
			values = values || null;
			var slot = ! values ? ++this.slotIndex : values.slot;
			
			var element = this.createElement("DIV", {"class": "section", "data-span": span, "data-slot": slot}, {"onclick": function(e){RLayout.editStart(this);}});
			
			element.row = row;
			element.slot = slot;
			element.span = span;
			
			element.elementCount = (typeof elementCount2Slot !== "undefined" && typeof elementCount2Slot[ slot + '' ] !== "undefined") ? parseInt(elementCount2Slot[slot]) : 0;
			
				
			element.innerHTML = '<span class="count"></span>';
			
			this.createRuler(element);
			
			element.questionsWidth = 33;
			element.setQuestionsWidth = function(width){
				this.questionsWidth = width;
				this.division.setQuestionsWidth(width);
			}
			
			this.createFieldset(element);
			this.createDivision(element);
			this.createFloating(element);
			
			element.setQuestionsWidth( values ? values.questionsWidth : 33);
			
			element.useFieldset  = 0;
			element.setUseFieldset = function( useFieldset ){
				this.useFieldset = useFieldset;
				if(useFieldset == 1){
					this.fieldset.appendChild(this.division);
					this.fieldset.style.display = "block";
				}else{
					dojo.place(this.division,this.arrow,"before");
					this.fieldset.style.display = "none";
				}
			}		
			
			element.legend = null;
			element.setLegend= function( legend ){
				var text = dojo.trim(legend);
				this.legend = text;
				if( ! text  && text !== "0" ){
					return this.legendNode.style.display = "none";
				}
				this.legendNode.style.display = "";
				this.legendNode.innerHTML = text;
			}
			

			element.direction = 0;
			this.createArrow(element);
			
			element.setUseFieldset(  values ? values.useFieldset : 0 ) ; 
			
			element.setLegend(  values ? values.legend :  ''  );
			
			
			element.setDirection = function(dir){
				console.log("section.setDirection: ", dir);
				this.direction = dir;
				var divisionState = dir == 1 ? 0 : 1 ;

				this.division.style.visibility  = (dir == 1) ? "hidden" : "visible" ;
				this.floating.style.display = (dir == 1) ? "block" : "none" ;
				var _class = dir ? 'arrowRight' : 'arrowDown';
				var char = dir ? '&#8594;' : '&#8595;';
				this.arrow.className = _class;
				this.arrow.innerHTML = char;				
			}
			element.setDirection( values ? values.direction : 0 );
			
			
			// Height dots
			
			element.heightNode = this.createElement("DIV", {"class": "height", "title": mText.height});
			element.appendChild(element.heightNode);
			
			element.minHeightNode = this.createElement("DIV", {"class": "minHeight", "title": mText.minHeight});
			element.appendChild(element.minHeightNode);
			
			element.setHeight = function(height){
				var intVal = parseInt(height);
				this.height = height;
				if(height){
					this.style.height =  intVal + "px";
				}
				
				if(height){
					this.heightNode.style.display = "block";
					this.heightNode.style.top = intVal + "px";
				}else{
					this.heightNode.style.display = "none";					
				}
			}
			
			element.setMinHeight = function(minHeight){
				var intVal = parseInt(minHeight);
				this.minHeight =  minHeight;
				if(intVal >= 130){
					this.style.minHeight =  intVal + "px";
				}
				
				if(minHeight){
					this.minHeightNode.style.display = "block";
					this.minHeightNode.style.top = intVal + "px";
				}else{
					this.minHeightNode.style.display = "none";
				}
				
			}
			
			element.setHeight(  values ? values.height : "" ) ;
			element.setMinHeight(  values ? values.minHeight : "" ) ;
			
			// slot title and elements count
			element.slotTitle = '';
			this.createTitle(element);
			element.setSlotTitle = function(title){
				this.slotTitle = dojo.trim(title);
				this.slotTitleNode.innerHTML = '<span title="'+title+'">' + title + '</span><div class="m4jCLR"></div>';				
			}
			element.setSlotTitle( values ? values.slotTitle : 'SLOT-' + slot );
			
			var cbTitle = mText.contains.replace("%s", element.elementCount);
			var countButton = this.createElement("DIV", {"class": "countButton", "title": cbTitle});
			countButton.innerHTML = element.elementCount;
			element.appendChild(countButton);
			
			return element;
		},
		
		createRuler: function(section){
			var ruler = this.createElement("SPAN", 
										  {"class": "ruler"},
										  {
											  "onmousedown" : function(e){
												  console.log("onmousedown");
												 RLayout.dragStartPosition = dojo.clone(RLayout.globalMouse);
												 RLayout.draggingRuler = this;
											  }
										  });
			ruler.innerHTML = '&#8596;';
			ruler.section = section;
			section.appendChild(ruler);			
		},
		
		createDivision: function(section){
			var questionsWidth = section.questionsWidth || 33;
			var answersWidth = 100 - questionsWidth;
			var slot = section.slot;
			
			var div = this.createElement("DIV", {"class": "division", "id": "_division"+ slot});
			div.questions = this.createElement("DIV", {"class": "question", "id": "_question"+ slot, "style": "width: " +questionsWidth+ "%;" });
			div.questions.innerHTML = mText.questions;

			div.fields = this.createElement("DIV", {"class": "field", "id": "_field"+ slot, "style": "width: " +answersWidth+ "%;" });
			div.fields.innerHTML = mText.fields;
			
			div.appendChild(div.questions);
			div.appendChild(div.fields);
			div.setQuestionsWidth = function(width){
				var answersWidth = 100 - width;
				this.questions.style.width = width + "%";
				this.fields.style.width = answersWidth + "%";
			}
			
			section.division = div;
			section.appendChild(div);
		},
				
		createArrow: function(section){
			var dir = section.direction || 0;
			var _class = dir ? 'arrowRight' : 'arrowDown';
			var char = dir ? '&#8594;' : '&#8595;';
			section.arrow = this.createElement("DIV", {"class": _class, "id": "_arrow"+ section.slot});
			section.arrow.innerHTML = '<span>'+char+'</span>';
			section.appendChild(section.arrow);
		},
		
		createFieldset: function(section){
			section.fieldset = this.createElement("FIELDSET", {"style": "display:none;", "id": "_fieldset"+ section.slot});
			section.legendNode =  this.createElement("LEGEND",{"id": "_legend"+ section.slot});
			section.fieldset.appendChild(section.legendNode);
			section.appendChild(section.fieldset);
		},
		
		createFloating: function(section){
			section.floating = this.createElement("DIV", {"class":"floating","style": "display:none;", "id": "_floating"+ section.slot});
			section.floating.innerHTML = '<span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span>' +
										 '<span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span>'	;
			section.appendChild(section.floating);
		},
		
		createTitle: function(section){
			section.slotTitleNode = this.createElement("DIV", {"class":"slotTitle", "id": "_title"+ section.slot});
			section.slotTitleNode.innerHTML = '<span>' + section.slotTitle + '</span>';
			section.appendChild(section.slotTitleNode);
		},
		
		createElement: function(tag, attributes, listeners){
			attributes = attributes || null;
			listeners = listeners || null;
			var elm = document.createElement(tag.toUpperCase());

			if(attributes){
				for(var i in attributes){
					elm.setAttribute(i,attributes[i]);
				}
			}
			
			if(listeners){
				for(var i in listeners){
					dojo.connect(elm, i, listeners[i]);
				}
			}
			return elm;
		},
		
		removeNode: function(node){
			node.parentNode.removeChild(node);
		},	
		
		setLayoutWidth: function(){
			this.layoutWidth = parseInt( dojo.byId("layoutWidth").value );
			this.layoutWidthUnit = dojo.byId("layoutWidthUnit").value;
			
			dojo.byId('whizzard').style.width = this.layoutWidth + this.layoutWidthUnit;
			this.getChunkWidth();
		},
		
		askRemoveRow: function(node){
			return;
			var isDelete = window.confirm(mText.askdelete);
			if(isDelete){
				this.removeNode(node.row);				
			}
		},		
		moveRow: function(node,direction){
			
			direction = direction || 0;
			var row = node.row;
			if(!row) return;
			var next = this.findNextRow(row, direction);
			if(!next) return;
			
			
			var dimRow = dojo.coords(row);
			var dimNext = dojo.coords(next);

			console.log("dimRow",dimRow);
			console.log("dimNext",dimNext);
			
			row.style.position = "absolute";
			next.style.position = "absolute";
			
			var dummy = this.createElement("DIV",{"style": "display:block; padding:0; margin:0; width:100%; height:"+dimRow.h+"px;"});
			var dummy2 = this.createElement("DIV",{"style": "display:block; padding:0; margin:0; width:100%; height:"+dimNext.h+"px;"});
			
			dojo.place(dummy,row,"after");
			dojo.place(dummy2,next,"after");
			
			
			if(direction){
				

				this.animFlip(next, 130);
				this.animFlip(row, -130, dojo.hitch(this,function(){
					dojo.place(row, next, "before");
					row.style.position = "";
					next.style.position = "";
					row.style.marginTop = "";
					next.style.marginTop = "";
					this.removeNode(dummy);
					this.removeNode(dummy2);
					
				}));
								
			}else{
				

				this.animFlip(next, -130);
				this.animFlip(row, 130, dojo.hitch(this,function(){
					dojo.place(row, next, "after");
					row.style.position = "";
					next.style.position = "";
					row.style.marginTop = "";
					next.style.marginTop = "";
					this.removeNode(dummy);
					this.removeNode(dummy2);
				}));
				
			}
		},
		
		findNextRow: function(row, direction){
		
			if(direction){
				var prev = row.previousSibling;
				return (!prev ) ? false : ( dojo.hasClass(prev,"_row") ? prev : this.findNextRow(prev, direction) );
			}else{
				var next = row.nextSibling;
				return (!next ) ? false : ( dojo.hasClass(next,"_row") ? next : this.findNextRow(next, direction) );
				
			}
		},
		
		animFlip: function(node, marginTopEnd, onEnd){
			dojo.animateProperty( {
				node : node,
				duration :300,
				properties :{
					marginTop: {
						start: 0,
						end: marginTopEnd,
						unit: "px"
					}
				},
				onEnd: onEnd
			}).play();	
			
		},
		
		
		editSlot: function(section){
			this.currentSection = section;
			dojo.byId("slotHeight").value = section.height;
			dojo.byId("slotMinHeight").value = section.minHeight;
			dojo.byId("slotTitle").value = section.slotTitle;
			dojo.byId("slotLegendName").value = section.legend;
			dojo.byId("slotId").innerHTML = section.slot;
			dojo.byId("containingElements").innerHTML = section.elementCount;
			
			
			
			_setSpecialCheckBox(this.switchUseFieldset ,section.useFieldset,0);	
			_setSpecialCheckBox(this.switchArrangement ,section.direction,1);		
			var width = section.questionsWidth;
			var fields = 100 - width;
			dojo.byId("slotWidthQuestions").value = width;
			dojo.byId("slotDivisionFields").innerHTML = fields + "%";
			this.checkHiddenFields();
		},
		editStart: function(section){
			var ws = _WindowSize();
			var editPanePos = {
					x: ( ws.width - 800 ) / 2,
					y: ( ws.height - 600 ) / 2
			};
			
			var sectionPos =  _ViewportOffset(section, true);
			dojo.style(this.animPane,{
				top: sectionPos.t + "px",
				left: sectionPos.l + "px",
				width: sectionPos.w + "px",
				height: sectionPos.h + "px",
				opacity: 0
			});
			

			this.hidePane.style.left = 0;
			_fx.fadeOpacity(this.hidePane, 300, 0, 0.6);
			_fx.fadeOpacity(this.animPane, 300, 0, 1);
			_fx.animatePosition(this.animPane,300,sectionPos.l,editPanePos.x,"px",sectionPos.t,editPanePos.y,"px");
			_fx.animateDim(this.animPane,300,sectionPos.w,800,"px",sectionPos.h,600,"px",dojo.hitch(this,function(){
				

				this.editSlot(section);
				
				this.editPane.style.top = editPanePos.y + "px";
				this.editPane.style.left = editPanePos.x + "px";
				_fx.fadeOpacity(this.editPane, 300, 0, 1, dojo.hitch(this,function(){
					this.animPane.style.left="-9999em";
				}));
				
			}));
			
		},
		
		closeEdit: function(){

			_fx.fadeOpacity(this.hidePane, 300, 0.6, 0);
			_fx.fadeOpacity(this.editPane, 300, 1, 0, function(){RLayout.editPane.style.left="-9999em"; RLayout.hidePane.style.left="-9999em";});
		},
		
		checkHiddenFields: function(){
			var useFieldset = parseInt( dojo.byId("checkbox_slotUseFieldset").value );
			var fieldArrangment = parseInt( dojo.byId("checkbox_slotFieldArrangement").value );
			
			console.log("checkHiddenFields()");
			console.log("useFieldset: "+ useFieldset + " | fieldArrangement: " + fieldArrangment );
			 
			
			_S("hideInputLegend").display = useFieldset?  'none' : 'block'; 

			_S("hideInputDivision1").display = fieldArrangment ? 'block' : 'none';
			_S("hideInputDivision2").display = fieldArrangment ? 'block' : 'none';
		},
		useFieldset: function(){
			var useFieldset = parseInt( dojo.byId("checkbox_slotUseFieldset").value  );
			this.currentSection.setUseFieldset(useFieldset);
			this.checkHiddenFields();
		},
		arrangement: function(){
			var direction = parseInt( dojo.byId("checkbox_slotFieldArrangement").value );
			this.currentSection.setDirection(direction);
			this.checkHiddenFields();
			
		},
		
		validateNumbers: function(event){
			var cc = event.charCode ;
			console.log("validateNumbers charCode: ", cc);
			if(cc === 13) RLayout.setQuestionsWidth(dojo.byId("slotWidthQuestions"));
			return (cc >= 48 && cc <= 57) || cc === 0 || cc === 13;
		},
		
		setLegend: function( input ){
			this.currentSection.setLegend(input.value);
		},
		
		setHeight: function( input ){
			this.currentSection.setHeight(input.value);
		},
		
		setMinHeight: function( input ){
			this.currentSection.setMinHeight(input.value);
		},
		
		setSlotTitle: function( input ){
			this.currentSection.setSlotTitle(input.value);
		},
		
		
		setQuestionsWidth: function(input){
			var width = parseInt(input.value);
			if(width > 100){
				input.value = 100;
				width = 100;
			}
			var fieldsWidth = 100 - width;
			dojo.byId("slotDivisionFields").innerHTML = fieldsWidth + "%";
			this.currentSection.setQuestionsWidth(width);
		},
		
		toJson: function(){
			this.data.rows = [];
			this.data.slotIndex = this.slotIndex;
			this.data.layoutWidth = this.layoutWidth;
			this.data.layoutWidthUnit = this.layoutWidthUnit;
			this.data.slotCount = 0;
			var slotOrder = 1;
			var orderToSlot = {};
			var rows = [];
			dojo.query("div._row", dojo.byId("whizzard")).forEach(dojo.hitch(this,function(row){
				
				var sections = [];
				
				dojo.query("div.section", row).forEach(dojo.hitch(this,function(section){
					this.data.slotCount++;
					orderToSlot[slotOrder] = section.slot;
					var values = {
							slotTitle: section.slotTitle,
							slot: section.slot,
							order: slotOrder++,
							span: section.span,
							questionsWidth: section.questionsWidth,
							useFieldset: section.useFieldset,
							legend: section.legend,
							direction: section.direction,
							height: section.height,
							minHeight: section.minHeight
						};
					sections.push(values);
				}));
				rows.push(sections);				
			}));
			this.data.rows = rows;
			this.data.orderToSlot = orderToSlot;
			console.log("this.data: ", this.data);
			dojo.byId("json").value = dojo.toJson(this.data);
			console.log(dojo.byId("json").value);
		}
		
		
		
		
		
}//EOF RLayout

dojo.addOnLoad(function(){ RLayout.init(); });




var checkBasicData = function(){
	var name = dojo.byId("name");
	var trimedValue = name.value.replace(/\s+$/,"").replace(/^\s+/,"");
	if(trimedValue != ""){
		RLayout.toJson();
//		return false;
		return true;
	}else {
		alert(errorNoName);
		return false;
	}
}

var submitOnNew = function(){
	if(checkBasicData()) {
		m4j_submit("new");
	}else return false;
}

var submitOnUpdateApply = function(){
	if(checkBasicData()) {
		dojo.byId('apply').value = 1;
		m4j_submit("update");
	}else return false;
}

var submitOnUpdate = function(){
	if(checkBasicData()) {
		dojo.byId('apply').value = 0;
		m4j_submit("update");
	}else return false;
}

var submitOnUpdateProceed = function(){
	if(checkBasicData()) {
		m4j_submit("updateproceed");
	}else return false;
}