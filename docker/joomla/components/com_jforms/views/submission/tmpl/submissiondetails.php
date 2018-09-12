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
	<div>
		<div>

			<!-- BRICK : fly_1 -->
			<?php echo $this->loadTemplate('details'); ?>
		</div>
		<div>
			<?php 
			if($this->checkoutProcess){ ?>
				<h3>
					<?php echo JText::_("JFORMS_LAYOUT_PAYMENT_DETAILS") ?>
					<?php if(!empty($this->currentjForm->options->payment) AND !empty($this->paymentType) AND !empty($this->paymentPlugins)){ ?>
						<span class="btn-change-payment btn btn-success btn-mini"><?php echo JText::_("JFORMS_CHANGE_PAYMENT_GATEWAY"); ?></span>
					<?php } ?>
				</h3>
				<?php echo $this->loadTemplate('summary'); ?>
				<?php echo $this->paymentInstructions; ?>
				<?php echo $this->loadTemplate('choose'); ?>
			<?php
				if(empty($this->paymentPlugins)){
					echo '<p class="no_payment_gateways">'. JText::_("JFORMS_NO_PAYMENTS_ENABLED") .'</p>';
				}
				
				if(!empty($this->paymentForm)){
					echo $this->paymentForm;
				}
			}
			?>
			<?php echo $this->loadTemplate('data'); ?>
		</div>
	</div>
	<?php } ?>
</div>

