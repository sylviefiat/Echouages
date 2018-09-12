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

jimport('joomla.application.component.modellist');


/**
* Jforms List Model
*
* @package	Jforms
* @subpackage	Classes
*/
class JformsCkClassModelList extends JModelList
{
	/**
	* Data array
	*
	* @var array
	*/
	protected $_data = null;

	/**
	* Pagination object
	*
	* @var object
	*/
	protected $_pagination = null;

	/**
	* Total
	*
	* @var integer
	*/
	protected $_total = null;

	/**
	* Context string for the model type.  This is used to handle uniqueness
	*
	* @var string
	*/
	protected $context = null;

	/**
	* Filterable fields keys
	*
	* @var array
	*/
	protected $filter_vars = array();

	/**
	* Search entries
	*
	* @var array
	*/
	protected $search_vars = array();
	


	protected $last_db_query = '';
	protected $queryKey = '';


	/**
	* Constructor
	*
	* @access	public
	* @param	array	$config	An optional associative array of configuration settings.
	* @return	void
	*/
	public function __construct($config = array())
	{
		parent::__construct($config);

		$layout = $this->getLayout();
		$jinput = JFactory::getApplication()->input;
		$render = $jinput->get('render', null, 'CMD');

		$this->context = strtolower($this->option . '.' . $this->getName()
					. ($layout?'.' . $layout:'')
					. ($render?'.' . $render:'')
					);
	}

	/**
	* Method to store an EXTRA at the end of the SQL query. (LIMIT for example)
	*
	* @access	public
	* @param	string	$extra	
	* @return	void
	*
	* @deprecated	1
	*/
	public function addExtra($extra)
	{
		$this->addQuery('extra', $extra);
	}
	
	public function addHaving($query)
	{
		$this->addQuery('having', $query);
	}
	
	public function addKey($key)
	{
		$this->queryKey = $key;
	}

	/**
	* Method to store a PRIORITARY ORDER for the SQL query. Used to group the
	* fields.
	* Deprecated : use addGroupOrder()
	*
	* @access	public
	* @param	string	$groupby	
	* @return	void
	*/
	public function addGroupBy($groupby)
	{
		$this->addGroupOrder($groupby);
	}

	/**
	* Method to store a PRIORITARY ORDER for the SQL query. Used to group the
	* fields per value.
	*
	* @access	public
	* @param	string	$groupOrder	
	* @return	void
	*/
	public function addGroupOrder($groupOrder)
	{
		//Legacy support
		$this->addQuery('groupby', $groupOrder);

		$this->addQuery('groupOrder', $groupOrder);
	}

	/**
	* Method to store a JOIN entry for the SQL query.
	*
	* @access	public
	* @param	string	$join	
	* @param	string	$type	
	* @return	void
	*/
	public function addJoin($join, $type = 'left')
	{
		$join = preg_replace("/^((LEFT)?(RIGHT)?(INNER)?(OUTER)?\sJOIN)/", "", $join);
		$this->addQuery('join.' . strtolower($type), $join);
	}

	/**
	* Method to store an ORDER entry for the SQL query.
	*
	* @access	public
	* @param	string	$order	
	* @return	void
	*/
	public function addOrder($order)
	{
		$this->addQuery('order', $order);
	}

	/**
	* Concat SQL parts in query. (Suggested by Cook Self Service)
	*
	* @access	public
	* @param	string	$type	SQL command.
	* @param	string	$queryElement	Command content.
	* @return	void
	*/
	public function addQuery($type, $queryElement)
	{
		$queryElement = trim($queryElement);
		$queries = $this->getState('query.' . $type, array());
		if (!in_array($queryElement, $queries) AND !empty($queryElement)) 
		{
			$queries[] = $queryElement;
			$this->setState('query.' . $type, $queries);
		}
	}

	/**
	* Method to concat a search entry.
	*
	* @access	protected
	* @param	string	$instance	
	* @param	string	$namespace	
	* @param	string	$method	
	* @return	void
	*/
	protected function addSearch($instance, $namespace, $method)
	{
		$search = new stdClass();
		$search->method = $method;


		if (!isset($this->_searches[$instance]))
			$this->_searches[$instance] = array();

		$this->_searches[$instance][$namespace] = $search;
	}

	/**
	* Method to store a SELECT entry for the SQL query.
	*
	* @access	public
	* @param	string	$select	
	* @return	void
	*/
	public function addSelect($select)
	{
		$this->addQuery('select', $select);
	}

	/**
	* Method to store a WHERE entry for the SQL query.
	*
	* @access	public
	* @param	string	$where	
	* @return	void
	*/
	public function addWhere($where)
	{
		$this->addQuery('where', $where);
	}

