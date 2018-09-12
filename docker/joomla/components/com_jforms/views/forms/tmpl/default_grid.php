<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Forms
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


JHtml::addIncludePath(JPATH_ADMIN_JFORMS.'/helpers/html');
JHtml::_('behavior.tooltip');

$model		= $this->model;
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering' && $listDirn != 'desc';
JDom::_('framework.sortablelist', array(
	'domId' => 'grid-forms',
	'listOrder' => $listOrder,
	'listDirn' => $listDirn,
	'formId' => 'adminForm',
	'ctrl' => 'forms'
));
?>

<?php
$config = JComponentHelper::getParams('com_jforms');
$currency_code = $config->get("currency", "USD");
$currencies = JformsHelper::enumList('submissions', 'currency');
$currency = $currencies[$currency_code];

// check no forms has price
$payment_enabled = false;
foreach($this->items as $it){
	if(!empty($it->options->payment)){
		$payment_enabled = true;
		break;
	}
}
?>

<?php if(count( $this->items )){ ?>
	<div class="clearfix"></div>
	<table class="forms_list table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>N.</th>
				<th style="text-align:left">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_NAME", 'a.name', $listDirn, $listOrder ); ?>
				</th>
				<th><?php if($payment_enabled) echo JText::_('JFORMS_FIELDS_PRICE'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++):
			$row = &$this->items[$i];
			?>
			<tr>
				<td><?php echo $i +1 ?></td>
				<td class="it_main">
					<h5 class="form_name">
						<?php echo $row->name_ml ;?>
					</h5>
					<?php echo $row->description_ml; ?>
				</td>
				<td class="price">
<?php
		if(!empty($row->options) AND ($row->options->payment AND $row->options->show_price_list)){
			$basicPrice = $row->options->price;
			$tax = $row->options->tax;
			$amount_type = $row->options->amount_type;
			?>
			<?php echo JformsHelper::formatPrice( $basicPrice ); ?>
			<?php
			if($amount_type != 'fixed'){
				echo '<span class="small">('. JText::_('JFORMS_FIELDS_BASE') .')</span>';
			} ?>
<?php } ?>				
				</td>
				<td class="it_actions">
					<a class="form_link" href="<?php echo JRoute::_('index.php?option=com_jforms&view=submission&layout=submission&frm='. $row->id , false); ?>">
						<span style="white-space: nowrap;" class="btn btn-success btn-small">
						<?php echo JText::_("JFORMS_BTN_VIEW"); ?>
						</span>
					</a>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		endfor;
		?>
		</tbody>
	</table>
<?php } else {
	echo JText::_("JFORMS_NO_FORMS_YET");
} ?>



