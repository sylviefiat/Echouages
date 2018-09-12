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

$jForm = $this->currentjForm;
$width = 'width: 33.25%';
if(isset($jForm->generate_pdf) AND !$this->isPdf){
	$width = 'width: 25%';
}
?>
<table class="item_info">
	<tr>
		<td>
			<span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_FORM_NAME" ); ?>
			</span>:<br /><?php echo $jForm->name_ml; ?>
		</td>
		<td>
			<span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_CREATION_DATE" ); ?>
			</span>:<br /><?php echo JDom::_('html.fly.datetime', array(
					'dataKey' => 'creation_date',
					'dataObject' => $this->item,
					'dateFormat' => 'd-m-Y H:i:s'
				));?>
		</td>
		<td>
			<span class="info_label">
			<?php echo JText::_( "JFORMS_SUBMISSION_ID" ); ?>
			</span>:<br />
			<?php echo $this->item->id; ?>
		</td>
		<td>
			<span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_IP_ADDRESS" ); ?>
			</span>:<br /><?php echo $this->item->ip_address; ?>
		</td>
		<?php if(isset($jForm->generate_pdf) AND !$this->isPdf){ ?>
		<td>
			<span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_PDF" ); ?>
			</span>:<br />
			<?php if(!empty($this->item->pdf) AND is_file(JPath::clean(JPATH_SITE .DS. JformsHelper::getDirectory($this->item->pdf)))){ ?>
				<a href="<?php echo JRoute::_('index.php?option=com_jforms&task=file&path='. $this->item->pdf .'&action=download', false); ?>"><?php echo basename($this->item->pdf); ?></a>
			<?php } else { ?>
				<a class="btn btn-warning btn-small btn-printpdf" href="<?php echo JRoute::_('index.php?option=com_jforms&view=submission&layout=submissiondetails&task=submission.printpdf&cid[0]='. $this->item->id .'&'. JSession::getFormToken() .'=1', false); ?>"><?php echo JText::_("JFORMS_PRINT_PDF"); ?></a>
			<?php } ?>
		</td>
		<?php } ?>
	</tr>
</table>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.btn-printpdf').on('click',function(){
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
