<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Cpanel
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');



/**
* HTML View class for the Jforms component
*
* @package	Jforms
* @subpackage	Cpanel
*/
class JformsCkViewCpanel extends JformsClassView
{
	/**
	* Execute and display a template script.
	*
	* @access	public
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		if (!in_array($layout, array('default')))
			JError::raiseError(0, $layout . ' : ' . JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'));

		$fct = "display" . ucfirst($layout);

		$this->addForkTemplatePath();
		$this->$fct($tpl);			
		$this->_parentDisplay($tpl);
	}

	/**
	* Execute and display a template : jForms
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displayDefault($tpl = null)
	{
		$this->menu = JformsHelper::addSubmenu('cpanel', 'default', 'cpanel');
		$this->params = new JRegistry();

		// Define the default title
		$this->params->def('title', JText::_('JFORMS_LAYOUT_JFORMS'));

		$this->_prepareDocument();

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');
		//Toolbar initialization



	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsViewCpanel')){ class JformsViewCpanel extends JformsCkViewCpanel{} }

