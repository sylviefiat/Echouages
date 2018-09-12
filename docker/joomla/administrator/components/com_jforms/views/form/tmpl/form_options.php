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
<?php $fieldSet = $this->form->getFieldset('form.options');?>
<?php $fieldSet_OtherOpts = $this->form->getFieldset('form.other_options'); ?>
<span class="fieldsform form-horizontal">


	<?php
	// Save data in DB
	$field = $fieldSet['jform_save_data_in_db'];
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
	/*
	$field = $fieldSet_OtherOpts['jform_options_autosave'];
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
	$field = $fieldSet_OtherOpts['jform_options_passphrase'];
	?>
	<div class="control-group spanInline form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	<?php  */ ?>
	
</span>

<span class="fieldsform form-horizontal row-fluid span12">
<legend><?php echo JText::_("JFORMS_VIEW_PDF_OPTIONS"); ?></legend>

	<?php
	// Generate PDF
	$field = $fieldSet['jform_generate_pdf'];
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
	// Generate PDF
	$field = $fieldSet_OtherOpts['jform_options_layout_pdf'];
	@$field->jdomOptions = array_merge($field->jdomOptions,array(
		'list' => $this->lists['select']['layout_pdf']->list
			));
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
<legend><?php echo JText::_("JFORMS_VIEW_LAYOUT_OPTIONS"); ?></legend>
	<?php
	// Show / Hide title
	$field = $fieldSet_OtherOpts['jform_options_show_title'];
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
	// Layout type
	$field = $fieldSet['jform_layout_type'];
	$field->jdomOptions = array(
		'list' => $this->lists['select']['layout_type']->list
			);
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>	

	<?php
	// Show / Hide wizard nav bar
	$field = $fieldSet_OtherOpts['jform_options_show_wizard_nav'];
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	<?php
	// Generate PDF
	$field = $fieldSet_OtherOpts['jform_options_layout_pre_form'];
	@$field->jdomOptions = array_merge($field->jdomOptions,array(
		'list' => $this->lists['select']['layout_pre_form']->list
			));
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	<?php
	// Generate PDF
	$field = $fieldSet_OtherOpts['jform_options_layout_form'];
	@$field->jdomOptions = array_merge($field->jdomOptions,array(
		'list' => $this->lists['select']['layout_form']->list
			));
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	<?php
	// LAYOUT edit form
	$field = $fieldSet_OtherOpts['jform_options_layout_edit_form'];
	@$field->jdomOptions = array_merge($field->jdomOptions,array(
		'list' => $this->lists['select']['layout_edit_form']->list
			));
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	<?php
	// Generate PDF
	$field = $fieldSet_OtherOpts['jform_options_layout_fly'];
	@$field->jdomOptions = array_merge($field->jdomOptions,array(
		'list' => $this->lists['select']['layout_fly']->list
			));
	?>
	<div class="control-group form-vertical <?php echo 'field-' . $field->id . $field->responsive; ?>">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	
	    <div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
</span>