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


$enabledFilters = $this->params->get('filters',array());
?>
<div class="component_container">
<h2><?php echo $this->title;?></h2>
<form action="<?php echo(JRoute::_("index.php")); ?>" method="post" name="adminForm" id="adminForm">
	<div>
		<div>

			<?php if(in_array('status',$enabledFilters)){ ?>
				<div class="pull-left">
					<?php echo $this->filters['filter_status']->input;?>
				</div>
			<?php } ?>
			
			<?php if(in_array('payment_status',$enabledFilters)){ ?>
				<div class="pull-left">
					<?php echo $this->filters['filter_payment_status']->input;?>
				</div>
			<?php } ?>
			
			<?php if(in_array('creation_date',$enabledFilters)){ ?>
				<div class="pull-left">
						<?php echo $this->filters['filter_creation_date_from']->input;?>
						<?php echo $this->filters['filter_creation_date_to']->input;?>
				</div>
			<?php } ?>

			<?php if(in_array('form',$enabledFilters)){ ?>
				<div class="pull-left">
					<?php echo $this->filters['filter_form_id']->input;?>
				</div>
			<?php } ?>

			<div class="pull-right">
				<?php echo $this->filters['limit']->input;?>
			</div>
			
			<?php if(in_array('search',$enabledFilters)){ ?>
				<div class="clearfix"></div>
				<div class="pull-right">
					<?php echo $this->filters['search_search']->input;?>
				</div>
			<?php } ?>

		</div>
		<div>

			<!-- BRICK : grid -->
			<?php echo $this->loadTemplate('grid'); ?>
		</div>
		<div>

			<!-- BRICK : pagination -->
			<?php echo $this->pagination->getListFooter(); ?>
		</div>
	</div>


	<?php 
		$jinput = JFactory::getApplication()->input;
		echo JDom::_('html.form.footer', array(
		'values' => array(
					'view' => $jinput->get('view', 'submissions'),
					'layout' => $jinput->get('layout', 'default'),
					'boxchecked' => '0',
					'filter_order' => $this->escape($this->state->get('list.ordering')),
					'filter_order_Dir' => $this->escape($this->state->get('list.direction'))
				)));
	?>
</form>
</div>