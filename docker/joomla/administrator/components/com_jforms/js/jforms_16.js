jQuery(document).ready(function(){
	jQuery('[onclick^=codemirrorToggler]').on('click',function(){
		var iframe = jQuery(this).closest('.controls').find('textarea[data-editor=codemirror]').next();
		if(iframe.length > 0){
			iframe.css('height','400px');
		}
	});
});