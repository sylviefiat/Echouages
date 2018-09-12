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
<?php $fieldSet = $this->form->getFieldset('form.extra');?>
	<?php
	// Main folder
	$field = $fieldSet['jform_emails'];
	$field->jdomOptions = array_merge($field->jdomOptions,array(
		'enumList' => $this->lists['select']['emails']
	));
	?>
<?php echo $field->input; ?>

<?php
$fake_form = $this->item->fake_form;
$variables = array();

if($fake_form instanceof JForm){
	$fake_fieldSets = $fake_form->getFieldsets();

	foreach($fake_fieldSets as $k => $fset){
		$fsetLabel = $fset->label;
		if($fsetLabel == ''){
			$fsetLabel = ucwords(str_replace('_',' ',$fset->name));
		} else if($fsetLabel === strtoupper($fsetLabel)){
			$fsetLabel = '{'. $fset->label .'}';
		}
		
		$group = array();
		$group['text'] = $fset->label;
		$group['label'] = $fsetLabel;
		$group['name'] = str_replace('.','_',$fset->name);
		
		$fake_fieldSet = $fake_form->getFieldset($k);			
		foreach($fake_fieldSet as $fld){
			$text = $label = $fake_form->getFieldAttribute($fld->fieldname,'label');				
			if($label === strtoupper($label)){
				$label = '{'. $label .'}';
			}
			
			$name = $fake_form->getFieldAttribute($fld->fieldname,'name');
			if(empty($text) OR $fld->type == 'ckspacer' OR $fld->type == 'spacer'){
				$text = $name;
			}
			
			$name = '[[form_data:'. $name .']]';
			$group['fields'][] = array('text' => $text, 'label' => $label, 'variable' => $name);
			
			if($fld->type == 'ckfile'){			
				$group['fields'][] = array('text' => true, 'label' => $label, 'variable' => str_replace(']]',':SAFE_URL_TO_FILE]]',$name));				
				$group['fields'][] = array('text' => -1, 'label' => $label, 'variable' => str_replace(']]',':DIRECT_URL_TO_FILE]]',$name));				
			}
		}
		
		$variables[] = $group;		
	}
}
?>

<div style="display: none;">
<span id="varsSelector">

<select class="addVar">
	<option value=""><?php echo JText::_("JFORMS_SELECT"); ?></option>
	
<?php
	$formParts = JformsHelper::buildFormParts($fake_form,$this->item->jForm);
	
// group formParts
$formParts_fieldsets = ByGiroHelper::groupArrayByValue($formParts['fieldsets'], 'stepId');
$formParts_fields = ByGiroHelper::groupArrayByValue($formParts['fields'], 'fieldsetName');
	?>
	<optgroup class="l1" label="Form">
<?php 
	if(!empty($formParts['steps'])){ ?>
			<?php unset($step);
				foreach($formParts['steps'] as $key2 => $step){ ?>
				<optgroup class="l2" label="Step: ID <?php echo $step['id']; ?> <?php echo $step['name']; ?>">
					<option value="{step:<?php echo $key2; ?> html=input}"><?php echo JText::_("JFORMS_VARIABLES_STEPS_HTML_INPUT") ?></option>
					<option value="{step:<?php echo $key2; ?> html=output}"><?php echo JText::_("JFORMS_VARIABLES_STEPS_HTML_OUPUT") ?></option>
					<option value="[[steps:<?php echo $key2; ?>:name]]">Name</option>
					<option value="[[steps:<?php echo $key2 ; ?>:description]]">Description</option>
					<option value="[[steps:<?php echo $key2 ; ?>:class]]">Class</option>
			<?php
					if(!empty($formParts_fieldsets[$step['id']])){
						$fieldsets = $formParts_fieldsets[$step['id']];
						
						unset($step_fset);
						foreach($fieldsets as $key3 => $step_fset){
						
						?>
						<optgroup class="l3" label="Fieldset: <?php echo $step_fset['name']; ?>">
							<option value="{fieldset:<?php echo $key3; ?> html=input}"><?php echo JText::_('JFORMS_VARIABLES_FIELDSET_INPUT') ?></option>
							<option value="{fieldset:<?php echo $key3; ?> html=output}"><?php echo JText::_('JFORMS_VARIABLES_FIELDSET_OUTPUT') ?></option>
							<option value="[[fieldsets:<?php echo $key3; ?>:name]]">Name</option>
							<option value="[[fieldsets:<?php echo $key3; ?>:label]]">Label</option>
							<option value="[[fieldsets:<?php echo $key3; ?>:description]]">Description</option>
						<?php 
							if(!empty($formParts_fields[$key3])){
								$fields = $formParts_fields[$key3];
								
								foreach($fields as $key4 => $fi){
									$label = JText::_($fi['xml']['label']);
									if($fi['xml']['type'] == 'ckspacer' OR $fi['xml']['type'] == 'spacer'){
										$label = $fi['xml']['name'];
									}
								?>
									<optgroup class="l4" label="Field: <?php echo $label; ?>">
										<option value="[[<?php echo 'fields:'. $key4 .':html:input'; ?>]]"><?php echo JText::_("JFORMS_VARIABLES_FIELD_HTML_INPUT") ?></option>
										<option value="[[<?php echo 'fields:'. $key4 .':html:output'; ?>]]"><?php echo JText::_("JFORMS_VARIABLES_FIELD_HTML_OUTPUT") ?></option>
										<option value="[[<?php echo 'fields:'. $key4 .':input'; ?>]]"><?php echo JText::_("JFORMS_VARIABLES_FIELD_INPUT") ?></option>
										<option value="[[<?php echo 'fields:'. $key4 .':label'; ?>]]"><?php echo JText::_("JFORMS_VARIABLES_FIELD_LABEL") ?></option>
										<option value="[[<?php echo 'fields:'. $key4 .':output'; ?>]]"><?php echo JText::_("JFORMS_VARIABLES_FIELD_OUTPUT") ?></option>
									</optgroup>
							<?php }
							}							
						?>					
						</optgroup>
					<?php } ?>
				<?php } ?>

				</optgroup>
			<?php } ?>
<?php } ?>
</optgroup>	
	
	<optgroup class="l1" label="<?php echo JText::_("JFORMS_DATA_FORMS_VARIABLES"); ?>"> 
