function submitStep(fId,data,tk){
	var dataToSubmit = {
			"frm": fId,
			"frmData": data
			};
		dataToSubmit[tk] = 1;
	
	jQuery('#system-message').jdomAjax({
		namespace:"jforms.submission.ajax.savestep", 
		vars:dataToSubmit,
		success: function(object, data, textStatus, jqXHR)
		{
			var thisp = this;
			
			// fill the object with the returned html
			$(object).html('').html(data);
			$(object).ready(function()
			{
				if (typeof(thisp.ready) != 'undefined')
					thisp.ready(object, data, textStatus, jqXHR);	
			});
		},		
		loading: function(object)
		{
	
		},
		error: function(object, jqXHR, textStatus, errorThrown)
		{
		
		}
	});
}

function pricerule(rule,cond){
	var forms,checkAll = false;
	
	if(typeof rule == 'undefined'){
		forms = jQuery('form');
		checkAll = true;
	} else {
		forms = rule.trigger.closest('form');
	}

	if(!(forms instanceof jQuery)){
		forms = jQuery(forms);
	}
	
	if(forms.length == 0){
		return;
	}
	
	var checkPriceTriggers = function(form){
		if(!(form instanceof jQuery)) return;
		var basicInput = form.find('input.basicPrice'),
		taxInput = form.find('input.tax'),
		totalInput = form.find('input.totalToPay'),
		allInputs = form.find('[class^="condRule[priceRule"], [class*=" condRule[priceRule"], [cond-rules], [cond-rule-task="priceRule"]'),
		formElements = ['input','select','textarea'];
		
		var rulesGroups = {};
		if(form.attr('data-type-form') == 'dynamic'){			
			rulesGroups = allInputs.condRules('getRules');
		}
		
		var validCalculations = {};
		for(var tHash in rulesGroups){
			var rules = rulesGroups[tHash];

			jQuery.each(rules,function(i,rule){
				if(rule.task != 'pricerule') return true;
			
				var triggerEle = rule.trigger;		
				if(triggerEle.is(':disabled')) return true;
				
				var cond = jQuery(this).condRules('checkRule',rule,false); // test
				if(!cond){
					return true;
				}

				var price = parseFloat(rule.taskValues[1]) || 0;
				if(rule.taskValues[1] == 'this'){
					price = parseFloat(triggerEle.val()) || 0;
				}					
				
				if(price <= 0 || typeof validCalculations[i] != 'undefined') return true;
				
				validCalculations[i] = {
					variation: rule.taskValues[0] || '+',
					price: price,
					type: (rule.taskValues[2] == '%') ? '%' : '',
					element: triggerEle,
					rule: rule
				};				
			});		
		};
		
		updatePrices(validCalculations,form);	
	}
	
	forms.each(function(){
		form = jQuery(this);
		if(checkAll){
			checkPriceTriggers(form);		
		} else {
			delay(function(){
				checkPriceTriggers(form);
			},
			200,
			(form.attr('id') + form.attr('class') + form.attr('name')).replace(/\s+/g, ''));
		}		
		
	});

}

function updatePrices(calcRules,forms){
	if(typeof forms == 'undefined'){
		forms = jQuery('form');
	} else if(!(forms instanceof jQuery)){
		forms = jQuery(forms);
	}
	
	forms.each(function(){
		var form = jQuery(this),
		basicInput = form.find('input.basicPrice'),
		basic = parseFloat(basicInput.val()) || 0,
		taxInput = form.find('input.tax'),
		tax = parseFloat(taxInput.val()) || 0,
		tax_amount = total = 0,
		partial = basic,tpartial,
		price,info,type,
		sampleRow = form.find('.subtotal_amount').closest('tr').clone(true),
		extrasContainer = form.find('.payment_summary tbody.extras');
		extrasContainer.empty();
		
		sampleRow.find('.subtotal_amount').removeClass('subtotal_amount').addClass('extras_amount').html('');
		sampleRow.find('.summary_label').html('');
		
		jQuery.each(calcRules,function(i,v){
			price = v.price;
			type = '';
			if(v.type == '%'){
				price = v.price * partial / 100;
				type = '%';
			}
			info = ' <span class="small">('+ (v.variation || '+') +' '+ v.price +''+ type +')</span>';
						
			var err = false;
			try{
				tpartial = eval(partial +' '+ (v.variation || '+') +' '+  price);
			} catch(err){
				
			}
			
			if(err){
				return true;
			}

			var amount_variation = tpartial - partial;
			priceVar = (amount_variation < 0) ? '-' : '+';
			amount_variation = Math.abs(amount_variation);
			
			partial = tpartial;
			
			var eleId = v.element.attr('id'),
			label = form.find('[for="'+ eleId +'"]').first().clone(true),
			row = sampleRow.clone(true);
			
			if(v.element.is('input[type="checkbox"],input[type="radio"],select[multiple]')){
				
				if(v.element.is('select[multiple]')){
					if(v.rule.values && v.rule.values.indexOf('*') < 0){
						var subLabel = [];
						v.element.find('option').each(function(){
							if(v.rule.values.indexOf(jQuery(this).attr('value')) >= 0){
								subLabel.push(jQuery(this).html());
							}
						});
						
						label = label.append(': ')
							.append(jQuery('<span class="label-opt small"></span>').append(subLabel.join(', ')));
					}
				} else {
					var parentContainer = v.element.closest('.input-container');
					if(parentContainer.length > 0){
						var parentId = parentContainer.attr('id'),
						parentLabel = form.find('[for="'+ parentId +'"]').first().clone(true);
						if(v.rule.values && v.rule.values.indexOf('*') < 0){
							parentLabel = parentLabel.append(': ').wrap('<div></div>').parent()
								.append(jQuery('<span class="label-opt small"></span>').append(label));
						}
						label = parentLabel.children();
					}
				}
			}

			// add row with details
			row.find('.summary_label').append(label).append(info);
			row.find('.extras_amount').html(amount_variation.toFixed(2));
			row.find('.price_variation').html(priceVar);
			
			extrasContainer.append(row);
			
		});
		
		// update DOM elements
		form.find('.subtotal_amount').text(partial.toFixed(2));
		
		if(tax > 0){
			tax_amount = tax * partial / 100;
			form.find('.tax_amount').text(tax_amount.toFixed(2));
		}
		
		form.find('.total_amount').text((partial + tax_amount).toFixed(2));	
	});
}

jQuery(document).ready(function(){
	pricerule();
});
