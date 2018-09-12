// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// @license: Mad4Media Javascipt License - copyright Mad4Media - Fahrettin Kutyol - All rights reserved    ++
// (re-) publishing or forking for any purpose of commercial or non-commercial use is not allowed.		   ++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

dojo.addOnLoad(function(){
	dojo.query("textarea.pfmCodeMirror").forEach(function(textarea){
		var type = textarea.getAttribute('data-syntax') || 'text/html';
		var width = textarea.getAttribute('data-width') || '100%';
		var height = textarea.getAttribute('data-height') || '300px';
		
		var cm = CodeMirror.fromTextArea(textarea,{
			lineNumbers: true,
		    theme: "eclipse",
		    matchBrackets: true,
		    indentUnit: 4,
		    indentWithTabs: true,
		    enterMode: "keep",
		    tabMode: "shift",
	        extraKeys: {"Ctrl-Space": "autocomplete"},
			mode: type,
		});
		
		cm.setSize(width,height);
		
		
	});
});


