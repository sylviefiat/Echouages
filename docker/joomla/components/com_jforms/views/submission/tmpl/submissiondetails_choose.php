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

?>

<div class="payment_chooser">
	<form class="payment_form" action="<?php echo(JRoute::_("index.php")); ?>" method="get" name="adminForm" id="adminForm" enctype='multipart/form-data'>
	<div class="jforms_payments">
	<?php 
		$counter = 0;
		foreach($this->paymentPlugins as $k => $ptype){
			$counter++;
			$image = $ptype->title;
			if(!empty($ptype->image)){
				$image = '<img src="'. $ptype->image .'" title="'. $ptype->title .'"/>';
			}
			
			$checked = '';
			if((empty($this->paymentType) AND $counter == 1) OR $this->paymentType == $ptype->name){
				$checked = ' checked="checked"';
			}
	?>
		
		<div class="pt_payment">
			<div class="pt_input">
				<input id="pt_<?php echo $k ?>"<?php echo $checked; ?> type="radio" value="<?php echo $ptype->name ?>" name="pg">
			</div>
			<div class="pt_lab">
				<label for="pt_<?php echo $k ?>">
					<?php echo $image; ?>
				</label>
			</div>
			<div>
				<?php if(!empty($ptype->client_desc)){ ?>
				<i class="icomoon pt_info icon-info"></i>
				<span class="payment_info_content">
					<?php echo $ptype->client_desc; ?>
				</span>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
		<div class="footer_actions">
			<div class="action_left">
			<?php if ($this->model->canEdit($this->item, true)){ ?>
				<span class="btn jForms_btn-prev">
					<i class="icomoon icon-arrow-left"></i>
					<?php echo JText::_("JFORMS_PREV"); ?>
				</span>
			<?php } ?>
			</div>
			<div  class="action_right">
				<span class="btn jForms_btn-next">
					<?php echo JText::_("JFORMS_NEXT"); ?>
					<i class="icomoon icon-arrow-right"></i>
				</span>
			</div>
		</div>
	</div>
		<?php 

			$jinput = JFactory::getApplication()->input;
			echo JDom::_('html.form.footer', array(
			'dataObject' => $this->item,
			'values' => array(
						'id' => $this->state->get('submission.id'),
						'frm' => $this->item->form_id
					)));
		?>
	</form>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){		
		jQuery(".icon-info.pt_info").each(function(){
			var that = this,
				content = jQuery(that).parent().find('.payment_info_content');
				
			jQuery(this).tooltip({
				html: true,
				container: 'body',
				title: function(){return content.html();}
			})
		});
		
		<?php if(empty($this->paymentType) AND !empty($this->paymentPlugins)){ ?>
			jQuery('.payment_chooser').show();		
		<?php } else { ?>
			jQuery('.payment_chooser').hide();
		<?php } ?>
		
		jQuery('.btn-change-payment').on('click',function(){
			jQuery('.payment_chooser').slideToggle();
		});
		
		<?php if ($this->model->canEdit($this->item, true)){ ?>		
		jQuery('#adminForm .jforms_payments .jForms_btn-prev').on('click',function(){
			jQuery('#adminForm #layout').val('editsubmission');
			jQuery('#adminForm').submit();
		});
		<?php } ?>
		
		jQuery('.jforms_payments .jForms_btn-next').on('click',function(){
			jQuery('#adminForm #task').val('');
			jQuery('#adminForm').submit();			
		});
	});
</script>