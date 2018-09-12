// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// @license: Mad4Media Javascipt License - copyright Mad4Media - Fahrettin Kutyol - All rights reserved    ++
// (re-) publishing or forking for any purpose of commercial or non-commercial use is not allowed.		   ++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

var OptManager = {
		wrapNode: null,
		dragging: false,		
		currentItem: undefined,
		dummy: null,
		mouse: null,
		dropFocus: undefined,
		space: 8,	
		place: "before",
		stopBubble: 0,
		factoryNode: null,
		useValues: 0,
		feedTemplateNode: null,
		feedType: 0,
		feedAddingType: 0,
		manualWrap: null,
		sqlWrap: null,
		
		onMouseDown: function(e){
			if(!OptManager.stopBubble){
				OptManager.dragging = 1;
				OptManager.currentItem = this;
				OptManager.addDummy(this);
				var bounds = _ViewportOffset(this,true);
				document.body.appendChild(this);
				var pos = OptManager.getPosition();
				dojo.style(this,{position: "absolute", opacity: 0.5, left: pos.left, top: pos.top });
			}
		},
		
		getPosition: function(){
			return {
				left: ( OptManager.mouse.x + OptManager.space ) +"px",
				top: ( OptManager.mouse.y + OptManager.space ) +"px"
			};
		},
		
		
		onMouseUp: function(e){
			if(this.dragging){
				this.dragging = 0;
				var db =  _ViewportOffset(this.dummy,false);
				
				var node = this.currentItem;
				dojo.animateProperty({
					node: node,
					properties: {
						top: db.t,
						left: db.l,
						opacity: 1
						},
					onEnd: function(){ OptManager.onEnd(); },
					duration: 300
				}).play();
				
				
			}
		},
		
		onEnd: function(){
			dojo.style(this.currentItem,{position: "relative", opacity: 1, top: "auto", left: "auto"});
			dojo.place(this.currentItem,this.dummy,"before");
			this.dropFocus = null;
			this.removeDummy();
			this.currentItem = null;	
		},
		
		onMouseMove: function(e){
			if(this.dragging){
				var pos = OptManager.getPosition();
				dojo.style(this.currentItem,{ left: pos.left, top: pos.top });
			}
		},
		
		onMouseOver: function(e){
			if(OptManager.currentItem == this) return false;
			if(OptManager.dragging && ! this.placed){
				this.placed = 1;
				OptManager.dropFocus = this;
				var bounds = _ViewportOffset(this,true);
				var place = (bounds.t > OptManager.dummy.bounds.t) ? "after" : "before";
				dojo.place(OptManager.dummy, this, place);
				OptManager.dummy.bounds = _ViewportOffset(OptManager.dummy,true);
				
			}
		},
		onMouseOut: function(e){
			if(OptManager.currentItem == this) return false;
			if(OptManager.dragging){
				this.placed = 0;			
				OptManager.dropFocus = null;
			}
		},
		removeDummy: function(){
			this.dummy.parentNode.removeChild(this.dummy);
			this.dummy = null;
		},
		
		addDummy: function(node){
			var dummy = document.createElement("DIV");
			dummy.className = "optionsWrapDummy";
			dojo.place(dummy,node,"before");
			dummy.bounds =  _ViewportOffset(dummy,true);
			this.dummy = dummy;
		},
		
		remove: function(node){
			var found = 0, proceed = 1;
			while (proceed) {
				node = node.parentNode;
				found = (node.className=="optionsWrap unselectable");
				proceed = ! (node.className=="optionsWrap unselectable" || node == document.body);
			}
			if(found){
				_fx.fadeOpacity(node, 300, 1, 0,dojo.hitch(this,function(){ 
					_fx.animToHeight(node, 300, 0, dojo.hitch(this,function(){
						node.parentNode.removeChild(node);
					}));
				}));	
			}
		},
		
		copy: function(node){
			var found = 0;
			while (!found) {
				node = node.parentNode;
				found = (node.className=="optionsWrap unselectable");
			}
			var clone = dojo.clone(node);
			clone.placed = 0;
			dojo.connect(clone,"mousedown" , OptManager.onMouseDown);
			dojo.connect(clone,"mouseover" , OptManager.onMouseOver);
			dojo.connect(clone,"mouseout" , OptManager.onMouseOut);
			dojo.connect(clone,"selectstart" , function(){
				if(this.className=="optionInput") return true;
				else return false;});
			clone.unselectable = "on";
			clone.style.MozUserSelect = "none";
			
			var dummy = document.createElement("DIV");
			dummy.className="optionsWrapDummy";
			dummy.style.height = "0px";
			dojo.place(dummy,node,"after");
			
			_fx.animToHeight(dummy, 300, 55, dojo.hitch(this,function(){
				dojo.place(clone,dummy,"before");
				dojo.style(clone,{opacity: 0});
				dojo.anim(clone,{opacity: 1}).play();
				dummy.parentNode.removeChild(dummy);
			}));
			
		},
		
		setBubble: function(state){
			if(! this.dragging){
				this.stopBubble = state;
			}
		},
		
		add: function(text,value){
			text = (text === undefined) ? '' : text;
			value = (value === undefined) ? '' : value;
			
			text = text.replace(/"/g, '&quot;')  || "";
			value = value.replace(/"/g, '&quot;') || "";
			var isDisabled = this.useValues ? "" : 'disabled';
			var color = this.useValues ? "" : 'style="color: #888; "';
			
			var wrap = document.createElement("DIV");
			wrap.className = "optionsWrap unselectable";
			var html = this.factoryNode.innerHTML;
			wrap.innerHTML = (html.replace("{textdisabled}", color).replace("{inputdisabled}", isDisabled).replace("{textvalue}",text).replace("{valuevalue}",value) );
			wrap.placed = 0;
			dojo.connect(wrap,"mousedown" , OptManager.onMouseDown);
			dojo.connect(wrap,"mouseover" , OptManager.onMouseOver);
			dojo.connect(wrap,"mouseout" , OptManager.onMouseOut);
			dojo.connect(wrap,"selectstart" , function(){return false;});
			wrap.unselectable = "on";
			
			
			this.wrapNode.appendChild(wrap);
			
		},		
		
		toggleUseValues: function(){
			
			this.useValues = this.useValues ? 0 : 1;			
			var isDisabled = this.useValues ? false : true;
			var color = this.useValues ? "inherit" : "#888";
			
		    var inputs = dojo.query(".optionValuesInput");
		    inputs.forEach(function(input){
		    	input.disabled = isDisabled;
		    	
		    });
		    
		    var texts = dojo.query(".optionValuesInputText");
		    texts.forEach(function(text){
		    	text.style.color = color;
		    	
		    });
		    
		},
		
		empty: function(){
			var isEmpty = confirm(mText.askemptyoptions);
			if(isEmpty){
				this.wrapNode.innerHTML = "";
				return true;
			}else return false;
		},
		
		
		parse: function(){
			var dragItems = dojo.query(".optionsWrap",this.wrapNode);
			dragItems.forEach(function(item){
				item.placed = 0;
				dojo.connect(item,"mousedown" , OptManager.onMouseDown);
				dojo.connect(item,"mouseover" , OptManager.onMouseOver);
				dojo.connect(item,"mouseout" , OptManager.onMouseOut);
				dojo.connect(item,"selectstart" , function(){return false;});
				item.unselectable = "on";
			});

		},
		
		feed: function(content){
			var code = this.feedTemplateNode.innerHTML;
			code = code.replace(/FEEDREPLACE/g,"feedList");
			mWindow.setCode(code);
			mWindow.content();
		},
		
		feedProcess: function(){
			var list = dojo.byId("feedList").value;	
			var data = [];
			var route = parseInt(this.feedType);
			switch (route) {
			
			default:
			case 0:
				var split = list.split("\n");
				for(var t= 0, l= split.length; t<l;t++){
					var val = _trim(split[t]);
					if(val){ data.push([val,'']);}
				}
				break;

			case 1:
				var split = list.split(";");
				for(var t= 0, l= split.length; t<l;t++){
					var val = _trim(split[t]);
					if(val){ data.push([val,'']);}
				}			
				break;
			
			case 2:
				var split = list.split("\n");
				for(var t= 0, l= split.length; t<l;t++){
					var val = _trim(split[t]);
					if(val){
						var split2 = val.split(";");
						if(split2.length == 2){
							 data.push([ _trim(split2[0]), _trim(split2[1])]);	
						}
					}
				}
				break;
			
			case 3:
				var split = list.split("\n");
				for(var t= 0, l= split.length; t<l;t++){
					var val = _trim(split[t]);
					if(val){
						var split2 = val.split(",");
						if(split2.length == 2){
							 data.push([ _trim(split2[0]), _trim(split2[1])]);	
						}
					}
				}
				break;			
			}
			
			if(this.feedAddingType){
					if(! this.empty()) return false;
			}
			for(var t= 0, l= data.length; t<l;t++){
				this.add(data[t][0],data[t][1]);
			}
		
			mWindow.close();
		},
		toggleDataType: function(val){
			val = parseInt(val);
			if(val){
				this.manualWrap.style.display = "none";
				this.sqlWrap.style.left =  "auto";
				this.sqlWrap.style.position =  "relative";
			}else{
				this.manualWrap.style.display = "block";
				this.sqlWrap.style.left = "-9999em";
				this.sqlWrap.style.position =  "absolute";
			}
			
		}
			
}


dojo.addOnLoad(function(){

	OptManager.manualWrap = dojo.byId("manualWrap");
	OptManager.sqlWrap = dojo.byId("sqlWrap");
	OptManager.toggleDataType(isOptionsDataType);
	
	OptManager.wrapNode = dojo.byId("optionsRootNode");
	OptManager.factoryNode = dojo.byId("optionsFactoryNode");
	OptManager.factoryNode.style.display = "none";
	document.body.appendChild(OptManager.factoryNode);
	OptManager.feedTemplateNode = dojo.byId("optionsFeedTemplate");
	
	dojo.connect(document,"mousemove",function(e){
		OptManager.mouse = detectMousePosition(e);
		OptManager.onMouseMove();
	})
	
	dojo.connect(document,"mouseup",function(e){
		OptManager.onMouseUp();
	})
	OptManager.parse();
	
})
