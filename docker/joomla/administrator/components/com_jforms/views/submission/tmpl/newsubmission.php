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
// $formId = $jForm->jforms_id = 'jForm'. $jForm->id . ByGiroHelper::generateRandomString();
$formId = $jForm->jforms_id = 'adminForm';

if(!($this->form instanceof JForm)){
	return;
}
?>
<script language="javascript" type="text/javascript">
	//Secure the user navigation on the page, in order preserve datas.
	var holdForm = true;
	window.onbeforeunload = function closeIt(){	if (holdForm) return false;};

jQuery(document).ready(function(){	
	jQuery("#<?php echo $formId ?>").validationEngine();
});
</script>

<div class="component_container">

<?php
$formData = array();
$formData['form'] = $this->form;
$formData['jforms_snapshot'] = $jForm;

JformsHelper::triggerEvents('on_before_display',$formData);

$this->form = $formData['form'];
$this->form->jForm = $jForm = $formData['jforms_snapshot'];

$this->form_task = 'submission.save';
$this->form->isCustom = false;

$this->formParts = JformsHelper::buildFormParts($this->form,$jForm, $this->item->form_data);
$typeForm = '';
if(!empty($jForm->options->amount_type) AND $jForm->options->amount_type == 'dynamic'){
	$typeForm = 'data-type-form="dynamic"';
}
?>
<?php if(!empty($this->currentjForm->options->show_title)){ ?>
	<h2><?php echo $this->title;?></h2>
<?php } ?>
	<form action="index.php" <?php echo $typeForm; ?> method="post" name="<?php echo $formId ?>" id="<?php echo $formId ?>" class='form-validate' enctype='multipart/form-data'>
		<div>
			<ul class="nav nav-tabs jForms_tabs">
				<li>
					<a href="#details"><?php echo JText::_("JFORMS_JFORM_DETAILS"); ?></a>
				</li>
				<li class="active">
					<a href="#formlayout"><?php echo JText::_("JFORMS_JFORM_FORM"); ?></a>
				</li>
			</ul>	
			<div class="tab-content">
				<div class="tab-pane" id="details">		
					<?php echo $this->loadTemplate('details'); ?>
				</div>
				<div class="tab-pane active" id="formlayout">
					<?php echo $this->description; ?>
					<?php if(count($jForm->fieldsets) > 1 AND $jForm->layout_type == 'wizard'){ ?>
						<?php echo $this->loadTemplate('wizard'); ?>
					<?php } else { ?>
						<?php echo $this->loadTemplate('form'); ?>
					<?php } ?>
				</div>
<?php if(!empty($jForm->options) AND ($jForm->options->payment AND $jForm->options->show_price_item)){
$config = JComponentHelper::getParams('com_jforms');
$currency_code = $config->get("currency", "USD");

$currencies = JformsHelper::enumList('submissions', 'currency');
$currency = $currencies[$currency_code];

$basicPrice = $jForm->options->price;
$tax = $jForm->options->tax;
?>
<div class="payment_summary">
<input type="hidden" class="basicPrice" value="<?php echo $basicPrice; ?>"/>
	<table class="table table-striped table-bordered table-condensed">
<?php if($jForm->options->amount_type == 'dynamic'){ ?>
			<tr>
				<td class="summary_label">
					<?php echo JText::_("JFORMS_PAYMENT_BASE_PRICE"); ?>
				</td>

				<td class="summary_price">
					<?php echo JformsHelper::formatPrice( $basicPrice ); ?>
				</td>
			</tr>
		<tbody class="extras"></tbody>
<?php } ?>
			<tr>
				<td class="summary_label">
					<?php echo JText::_("JFORMS_PAYMENT_SUBTOTAL"); ?>
				</td>

				<td class="summary_price">
					<span class="price_variation"></span>
					<?php echo JformsHelper::formatPrice('<span class="subtotal_amount">0</span>'); ?>
				</td>
			</tr>				
			<?php if(!empty($tax)){ ?>
			<tr>
				<td class="summary_label">
					<?php echo JText::_( "JFORMS_PAYMENT_TAX" ); ?> <span class="small">(<?php echo $tax .' %'; ?>)</span>
				</td>

				<td class="summary_price">
					<input type="hidden" class="tax" value="<?php echo $tax; ?>"/>
					+ <?php echo JformsHelper::formatPrice('<span class="tax_amount">0</span>'); ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="summary_label">
					<?php echo JText::_( "JFORMS_PAYMENT_TOTAL" ); ?>
					<span class="small">(<?php echo $currency['text']; ?>)<span>
				</td>

				<td class="summary_price">
					<?php echo JformsHelper::formatPrice('<span class="total_amount">0</span>'); ?>
				</td>
			</tr>
	</table>
</div>
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
						'frm' => $this->frm
						)
					)
				);
		?>
	</form>
	<?php JformsHelper::triggerEvents('on_after_display',$formData); ?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.jForms_tabs a').click(function (e) {
	  e.preventDefault();
	  jQuery(this).tab('show');
	});
});
</script>