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

$options = $this->form->jForm->options;
$steps = $this->formParts['steps'];

// group formParts
$formParts_fieldsets = ByGiroHelper::groupArrayByValue($this->formParts['fieldsets'], 'stepId');
$formParts_fields = ByGiroHelper::groupArrayByValue($this->formParts['fields'], 'fieldsetName');
?>

<div id="<?php echo $this->form->jForm->formHash ?>Step1">
<?php
	$counter = 0;
	foreach($steps as $step){
		$counter++;		
		if(empty($formParts_fieldsets[$step['id']])){
			continue;
		}
		$fieldsets = $formParts_fieldsets[$step['id']];
		
		?>
		<div class="fieldsform <?php echo @$step['class'] ?>">
			<?php echo @$step['description'] ?>
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
				<fieldset class="fieldsform <?php echo @$fset['class']; ?>">
					<legend><?php echo @$fset['label']; ?></legend>					
					<?php echo @$fset['description']; ?>
					<?php 
						unset($fi);
						foreach($fields as $fi){
							echo ByGiroHelper::getHtmlField($fi['html'],$fi['field'],'input',$this->form);
						}
					?>
				</fieldset>
		<?php } ?>
		</div>
<?php } ?>

	<div class="footer_actions">
		<div class="action_left">
		</div>
		<div  class="action_right">
			<span style="" class="btn btn-success jForms_btn-next">
				<?php echo JText::_("JFORMS_SUBMIT"); ?>
			</span>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function(){
	var formName = '<?php echo $this->form->jForm->formHash ?>';	
	jQuery('#'+formName+'Step1').find('.jForms_btn-next').on('click',function(e,data){
		if(checkFormStep(1,formName)){
			Joomla.submitform('<?php echo $this->form_task; ?>','#'+formName);
		}
	});
});
</script>