	/**
	* Method to build a SQL search string.
	*
	* @access	protected
	* @param	string	$instance	
	* @param	string	$searchText	
	* @param	string	$options	
	*
	* @return	string	The formated SQL string for the research.
	*/
	protected function buildSearch($instance, $searchText, $options = array('join' => 'AND', 'ignoredLength' => 0))
	{
		if (!isset($this->_searches[$instance]))
			return;

		$db= JFactory::getDBO();
		$tests = array();
		foreach($this->_searches[$instance] as $namespace => $search)
		{
			$test = "";
			switch($search->method)
			{
				case 'like':
					$test = $namespace . " LIKE " . $db->Quote("%%s%");
					break;

				case 'exact':
					$test = $namespace . " = " . $db->Quote("%s");
					break;

				case '':
					break;
			}

			if ($test)
				$tests[] = $test;
		}

		if (!count($tests))
			return "";

		$whereSearch = implode(" OR ", $tests);

		//SPLIT SEARCHED TEXT
		$searchesParts = array();

		foreach(explode(" ", $searchText) as $searchStr)
		{
			$searchStr = trim($searchStr);
			if ($searchStr == '')
				continue;

			if ((isset($options['ignoredLength'])) && (strlen($searchStr) <= $options['ignoredLength']))
				continue;

			if ($search->method == 'like')
			{
				$version = new JVersion();
				if ($version->isCompatible('1.7'))
					$searchStr = $db->escape($searchStr);
				else
					$searchStr = $db->getEscaped($searchStr);
			}
	

			$searchesParts[] = "(" . str_replace("%s", $searchStr, $whereSearch) . ")";
		}

		if (!count($searchesParts))
			return;

		if (isset($options['join']))
			$join = strtoupper($options['join']);
		else
			$join = "AND";

		$where = implode(" " . $join . " ", $searchesParts);

		return $where;
	}

	/**
	* Check if the user can access to the configuration.
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canAdmin()
	{
		$acl = JformsHelper::getActions(0,$this->getName());

		if ($acl->get('core.admin'))
			return true;

		return false;
	}

	/**
	* Check if the user can create new items.
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canCreate()
	{
		$acl = JformsHelper::getActions(0,$this->getName());
		
		if ($acl->get('core.create'))
			return true;
		
		return false;
	}

	/**
	* Method to test whether a user can delete items.
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canDelete()
	{
		$acl = JformsHelper::getActions(0,$this->getName());
		
		if ($acl->get('core.delete'))
			return true;

		if ($acl->get('core.delete.own'))
			return true;
		
		return false;
	}

	/**
	* Check if the user can edit items.
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canEdit()
	{
		$acl = JformsHelper::getActions(0,$this->getName());
		
		if ($acl->get('core.edit'))
			return true;

		if ($acl->get('core.edit.own'))
			return true;
		
		return false;
	}

	/**
	* Check if the user can edit the states (publish, default, ...).
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canEditState()
	{
		$acl = JformsHelper::getActions(0,$this->getName());
		
		if ($acl->get('core.edit.state'))
			return true;
		
		return false;
	}

	/**
	* Check if allowed to process any acl task.
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canSelect()
	{
		if ($this->canAdmin())
		return true;

		if ($this->canEdit())
		return true;

		if ($this->canDelete())
		return true;

		if ($this->canEditState())
		return true;

		if ($this->canEditState())
		return true;

		return false;
	}


	/**
	* Check if the user can access this item.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed.
	*/
	public function canAccess()
	{
		if (!$this->canView())
			return false;


		return true;
	}
	

	/**
	* Check if the user can view the item.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed.
	*/
	public function canView()
	{
		$acl = JformsHelper::getActions(0,$this->getName());

		//Check publish state
		if ($acl->get('core.view.list')) 
		{
			return true;
		}
		
		//Not allowed to access own item
		if ($acl->get('core.view.own'))
		{
			return true;
		}

		// check the user is logged
		$user = JFactory::getUser();
		if(!$user->id){ // user not logged
			// at this point no guest user can have access and the user is not logged
			// so let's assume some NOT GUEST usergroups should have access, redirect the user to the login page
			ByGiroHelper::loginFirstly();
		}
		
		return false;
	}	


	/**
	* Temporary function, before FoF implementation. Return the table Foreign Key
	* name of a field.
	*
	* @access	public static
	* @param	string	$fieldname	FK field name
	*
	* @return	string	The table name. # is used as prefix to significate the component name table prefix.
	*
	* @since	Cook 2.6.3
	*/
	public static function fkTable($fieldname)
	{
		$tbl = '#__';
		$com = 'jforms_';

		switch($fieldname)
		{
			case 'access': return $tbl. 'viewlevels';
			case 'created_by': return $tbl. 'users';
			case 'form_id': return $tbl.$com. 'forms';	
		}
	}

