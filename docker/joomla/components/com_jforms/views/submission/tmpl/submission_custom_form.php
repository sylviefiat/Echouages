<?php
/*
* @version		0.4.4
* @package		jForms
* @subpackage	Submissions
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		GNU GPL v3 or later
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="<?php echo $this->form->jForm->formHash ?>Step1">
	<?php echo $this->layout; ?>
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