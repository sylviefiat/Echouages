// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// @license: Mad4Media Javascipt License - copyright Mad4Media - Fahrettin Kutyol - All rights reserved    ++
// (re-) publishing or forking for any purpose of commercial or non-commercial use is not allowed.		   ++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
var bubbleWrap = undefined;

function showBubble(e){
	var bW = bubbleWrap;
	this.contentNode.innerHTML = this.bubbleInfo;
	
	var myBounds = _ViewportOffset(this,false);
		
	var bwDim = _Dimensions(bW);
	var top = myBounds.t - bwDim.height +5; 
	var left = (myBounds.l+Math.round((myBounds.w)/2))-104;
	
	if(this.special){
		left = myBounds.l-95;
		top += 5;
	}
	
	bW.style.top = top + "px";
	bW.style.left = left + "px";
}	

function hideBubble(e){
	bubbleWrap.style.top = "0px";
	bubbleWrap.style.left = "-9999em";
	
}

dojo.addOnLoad(function(){
	var n = document.createElement("div");
	n.id = "bubble_tooltip";
	document.body.appendChild(n);
	n. innerHTML = '<div class="bubble_top"><span></span></div><div class="bubble_middle"><span id="bubble_tooltip_content"></span></div>'+
		'<div class="bubble_bottom"></div>';	
	bubbleWrap = n;
	var content = dojo.byId("bubble_tooltip_content");
	
	if(!m4jShowTooltip) return;
	
	var startNode = document.getElementById("proforms_proforms");
	if(!startNode) startNode = document.body;
	var children = startNode.getElementsByTagName('IMG');
	for(t=0;t<children.length;t++){
		var isBubbleTip = (children[t].className == "m4jInfoImage");
		var info = children[t].getAttribute("alt");
		if(info!= undefined && isBubbleTip){
			children[t].responsive = false;
			children[t].special = false;
			children[t].onmouseover = showBubble;
			children[t].onmouseout = hideBubble;
			if(info.trim != ""){
				children[t].bubbleInfo = info;
				children[t].contentNode = content;
			}
		}
	}
	
	dojo.query("DIV.pfmInfoImage").forEach(dojo.hitch(this, function(div){
		var info = dojo.trim( div.innerHTML );
		div.responsive = true;
		
		if(info!= undefined ){
			div.special = false;
			div.onmouseover = showBubble;
			div.onmouseout = hideBubble;
			if(info.trim != ""){
				div.bubbleInfo = info;
				div.contentNode = content;
			}
		}
	}));
	
	
	
	var ask2Confirm = dojo.byId("m4jAsk2Confirm");
	if(ask2Confirm != undefined){
		ask2Confirm.bubbleInfo = dojo.byId("m4jAsk2ConfirmDesc").innerHTML;
		ask2Confirm.contentNode = content;
		ask2Confirm.onmouseover = showBubble;
		ask2Confirm.onmouseout = hideBubble;
		ask2Confirm.special = true;
	}
	
});




