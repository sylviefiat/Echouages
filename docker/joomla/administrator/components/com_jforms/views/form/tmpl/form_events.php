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
<?php $fieldSet = $this->form->getFieldset('form.events');?>
<?php $fieldSet_OtherOpts = $this->form->getFieldset('form.other_options'); ?>

<fieldset class="fieldsform form-horizontal">
	<?php
	// redirect
	$field = $fieldSet_OtherOpts['jform_options_redirect'];
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
	// Redirect after submit
	$field = $fieldSet['jform_redirect_after_submit'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
</fieldset>
<?php $fieldSet = $this->form->getFieldset('form.extra');?>
	<?php
	// Main folder
	$field = $fieldSet['jform_events'];
	$field->jdomOptions = array_merge($field->jdomOptions,array(
		'enumList' => $this->lists['select']['events']
	));
	?>
<?php echo $field->input; ?>