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
* Jforms List Model
*
* @package	Jforms
* @subpackage	Classes
*/
class JformsCkModelForms extends JformsClassModelList
{
	/**
	* The URL view item variable.
	*
	* @var string
	*/
	protected $view_item = 'form';

	/**
	* Constructor
	*
	* @access	public
	* @param	array	$config	An optional associative array of configuration settings.
	* @return	void
	*/
	public function __construct($config = array())
	{
		//Define the sortables fields (in lists)
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'a.name',
				'ordering', 'a.ordering',

			);
		}

		//Define the filterable fields
		$this->set('filter_vars', array(
			'published' => 'varchar',
			'sortTable' => 'cmd',
			'directionTable' => 'cmd',
			'limit' => 'cmd'
				));

		//Define the searchable fields
		$this->set('search_vars', array(
			'search' => 'string'
				));


		parent::__construct($config);
		
	}

	/**
	* Method to get a list of items.
	*
	* @access	public
	*
	* @return	mixed	An array of data items on success, false on failure.
	*
	* @since	11.1
	*/
	public function getItems()
	{

		$items	= parent::getItems();
		$app	= JFactory::getApplication();


		$this->populateParams($items);

		//Create linked objects
		$this->populateObjects($items);

		return $items;
	}

	/**
	* Method to get the layout (including default).
	*
	* @access	public
	*
	* @return	string	The layout alias.
	*/
	public function getLayout()
	{
		$jinput = JFactory::getApplication()->input;
		return $jinput->get('layout', 'default', 'STRING');
	}

	/**
	* Method to get a store id based on model configuration state.
	* 
	* This is necessary because the model is used by the component and different
	* modules that might need different sets of data or differen ordering
	* requirements.
	*
	* @access	protected
	* @param	string	$id	A prefix for the store id.
	* @return	void
	*
	* @since	1.6
	*/
	protected function getStoreId($id = '')
	{
		// Compile the store id.





		return parent::getStoreId($id);
	}

	/**
	* Prepare some additional derivated objects.
	*
	* @access	public
	* @param	object	&$items	The items to populate.
	* @return	void
	*
	* @since	Cook 2.0
	*/
	public function populateObjects(&$items)
	{
		// multilanguage
		$tables = JformsHelper::getMultilangTables(); 
	
		foreach($items as &$item)
		{
			
			$item = ByGiroHelper::stringToJsonFields($item);
			
			

			// multilanguage
			if(isset($tables['forms'])){
				$item = ByGiroHelper::getMlFields($item,$tables['forms']);
			}		
		}
	
		parent::populateObjects($items);
	}

	/**
	* Method to auto-populate the model state.
	* 
	* This method should only be called once per instantiation and is designed to
	* be called on the first call to the getState() method unless the model
	* configuration flag to ignore the request is set.
	* 
	* Note. Calling getState in this method will result in recursion.
	*
	* @access	public
	* @param	string	$ordering	
	* @param	string	$direction	
	* @return	void
	*
	* @since	11.1
	*/
	public function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$acl = JformsHelper::getActions();

		parent::populateState('a.ordering', 'asc');

		//Only show the published items
		if (!$acl->get('core.admin') && !$acl->get('core.edit.state'))
			$this->setState('filter.published', 1);
	}

	/**
	* Preparation of the list query.
	*
	* @access	protected
	* @param	object	&$query	returns a filled query object.
	* @return	void
	*/
	protected function prepareQuery(&$query)
	{

		$acl = JformsHelper::getActions();

		//FROM : Main table
		$query->from('#__jforms_forms AS a');


		// load current language
		$lang = JFactory::getLanguage();
		$lang_tag = strtolower(str_replace('-','', $lang->getTag()));
		if($lang_tag != ''){
			$lang_tag = '_'. $lang_tag;
		}


		//IMPORTANT REQUIRED FIELDS
		$this->addSelect(	'a.id,'
						.	'a.access,'
						.	'a.published');

		switch($this->getState('context', 'all'))
		{
			case 'forms.default':

				//BASE FIELDS

				$fields = array(
								'description',
								'name'
								);

				$tables = JformsHelper::getMultilangTables();
				if(isset($tables['forms']) AND $lang_tag != ''){
					foreach($tables['forms'] as $fi){
						if(in_array($fi,$fields)){
							$fields[] = $fi . $lang_tag;
							
							// to search on the correct language
							$this->addSearch('search', 'a.'. $fi . $lang_tag, 'like');
						}
					}
				}
				$selectFields = 'a.'. implode(',a.',$fields);
				$this->addSelect($selectFields);
				
				$this->addSelect(	'a.options' );
				break;
				
			case 'forms.filter':

				//BASE FIELDS

				$fields = array(
								'name'
								);

				$tables = JformsHelper::getMultilangTables();
				if(isset($tables['forms']) AND $lang_tag != ''){
					foreach($tables['forms'] as $fi){
						if(in_array($fi,$fields)){
							$fields[] = $fi . $lang_tag;
						}
					}
				}
				$selectFields = 'a.'. implode(',a.',$fields);
				$this->addSelect($selectFields);
								
								
				break;
			case 'all':
				//SELECT : raw complete query without joins
				$this->addSelect('a.*');

				// Disable the pagination
				$this->setState('list.limit', null);
				$this->setState('list.start', null);
				break;
		}

		//FILTER - Access for : Root table
		$whereAccess = $wherePublished = true;
		$allowAuthor = false;
		$this->prepareQueryAccess('a', $whereAccess, $wherePublished, $allowAuthor);
		$query->where("$whereAccess AND $wherePublished");

		//WHERE - SEARCH : search_search : search on Name + Description
		$search_search = $this->getState('search.search');
		$this->addSearch('search', 'a.name', 'like');
		$this->addSearch('search', 'a.description', 'like');
		if (($search_search != '') && ($search_search_val = $this->buildSearch('search', $search_search)))
			$this->addWhere($search_search_val);

		parent::prepareQuery($query);
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsModelForms')){ class JformsModelForms extends JformsCkModelForms{} }

