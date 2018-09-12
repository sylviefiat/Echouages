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


JHtml::addIncludePath(JPATH_ADMIN_JFORMS.'/helpers/html');
JHtml::_('behavior.tooltip');
//JHtml::_('behavior.multiselect');

$model		= $this->model;
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering' && $listDirn != 'desc';


$enabledColumns = $this->params->get('show_columns',array());
?>
<div class="clearfix"></div>
<div class="">
	<table class='table' id='grid-submissions'>
		<thead>
			<tr>
				<?php if ($model->canSelect()): ?>
				<th>
					<?php echo JDom::_('html.form.input.checkbox', array(
						'dataKey' => 'checkall-toggle',
						'title' => JText::_('JGLOBAL_CHECK_ALL'),
						'selectors' => array(
							'onclick' => 'Joomla.checkAll(this);'
						)
					)); ?>
				</th>
				<?php endif; ?>

				<?php if(in_array('username',$enabledColumns)){ ?>
					<th style="text-align:left">
						<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_CREATED_BY_USERNAME", '_created_by_.username', $listDirn, $listOrder ); ?>
					</th>
				<?php } ?>
				
				<?php if(in_array('form',$enabledColumns)){ ?>				
					<th style="text-align:left">
						<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_FORM_NAME", '_form_id_.name', $listDirn, $listOrder ); ?>
					</th>
				<?php } ?>

				<?php if(in_array('creation_date',$enabledColumns)){ ?>				
					<th style="text-align:center">
						<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_CREATION_DATE", 'a.creation_date', $listDirn, $listOrder ); ?>
					</th>
				<?php } ?>

				<?php if(in_array('ip_address',$enabledColumns)){ ?>
					<th style="text-align:center">
						<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_IP_ADDRESS", 'a.ip_address', $listDirn, $listOrder ); ?>
					</th>
				<?php } ?>

				<?php if(in_array('pdf',$enabledColumns)){ ?>
					<th style="text-align:center">
						<?php echo JText::_("JFORMS_FIELD_PDF"); ?>
					</th>
				<?php } ?>

				<?php if(in_array('status',$enabledColumns)){ ?>
					<th style="text-align:center">
						<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_STATUS", 'a.status', $listDirn, $listOrder ); ?>
					</th>
				<?php } ?>

				<?php if(in_array('payment_status',$enabledColumns)){ ?>
					<th style="text-align:center">
						<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_PAYMENT_STATUS", 'a.payment_status', $listDirn, $listOrder ); ?>
					</th>
				<?php } ?>
<?php /*  ?>
				<th style="text-align:center">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_SECRET_PASSWORD", 'a.passphrase', $listDirn, $listOrder ); ?>
				</th>
<?php  */ ?>
				<th>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++):
			$row = &$this->items[$i];
			?>

			<tr class="<?php echo "row$k"; ?>">
				<?php if ($model->canSelect()): ?>
				<td>
					<?php if ($row->params->get('access-edit') || $row->params->get('tag-checkedout')): ?>
						<?php echo JDom::_('html.grid.checkedout', array(
													'dataObject' => $row,
													'num' => $i
														));
						?>
					<?php endif; ?>
				</td>
				<?php endif; ?>

				<?php if(in_array('username',$enabledColumns)){ ?>
					<td style="text-align:left">
						<?php echo $row->_created_by_username;  ?>
					</td>
				<?php } ?>

				<?php if(in_array('form',$enabledColumns)){ ?>
					<td style="text-align:left">
						<a class="form_link" target="_blank" href="<?php echo JRoute::_('index.php?option=com_jforms&view=submission&layout=submission&frm='. $row->form_id , false); ?>">
						<?php echo $row->_form_id_name;  ?>
						</a>
					</td>
				<?php } ?>

				<?php if(in_array('creation_date',$enabledColumns)){ ?>
					<td style="text-align:center">
						<?php echo JDom::_('html.fly.datetime', array(
							'dataKey' => 'creation_date',
							'dataObject' => $row,
							'dateFormat' => 'd-m-Y H:i:s'
						));?>
					</td>
				<?php } ?>

				<?php if(in_array('ip_address',$enabledColumns)){ ?>
					<td style="text-align:center">
						<?php echo $row->ip_address;  ?>

					</td>
				<?php } ?>

				<?php if(in_array('pdf',$enabledColumns)){ ?>
					<td style="text-align:center">
					<?php if($row->_form_id_generate_pdf){ ?>
						<?php if(!empty($row->pdf)){ ?>
							<a href="<?php echo JRoute::_('index.php?option=com_jforms&task=file&path='. $row->pdf, false); ?>"><?php echo basename($row->pdf) ?></a>
						<?php } else { ?>
							<a class="btn btn-warning btn-small btn-printpdf" href="<?php echo JRoute::_('index.php?option=com_jforms&view=submissions&layout=default&task=submission.printpdf&cid[0]='. $row->id .'&'. JSession::getFormToken() .'=1', false); ?>"><?php echo JText::_("JFORMS_PRINT_PDF"); ?></a>
						<?php } ?>					
					<?php } ?>
					</td>
				<?php } ?>

				<?php if(in_array('status',$enabledColumns)){ ?>
					<td style="text-align:center">
						<?php echo $this->lists['enum']['submissions.status'][$row->status]['text'];  ?>
					</td>
				<?php } ?>

				<?php if(in_array('payment_status',$enabledColumns)){ ?>
					<td style="text-align:center">
						<?php echo $this->lists['enum']['submissions.payment_status'][$row->payment_status]['text'];  ?>
					</td>
				<?php } ?>
<?php /* ?>
				<td style="text-align:center">
					<?php echo $row->passphrase;  ?>
				</td>
<?php */ ?>
				<td>
					<a class="btn btn-small btn-info" href="<?php echo JRoute::_('index.php?option=com_jforms&view=submission&layout=submissiondetails&cid[]='. $row->id , false); ?>"><?php echo JText::_("JFORMS_DETAILS"); ?></a>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		endfor;
		?>
		</tbody>
	</table>
</div>
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
