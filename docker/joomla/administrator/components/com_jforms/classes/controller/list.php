<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');


/**
* Jforms  Controller
*
* @package	Jforms
* @subpackage	
*/
class JformsCkClassControllerList extends JControllerAdmin
{
	/**
	* Result of the task execution.
	*
	* @var mixed
	*/
	protected $_result;

	/**
	* Constructor
	*
	* @access	public
	* @return	void
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Customize the redirection depending on result.
	* (proposed by Cook Self Service).
	*
	* @access	protected
	* @param	mixed	$result	bool or integer. The result from  the task operation
	* @param	array	$redirections	The redirections (option.view.layout) ordered by task result [0,1,...]
	* @param	array	$vars	Eventual added vars to the redirection.
	*
	* @return	void	
	* @return	void
	*/
	protected function applyRedirection($result, $redirections, $vars = array())
	{
		if ($result === null)
			$result = 1;
		else
			$result = (int)$result;

		if (!$this->_result)
			$this->_result = $result;

		if (!isset($redirections[$result]))
			return;		//Keep the default redirection

		//Get the selected redirection depending on result
		$redirection = $redirections[$result];
		switch($redirection)
		{
			//Stay on the same page
			case 'stay':
				$this->setRedirect(JformsHelper::urlRequest($vars));
				return;
				break;

			//Return to the previous page in navigation history
			case 'previous':
				//TODO
				break;
		}
		$url = explode(".", $redirection);

		//Get from given url parts (empty string will keep the current value)
		if (isset($url[0]))
			$values['option']	= (!empty($url[0])?$url[0]:$this->option);

		if (isset($url[1]))
			$values['view'] 	= (!empty($url[1])?$url[1]:$this->view_list);

		if (isset($url[2]))
			$values['layout']	= (!empty($url[2])?$url[2]:$this->getLayout(true));

		$jinput = JFactory::getApplication()->input;


		//Followers : If value is defined in the current form, it will be added in the request
		$followers = array(	'cid' => 'ARRAY',
							'tmpl' => 'CMD',
							'Itemid' => 'CMD',
							'lang' => 'CMD');


		//Filters followers
		$model = CkJModel::getInstance($this->view_list, 'JformsModel');
		if ($model)
		{
			// Applies the filters follower only in case of a 'new' item
			$cid = $jinput->get('cid', null, 'array');
			if (!count($cid))
			{
				$filters = $model->get('filter_vars');
				foreach($filters as $filterName => $type)
				{
					$type = 'STRING'; //When filter is empty, don't follow, so FILTER is not used.
					$filterVar = 'filter_' . $filterName;
					//Adds a filter follower
					$followers[$filterVar] = $type;
				}
			}
		}

		//Apply the followers values
		foreach($followers as $varName => $varType)
		{
			if($pos = strpos($varType, ":"))
				$varType = substr($varType, 0, $pos);

			$value = $jinput->get($varName, '', strtoupper($varType));
			if (($varType == 'ARRAY') && !empty($value))
			{
				$value = implode(",", $value);
				$varName .= "[]";
			}

			if ($value != '')
				$values[$varName] = $value;
		}

		//Override with vars in params
		foreach($vars as $key => $value)
			$values[$key] = $value;

		//Prepare the url
		foreach($values as $key => $value)
			if ($value !== null)
				$parts[] = $key . '=' . $value;

		//Apply redirection
		$this->setRedirect(
			JRoute::_("index.php?" . implode("&", $parts), false)
		);
	}

	/**
	* Proxy to get the model. Note that we merged all tasks functions in the item
	* model.
	*
	* @access	public
	* @param	string	$name	The name of the model.
	* @param	string	$prefix	The prefix for the PHP class name.
	* @param	array	$config	The configuration.
	*
	* @return	JModel	The requested Model.
	*
	* @since	11.1
	*/
	public function getModel($name = '', $prefix = 'JformsModel', $config = array('ignore_request' => true))
	{
		if (empty($name))
		{
			//Group all tasks in the item model
			if ($this->getTask())
				$name = $this->view_item;
			else
				$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	/**
	* the browser or returns false if no redirect is set.
	*
	* @access	public
	*
	* @return	boolean	False if no redirect exists.
	*
	* @since	11.1
	*/
	public function redirect()
	{
		if ($this->redirect)
		{

			$app = JFactory::getApplication();
			$jinput = $app->input;

			$refresh = $jinput->get('refresh',1, 'INT');
			//Return JSON response
			if ($jinput->get('return','','STRING') == 'json')
			{
				// Enqueue the simple returned message (if not specified in the query to ignore it)
				if ((!empty($this->message)) && (!$jinput->get('ignoreMsg')))
					$app->enqueueMessage($this->message, $this->messageType);

				JformsClassAjax::responseJson(array(
					'result' => (isset($this->_result)?$this->_result:1),
					'redirect' => $this->redirect,
					'renderExceptions' => 'html',
					'refresh' => $refresh
				));				
			}

			$app = JFactory::getApplication();
			$app->redirect($this->redirect, $this->message, $this->messageType);
		}

		return false;
	}

	/**
	* Method to save the submitted ordering values for records via AJAX.
	*
	* @access	public
	* @return	void
	*
	* @since	3.0
	*/
	public function saveOrderAjax()
	{
		$jinput = JFactory::getApplication()->input;

		// Get the input
		$pks   = $jinput->post->get('cid', array(), 'array');
		$order = $jinput->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsClassControllerList')){ class JformsClassControllerList extends JformsCkClassControllerList{} }

