<?php

/*
* @version		0.4.4
* @package		jForms
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		GNU GPL v3 or later
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$options = array();
if(isset($displayData['extraOptions'])){
	$options = $displayData['extraOptions'];
}

$form_data = ByGiroHelper::arrayToObject($displayData['form_data']);
$mainForm = ByGiroHelper::arrayToObject($displayData['jforms_snapshot']);


if(empty($this->layout) AND !empty($this->item->options->$lay_var)){
	$this->formParts['layouts'] = ByGiroHelper::groupArrayByValue($this->formParts['layouts'], 'type');
	
	if(!empty($this->formParts['layouts']['type'])){
		$this->formParts['layouts']['type'][$lay_var] = ByGiroHelper::groupArrayByValue($this->formParts['layouts']['type'][$lay_var], 'id');
	}
	
	@$layout_form = $this->formParts['layouts']['type'][$lay_var][$this->item->options->$lay_var];
	if(!empty($layout_form)){
		$this->layout = $layout_form->template;
	}
}

/*

			if(method_exists($field,'getXML')){
				$ele = $field->getXML();
				$attributes = current($ele->attributes());
			}

*/
?>
<h1><?php echo $mainForm->name_ml ?></h1>
<?php echo $mainForm->description_ml; ?>

<?php
$k = 0;
foreach($mainForm->fieldsets as $jFset){
	@$form = $jFset->form;
	if(!($form instanceof JForm)){
		continue;
	}
	
	
	$jFset = ByGiroHelper::getMlFields($jFset,array('name','description'));
	
	echo '<div class="fset_container">';
	if($jFset->name_ml != ''){
		echo '<h3 style="font-style: italic;">'. $jFset->name_ml .'</h3>';
	}
	
	if($jFset->description_ml != ''){
		echo $jFset->description_ml;
	}

	$unwantedFields = array('hidden','ckcaptcha');
	$fieldsets = $form->getFieldsets();

	$countFsets = count($fieldsets);
	foreach($fieldsets as $k => $fset){
		// repeatable fieldset
		if(strpos($fset->name,'_modal') !== false){
			continue;
		}	
	
		// check ACL
		if(!empty($fset->canView) AND class_exists('ByGiroHelper') AND !(isset($options['viewAll']) AND $options['viewAll'])){
			$fset->canView = ByGiroHelper::canAccess($fset->canView);					
		} else {
			$fset->canView = true;
		}

		if(!$fset->canView){
			continue;
		}	
	
		$legend = '';
		if($countFsets > 1){
			if($fset->label != ''){
				$label = JText::_($fset->label);
			} else {
				$label = JText::_('JFORMS_SUBSTEP') .' '. ($k +1);
			}
			$legend = '<legend class="jforms">'. $label .'</legend>';
		}

		$fset_description = '';
		if($fset->description != ''){
			$fset_description = JText::_($fset->description);
		}
		
		$fields = $form->getFieldset($fset->name);
		$fieldsToRender = array();
		foreach($fields as $fi){
			if(in_array($fi->type,$unwantedFields)){
				continue;
			}
			
			$fieldName = $fi->fieldname;
			$groups = $fi->group;
		
			if(!isset($options['printAll']) OR !$options['printAll']){
				if(!$fset->printable OR $fset->printable != 'true'){
					continue;
				}
				
				$printable = $form->getFieldAttribute($fieldName,'printable',false,$groups);
				if($printable == 'false'){
					continue;
				}
			}
			
			$fieldsToRender[] = $fieldName;
		}
		
		if(count($fieldsToRender) == 0){
			continue;
		}

		?>

<fieldset class="<?php if(isset($fset->class)){echo $fset->class; } ?>">
<?php echo $legend; ?>
<?php echo $fset_description; ?>
<?php if(isset($fset->repeatable) AND $fset->repeatable){
			echo JDom::_('html.list.table', array(
							'form' => $form,
							'domClass' => 'table table-striped table-bordered table-condensed fieldset_table',
							'fieldsetName' => $fset->name,
							'fieldsToRender' => $fieldsToRender,
							'dataList' => $form_data[$fset->name],
							'cid' => $displayData['id'],
							'indirect' => 'direct',
							'extraOptions' => $options,
							'loadItemsByJs' => false
						));		
		} else {
			echo JDom::_('html.object.table', array(
							'form' => $form,
							'domClass' => 'table table-striped table-bordered table-condensed fieldset_table',
							'fieldsetName' => $fset->name,
							'fieldsToRender' => $fieldsToRender,
							'dataObject' => $form_data,
							'cid' => $displayData['id'],
							'indirect' => 'direct',
							'extraOptions' => $options,
							'tmplEngine' => ''
						));
		} ?>
</fieldset><br />
	<?php } ?>
	</div>
<?php } ?>

