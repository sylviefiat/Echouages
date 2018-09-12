<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Forms
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


$fieldSets = $this->form->getFieldsets();
?>
<?php $fieldSet = $this->form->getFieldset('form.payments');?>

<span class="fieldsform form-horizontal row-fluid span12">
	<?php
	$field = $fieldSet['jform_options_payment'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	<?php
	$field = $fieldSet['jform_options_amount_type'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	
	<?php
	// Price
	$field = $fieldSet['jform_options_price'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>	
	
	<?php
	// Price
	$field = $fieldSet['jform_options_tax'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>

	<div class="control-group spanInline form-vertical condRule[show,#jform_options_amount_type,fixed]">
		<div class="control-label">
			<label for="total_to_pay">
				<?php echo JText::_("JFORMS_FIELD_TOTAL_TO_PAY"); ?>
			</label>
		</div>
	
	    <div class="controls">
			<input value="0" id="total_to_pay" type="text" class="input-mini"/>
		</div>
	</div>
<div class="clearfix"></div>
	<?php
	$field = $fieldSet['jform_options_show_price_list'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	<?php
	$field = $fieldSet['jform_options_show_price_item'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
</span>

<span class="fieldsform form-horizontal row-fluid span12">
<legend><?php echo JText::_("JFORMS_VIEW_PAYMENT_STATIC_PAGES"); ?></legend>
	<?php
	$field = $fieldSet['jform_options_payment_instructions'];
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->getInputI(); ?>
		</div>
	</div>
	
	<?php
	$field = $fieldSet['jform_options_thank_you'];
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->getInputI(); ?>
		</div>
	</div>
	
	<?php
	$field = $fieldSet['jform_options_failed_payment'];
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->getInputI(); ?>
		</div>
	</div>
</span>

<?php
$formParts = JformsHelper::buildFormParts($this->item->fake_form,$this->item->jForm);

// group formParts
$formParts_fieldsets = ByGiroHelper::groupArrayByValue($formParts['fieldsets'], 'stepId');
$formParts_fields = ByGiroHelper::groupArrayByValue($formParts['fields'], 'fieldsetName');

$formFields = array();
if(!empty($formParts['steps'])){
	unset($step);
	foreach($formParts['steps'] as $key2 => $step){ 
		$optgroup1Label = 'Step: ID '. $step['id'] .' '. $step['name'];
		$optgroup1Key = $step['id'];
		
		if(empty($formParts_fieldsets[$step['id']])){
			continue;
		}
		
		$fieldsets = $formParts_fieldsets[$step['id']];
		
		unset($step_fset);
		foreach($fieldsets as $key3 => $step_fset){
			$optgroup2Label = $step_fset['name'];
			$optgroup2Key = $key3;
			if(empty($formParts_fields[$key3])){
				continue;
			}
		
			foreach($formParts_fields[$key3] as $key4 => $fi){
				if(!empty($fi['xml']['label'])){
					$label = JText::_($fi['xml']['label']);
				}
		
				if(empty($fi['xml']['label']) OR ($fi['xml']['type'] == 'ckspacer' OR $fi['xml']['type'] == 'spacer')){
					$label = $fi['xml']['name'];
				}
				
				$formFields[$key4] = (object)array(
					'value' => $key4,
					'text' => $label,
					'optgroup1Key' => $optgroup1Key,
					'optgroup1Label' => $optgroup1Label,
					'optgroup2Key' => $optgroup2Key,
					'optgroup2Label' => $optgroup2Label
				);
			}
			
		}
		
	}
}

?>
<span class="fieldsform form-horizontal row-fluid span12">
<legend><?php echo JText::_("JFORMS_VIEW_PAYMENT_CLIENT_FIELDS"); ?></legend>

	<?php
	$fields = array(
		'jform_options_client_name',
		'jform_options_client_lastname',
		'jform_options_client_address',
		'jform_options_client_zip',
		'jform_options_client_city',
		'jform_options_client_state',
		'jform_options_client_country',
		'jform_options_client_phone_number',
		'jform_options_client_mobile_number',
		'jform_options_client_email'
	);

	foreach($fields as $fi){
		$field = $fieldSet[$fi];
		
		$field->jdomOptions = array_merge($field->jdomOptions,array(
			'list' => $formFields,
			'groupBy' => array(
				'optgroup1Key' => 'optgroup1Label',
				'optgroup2Key' => 'optgroup2Label',
				)
		));
	?>
		<div class="control-group <?php echo 'field-' . $field->id . $field->responsive; ?>">
			<div class="control-label">
				<?php echo $field->label; ?>
			</div>
		
			<div class="controls">
				<?php echo $field->input; ?>
			</div>
		</div>
<?php } ?>
</span>

<script type="text/javascript">
	function calculatePrice(type){
		var type = (typeof type == 'undefined') ? 'price' : type,
			priceObj = jQuery('#jform_options_price'),
			price = parseFloat(priceObj.val()) >= 0 ? parseFloat(priceObj.val()) : 0,
			taxObj = jQuery('#jform_options_tax'),
			tax = parseFloat(taxObj.val()) >= 0 ? parseFloat(taxObj.val()) : 0,
			totalObj = jQuery('#total_to_pay'),
			total = parseFloat(totalObj.val()) >= 0 ? parseFloat(totalObj.val()) : 0;
			
		switch(type){
			case 'price':
			case 'tax':
				total = price * (tax + 100) / 100;
				totalObj.val(total.toFixed(2));
				break;
			
			case 'total':
				price = total * 100 / (tax + 100);
				priceObj.val(price.toFixed(2));
				break;
		}
	}

	jQuery(document).ready(function(){
		calculatePrice('price');
		
		jQuery('#jform_options_price').on('keyup',function(e){
			calculatePrice('price');
		});
		
		jQuery('#jform_options_tax').on('keyup',function(e){
			calculatePrice('tax');
		});
		
		jQuery('#total_to_pay').on('keyup',function(e){
			calculatePrice('total');
		});
	});
</script>