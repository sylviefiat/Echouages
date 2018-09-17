function editItem(listName, itemId){
	var item = window[listName + 'Item'],
		list = jQuery('#'+ listName +'_list'),
		form,
		modalBody,
		setRadioSelectValues = false,
		reactivatePlugins = false,
		formTemplater = doT.template(document.getElementById('tmpl_'+ listName +'_form').text);
	
	if(typeof jQuery.fn.jListByGiro != 'undefined' && typeof list.data('plugin_jListByGiro') != 'undefined'){
		if(typeof itemId == 'undefined' || itemId <= 0){
			if(typeof window[listName +'_list_counter'] == 'undefined'){
				// let's sort by id first
				list.jListByGiro('sort','id');
				var items = list.jListByGiro('items');
				
				itemId = 1;
				if(typeof items[(items.length -1)] != 'undefined'){
					itemId = parseInt(items[(items.length -1)].id) +1;
				}
				window[listName +'_list_counter'] = item.id = itemId;
			} else {
				item.id = itemId = window[listName +'_list_counter']++;
			}
		} else {
			// get item from LIST
			item = list.jListByGiro('getItem',{id:itemId});
			if(jQuery.isEmptyObject(item)){
				return;
			}
		}
	} else {
		if(typeof itemId == 'undefined' || itemId <= 0){
			window[listName +'_list_counter']++;
			var items = list.find('[data-item-id]');
			
			var ids = new Array();
			items.each(function(ind){
				var id = parseInt(jQuery(this).attr('data-item-id'));
				if(id >= 0){
					ids.push(id);
				}
			});
			
			var itemId = parseInt(findMax(ids));
			if(itemId >= 0){
				itemId += 1;
			} else {
				itemId = 1;
			}
			item.id = itemId;
		} else {
			item = list.find('[data-item-id="'+ itemId +'"]').data('item');
		}
	}

	form = jQuery('#'+ listName +'_forms').find('[data-item-id="'+ itemId +'"]');

	// check if we have already a form edited IF NOT get a new one
	if(typeof form == 'undefined' || form.length <= 0){
		form = formTemplater(item);
		
		// JOOMLA fix textareaname JCE
		form = form.replace(new RegExp('textareaname', 'g'), 'textarea name');
		
		setRadioSelectValues = true;
		reactivatePlugins = true;
	}	

	// replace modal
	modalBody = jQuery('#'+ listName +'_modal .modal-body');
	modalBody.append(form);
	if(setRadioSelectValues){
		setValues(modalBody,item);
	}
	if(reactivatePlugins){
		enablePlugins(modalBody);
	}
	
	jQuery('#'+ listName +'_modal').modal();
}

function enablePlugins(jQueryObj){
	// tags manager
	if(typeof jQuery.fn.tagsManagerByGiro != 'undefined'){
		jQueryObj.find('input[tgman]').tagsManagerByGiro();
	}

	// timepickerbygiro
	if(typeof jQuery.fn.timepickerbygiro != 'undefined'){
		jQueryObj.find('div.timepicker-bygiro').each(function () {
		  var $timepicker = $(this);
		  $timepicker.timepickerbygiro($timepicker.data());
		});
	}
	
	// reactivate radio buttons
	radiosInit(jQueryObj);

	// activate bootstrap popover
	jQueryObj.find('[data-toggle="popover"]').popover()
		.on("hidden", function (e) {
			e.stopPropagation();
		});
	
	// activate bootstrap tooltip
	jQueryObj.find(".hasTooltip").tooltip({"html": true,"placement": "right"}).on("hidden", function (e) {
			e.stopPropagation();
		});
		
}

function disablePlugins(jQueryObj){
	// remove tinymce
	if(typeof tinyMCE != 'undefined'){
	/*
		var editorName = jQueryObj.find('textarea.mce_editable').attr('name');		
		tinyMCE.execCommand('mceFocus',false,editorName);
		tinyMCE.execCommand('mceRemoveControl',false, editorName);
	*/
	}
}

