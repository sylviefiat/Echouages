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

$created_by = JText::_("JFORMS_GUEST");
if(!empty($this->item->created_by)){
	$created_by = '<a href="index.php?option=com_users&task=user.edit&id='. $this->item->created_by .'" target="_blank">'. $this->item->_created_by_username .'</a>';
}

$width = 'width: 25%';
if(isset($this->item->_form_id_generate_pdf) AND !$this->isPdf){
	$width = 'width: 20%';
}
?>
<table class="item_info">
	<tr>
		<td style="<?php echo $width; ?>"><span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_CREATED_BY_USERNAME" ); ?>
			</span>:
			<div class="clear"></div>
			<?php echo $created_by; ?>
		</td>
		<td style="<?php echo $width; ?>"><span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_FORM_NAME" ); ?>
			</span>:
			<div class="clear"></div>
			<?php echo JDom::_('html.badge', array(
					'color' => 'success',
					'content' => $this->item->form_id
				));?>
				<a href="index.php?view=form&layout=form&option=com_jforms&cid[]=<?php echo $this->item->form_id ?>" target="_blank"><?php echo $jForm->name_ml; ?></a>
		</td>
		<td style="<?php echo $width; ?>"><span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_CREATION_DATE" ); ?>
			</span>:
			<div class="clear"></div>
			<?php echo JDom::_('html.fly.datetime', array(
					'dataKey' => 'creation_date',
					'dataObject' => $this->item,
					'dateFormat' => 'd-m-Y H:i:s'
				));?>
		</td>
		<td style="<?php echo $width; ?>"><span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_IP_ADDRESS" ); ?>
			</span>:
			<div class="clear"></div>
			<?php echo $this->item->ip_address; ?>
		</td>
		<?php if(isset($this->item->_form_id_generate_pdf) AND !$this->isPdf){ ?>
		<td style="<?php echo $width; ?>"><span class="info_label">
			<?php echo JText::_( "JFORMS_FIELD_PDF" ); ?>
			</span>:
			<div class="clear"></div>
			<a href="<?php echo JRoute::_("index.php?option=com_jforms&task=file&action=download&path=".$this->item->pdf.""); ?>"><?php echo basename($this->item->pdf) ?></a>
		</td>
		<?php } ?>
	</tr>
</table>