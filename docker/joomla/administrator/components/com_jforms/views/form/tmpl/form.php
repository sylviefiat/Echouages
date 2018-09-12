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
?>
<script language="javascript" type="text/javascript">
	//Secure the user navigation on the page, in order preserve datas.
	var holdForm = true;
	window.onbeforeunload = function closeIt(){	if (holdForm) return false;};
</script>

<form action="<?php echo(JRoute::_("index.php")); ?>" method="post" name="adminForm" id="adminForm" class='form-validate' enctype='multipart/form-data'>
	<?php
	$compat = '1.6';
	$version = new JVersion();
	if ($version->isCompatible('3.0'))
		$compat = '3.0';
	?>
	<div>

		<!-- BRICK : toolbar_sing -->
		<?php echo $this->renderToolbar();?>
	</div>
	<?php if ($compat == '3.0'): ?>
	<div class="row-fluid">
		<div id="contents" class="span12">		
	<?php elseif ($compat == '1.6'): ?>
	<div>
		<div>		
	<?php endif; ?>
	
			<ul class="nav nav-tabs jForms_tabs">
				<li class="active">
					<a href="#details"><?php echo JText::_("JFORMS_JFORM_DETAILS"); ?></a>
				</li>

				<li>
					<a href="#fieldsets"><?php echo JText::_("JFORMS_JFORM_FIELDSETS"); ?></a>
				</li>
				<li>
					<a href="#fields"><?php echo JText::_("JFORMS_JFORM_FIELDS"); ?></a>
				</li>
				<li>
					<a href="#events"><?php echo JText::_("JFORMS_JFORM_EVENTS"); ?></a>
				</li>
				<li>
					<a href="#emails"><?php echo JText::_("JFORMS_JFORM_EMAILS"); ?></a>
				</li>
				<li>
					<a href="#layouts"><?php echo JText::_("JFORMS_JFORM_LAYOUTS"); ?></a>
				</li>
				<li>
					<a href="#payments"><?php echo JText::_("JFORMS_JFORM_PAYMENTS"); ?></a>
				</li>
				<li>
					<a href="#integrations"><?php echo JText::_("JFORMS_JFORM_INTEGRATIONS"); ?></a>
				</li>
				<li>
					<a href="#options"><?php echo JText::_("JFORMS_JFORM_OPTIONS"); ?></a>
				</li>
				<li>
					<a href="#permissions"><?php echo JText::_("JFORMS_JFORM_PERMISSIONS"); ?></a>
				</li>
			</ul>	
			<div class="tab-content">
				<div class="tab-pane active" id="details">
					<!-- BRICK : multilanguage -->
					<?php echo $this->loadTemplate('details'); ?>
				</div>
				<div class="tab-pane" id="fieldsets">

					<!-- BRICK : fieldsets -->
					<?php echo $this->loadTemplate('fieldsets'); ?>
				</div>
				
				<div class="tab-pane" id="fields">

					<!-- BRICK : fields -->
					<?php echo $this->loadTemplate('fields'); ?>
				</div>
				
				<div class="tab-pane" id="events">

					<!-- BRICK : events -->
					<?php echo $this->loadTemplate('events'); ?>
				</div>
				<div class="tab-pane" id="emails">

					<!-- BRICK : emails -->
					<?php echo $this->loadTemplate('emails'); ?>
				</div>
				<div class="tab-pane" id="layouts">

					<!-- BRICK : layout -->
					<?php echo $this->loadTemplate('layouts'); ?>
				</div>
				<div class="tab-pane" id="payments">

					<!-- BRICK : payments -->
					<?php echo $this->loadTemplate('payments'); ?>
				</div>
				<div class="tab-pane" id="integrations">

					<!-- BRICK : integrations -->
					<?php echo $this->loadTemplate('integrations'); ?>
				</div>
				<div class="tab-pane" id="options">

					<!-- BRICK : options -->
					<?php echo $this->loadTemplate('options'); ?>
				</div>
				<div class="tab-pane" id="permissions">

					<!-- BRICK : permissions -->
					<?php echo $this->loadTemplate('permissions'); ?>
				</div>
			</div>
		</div>
	</div>	


	<?php 
		$jinput = JFactory::getApplication()->input;
		echo JDom::_('html.form.footer', array(
		'dataObject' => $this->item,
		'values' => array(
					'id' => $this->state->get('form.id')
				)));
	?>
</form>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.jForms_tabs a').click(function (e) {
	  e.preventDefault();
	  jQuery(this).tab('show');
	});
});
</script>
