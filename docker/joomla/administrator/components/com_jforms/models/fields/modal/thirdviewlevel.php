<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Viewlevels
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!class_exists('JformsClassFormField'))
	require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_jforms' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'loader.php');


/**
* Form field for Jforms.
*
* @package	Jforms
* @subpackage	Form
*/
class JFormFieldModal_Thirdviewlevel extends JdomClassFormFieldModal
{
	/**
	* Default label for the picker.
	*
	* @var string
	*/
	protected $_nullLabel = 'JFORMS_DATA_PICKER_SELECT_VIEWLEVEL';

	/**
	* Option in URL
	*
	* @var string
	*/
	protected $_option = 'com_jforms';

	/**
	* Modal Title
	*
	* @var string
	*/
	protected $_title;

	/**
	* View in URL
	*
	* @var string
	*/
	protected $_view = "thirdviewlevels";

	/**
	* Field type
	*
	* @var string
	*/
	protected $type = 'modal_thirdviewlevel';

	/**
	* Method to get the field input markup.
	*
	* @access	protected
	*
	* @return	string	The field input markup.
	*
	* @since	11.1
	*/
	public function getInput()
	{
		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT `title`' .
			' FROM #__viewlevels' .
			' WHERE id = '.(int) $this->value
		);
		$this->_title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}


		return parent::getInput();
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JFormFieldModal_Thirdviewlevel')){ class JFormFieldModal_Thirdviewlevel extends JformsCkJFormFieldModal_Thirdviewlevel{} }