function setValues(jQueryObj,item){

	// select
	jQueryObj.find('select').each(function(index){
		var fieldname = jQuery(this).attr('name').split('[').pop().replace(']', '');
		jQuery(this).val(item[fieldname]);
	});
	
	// radio, checkbox
	jQueryObj.find('input[type="radio"],input[type="checkbox"]').each(function(index){
		var val,fieldname = jQuery(this).attr('name').split('[').pop().replace(']', '');
		
		val = item[fieldname];
		if(typeof item[fieldname] == "boolean"){
			val = 0;
			if(item[fieldname]){
				val = 1;
			}
		}
		
		if(val == jQuery(this).val()){
			jQuery(this).prop('checked', true);
		} else {
			jQuery(this).prop('checked', false);
			jQuery(this).removeAttr('checked');
		}
	});
}

function saveItem(listName){
	var modalSelector = '#'+ listName +'_modal',
		list = jQuery('#'+ listName +'_list'),
		formTemplater = doT.template(document.getElementById('tmpl_'+ listName +'_item').text);

	// get item from FORM
	var formContainer = jQuery(modalSelector).find('[data-item-id]');
	var objData = formContainer.serializeObject();
	var itemId = formContainer.attr('data-item-id');
	var item;

	// process data for jList
	var cleanObj = {};
	cleanObj.id = itemId;
	jQuery.each(objData,function(index,value){
		if(index.indexOf('[')){
			var groups = index.split('[');
			index = groups[(groups.length - 1)].replace(']', '');
		}
		if(index == 'remove_item'){
			return true;
		}

		cleanObj[index] = value;
	});
	
	// manage the JDOM upload fields -current -remove -view
	jQuery.each(cleanObj,function(index,value){	
		if(index.indexOf('-current') >= 0){
			var originalIndex = index.split('-');
			originalIndex = originalIndex.splice(0,(originalIndex.length -1)).join('-');
			if(typeof cleanObj[originalIndex +'-remove'] != 'undefined'){
				// set original field
				if(typeof cleanObj[originalIndex] == 'undefined' || cleanObj[originalIndex] == ''){					
					if(cleanObj[originalIndex + '-remove'] != ''){
						cleanObj[originalIndex] = '';
					} else {
						cleanObj[originalIndex] = cleanObj[index];
					}
				}
				
				// remove field-current and field-remove
				delete cleanObj[index];
				delete cleanObj[originalIndex +'-remove'];
			}
		}
		
		if(index.indexOf('-view') >= 0){
			var originalIndex = index.split('-');
			originalIndex = originalIndex.splice(0,(originalIndex.length -1)).join('-');
			
			cleanObj[originalIndex] = cleanObj[index];
			
			// remove field-view
			delete cleanObj[index];
		}		
	});

	if(itemId != ''){
		if(typeof jQuery.fn.jListByGiro != 'undefined' && typeof list.data('plugin_jListByGiro') != 'undefined'){
			// check we already have the item
			item = list.jListByGiro('getItem',cleanObj);
			if(jQuery.isEmptyObject(item)){				
				// add item
				list.jListByGiro('add',cleanObj);
			} else {
				// update item
				list.jListByGiro('query',{
					task: 'update',
					where: ['id = '+ cleanObj.id ],
					justOne: true,
					set: cleanObj
				});
			}
		} else {
			// check we already have the item
			item = list.find('[data-item-id="'+ cleanObj.id +'"]');
			var newItem = formTemplater(cleanObj);
			newItem = jQuery(newItem).data('item',cleanObj);
			
			if(item.length > 0){
				// update item
				item.replaceWith(newItem);
			} else {
				// add item
				list.append(newItem);
			}
		}

		// move form to list forms container
		jQuery('#'+ listName +'_forms').find('[data-item-id="'+ itemId +'"]').remove();
		formContainer.appendTo('#'+ listName +'_forms');
	}

	// close modal and clean its content
	jQuery(modalSelector).modal('hide');
}

