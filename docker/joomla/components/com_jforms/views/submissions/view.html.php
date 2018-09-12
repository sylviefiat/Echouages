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
		if (!in_array($layout, array('default')))
			JError::raiseError(0, $layout . ' : ' . JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'));

		$fct = "display" . ucfirst($layout);

		$this->addForkTemplatePath();
		$this->$fct($tpl);			
		$this->_parentDisplay($tpl);
	}

	/**
	* Execute and display a template : My submissions
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
		$app = JFactory::getApplication();		
		$this->params = $params = $app->getParams();
		$user = JFactory::getUser();	
		
		$mode = $params->get('mode', 'own');
		$usergroups = $params->get('usergroups', array(2)); // 'registered' usergroup is usually 2
	
		$this->model		= $model	= $this->getModel();
		$this->canDo		= $canDo	= JformsHelper::getActions(0,$this->getName());

		//Check ACL before opening the view (prevent from direct access)
		if (!$model->canAccess())
			$model->setError(JText::_('JERROR_ALERTNOAUTHOR'));
			
		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
//			$app->enqueueMessage(implode(BR, array_unique($errors)), 'error');
			return false;
		}		
		
		switch($mode)
		{
			case 'own':
				if($user->id){
					$model->addWhere('a.created_by = '. $user->id);
				} else {
					$session = JFactory::getSession();
					$model->addWhere('a.session_id = '. $session->getId());
				}
				break;
				
			case 'usergroups':
				$model->addSelect('
				IFNULL((SELECT GROUP_CONCAT(u_map.group_id) FROM `#__user_usergroup_map` AS u_map
				WHERE a.created_by = u_map.user_id),1) as `usergroups`
				');

				$whereAccess = array();
				foreach($usergroups as $group){
					$whereAccess[] = 'FIND_IN_SET('.$group.',usergroups) > 0';
				}				
				$whereAccess = '('. implode(' OR ',$whereAccess) .')';
				
				if(!in_array(1,$usergroups)) // we also have to show GUEST's submissions
				{
					$model->addWhere('a.created_by > 0');
				}
				$model->addHaving($whereAccess);

				break;
		}
		
		$this->state		= $state	= $this->get('State');
		
		
		$state->set('context', 'submissions.default');
		$this->items		= $items	= $model->getItems();

		$this->pagination	= $this->get('Pagination');
		$this->filters = $filters = $model->getForm('default.filters');
		$this->menu = JformsHelper::addSubmenu('submissions', 'default');
		$lists = array();
		$this->lists = &$lists;

		// Define the default title
		$this->params->def('title', JText::_('JFORMS_LAYOUT_MY_SUBMISSIONS'));

		$this->_prepareDocument();

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');


		
		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');
		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');


		//Filters
		// Status
		$filters['filter_status']->jdomOptions = array(
			'list' => JformsHelper::enumList('submissions', 'status')
		);

		// Payment status
		$filters['filter_payment_status']->jdomOptions = array(
			'list' => JformsHelper::enumList('submissions', 'payment_status')
		);

		// Form > Name
		$modelForm_id = CkJModel::getInstance('forms', 'JformsModel');
		$modelForm_id->setState('context', 'forms.filter');
		$filters['filter_form_id']->jdomOptions = array(
			'list' => $modelForm_id->getItems()
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
			'_created_by_.username' => JText::_('JFORMS_FIELD_CREATED_BY_USERNAME'),
			'_form_id_.name' => JText::_('JFORMS_FIELD_FORM_NAME'),
			'a.creation_date' => JText::_('JFORMS_FIELD_CREATION_DATE'),
			'a.ip_address' => JText::_('JFORMS_FIELD_IP_ADDRESS'),
			'a.status' => JText::_('JFORMS_FIELD_STATUS'),
			'a.payment_status' => JText::_('JFORMS_FIELD_PAYMENT_STATUS'),
			'a.passphrase' => JText::_('JFORMS_FIELD_SECRET_PASSWORD')
		);
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsViewSubmissions')){ class JformsViewSubmissions extends JformsCkViewSubmissions{} }

