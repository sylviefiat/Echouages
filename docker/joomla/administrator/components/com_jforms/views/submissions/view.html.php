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



/**
* HTML View class for the Jforms component
*
* @package	Jforms
* @subpackage	Submissions
*/
class JformsCkViewSubmissions extends JformsClassView
{
	protected $view = 'submissions';
	
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
		if (!in_array($layout, array('default', 'modal')))
			JError::raiseError(0, $layout . ' : ' . JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'));

		$fct = "display" . ucfirst($layout);

		$this->addForkTemplatePath();
		$this->$fct($tpl);			
		$this->_parentDisplay($tpl);
	}

	/**
	* Execute and display a template : Submissions
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
		$document	= JFactory::getDocument();
		$this->title = JText::_("JFORMS_LAYOUT_SUBMISSIONS");
		$document->title = $document->titlePrefix . $this->title . $document->titleSuffix;

		$this->model		= $model	= $this->getModel();
		$this->state		= $state	= $this->get('State');
		$this->params 		= $state->get('params');
		$state->set('context', 'submissions.default');
		$this->items		= $items	= $this->get('Items');
		$this->canDo		= $canDo	= JformsHelper::getActions();
		$this->pagination	= $this->get('Pagination');
		$this->filters = $filters = $model->getForm('default.filters');
		$this->menu = JformsHelper::addSubmenu('submissions', 'default');
		$lists = array();
		$this->lists = &$lists;

		
		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');
		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');


		//Filters
		// Form > Name
		$modelForm_id = CkJModel::getInstance('forms', 'JformsModel');
		$modelForm_id->addOrder('a.id');
		$formModelState = $modelForm_id->getState();
		$formModelState->set('context', 'forms.default');
		$forms = $modelForm_id->getItems();
		$filters['filter_form_id']->jdomOptions = array(
			'list' => $forms
		);

		// Status
		$filters['filter_status']->jdomOptions = array(
			'list' => JformsHelper::enumList('submissions', 'status')
		);

		// Payment status
		$filters['filter_payment_status']->jdomOptions = array(
			'list' => JformsHelper::enumList('submissions', 'payment_status')
		);

		// Sort by
		$filters['sortTable']->jdomOptions = array(
			'list' => $this->getSortFields('default')
		);

		// Limit
		$filters['limit']->jdomOptions = array(
			'pagination' => $this->pagination
		);

		//Toolbar initialization

		JToolBarHelper::title(JText::_('JFORMS_LAYOUT_SUBMISSIONS'), 'jforms_submissions');
		// New
		if ($model->canCreate())
			CkJToolBarHelper::addNew('submission.add', "JFORMS_JTOOLBAR_NEW");

		// Edit
		if ($model->canEdit())
			CkJToolBarHelper::editList('submission.edit', "JFORMS_JTOOLBAR_EDIT");

		// Delete
		if ($model->canDelete())
			CkJToolBarHelper::deleteList(JText::_('JFORMS_JTOOLBAR_ARE_YOU_SURE_TO_DELETE'), 'submission.delete', "JFORMS_JTOOLBAR_DELETE");

		// Config
		if ($model->canAdmin())
			CkJToolBarHelper::custom( 'submission.export', 'download', 'JFORMS_JTOOLBAR_EXPORT', 'JFORMS_JTOOLBAR_EXPORT', true );
			CkJToolBarHelper::custom( 'submission.deletepdf', 'trash', 'JFORMS_JTOOLBAR_DELETE_PDF', 'JFORMS_JTOOLBAR_DELETE_PDF', true );
			CkJToolBarHelper::preferences('com_jforms');
	}

	/**
	* Execute and display a template : Submissions
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
		$document	= JFactory::getDocument();
		$this->title = JText::_("JFORMS_LAYOUT_SUBMISSIONS");
		$document->title = $document->titlePrefix . $this->title . $document->titleSuffix;

		$this->model		= $model	= $this->getModel();
		$this->state		= $state	= $this->get('State');
		$this->params 		= $state->get('params');
		$state->set('context', 'submissions.modal');
		$this->items		= $items	= $this->get('Items');
		$this->canDo		= $canDo	= JformsHelper::getActions();
		$this->pagination	= $this->get('Pagination');
		$this->filters = $filters = $model->getForm('modal.filters');
		$this->menu = JformsHelper::addSubmenu('submissions', 'modal');
		$lists = array();
		$this->lists = &$lists;

		
		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');
		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');


		//Filters
		// Form > Name
		$modelForm_id = CkJModel::getInstance('forms', 'JformsModel');
		$modelForm_id->addOrder('a.id');
		$formModelState = $modelForm_id->getState();
		$formModelState->set('context', 'forms.default');
		$forms = $modelForm_id->getItems();
		$filters['filter_form_id']->jdomOptions = array(
			'list' => $forms
		);

		// Status
		$filters['filter_status']->jdomOptions = array(
			'list' => JformsHelper::enumList('submissions', 'status')
		);

		// Payment status
		$filters['filter_payment_status']->jdomOptions = array(
			'list' => JformsHelper::enumList('submissions', 'payment_status')
		);

		// Limit
		$filters['limit']->jdomOptions = array(
			'pagination' => $this->pagination
		);

		//Toolbar initialization

		JToolBarHelper::title(JText::_('JFORMS_LAYOUT_SUBMISSIONS'), 'jforms_submissions');
		// New
		if ($model->canCreate())
			CkJToolBarHelper::addNew('submission.add', "JFORMS_JTOOLBAR_NEW");

		// Edit
		if ($model->canEdit())
			CkJToolBarHelper::editList('submission.edit', "JFORMS_JTOOLBAR_EDIT");

		// Delete
		if ($model->canDelete())
			CkJToolBarHelper::deleteList(JText::_('JFORMS_JTOOLBAR_ARE_YOU_SURE_TO_DELETE'), 'submission.delete', "JFORMS_JTOOLBAR_DELETE");

		// Config
		if ($model->canAdmin())
			CkJToolBarHelper::preferences('com_jforms');
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
			'a.id' => JText::_('JFORMS_FIELD_ID'),
			'a.ip_address' => JText::_('JFORMS_FIELD_IP_ADDRESS'),
			'_created_by_.username' => JText::_('JFORMS_FIELD_CREATED_BY_USERNAME'),
			'a.form_id' => JText::_('JFORMS_FIELD_FORM'),
			'_form_id_.name' => JText::_('JFORMS_FIELD_FORM_NAME'),
			'a.status' => JText::_('JFORMS_FIELD_STATUS'),
			'a.payment_status' => JText::_('JFORMS_FIELD_PAYMENT_STATUS'),
			'a.passphrase' => JText::_('JFORMS_FIELD_SECRET_PASSWORD'),
			'a.creation_date' => JText::_('JFORMS_FIELD_CREATION_DATE')
		);
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsViewSubmissions')){ class JformsViewSubmissions extends JformsCkViewSubmissions{} }

