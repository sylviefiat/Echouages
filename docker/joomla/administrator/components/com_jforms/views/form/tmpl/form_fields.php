<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.2.7
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
<?php $fieldSet = $this->form->getFieldset('form.extra');?>
	<?php
	// Main folder
	$field = $fieldSet['jform_fields'];
	$field->jdomOptions = array_merge($field->jdomOptions,array(
		'enumList' => $this->lists['select']['fields']
	));
	?>
<?php echo $field->input; ?>