<?php foreach($variables as $var){
	if(count($var['fields']) == 0){
		continue;
	}
?>
	  <optgroup class="l2" label="<?php echo JText::_($var['text']) ; ?>">
		<option class="allVariables" value="formVariablesTable_<?php echo $var['name']; ?>"><?php echo JText::_($var['text']) .' - '. JText::_("JFORMS_ALL_VARIABLES"); ?></option>
		<?php
			$previous = '';
			foreach($var['fields'] as $v){
				if($v['text'] === true){
					$text = $previous;
					$valueText = JText::_("JFORMS_VALUE_SAFE_URL");
				} else if($v['text'] === -1){
					$text = $previous;
					$valueText = JText::_("JFORMS_VALUE_DIRECT_URL");
				} else {
					$valueText = JText::_("JFORMS_VALUE");
					$previous = $text = JText::_($v['text']); ?>
					
					<option value="<?php echo $v['label']; ?>"><?php echo $text; ?> - <?php echo JText::_("JFORMS_LABEL"); ?></option>
		  <?php } ?>
		  
				<option value="<?php echo $v['variable']; ?>"><?php echo $text; ?> - <?php echo $valueText ?></option>
		<?php } ?>
	  </optgroup>
<?php } ?>
  </optgroup>

  
<optgroup class="l1" label="<?php echo JText::_("JFORMS_USER_VARIABLES"); ?>">
	<?php 
		$wanted_variables = array('id','username','name','email','registerDate','lastvisitDate');
		foreach($this->user as $k => $v){
			if(!in_array($k,$wanted_variables)){
				continue;
			}
			$var = '[[user:'.$k .']]';
			?>
			<option value="<?php echo $var; ?>"><?php echo $var; ?> (<?php echo JText::sprintf("JFORMS_EXAMPLE_VARIABLE",$v); ?>)</option>
	<?php } ?>
</optgroup>

<optgroup class="l1" label="<?php echo JText::_("JFORMS_JFORMS_VARIABLES"); ?>">
	<?php 
		$wanted_variables = array('id','name','alias','description','message_after_submit','redirect_after_submit');
		foreach($this->item as $k => $v){
			if(!in_array($k,$wanted_variables)){
				continue;
			}
			$var = '[[jforms_snapshot:'.$k .']]';
			?>
			<option value="<?php echo $var; ?>"><?php echo $var; ?></option>
	<?php } ?>
</optgroup>