function removeItem(listName,itemId){
	var form,list = jQuery('#'+ listName +'_list'),
		formTemplater = doT.template(document.getElementById('tmpl_'+ listName +'_form').text);

	if(typeof jQuery.fn.jListByGiro != 'undefined' && typeof list.data('plugin_jListByGiro') != 'undefined'){
		// remove item from list
		list.jListByGiro('query',{
			task: 'delete',
			where: ['id = '+ itemId ],
			justOne: true
		});
	} else {
		list.find('[data-item-id="'+ itemId +'"]').remove();
	}

	// check if we have already a form edited IF NOT get a new one
	form = jQuery('#'+ listName +'_forms').find('[data-item-id="'+ itemId +'"]');
	
	if(typeof form == 'undefined' || form.length <= 0){
		form = formTemplater({id:itemId});
		
		// add item in the forms_list
		jQuery('#'+ listName +'_forms').append(form);
	}	
		
	// remove form
	jQuery('#'+ listName +'_forms')
		.find('[data-item-id="'+ itemId +'"]')
		.find('input.remove_item').val('1');	
}

function initList(listName,options){
	var tmpl = document.getElementById('tmpl_'+ listName +'_item').text,
		list = jQuery('#'+ listName +'_list');
		
	if(typeof tmpl != 'undefined'){
		var formTemplater = doT.template(document.getElementById('tmpl_'+ listName +'_item').text);
		// options.templater = function(){return formTemplater;};	
		options.templater = formTemplater;	
	}
	
	if(typeof jQuery.fn.jListByGiro != 'undefined' && typeof options.jListByGiro != 'undefined' && options.jListByGiro){
		list.jListByGiro(options);
	} else {
		if(typeof formTemplater == 'undefined'){
			return;
		}
		var list_counter = 0;

		// check IDs if missing
		options.values = checkItemsIds(options.values);

		jQuery.each(options.values,function(ind,item){
			if(typeof item != 'object' || item == null){
				return;
			}
			list_counter++;
			
			item.list_counter = list_counter;
			var newItem = formTemplater(item);
			newItem = jQuery(newItem).data('item',item);
			list.append(newItem);
		});
	}
}

function checkItemsIds(items){		
	var ids = [];

	// get all IDs if any
	jQuery.each(items,function(ind,item){
		var id = parseInt(item.id);
		if(id >= 0){
			ids.push(id);
		}
	});
	
	var maxId = (parseInt(findMax(ids)) || 0);
	
	// set missing Ids
	jQuery.each(items,function(ind,item){
		var id = (parseInt(item.id) || 0);
		if(id <= 0){
			maxId++;
			item.id = maxId;
			items[ind] = item;
		}
	});
	
	return items;
}

function findMax(a){
	var m = -Infinity, i = 0, n = a.length;

	for (; i != n; ++i) {
		if (a[i] > m) {
			m = a[i];
		}
	}

	return m;
}

jQuery.fn.serializeObject = function()
{
    var a,o = {};
	a = this.find('input,textarea,select').serializeArray();
    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

jQuery(document).ready(function(){
	// add triggers
	jQuery('[data-repeatable]').on('click','.btn-edit',function(){
		var listName = jQuery(this).closest('[data-repeatable]').attr('data-repeatable');
		var itemId = jQuery(this).closest('[data-item-id]').attr('data-item-id');
		editItem(listName,itemId);
	});

	jQuery('[data-repeatable]').on('click','.btn-new',function(){
		var listName = jQuery(this).closest('[data-repeatable]').attr('data-repeatable');
		editItem(listName);
	});
	
	jQuery('[data-repeatable]').on('click','.btn-apply',function(){
		var listName = jQuery(this).closest('[data-repeatable]').attr('data-repeatable');
		saveItem(listName);
	});
	
	jQuery('[data-repeatable]').on('click','.btn-delete',function(){
		var r=confirm(Joomla.JText._("PLG_JDOM_ALERT_ASK_BEFORE_REMOVE"));
		if(!r){
			return;
		}
		var listName = jQuery(this).closest('[data-repeatable]').attr('data-repeatable');
		var itemId = jQuery(this).closest('[data-item-id]').attr('data-item-id');
		
		removeItem(listName,itemId);
	});
	
	// clean the modalBody content
	jQuery('.modal[data-repeatable]').on('hidden', function(){
		var listName = jQuery(this).closest('[data-repeatable]').attr('data-repeatable');
		disablePlugins(jQuery('#'+ listName +'_modal .modal-body'));
		jQuery(this).find('.modal-body').html('');
	});
});
