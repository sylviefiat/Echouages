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


$model		= $this->model;
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering' && $listDirn != 'desc';

$version = new JVersion();
$checkAll = 'Joomla.checkAll(this);';
if(version_compare($version->RELEASE, '2.5', '<=')){
	$checkAll = 'checkAll(this);';
}
?>
<div class="clearfix"></div>
<div class="">
	<table class='table' id='grid-submissions'>
		<thead>
			<tr>
				<th class="row_id">
					<?php echo JText::_( 'NUM' ); ?>
				</th>

				<?php if ($model->canSelect()): ?>
				<th>
					<?php echo JDom::_('html.form.input.checkbox', array(
						'dataKey' => 'checkall-toggle',
						'title' => JText::_('JGLOBAL_CHECK_ALL'),
						'selectors' => array(
							'onclick' => $checkAll
						)
					)); ?>
				</th>
				<?php endif; ?>

				<th>

				</th>

				<th style="text-align:center">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_ID", 'a.id', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center" width="100px">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_IP_ADDRESS", 'a.ip_address', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center" width="100px">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_CREATED_BY_USERNAME", '_created_by_.username', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_FORM", 'a.form_id', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_FORM_NAME", '_form_id_.name', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center">
					<?php echo JText::_("JFORMS_FIELD_PDF"); ?>
				</th>

				<th style="text-align:center">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_STATUS", 'a.status', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_PAYMENT_STATUS", 'a.payment_status', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_SECRET_PASSWORD", 'a.passphrase', $listDirn, $listOrder ); ?>
				</th>

				<th style="text-align:center" width="100px">
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_CREATION_DATE", 'a.creation_date', $listDirn, $listOrder ); ?>
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
				<td class="row_id">
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>

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

				<td>
					<div class="btn-group">
						<?php if ($model->canEdit()): ?>
							<?php echo JDom::_('html.grid.task', array(
								'commandAcl' => array('core.edit.own', 'core.edit'),
								'enabled' => ((bool)$row->params->get('access-edit')),
								'label' => 'JFORMS_JTOOLBAR_EDIT',
								'num' => $i,
								'task' => 'submission.edit'
							));?>
						<?php endif; ?>
						<?php echo JDom::_('html.link.button.icon', array(
							'icon' => 'eye-open',
							'href' => 'index.php?option=com_jforms&view=submission&layout=submission&cid[]='. $row->id,
							'tooltip' => true,
							'link_title' => JText::_('JFORMS_BTN_VIEW'),
							'domClass' => 'btn-micro'
						));?>
						<?php if ($model->canDelete()): ?>
							<?php echo JDom::_('html.grid.task', array(
								'alertConfirm' => 'JFORMS_TOOLBAR_ARE_YOU_SURE_TO_DELETE_THIS_ITEM',
								'commandAcl' => array('core.delete.own', 'core.delete'),
								'enabled' => ((bool)$row->params->get('access-delete')),
								'label' => 'JFORMS_JTOOLBAR_DELETE',
								'num' => $i,
								'task' => 'submission.delete'
							));?>
						<?php endif; ?>
					</div>
				</td>

				<td style="text-align:center">
					<?php echo $row->id;  ?>
				</td>

				<td style="text-align:center" width="100px">
					<?php echo JDom::_('html.fly', array(
						'dataKey' => 'ip_address',
						'dataObject' => $row,
						'route' => array('view' => 'submission','layout' => 'submission','cid[]' => $row->id)
					));?>
				</td>

				<td style="text-align:center" width="100px">
					<?php echo $row->_created_by_username;  ?>
				</td>

				<td style="text-align:center">
					<?php echo $row->form_id;  ?>
				</td>

				<td style="text-align:center">
					<?php echo $row->_form_id_name;  ?>
				</td>

				<td style="text-align:center">
					<a href="<?php echo JRoute::_("index.php?option=com_jforms&task=file&action=download&path=".$row->pdf.""); ?>"><?php echo basename($row->pdf) ?></a>
				</td>

				<td style="text-align:center">
					<?php echo $this->lists['enum']['submissions.status'][$row->status]['text'];  ?>
				</td>

				<td style="text-align:center">
					<?php echo $this->lists['enum']['submissions.payment_status'][$row->payment_status]['text'];  ?>
				</td>

				<td style="text-align:center">
					<?php echo $row->passphrase;  ?>
				</td>

				<td style="text-align:center" width="100px">
					<?php echo JDom::_('html.fly.datetime', array(
						'dataKey' => 'creation_date',
						'dataObject' => $row,
						'dateFormat' => 'd-m-Y H:i:s'
					));?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		endfor;
		?>
		</tbody>
	</table>
