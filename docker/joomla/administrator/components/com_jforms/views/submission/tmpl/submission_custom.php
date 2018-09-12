<?php
/*
* @version		0.4.4
* @package		jForms
* @subpackage	Submissions
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		GNU GPL v3 or later
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$jForm = $this->item->jforms_snapshot;
$layout_type = JformsHelper::replaceLayoutSteps($jForm,$jForm->form,$this->layout,array(),array(),$wizard_steps_lists);
$this->wizard_steps = $wizard_steps_lists;

JformsHelper::replaceLayoutFieldsets($jForm,$jForm->form,$this->layout);

$replacerOpts = array(
	'type' => 'output',
	'form' => $jForm->form
);

// replace all jforms tags and language tags
$allData = array_merge((array)$this->item,$this->formParts);
$this->layout = JformsHelper::replacer($this->layout,$allData, true,'JformsHelper::replaceField', $replacerOpts);	
	
?>

<?php echo $this->layout; ?>