	/**
	* Method to get a customized form.
	*
	* @access	public
	* @param	string	$instance	The name of the form in XML file.
	* @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	* @param	string	$control	The name of the control group.
	*
	* @return	JXMLElement	A Fieldset containing all the field parameters (XML node)
	*
	* @since	Cook 2.0
	*/
	public function getForm($instance = 'default.filters', $loadData = true, $control = null)
	{
		$model = CkJModel::getInstance($this->view_item, 'JformsModel');
		$form = $model->getForm(null, $loadData, $control);

		if (empty($form))
			return null;

		if ($loadData)
		{
			//Fill the form with the states vars (For filters)
			foreach ($this->filter_vars as $filterVar => $type)
			{
				switch($filterVar)
				{
					case 'sortTable':
						$fieldName = $filterVar;
						$stateVar = 'list.ordering';
						break;

					case 'directionTable':
						$fieldName = $filterVar;
						$stateVar = 'list.direction';
						break;
				
					case 'limit':
						$fieldName = $filterVar;
						$stateVar = 'list.limit';
						break;
			
					default:
						$fieldName = 'filter_' . $filterVar;
						$stateVar = 'filter.' . $filterVar;
						break;
				}
				$value = $this->getState($stateVar);
		
				$form->setValue($fieldName, '', $value);
			}

			//Fill the form with the states vars (For Searches)
			foreach ($this->search_vars as $searchVar => $type)
			{		
				$value = $this->getState('search.' . $searchVar);
				$form->setValue('search_' . $searchVar, '', $value);
			}			
		}

		$fieldSet = $form->getFieldset($instance);
	
		//Check ACL (access property)
		$allowedFields = array();
		foreach($fieldSet as $name => $field)
		{
			if ((method_exists($field, 'canView')) && !$field->canView())
				continue;

			$allowedFields[$name] = $field;
		}
		return $allowedFields;
	}

	/**
	* Method to get an array of data items. Override to catch the errors.
	*
	* @access	public
	*
	* @return	array	Items objects.
	*
	* @since	11.1
	*/
	public function getItems()
	{
		try
		{
			$key = null;
			if(!empty($this->queryKey)){
				$key = $this->queryKey;
			}
			$result = $this->_getItems($key);
			$db = $this->getDbo();
			if ($error = $db->getErrorMsg()) {
				if (!$this->canAdmin()){
					throw new Exception(JText::_('JFORMS_ERROR_INVALID_QUERY') .': '. $error);
				}
			} else {
				$this->last_db_query = $db->getQuery();
			}
		}
		catch (JException $e)
		{

		}
		return $result;
	}

	/**
	* Get the current layout. Abstract function to override.
	*
	* @access	public
	*
	* @return	string	The default layout alias.
	*
	* @since	11.1
	*/
	public function getLayout()
	{
		return 'default';
	}