<optgroup class="l1" label="<?php echo JText::_("JFORMS_OTHER_VARIABLES"); ?>">
	<option value="[[ip_address]]">[[ip_address]]</option>
	<option value="[[creation_date:d M Y H:i:s]]">[[creation_date:d M Y H:i:s]] (<?php echo JText::_("JFORMS_CREATION_DATE_VARIABLE_INFO"); ?>)</option>
	<option value="[[form_data:page_url]]">[[form_data:page_url]] (<?php echo JText::_("JFORMS_PAGE_URL_VARIABLE_INFO"); ?>)</option>
	<option value="[[form_data:page_title]]">[[form_data:page_title]] (<?php echo JText::_("JFORMS_PAGE_TITLE_VARIABLE_INFO"); ?>)</option>
	<option value="[[pdf]]">[[pdf]] (<?php echo JText::_("JFORMS_PDF_VARIABLE_INFO"); ?>)</option>
	<option value="[[pdf:SAFE_URL_TO_FILE]]">[[pdf:SAFE_URL_TO_FILE]] (<?php echo JText::_("JFORMS_PDF_SAFE_URL_VARIABLE_INFO"); ?>)</option>
	<option value="[[pdf:DIRECT_URL_TO_FILE]]">[[pdf:DIRECT_URL_TO_FILE]] (<?php echo JText::_("JFORMS_PDF_DIRECT_URL_VARIABLE_INFO"); ?>)</option>
	<option disabled="disabled" value="[[password]]">[[password]] (new feature, coming soon!)</option>
</optgroup>
</select>
</span>

<?php foreach($variables as $var){ ?>
<span id="formVariablesTable_<?php echo $var['name']; ?>">
	<table>
		<thead><tr><th colspan="2"><?php echo $var['text']; ?></th></tr></thead>
		<tbody>
	<?php foreach($var['fields'] as $var){ ?>
			<tr>
				<td><?php echo $var['label']; ?></td><td><?php echo $var['variable']; ?></td>
			</tr>
	<?php } ?>
		</tbody>
	</table>
</span>
<?php } ?>
</div>

<script type="text/javascript">
// fix the select list added in modal
jQuery.fn.modal.Constructor.prototype.enforceFocus = function () {};

jQuery(document).ready(function(){	
	var fields = [
		'to',
		'from',
		'reply_to',
		'cc',
		'bcc',
		'subject',
		'body'
		];
		
	var tmplForm = jQuery('#tmpl_'+ window['emails']['tmpl_form'] +'_form').html();
	tmplForm = jQuery(tmplForm).wrap('<div>').parent();
	
	jQuery.each(fields,function(i,v){
		var label = tmplForm.find('#jform_emails_\\{\\{\\=it\\.id\\}\\}_'+v+'_label'),		
		varsSelector = jQuery('#varsSelector').clone();
		if(v != 'body'){
			varsSelector.find('option.allVariables').attr('disabled',true);			
		}
		varsSelector.find('.addVar').attr('data-target','#jform_emails_{{=it.id}}_'+v);
		varsSelector = varsSelector.html();
		varsSelector = varsSelector.replace(/"/g, '&quot;');

		label.after(' <span data-toggle="popover" data-html="true" data-title="<?php echo JText::_("JFORMS_SELECT_A_VARIABLE"); ?> <span class=&quot;close_popover&quot;>&times;</span>" data-content="'+ varsSelector +'" class="btnVariables btn btn-warning btn-mini"><?php echo JText::_("JFORMS_VARIABLES"); ?></span>');
	});
	
	jQuery('#tmpl_'+ window['emails']['tmpl_form'] +'_form').html(tmplForm.html());
	
	jQuery('body').on('change','.addVar',function(){
		var target = jQuery(this).attr('data-target'),
			value = jQuery(this).val();

		if(value.indexOf('formVariablesTable_') >= 0){
			value = jQuery('#'+ value).html();
		}		

		var that = jQuery('.modal-body').find(target);
		if(that.is('textarea')){
			var editorType = that.attr('data-editor');
			switch(editorType){
				case 'tinymce':					
				case 'jce':
					value = that.val() + value;
					that.val(value);
					
					if(typeof tinyMCE != 'undefined'){
						tinyMCE.execCommand('mceFocus',false,that.attr('id'));
						tinyMCE.activeEditor.execCommand('mceInsertContent', false, value);					
					}					
					break;
					
				default:
					value = that.val() + value;
					that.val(value);
					break;
			}

		} else {
			value = that.val() + value;
			that.val(value);
		}
		
		jQuery('[data-toggle="popover"]').popover('hide');
	});
	
	jQuery('body').on('click','.popover .close_popover',function(){
		jQuery('[data-toggle="popover"]').popover('hide');
	});
	
	jQuery('body').on('click', function (e) {
		jQuery('[data-toggle="popover"]').each(function () {
			//the 'is' for buttons that trigger popups
			//the 'has' for icons within a button that triggers a popup
			if (!jQuery(this).is(e.target) && jQuery(this).has(e.target).length === 0 && jQuery('.popover').has(e.target).length === 0) {
				jQuery(this).popover('hide');
			}
		});
	});	
});
</script>
