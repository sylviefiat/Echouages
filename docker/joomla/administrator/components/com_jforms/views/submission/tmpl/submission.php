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

$formOptions = array(
	'layout' => 'table'
);

$jForm = $this->item->jforms_snapshot;
$form = $jForm->form;

if(!empty($jForm->layouts)){
	$jForm->layouts = ByGiroHelper::groupArrayByValue($jForm->layouts, 'type');
	
	foreach($jForm->layouts as $type => $lays){
		$jForm->layouts[$type] = ByGiroHelper::groupArrayByValue($lays, 'id',false);
	}
}


if($this->isPdf){	
	if(!empty($this->layout_pdf)){
		$lay_route = explode('_',$this->layout_pdf);		
		
		if($lay_route[0] == 'c'){
			@$layouts_pdf = $this->currentjForm->layouts['pdf'];
		} else {
			@$layouts_pdf = $jForm->layouts['pdf'];
		}
		
		@$lay = $layouts_pdf[$lay_route[1]];
		if(!empty($lay)){
			$this->layout = $lay->template;
		}
	}
}

$lay_var = 'fly';
if(empty($this->layout)){
	@$lay = $jForm->layouts[$lay_var][$jForm->options->{'layout_'. $lay_var}];
	if(!empty($lay)){
		$this->layout = $lay->template;
	}
}

$form->isCustom = (!empty($this->layout)) ? true : false;
$this->formParts = JformsHelper::buildFormParts($form,$jForm,$this->item->form_data,$formOptions);

$statuses = $this->lists['enum']['submissions.payment_status'];
?>
<div class="component_container">

<form action="<?php echo(JRoute::_("index.php")); ?>" method="post" name="adminForm" id="adminForm" enctype='multipart/form-data'>

	<?php
	$compat = '1.6';
	$version = new JVersion();
	if ($version->isCompatible('3.0'))
		$compat = '3.0';
	?>
	<div>

		<!-- BRICK : toolbar_sing -->
		<?php echo $this->renderToolbar();?>
	</div>

	<?php if($form->isCustom){ ?>
		
		<div>
			<?php echo $this->loadTemplate('custom'); ?>
		</div>
		
	<?php } else { ?>
<h2>
	<?php echo $this->title;?>
	<?php if(!empty($this->currentjForm->options->payment)){ ?>
		<span class="small">(<?php echo JText::_("JFORMS_FIELD_PAYMENT_STATUS"); ?>: <span class="label label-warning"><?php echo $statuses[$this->item->payment_status]['text'] ?></span>)</span>
	<?php } ?>
</h2>
		<div>
			<?php echo $this->description; ?>
		</div>
				
		<?php if ($compat == '3.0'): ?>
		<div class="row-fluid">
			<div id="contents" class="span12">		
		<?php elseif ($compat == '1.6'): ?>
		<div>
			<div>		
		<?php endif; ?>
				<div>
					<!-- BRICK : details -->
					<?php echo $this->loadTemplate('details'); ?>
				</div>
				<?php echo $this->loadTemplate('summary'); ?>
				<div>
					<?php echo $this->loadTemplate('data'); ?>
				</div>
				
			</div>
		</div>
	
	<?php } ?>
	
		<input name="_download" type="hidden" id="_download" value=""/>

	<?php 
		$jinput = JFactory::getApplication()->input;
		echo JDom::_('html.form.footer', array(
		'dataObject' => $this->item,
		'values' => array(
					'id' => $this->state->get('submission.id'),
					'preview' => false,
					'download_pdf' => 1
				)));
	?>
</div>

<!-- Modal -->
<?php 
if(!$this->isPdf){
	$body = JText::_("JFORMS_FIELD_LAYOUT_PDF"). '<br />
	<select name="layout_pdf" id="layout_pdf">
			<optgroup class="l1" label="'. JText::_("JFORMS_LAYOUTS_STORED") .'">
				<option value="s_">'. JText::_("JFORMS_DEFAULT") .'</option>';
		
	unset($lay);
	$layouts_pdf = (!empty($jForm->layouts['pdf'])) ? $jForm->layouts['pdf'] : array();
	foreach($layouts_pdf as $lay){
		$body .= '<option value="s_'. $lay->id .'">ID: '. $lay->id .' - '. $lay->name .'</option>';
	}
	$body .= '</optgroup>';
	
	if(!empty($this->currentjForm->layouts['pdf'])){
		$body .= '<optgroup class="l1" label="'. JText::_("JFORMS_LAYOUTS_CURRENT") .'">';

		unset($lay);
		$layouts_pdf = $this->currentjForm->layouts['pdf'];
		foreach($this->currentjForm->layouts['pdf'] as $lay){
			$body .= '<option value="c_'. $lay->id .'">ID: '. $lay->id .' - '. $lay->name .'</option>';
		}
		$body .= '</optgroup>';
	}
	
	$body .= '</select>';
	
	$footer = '<a class="btn btn-cancel" onclick="jQuery(\'#print_pdf\').val(\'\');" data-dismiss="modal" aria-hidden="true">'. JText::_("JCANCEL") .'</a>'
		.	'<a class="btn btn-primary btn-preview">'. JText::_("JFORMS_PREVIEW") .'</a>'
		.	'<a class="btn btn-success btn-apply">'. JText::_("JFORMS_DOWNLOAD") .'</a>';
			
	echo JDom::_('html.fly.bootstrap.modal', array(
			'domId' => 'options_modal',
			'title' => JText::_("JFORMS_SELECT_PRINT_PDF_OPTIONS"),
			'body' => $body,
			'footer' => $footer
		));
?>
<script type="text/javascript">
	jQuery(document).ready(function(){
	
		var jtoolbar_button = jQuery('#toolbar-print button');
		var on_click = jtoolbar_button.attr('onclick'),
			boxchecked_required = on_click.indexOf('document.adminForm.boxchecked.value');
		
			jtoolbar_button.attr('onclick','');
			
		jtoolbar_button.on('click',function(){
			if(boxchecked_required >= 0){
				if (document.adminForm.boxchecked.value==0){
					alert('<?php echo JText::_("JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST"); ?>');
				} else {
					jQuery('#options_modal').modal('show');
				}
			} else {
				jQuery('#options_modal').modal('show');
			}
		});
		
		jQuery('#options_modal .btn-apply').on('click',function(){
			jQuery('#options_modal').modal('hide');
			jQuery('#preview').val('');
			eval(on_click);
		});
		
		jQuery('#options_modal .btn-preview').on('click',function(){
			jQuery('#options_modal').modal('hide');
			jQuery('#preview').val(1);
			
			eval(on_click);
		});
		
		jQuery('#options_modal').on('hidden',function(){
			jQuery('#layout_pdf').val('');
		});
	});
</script>
<?php } ?>