	/**
	* Method to get a JDatabaseQuery object for retrieving the data set from a
	* database.
	*
	* @access	public
	*
	* @return	JDatabaseQuery	A JDatabaseQuery object to retrieve the data set.
	*
	* @since	11.1
	*/
	public function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$this->prepareQuery($query);
		return $query;
	}

	/**
	* Proxy to get the model.
	*
	* @access	public
	* @param	bool	$item	If true, return the item model
	*
	* @return	JModel	Return the model.
	*
	* @since	1.6
	*/
	public function getModel($item = false)
	{
		if ($item)
			return CkJModel::getInstance($this->view_item, 'JformsModel');

		return parent::getModel();
	}

	/**
	* Alternative to avoid userVar beeing updated for Ajax calls.
	*
	* @access	public
	* @param	string	$key	The key of the user state variable.
	* @param	string	$request	The name of the variable passed in a request.
	* @param	string	$default	The default value for the variable if not found. Optional.
	* @param	string	$type	Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	* @param	string	$resetPage	If true, the limitstart in request is set to zero
	* @return	void
	*/
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;

		$new_state = $jinput->get($request, $cur_state, $type);

		//Only POST queries can apply changes on the states vars.
		if ($jinput->getMethod() == 'POST')
		{
			// Whatever filtering permanent state changed, the pagination returns to the first page
			if ($resetPage && !empty($new_state) && ($cur_state != $new_state))
			{
				$this->setState('limitstart', 0);
				$app->setUserState($this->context . '.limitstart', 0);
			}

			// Save the new value only if it is set in this request.
			if ($new_state !== null)
				$app->setUserState($key, $new_state);
			else
				$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	* Load a N:x relation list to objects array in the item.
	*
	* @access	public
	* @param	object	&$items	The items to populate.
	* @param	string	$objectField	The item property name used for this list.
	* @param	string	$xrefTable	Cross Reference (Xref) table handling this link.
	* @param	string	$on	The FK fieldname from Xref pointing to the origin
	* @param	string	$key	The ID fieldname from Origin.
	* @param	array	$states	Cascad states followers, for recursive objects.
	* @param	string	$context	SQL predefined query
	* @return	void
	*
	* @since	Cook 2.6.3
	*/
	public function loadXref(&$items, $objectField, $xrefTable, $on, $key, $states = array(), $context = 'object.default')
	{
		$db = JFactory::getDbo();

		foreach($items as $item)
		{
			if ($this->getState('xref.' . $objectField))
			{
				$model = CkJModel::getInstance($xrefTable, 'JformsModel');
		
				// Prepare the fields to load, trough a context profile
				$model->setState('context', $context);
		
				// Filter on the origin
				$model->addWhere(qn($db, $on) . '='. (int)$item->$key);
	
		
				//Cascad objects states
				// Apply the namespaced states to the relative base namespace
				if (count($states))
				foreach($states as $state)
				{
					if ($val = $this->getState('xref.' . $objectField . '.' . $state))
						$model->setState('xref.' . $state, $val);
				}
		
				// Set up the array in the item.
				$item->$objectField = $model->getItems();
			}			
		}
	}

	/**
	* Prepare some additional derivated objects.
	*
	* @access	public
	* @param	array	&$items	The objects to populate.
	* @return	void
	*/
	public function populateObjects(&$items)
	{
		foreach($items as &$item){
			if(isset($item->access) AND is_string($item->access)){
				$item->access = explode(',',$item->access);
			}
		}
	}

	/**
	* Prepare some additional important values.
	*
	* @access	public
	* @param	array	&$items	The objects to populate.
	* @return	void
	*/
	public function populateParams(&$items)
	{
		if (!isset($items) || empty($items))
			return;

		$model = CkJModel::getInstance($this->view_item, 'JformsModel');
		foreach ($items as &$item)
		{
			// TODO : attribs
		//			$itemParams = new JRegistry;
		//			$itemParams->loadString((isset($item->attribs)?$item->attribs:$item->params));

			//$item->params = clone ($this->getState('params'));

			$item->params = new JObject();

			if ($model)
			{
				if ($model->canView($item))
					$item->params->set('access-view', true);

				if ($model->canEdit($item))
					$item->params->set('access-edit', true);

				if ($model->canDelete($item))
					$item->params->set('access-delete', true);

				if ($model->isCheckedIn($item))
					$item->params->set('tag-checkedout', true);

				if (isset($item->published))
					$item->params->set('tag-published', $item->published);

				if (isset($item->default))
					$item->params->set('tag-default', $item->default);

			}
		}
	}

	/**
	* Method to auto-populate the model state.
	*
	* @access	public
	* @param	string	$ordering	
	* @param	string	$direction	
	* @return	void
	*/
	public function populateState($ordering = null, $direction = null)
	{
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get('layout', null, 'CMD');
		$render = $jinput->get('render', '', 'CMD');

		if ($layout == 'ajax')
			$this->setState('context', 'ajax' . ($render?'.'.$render:''));

		$globalParams = JComponentHelper::getParams('com_jforms', true);
		$this->setState('params', $globalParams);

		// If the context is set, assume that stateful lists are used.
		if ($this->context)
		{
			$app = JFactory::getApplication();
	
		// FILTERS
			foreach($this->filter_vars as $var => $varType)
			{
				/*
				//1. First read the Request in URL
				//2. Then read the persistant value for THIS context
				//3. Finaly read the state var sent by the caller
				$value = $this->getUserStateFromRequest(
					$this->context . '.filter.' . $var, 
					'filter_' . $var, 
					$this->state->get('filter.' . $var), 
					$varType
				);
		*/
				//1. Read the state var sent by the caller
				//2. Then read the Request in URL
				//3. Finally read the persistant value for THIS context
				$value = $this->state->get('filter.' . $var, 
					$this->getUserStateFromRequest(
					$this->context . '.filter.' . $var, 
					'filter_' . $var, 
					null, 
					$varType
				));

				//Convert datetime entries back from a custom format
				if ($value && (preg_match("/^date:(.+)/", $varType, $matches)))
				{
					$date = JformsHelperDates::timeFromFormat($value, $matches[1]);
					if ($date)
					{
						jimport('joomla.utilities.date');
						$jdate = new JDate($date);
						$value = JformsHelperDates::toSql($jdate);
					}
					else
						continue;
				}
				$this->setState('filter.' . $var, $value);
			}

		// FILTERS : SEARCHES
			foreach($this->search_vars as $var => $varType)
			{
				//see Filters
				/*
				$value = $this->getUserStateFromRequest(
					$this->context . '.search.' . $var, 
					'filter_' . $var, 
					$this->state->get('search.' . $var), 
					$varType);
				*/

				//1. Read the state var sent by the caller
				//2. Then read the Request in URL
				//3. Finally read the persistant value for THIS context
				$value = $this->state->get('search.' . $var, 
					$this->getUserStateFromRequest(
					$this->context . '.search.' . $var, 
					'search_' . $var, 
					null, 
					$varType
				));
		
				$this->setState('search.' . $var, $value);
			}
	
	
		// PAGINATION : LIMIT
			//1. First read the state var sent by the caller
			//2. Then read the Request in URL
			//3. Then read the default limit value for THIS context
			//4. Finally read the list limit value from the Joomla configuration					
			$value = $this->state->get('list.limit',
						$app->getUserStateFromRequest('global.list.limit', 'limit',
							$this->state->get('list.limit.default', 
								$app->getCfg('list_limit')))
			);
			
			$limit = $value;
			$this->setState('list.limit', $limit);


		// PAGINATION : LIMIT START
			//1. First read the Request in URL
			//2. Then read the state var sent by the caller
			$value = $app->getUserStateFromRequest(
					$this->context . '.limitstart', 'limitstart', 
						$this->state->get('list.limitstart')
			);
			
			
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);


		// SORTING : ORDERING (Vocabulary confusion in Joomla. This is a SORTING. Ordering is an index value in the item.)
			//1. First read the Request in URL
			//2. Then read the default sorting value sent trough the args (called 'ordering')
			$value = $app->getUserStateFromRequest(
					$this->context . '.ordercol', 'filter_order', 
						$ordering
			);
				
				
			if (!in_array($value, $this->filter_fields))
			{
				$value = $ordering;
				$app->setUserState($this->context . '.ordercol', $value);
			}
			$this->setState('list.ordering', $value);


		// SORTING : DIRECTION
			//1. First read the Request in URL
			//2. Then read the default direction value sent trough the args.
			$value = $app->getUserStateFromRequest(
					$this->context . '.orderdirn', 'filter_order_Dir', 
						$direction
			);
				
			if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
			{
				$value = $direction;
				$app->setUserState($this->context . '.orderdirn', $value);
			}
			$this->setState('list.direction', $value);
		}
		else
		{
			$this->setState('list.start', 0);
			$this->state->set('list.limit', 0);
		}

		if (defined('JDEBUG'))
			$_SESSION["Jforms"]["Model"][$this->getName()]["State"] = $this->state;
	}

	/**
	* Method to easily filter the dates.
	*
	* @access	public
	* @param	string	$field	Field to apply the filter.
	* @param	string	$range	String to describe the starting time range, or predefined range. ex: [-4 day][-2 month][null][defined]
	* @param	string	$rangeEnd	String to describe the ending time range
	* @return	void
	*/
	public function prepareFilterTime($field, $range, $rangeEnd = null)
	{
		$db = JFactory::getDbo();

		// Get UTC for now.
		$dNow = new JDate;
		$dBegin = clone($dNow);
		$dEnd = clone($dNow);

		// Define the starting time.
		switch($range)
		{
	
			case 'now':
				// 1 hour back per default.
				$dBegin->modify('-1 hour');
				break;
		
			case 'today':
				//Align on the days bounds
		
				// Ranges that need to align with local 'days' need special treatment.
				$app	= JFactory::getApplication();
				$offset	= $app->getCfg('offset');

				// Reset the start time to be the beginning of today, local time.
				$dBegin	= new JDate('now', $offset);
				$dBegin->setTime(0, 0, 0);

				// Now change the timezone back to UTC.
				$tz = new DateTimeZone('GMT');
				$dBegin->setTimezone($tz);
				break;
	
			default: 		
				$dBegin->modify($range);
			break;
		}


		//Define the ending time.
		switch($rangeEnd)
		{
			case null: break;

	
			default: 		
				$dEnd->modify($rangeEnd);
			break;
		}

		// Search for null dates.
		if ($range == 'null')
		{
			$this->addWhere($field . " IS NULL ");
			return;
		}

		// Search for defined dates.
		if ($range == 'defined')
		{
			$this->addWhere($field . " <> NULL ");
			return;
		}

		// Time cannot be null.
		$this->addWhere($field . " IS NOT NULL ");

		// Apply the STARTING time filter.
		$this->addWhere($field . " >= " . $db->quote($dBegin->toSql()));			

		// Apply the ENDING time filter.
		$this->addWhere($field . " < " . $db->quote($dEnd->toSql()));			
	}

	/**
	* Prepare the query for filtering accesses. Can be used on foreign keys.
	*
	* @access	protected
	* @param	varchar	$table	The table alias (_tablealias_).
	* @param	varchar	&$whereAccess	The returned SQL access filter. Set to true to activate it.
	* @param	varchar	&$wherePublished	The returned SQL published filter. Set to true to activate it.
	* @param	varchar	&$allowAuthor	The returned SQL to allow author to pass. Set to true to activate it.
	* @return	void
	*/
	protected function prepareQueryAccess($table = 'a', &$whereAccess = null, &$wherePublished = null, &$allowAuthor = null)
	{
		$acl = JformsHelper::getActions(0,$this->getName());

		// Must be aliased ex : _tablename_
		if ($table != 'a')
			$table = '_' . trim($table, '_') . '_';


		// ACCESS - View Level Access
		if ($whereAccess)
		{
			// Select fields requirements
			if ($table != 'a')
				$this->addSelect($table . '.access AS `' . $table . 'access`');	

			$whereAccess = '1';
			if (!$this->canAdmin())
			{
			   $userId = JFactory::getUser()->id;
				$groups = JAccess::getGroupsByUser($userId);

				$whereAccess = array();
				foreach($groups as $group){
					$whereAccess[] = 'FIND_IN_SET('.$group.','. $table . '.access) > 0';
				}			
				
				$whereAccess = '('. implode(' OR ',$whereAccess) .')';
			}
		}

		// ACCESS - Author
		if ($allowAuthor)
		{
			// Select fields requirements
			if ($table != 'a')
				$this->addSelect($table . '.created_by AS `' . $table . 'created_by`');

			$allowAuthor = '0';
			//Allow the author to see its own unpublished/archived/trashed items
			if ($acl->get('core.edit.own') || $acl->get('core.view.own') || $acl->get('core.delete.own'))
				$allowAuthor = $table . '.created_by = ' . (int)JFactory::getUser()->get('id');
		
		}

		// ACCESS - Publish state
		if ($wherePublished)
		{
			// Select fields requirements
			if ($table != 'a')
				$this->addSelect($table . '.published AS `' . $table . 'published`');

			$wherePublished = '(' . $table . '.published = 1 OR ' . $table . '.published IS NULL)'; //Published or undefined state
			//Allow some users to access (core.edit.state)
			if ($acl->get('core.edit.state'))
				$wherePublished = '1'; //Do not filter

			// FILTER - Published state
			$published = $this->getState('filter.published');

			//Only apply filter on current table. Aand only if ACL permits.
			if (($table == 'a') && (is_numeric($published)) && $acl->get('core.edit.state'))
			{
				//Limit to publish state when filter is applied
				$wherePublished = $table . '.published = ' . (int)$published;
				//Does not apply the author condition when filter is defined
				$allowAuthor = '0';
			}
		}

		// Fallback values
		if (!$whereAccess)
			$whereAccess = '1';

		if (!$allowAuthor)
			$allowAuthor = '0';

		if (!$wherePublished)
			$wherePublished = '1';
	}

	/**
	* This feature is the blueprint of ORM-kind feature. It create the optimized
	* SQL query for mounting an object, including foreign links.
	*
	* @access	public
	* @param	array	$headers	The header structure. see:https://www.akeebabackup.com/documentation/fof/common-fields-for-all-types.html
	* @return	void
	*
	* @since	Cook 2.6.3
	*/
	public function prepareQueryHeaders($headers)
	{
		if (!count($headers))
			return;

		$db = JFactory::getDbo();

		foreach($headers as $namespace => $header)
		{
			// the namespace is used to localize the foreign key path
			$fieldAlias = $namespace = $header['name'];
			if (isset($header['namespace']))
				$namespace = $header['namespace'];

			$parts = explode('.' ,$namespace);
			$isFk = (count($parts) > 1);


			// Physical field name is always the last part
			$fieldname = $parts[count($parts)-1];
			$current = $parts[0];

			$parentTable = 'a';

			for($i = 0 ; $i < (count($parts)) ; $i++)
			{
				$isLast = ($i == (count($parts) - 1));
				$current = $parts[$i];

				// Select the field
				if ($isLast)
					break;

				$tableName = self::fkTable($current);
				$tableAlias = '_' . $current . '_';
		
				// Join the required tables
				$this->addJoin(qn($db, $tableName) 
					.	' AS ' . $tableAlias
					.	' ON ' . $tableAlias . '.id'
					.	' = ' . $parentTable . '.' . $current
	
					, 'LEFT');

				$parentTable = $tableAlias;
			}

			// Instance the field in query
			$this->addSelect($parentTable .'.'. $current . ' AS ' . qn($db, $fieldAlias));
		}
	}

	/**
	* Prepare the language translation of items for SQL query.
	*
	* @access	protected
	* @param	array	$fields	The fields you want to translate.
	* @param	array	$options	An array of configuration.
	* @return	void
	*/
	protected function prepareQueryTranslate($fields, $options = array())
	{
		if (empty($fields))
			return;

		//Define an alias prefix when the selected field is abroad FK. (ie: _product_category_title, use : _product_category_)
		$fieldPrefix = '';
		if (isset($options['fieldPrefix']))
		{
			$fieldPrefix = $options['fieldPrefix'];
			$tableAlias = $fieldPrefix;
			$langTableAlias = '__lang' . $fieldPrefix;
		}
		else
		{
			$tableAlias = 'a';
			$langTableAlias = '__lang_';
		}

		//The alias used in query for temporary load the related language item (be careful unicity of table aliases)
		if (isset($options['langTableAlias']))
			$langTableAlias = $options['langTableAlias'];

		//The table name from witch are stored the languages strings
		$tableFrom = '#__' . ltrim($this->option, '_com') . '_' . $this->getName();
		if (isset($options['tableFrom']))
			$tableFrom = $options['tableFrom'];

		//Define the field on which the filter is working. Language tag (ie: en-GB).
		$keyLang = 'language';
		if (isset($options['keyLang']))
			$keyLang = $options['keyLang'];

		//Define the recursive field FK which relate to the original item
		$keyXref = 'xref';
		if (isset($options['keyXref']))
			$keyXref = $options['keyXref'];

		//Limit to the root elements when not the root table (a.)
		if (isset($options['tableFrom']))
			$this->addWhere("($tableAlias.$keyXref IS NULL || $tableAlias.$keyXref = 0)");

		//Apply the filter
		$stateValue = $this->getState('filter.language');
		if ($stateValue !== null)
		{
			// Join language table (recursive 1 level)
			$this->addJoin("`$tableFrom` AS `$langTableAlias` ON ($langTableAlias.$keyXref = $tableAlias.id AND $langTableAlias.$keyLang = "
					. $this->_db->Quote($stateValue)
					. ')' , 'LEFT');

			//Translatable fields
			foreach($fields as $key)
				$this->addSelect("(CASE WHEN ($langTableAlias.$key IS NOT NULL AND $langTableAlias.$key > 0) THEN $langTableAlias.$key ELSE $tableAlias.$key END) AS `$fieldPrefix$key`");
		}
	}

	/**
	* Method to adjust the ordering of a row.
	*
	* @access	public
	* @param	array	$ids	The ID of the primary key to move.
	* @param	int	$inc	Delta increment, usually +1 or -1.
	*
	* @return	boolean	True on success
	*
	* @since	11.1
	*/
	public function reorder($ids, $inc)
	{
		$model = $this->getModel(true);

		$table = $model->getTable();
		$table->load($ids[0]);

		if (!$table->move($inc))
			return false;

		$conditions = $model->getReorderConditions($table);
		$conditions = (count($conditions)?implode(" AND ", $conditions):'');
		$table->reorder($conditions);

		return true;
	}

	/**
	* Saves the manually set order of records.
	*
	* @access	public
	* @param	array	$pks	An array of primary key ids.
	* @param	array	$order	order values
	*
	* @return	boolean	True on success
	*
	* @since	11.1
	*/
	public function saveorder($pks, $order)
	{
		$model = $this->getModel(true);
		$model->saveorder($pks, $order);
	}

	/**
	* Method to set model state variables. Update local vars.
	*
	* @access	public
	* @param	string	$property	The name of the property.
	* @param	mixed	$value	The value of the property to set or null.
	*
	* @return	mixed	The previous value of the property or null if not set.
	*
	* @since	11.1
	*/
	public function setState($property, $value = null)
	{
		if ($property == 'context')
			$this->context = $value;
	
		return parent::setState($property, $value);
	}

	/**
	* Synchronize the N:M references Add/Remove.
	*
	* @access	public
	* @param	string	$field	Fk fieldname in the Xref table
	* @param	array	$values	Array of ID of the values for $field
	* @param	string	$on	Fk fieldname pointing the origin referral.
	* @param	integer	$id	ID value of the origin.
	*
	* @return	boolean	True when success.
	*
	* @since	Cook 2.6.3
	*/
	public function updateXref($field, $values, $on, $id)
	{
		$db = JFactory::getDbo();

		$sqlValues = implode(',', $values);
		if (empty($sqlValues))
			$sqlValues = '0';


		// Get all current links in context
		$model = CkJModel::getInstance($this->getName(), 'JformsModel');
		$model->addWhere(qn($db, $on) . '='. $id);

		$xref = $model->getItems();
		$refs = array();

		$isNm = true;
		if ($field == null)
		{
			$isNm = false;
			$field = 'id';
		}

		$delete = array();
		foreach($xref as $row)
		{
			$refs[] = $row->$field;
			if (!in_array($row->$field, $values))
			{
				//Delete row
				$delete[] = $row->id;
			}
		}

		$create = array();
		foreach($values as $val)
		{
			if (!in_array($val, $refs))
			{
				//Create new row
				$create[] = $val;
			}
		}

		$result = true;

		// In case on N:M, the links are physical rows 
		if ($isNm)
		{
			//Apply delete
			$model = CkJModel::getInstance($this->view_item, 'JformsModel');
			if (count($delete))
				if (!$model->delete($delete))
					$result = false;


			// Create new entries
			$model = CkJModel::getInstance($this->view_item, 'JformsModel');
			if (count($create))
			foreach($create as $val)
			{
				if (!$model->save(array(
					'id' => 0, //New
					$on => $id,
					$field => $val	
				)))
					$result = false;
			}			
		}

		// In case of N:1, the links are FK from the opposite table
		else
		{
	
			if (count($delete))
			{
				$query = $db->getQuery(true);
				$query->update('#__jforms_' . $this->getName())

					// Unlink it
					->set(qn($db, $on) . '= NULL')

					// From the given list to delete
					->where(qn($db, $field) . ' IN (' . implode(',', $delete). ')');
			
				$db->setQuery($query);
	
	
				if (!$db->query())
					$result = false;
			}
	
			if (count($create))
			{
				$query = $db->getQuery(true);
				$query->update('#__jforms_' . $this->getName())

					// Link it
					->set(qn($db, $on) . '='. (int)$id)
			
					// Facultative security : ONLY free items are linkables $on = (NULL or O)
					->where('(' . qn($db, $on) . ' IS NULL OR ' . qn($db, $on) . ' = 0 '. ')')
			
					// From the given list to create
					->where(qn($db, $field) . ' IN (' . implode(',', $create). ')');
			
				$db->setQuery($query);
	
				if (!$db->query())
					$result = false;
	
			}			
		}

		return $result;
	}


	/**
	* Check if the user can edit items.
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canManage()
	{
		$acl = JformsHelper::getActions(0,$this->getName());
		
		if ($acl->get('core.manage'))
			return true;

		if ($acl->get('core.manage.own'))
			return true;
		
		return false;
	}


	public function cleanFilters(){
		$filters = $this->get('filter_vars');
		foreach($filters as $k => $v){
			$this->setState('filter.'. $k,null);
		}	
	}
	
	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   12.2
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		// joomla fix: set the MAX
		$this->setMaxLimit();
		
		// Create the pagination object.
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		if(!class_exists('CKJPagination')){
			require_once(JPATH_ADMIN_JFORMS .DS. 'classes' .DS. 'pagination.php');
		}
		$page = new CKJPagination($this->getTotal(), $this->getStart(), $limit);
		
		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}	
	
	function setMaxLimit(){
		$app = JFactory::getApplication();
		$config	= JComponentHelper::getParams( 'com_jforms' );		
		$context = str_replace('.','_',$this->context);
		
		$max_limit = $config->get("max_limit", 200);
		if ($app->isAdmin()){
			$param = "admin_max_limit_". $context;
		} else {			
			$param = "site_max_limit_". $context;
		}
		
		$max_limit = $config->get($param, $max_limit);			
		$limit = $this->getState('list.limit');
	
		if($limit == 0 OR $limit > $max_limit){
			$this->setState('list.limit', (int)$max_limit);
		}
	}
	
	public function _getItems($key = null)
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();
		
		try
		{
			$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'),$key);
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
	
	protected function _getList($query, $limitstart = 0, $limit = 0, $key = null)
	{
		$this->_db->setQuery($query, $limitstart, $limit);

		if(!empty($key)){
			$result = $this->_db->loadObjectList($key);
		} else {
			$result = $this->_db->loadObjectList();
		}
		
		return $result;
	}
	
	protected function prepareQuery(&$query)
	{
		//Populate only uniques strings to the query
		//SELECT
		foreach($this->getState('query.select', array()) as $select)
			$query->select($select);

		//JOIN
		foreach($this->getState('query.join.left', array()) as $join)
			$query->join('LEFT', $join);

		//WHERE
		foreach($this->getState('query.where', array()) as $where)
			$query->where($where);

		//GROUP ORDER : Prioritary order for groups in lists
		foreach($this->getState('query.groupOrder', array()) as $groupOrder)
			$query->order($groupOrder);

		//ORDER
		foreach($this->getState('query.order', array()) as $order)
			$query->order($order);

		//ORDER
		$orderCol = $this->getState('list.ordering');
		$orderDir = $this->getState('list.direction', 'asc');

		if ($orderCol)
			$query->order($orderCol . ' ' . $orderDir);	

		//HAVING
		foreach($this->getState('query.having', array()) as $having)
			$query->having($having);			
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsClassModelList')){ class JformsClassModelList extends JformsCkClassModelList{} }