</div>

<!-- Modal -->
<?php 
	$body = JText::_("JFORMS_SELECT_EXPORT_FORMAT"). '<br />
	<select name="export_format" id="export_format">
		<option value="">'. JText::_("JFORMS_SELECT") .'</option>
		<option value="csv">CSV</option>
		<option value="xlsx">XLSX</option>
		<option value="xls">XLS</option>
		<option value="xml">XML</option>
	</select>';
	
	$footer = '<a class="btn btn-cancel" onclick="jQuery(\'#export_format\').val(\'\');" data-dismiss="modal" aria-hidden="true">'. JText::_("JCANCEL") .'</a>'
		.	'<a class="btn btn-primary btn-apply">'. JText::_("JFORMS_DOWNLOAD") .'</a>';
			
	echo JDom::_('html.fly.bootstrap.modal', array(
			'domId' => 'export_modal',
			'title' => JText::_("JFORMS_SELECT_EXPORT_OPTIONS"),
			'body' => $body,
			'footer' => $footer
		));
?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		var jtoolbar_button = jQuery('#toolbar-download > button,#toolbar-download > .toolbar').first();
		var on_click = jtoolbar_button.attr('onclick'),
			boxchecked_required = on_click.indexOf('document.adminForm.boxchecked.value');
		
			jtoolbar_button.attr('onclick','');
			
		jtoolbar_button.on('click',function(){
			if(boxchecked_required >= 0){
				if (document.adminForm.boxchecked.value==0){
					alert('<?php echo JText::_("JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST"); ?>');
				} else {
					jQuery('#export_modal').modal('show');
				}
			} else {
				jQuery('#export_modal').modal('show');
			}
		});
		
		jQuery('#export_modal .btn-apply').on('click',function(){
			var format = jQuery('#export_format').val();
			if(format != ''){
				eval(on_click);
			}
		});
		
		jQuery('#export_modal').on('hidden',function(){
			jQuery('#export_format').val('');
		});
	});
</script>

<?php 
	$body = JText::_("JFORMS_FIELD_FORM"). '<br />
	<select name="frm" id="frm">
		<option value="">'. JText::_("JFORMS_SELECT") .'</option>';
		
	$forms = $this->filters['filter_form_id']->jdomOptions['list'];
	foreach($forms as $fm){
		$body .= '<option value="'. $fm->id .'">ID: '. $fm->id .' - '. $fm->name .'</option>';
	}
	
	$body .= '</select>';
	
	$footer = '<a class="btn btn-cancel" onclick="jQuery(\'#form_id\').val(\'\');" data-dismiss="modal" aria-hidden="true">'. JText::_("JCANCEL") .'</a>'
		.	'<a class="btn btn-primary btn-apply">'. JText::_("JFORMS_GO_TO_FORM") .'</a>';
			
	echo JDom::_('html.fly.bootstrap.modal', array(
			'domId' => 'new_submission_modal',
			'title' => JText::_("JFORMS_NEW_SUBMISSION_OPTIONS"),
			'body' => $body,
			'footer' => $footer
		));
?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		var jtoolbar_button = jQuery('#toolbar-new > button, #toolbar-new > .toolbar').first();
		var on_click = jtoolbar_button.attr('onclick'),
			boxchecked_required = on_click.indexOf('document.adminForm.boxchecked.value');
		
			jtoolbar_button.attr('onclick','');
			
		jtoolbar_button.on('click',function(){
			if(boxchecked_required >= 0){
				if (document.adminForm.boxchecked.value==0){
					alert('<?php echo JText::_("JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST"); ?>');
				} else {
					jQuery('#new_submission_modal').modal('show');
				}
			} else {
				jQuery('#new_submission_modal').modal('show');
			}
		});
		
		jQuery('#new_submission_modal .btn-apply').on('click',function(){
			var format = jQuery('#frm').val();
			if(format != ''){
				eval(on_click);
			}
		});
		
		jQuery('#new_submission_modal').on('hidden',function(){
			jQuery('#frm').val('');
		});
	});
</script>