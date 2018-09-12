<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Users
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
?>
<div class="grid_wrapper">
	<table  class='table' id='grid-users'>
		<thead>
			<tr>
				<th>
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_NAME", 'a.name', $listDirn, $listOrder ); ?>
				</th>

				<th>
					<?php echo JHTML::_('grid.sort',  "JFORMS_FIELD_USERNAME", 'a.username', $listDirn, $listOrder ); ?>
				</th>

				<th width="10px">
					<?php echo JText::_("JFORMS_FIELD_ID"); ?>
				</th>
			</tr>
		</thead>
	
		<tbody>
			<?php
		//Get the name of the field to populate on return
		$modalObject = JFactory::getApplication()->input->get('object', null, 'cmd');

		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++):
			$row = &$this->items[$i];
			?>

	
			<?php
			//Pickable rows
			//Receive the callback function
			$input = JFactory::getApplication()->input;
			$function	= $input->get('function', 'jSelectItem');
			//Prepare the params to send to the callback
			$pickValue = $row->id;
			$pickLabel = $this->escape(addslashes($row->name));
			$jsPick = "if (window.parent) window.parent.$function('$pickValue', '$pickLabel', '$modalObject');"
			?>
	
			<tr class="<?php echo "row$k"; ?> pickable-row"
				style="cursor:pointer"
				onclick="<?php echo $jsPick; ?>">

				<td>
					<?php echo $row->name;  ?>
				</td>

				<td>
					<?php echo $row->username;  ?>
				</td>

				<td width="10px">
					<?php echo $row->id;  ?>
				</td>

			</tr>
			<?php
			$k = 1 - $k;

		endfor;
		?>
		</tbody>
	</table>
</div>
