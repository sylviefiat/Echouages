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

$this->form->isCustom = (!empty($this->layout) AND $this->layout != 'default') ? true : false;
$this->formParts = JformsHelper::buildFormParts($this->form,$jForm);

$typeForm = '';
if(!empty($jForm->options->amount_type) AND $jForm->options->amount_type == 'dynamic'){
	$typeForm = 'data-type-form="dynamic"';
}
?>
<?php if(!empty($jForm->options->show_title)){ ?>
	<h2><?php echo $this->title;?></h2>
<?php } ?>
	<form action="index.php" <?php echo $typeForm; ?> method="post" name="<?php echo $formHash ?>" id="<?php echo $formHash ?>" class='form-validate' enctype='multipart/form-data' autocomplete="off">
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
