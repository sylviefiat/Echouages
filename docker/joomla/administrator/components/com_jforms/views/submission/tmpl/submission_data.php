<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Submissions
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$options = $this->item->jforms_snapshot->options;
$steps = $this->formParts['steps'];

// group formParts
$formParts_fieldsets = ByGiroHelper::groupArrayByValue($this->formParts['fieldsets'], 'stepId');
$formParts_fields = ByGiroHelper::groupArrayByValue($this->formParts['fields'], 'fieldsetName');

	$counter = 0;
	foreach($steps as $step){
		$counter++;		
		if(empty($formParts_fieldsets[$step['id']])){
			continue;
		}
		$fieldsets = $formParts_fieldsets[$step['id']];
		
		?>
		<div class="submitted_data <?php echo $step['class'] ?>">
			<?php echo $step['description'] ?>
			<?php
			unset($fset);
			$countFset = 0;
			foreach($fieldsets as $key => $fset){
				$countFset++;
				if(empty($formParts_fields[$key])){
					continue;
				}
				$fields = $formParts_fields[$key];
			?>
				<div class="fieldsform <?php echo @$fset['class']; ?>">
					<h3 class="legend"><?php echo $fset['label']; ?></h3>
					<?php echo $fset['description']; ?>
					
					<table class="table table-striped table-bordered table-condensed">
						<?php 
							unset($fi);
							foreach($fields as $fi){
								if(@$fi['xml']['type'] == 'ckfile'){
									$fi['field']->jdomOptions['indirect'] = false;
								}
								echo ByGiroHelper::getHtmlField($fi['html'],$fi['field'],'output',$this->item->jforms_snapshot->form);
							} ?>
					</table>
				</div>
		<?php } ?>
		</div>
<?php } ?>
