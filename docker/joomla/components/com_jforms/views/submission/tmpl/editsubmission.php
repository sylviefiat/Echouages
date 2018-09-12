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


JDom::_('framework.jquery.condrules');
JDom::_('framework.jquery.msg');
JDom::_('framework.jquery.extrajs');

$jForm = $this->form->jForm;
$formHash = $jForm->formHash = 'jForm'. $jForm->id . ByGiroHelper::generateRandomString();

if(!($this->form instanceof JForm)){
	return;
}
?>
<div class="component_container">

<?php
$formData = array();
$formData['form'] = $this->form;
$formData['jforms_snapshot'] = $jForm;

JformsHelper::triggerEvents('on_before_display',$formData);

$this->form = $formData['form'];
$this->form->jForm = $jForm = $formData['jforms_snapshot'];

if(!empty($jForm->layouts)){
	$jForm->layouts = ByGiroHelper::groupArrayByValue($jForm->layouts, 'type');
	
	foreach($jForm->layouts as $type => $lays){
		$jForm->layouts[$type] = ByGiroHelper::groupArrayByValue($lays, 'id',false);
	}
}

$this->form_task = 'submission.save';
$lay_var = 'form';
if(!empty($this->form->preform)){
	$lay_var = 'pre_form';
	$this->form_task = 'submission.forward';
}

if(empty($this->layout)){
	@$lay = $jForm->layouts[$lay_var][$jForm->options->{'layout_'. $lay_var}];
	if(!empty($lay)){
		$this->layout = $lay->template;
	}
}

$this->form->isCustom = (!empty($this->layout)) ? true : false;
$this->formParts = JformsHelper::buildFormParts($this->form,$jForm, $this->item->form_data);

?>
	<h2><?php echo $this->title;?></h2>
	<form action="index.php" method="post" name="<?php echo $formHash ?>" id="<?php echo $formHash ?>" class='form-validate' enctype='multipart/form-data'>
		<div>
		
			<div>
				<?php echo $this->description; ?>
			</div>
			
			<div>
				<?php if($this->form->isCustom){ ?>
					<?php echo $this->loadTemplate('custom'); ?>
				<?php } else if(count($jForm->fieldsets) > 1 AND $jForm->layout_type == 'wizard'){ ?>
					<?php echo $this->loadTemplate('wizard'); ?>
				<?php } else { ?>
					<?php echo $this->loadTemplate('form'); ?>
				<?php } ?>
			</div>
		</div>

			<input name="_download" type="hidden" id="_download" value=""/>

		<?php 
		echo @JDom::_('html.form.footer', array(
					'dataObject' => $this->item,
					'values' => array(
						'option' => 'com_jforms',
						'view' => 'submission',
						'layout' => 'submission',
						'id' => $this->submission_id,
						'frm' => $this->frm,
						'jform[page_url]' => $this->page_url,
						'jform[page_title]' => $this->page_title
						)
					)
				);
		?>
	</form>
	<?php JformsHelper::triggerEvents('on_after_display',$formData); ?>
</div>
<script language="javascript" type="text/javascript">
	//Secure the user navigation on the page, in order preserve datas.
	var holdForm = false;
	window.onbeforeunload = function closeIt(){	if (holdForm) return false;};
	
	jQuery(document).ready(function(){
		jQuery("#<?php echo $formHash ?>").validationEngine();
		
		var vhref = jQuery(location).attr('href'),
			vtitle = jQuery(this).attr('title'),
			hrefInput = jQuery("#<?php echo $formHash ?>").find('[name="jform[page_url]"]'),
			titleInput = jQuery("#<?php echo $formHash ?>").find('[name="jform[page_title]"]');
		
		if(hrefInput.val() == ''){
			hrefInput.val(vhref);
		}
		
		if(titleInput.val() == ''){
			titleInput.val(vtitle);
		}
	});
</script>
