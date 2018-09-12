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

$jForm = $this->item->jforms_snapshot;
$subTotal = $this->subTotal;
$tax = $this->tax;

$currency_code = $this->currency;
$currencies = JformsHelper::enumList('submissions', 'currency');
$currency = $currencies[$currency_code];

$basicPrice = $jForm->options->price;
?>
<div class="payment_summary">
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
		<?php foreach($this->calcDetails as $det){ ?>	
			<tr>
				<td class="summary_label">
					<?php echo $det->info; ?>
				</td>
				<td class="summary_price">
					<?php echo $det->variation .' '. JformsHelper::formatPrice( $det->amount_variation ); ?>
				</td>
			</tr>
		<?php } ?>	
<?php } ?>
			<tr>
				<td class="summary_label">
					<?php echo JText::_("JFORMS_PAYMENT_SUBTOTAL"); ?>
				</td>

				<td id="subtotal_amount" class="summary_price">
					<?php echo JformsHelper::formatPrice($subTotal); ?>
				</td>
			</tr>				
			<?php $tax_amount = 0;
				if(!empty($tax)){ ?>
			<tr>
				<td class="summary_label">
					<?php echo JText::_( "JFORMS_PAYMENT_TAX" ); ?> <span class="small">(<?php echo $tax .' %'; ?>)</span>
				</td>

				<td class="summary_price">
					<?php
					$tax_amount = round($subTotal * $tax / 100, 2);
					?>
					+ <?php echo JformsHelper::formatPrice($tax_amount); ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="summary_label">
					<?php echo JText::_( "JFORMS_PAYMENT_TOTAL" ); ?>
					<span class="small">(<?php echo $currency['text']; ?>)<span>
				</td>

				<td class="jshop_total summary_price">
					<?php echo JformsHelper::formatPrice(($tax_amount + $subTotal)); ?>
				</td>
			</tr>
	</table>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('form#paymentForm').submit(function(e){		
		if(typeof jQuery.msg != 'undefined'){
			var msg_opts = {
				autoUnblock : false,
				bgPath: (window.jQuery_msg.bgPath || ''),
				content: (window.jQuery_msg.content || 'Please wait...')
			};
			msg_opts = jQuery.extend({},window.jQuery_msg,msg_opts);
			jQuery.msg(msg_opts);
		}
	});
});
</script>