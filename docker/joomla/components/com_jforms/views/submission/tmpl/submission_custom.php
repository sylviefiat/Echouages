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

$layout_type = JformsHelper::replaceLayoutSteps($this->form->jForm,$this->form,$this->layout,array(),array(),$wizard_steps_lists);
$this->wizard_steps = $wizard_steps_lists;

JformsHelper::replaceLayoutFieldsets($this->form->jForm,$this->form,$this->layout);

$replacerOpts = array(
	'type' => 'input',
	'form' => $this->form
);

// replace all jforms tags and language tags
$this->layout = JformsHelper::replacer($this->layout,$this->formParts, true,'JformsHelper::replaceField', $replacerOpts);
?>

<?php if($layout_type == 'wizard'){ ?>
	<?php echo $this->loadTemplate('custom_wizard'); ?>
<?php } else { ?>
	<?php echo $this->loadTemplate('custom_form'); ?>
<?php } ?>