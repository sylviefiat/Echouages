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



/**
* HTML View class for the Jforms component
*
* @package	Jforms
* @subpackage	Forms
*/
class JformsCkViewForms extends JformsClassView
{
	protected $view = 'forms';
	
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
	* Execute and display a template : Forms
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
		$this->model		= $model	= $this->getModel();
		$model->addWhere('a.published = 1');
		$this->state		= $state	= $this->get('State');
		$this->params 		= $state->get('params');
		$state->set('context', 'forms.default');
		$this->items		= $items	= $this->get('Items');
		$this->canDo		= $canDo	= JformsHelper::getActions(0,$this->getName());
		$this->pagination	= $this->get('Pagination');
		$this->filters = $filters = $model->getForm('default.filters');
		$this->menu = JformsHelper::addSubmenu('forms', 'default');
		$lists = array();
		$this->lists = &$lists;

		// Define the default title
		$this->params->def('title', JText::_('JFORMS_LAYOUT_FORMS'));

		$this->_prepareDocument();

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');


		
		$lists['enum']['forms.layout_type'] = JformsHelper::enumList('forms', 'layout_type');


		//Filters
		// Sort by
		$filters['sortTable']->jdomOptions = array(
			'list' => $this->getSortFields('default')
		);

		// Limit
		$filters['limit']->jdomOptions = array(
			'pagination' => $this->pagination
		);

		//Toolbar initialization



	}

	/**
	* Execute and display a template : Forms
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displayModal($tpl = null)
	{
		$this->model		= $model	= $this->getModel();
		$this->state		= $state	= $this->get('State');
		$this->params 		= $state->get('params');
		$state->set('context', 'forms.modal');
		$this->items		= $items	= $this->get('Items');
		$this->canDo		= $canDo	= JformsHelper::getActions(0,$this->getName());
		$this->pagination	= $this->get('Pagination');
		$this->filters = $filters = $model->getForm('modal.filters');
		$this->menu = JformsHelper::addSubmenu('forms', 'modal');
		$lists = array();
		$this->lists = &$lists;

		// Define the default title
		$this->params->def('title', JText::_('JFORMS_LAYOUT_FORMS'));

		$this->_prepareDocument();

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');


		
		$lists['enum']['forms.layout_type'] = JformsHelper::enumList('forms', 'layout_type');


		//Filters
		// Layout type
		$filters['filter_layout_type']->jdomOptions = array(
			'list' => JformsHelper::enumList('forms', 'layout_type')
		);

		// Limit
		$filters['limit']->jdomOptions = array(
			'pagination' => $this->pagination
		);

		//Toolbar initialization



	}

	/**
	* Returns an array of fields the table can be sorted by.
	*
	* @access	protected
	* @param	string	$layout	The name of the called layout. Not used yet
	*
	* @return	array	Array containing the field name to sort by as the key and display text as value.
	*
	* @since	3.0
	*/
	protected function getSortFields($layout = null)
	{
		return array(
			'a.name' => JText::_('JFORMS_FIELD_NAME')
		);
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsViewForms')){ class JformsViewForms extends JformsCkViewForms{} }

