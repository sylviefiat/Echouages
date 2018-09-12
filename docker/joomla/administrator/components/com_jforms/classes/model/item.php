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

jimport('joomla.application.component.modeladmin');


/**
* Jforms Item Model
*
* @package	Jforms
* @subpackage	Classes
*/
class JformsCkClassModelItem extends JModelAdmin
{
	/**
	* Data array
	*
	* @var array
	*/
	protected $_data = null;

	/**
	* Item id
	*
	* @var integer
	*/
	public $_id = null;

	/**
	* Item by id.
	*
	* @var array
	*/
	public $_item = null; 
	
	/**
	* component name.
	*
	* @var array
	*/
	public $_comAlias = 'jform'; 

	/**
	* Item params
	*
	* @var array
	*/
	protected $_params = null;

	/**
	* Context string for the model type.  This is used to handle uniqueness
	*
	* @var string
	*/
	protected $context = null;

	/**
	* List of all fields files indexes
	*
	* @var array
	*/
	protected $fileFields = array();
	

	protected $last_db_query = '';


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
					

		// check multilanguages tables
		$app = JFactory::getApplication();
		if ($app->isAdmin() AND method_exists('JformsHelper','getMultilangTables') AND class_exists('multiLanguages')){
			$tables = JformsHelper::getMultilangTables();
			$multiLanguage = new multiLanguages();
			$multiLanguage->setExtension('jforms');
			$multiLanguage->checkTables($tables);
		}
					
	}

	/**
	* Method to update a file and eventually upload.
	*
	* @access	public
	* @param	string	$fieldName	Field that store the file name.
	* @param	array	$extensions	Allowed extensions.
	* @param	array	$options	Specific options.
	* @param	string	$dir	Root folder (can be a pattern).
	*
	* @return	boolean	False on failure or error, true otherwise.
	*/
	public function _upload($fieldName, $extensions = null, $options = array(), $dir = null)
	{
		//Send the id for eventual name or path parsing in upload
		$options['id'] = $this->getId();

		$config	= JComponentHelper::getParams( 'com_jforms' );
		$app = JFactory::getApplication();
				
		$groups = array();
		if($options['group'] != ''){
			$groups = explode('.',$options['group']);
		}		
		$groups[] = $fieldName;
		
		if (!$dir)
			$dir = '[DIR_' . strtoupper($this->view_list . '_' . $groups[0]) . ']';

		$jinput = JFactory::getApplication()->input;
		
		//Get the submited files if exists
		$fileInput = new JInput($_FILES);
		$files = $fileInput->get('jform', null, 'array');

		$uploadFile = array();
		//Process a conversion to get the right datas
		if (!empty($files)){
			foreach($files as $key => $params){				
				$value = $params[$fieldName];
				if(count($groups) > 1){
					$value = $params;
					foreach($groups as $group){
						$value = $value[$group];
					}
				}
				
				$uploadFile[$key] = $value;
			}
		}
		
		$post = $jinput->get('jform', null, 'array');

		$arrayPath = array_values($groups);
		array_pop($arrayPath);

		// Remove parameter
		$removeVarPath = array_merge($arrayPath,array($fieldName . '-remove'));
		$removeValue = ByGiroHelper::array_path_value($post, implode('.',$removeVarPath));

		
		// Previous value parameter
		$previousVarPath = array_merge($arrayPath,array($fieldName . '-current'));
		$previousValue = ByGiroHelper::array_path_value($post, implode('.',$previousVarPath));

		$remove	= (isset($removeValue)?$removeValue:null);
		$previous = (isset($previousValue)?$previousValue:null);


		// Upload file name
		$upload = (isset($uploadFile['name'])?$uploadFile['name']:null);

		// New value
		$fileName = $previous;

		//Check method
		$method = '';
		$changed = false;
		if (!empty($upload) OR !empty($options['content']))
		{
			$method = 'upload';			
			if(!empty($options['content'])){
				$uploadFile['name'] = basename($fileName);
				$uploadFile['content'] = $options['content'];
				$options["overwrite"] = 'yes';
				$options['rename'] = '{BASE}.{EXT}';
			} else {
				$changed = ($upload != $previous);
			}
		}

		if($changed){
			$remove = $config->get('action_for_old_files_' . $this->view_list . '_' . $groups[0], 'delete');
		}
		
		//Check if needed to delete files
		if (in_array($remove, array('remove', 'delete', 'thumbs', 'trash')) AND !empty($previous))
		{
			$fileName = "";		//Clear DB link (remove)
			$changed = true;
			
			$isUniqueFileOnAllItems = true;
			$isUniqueFileOnThisItem = true;
			
			// are we processing a submission fomr_data file?!
			$check = true;
			if($this->getName() == 'submission' AND $groups[0] != 'pdf'){
				$check = false;
			}
			
			//Process physical removing of the files (All, only thumbs, Move to trash)
			if (in_array($remove, array('delete', 'thumbs', 'trash')) AND $check)
			{
				// check we can safely delete this file NOT used by other items
				// get item
				$thisItem = $this->getItem();
				$mainField = $groups[0];
				$items = $thisItem->$mainField;
				$table = $this->getTable();
				$it_id = $groups[1];

				$checkIsUniqueFileOnThisItem = $config->get('checkIsUniqueFileOnThisItem',true);
				$checkIsUniqueFileOnAllItems = $config->get('checkIsUniqueFileOnAllItems',true);				
				
				$isUniqueFileOnThisItem = true;
				$isUniqueFileOnAllItems = true;

				// check if this file is ONLY used by this subitem
				if($checkIsUniqueFileOnThisItem){
					$isUniqueFileOnThisItem = ByGiroHelper::isValUniqueInArray($items,$fieldName,$previous, $it_id);
				}
				
				if($isUniqueFileOnThisItem AND $checkIsUniqueFileOnAllItems){
					// check if this file is ONLY used by this subitem (checking all items in DB)
					$isUniqueFileOnAllItems = ByGiroHelper::isValUniqueInDBItems($table->getTableName(),$mainField,$fieldName,$previous,$thisItem->id);
				}
			}
			
			if($isUniqueFileOnAllItems AND $isUniqueFileOnThisItem){
				$f = (preg_match("/\[.+\]/", $previous)?"":$dir.DS) . $previous;
				if (!JformsClassFile::deleteFile($f, $remove)){
					$app->enqueueMessage(JText::_("JFORMS_TASK_RESULT_IMPOSSIBLE_TO_DELETE"),'error');
				}
			}
		}
				
		switch($method)
		{
			case 'upload':

				// Process Upload
				$uploadClass = new JformsClassFileUpload($dir);
				$mime_types = $uploadClass->getMimeTypes();
				
				$ext_array = array();
				foreach($extensions as $ext){
					$ext = trim($ext);
					$mime = $mime_types[$ext];
					$ext_array[$mime][] = $ext;
					$ext_array['application/force-download'][] = $ext;					
				}
				
				foreach($ext_array as $key => $val){
					$ext_array[$key] = implode(',',$val);
				}
				
				$uploadClass->setAllowed($ext_array);

				$result = $uploadClass->uploadFile($uploadFile, $options);
				
				if (!$result)
				{				
					$app->enqueueMessage(JText::sprintf("JFORMS_TASK_RESULT_IMPOSSIBLE_TO_UPLOAD_FILE", $uploadFile['name']),'error');
					$changed = false;
					return false;
				} else if($options['test'] AND $result === true) {
					return true;
				} else {
					$fileName = $result->filename;
					$changed = true;
				}
				
				break;
		}
		
		$fileName = (!empty($fileName))?$fileName:'';
		return $fileName;
	}

	/**
	* 
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
	* Concat SQL parts in query. (Suggested by Cook Self Service)
	*
	* @access	public
	* @param	string	$type	SQL command.
	* @param	string	$queryElement	Command content.
	* @return	void
	*/
	public function addQuery($type, $queryElement)
	{
		$queries = $this->getState('query.' . $type, array());
		if (!in_array($queryElement, $queries))
		{
			$queries[] = $queryElement;
			$this->setState('query.' . $type, $queries);
		}
	}

	/**
	* 
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
	* Check if the user can access this item.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed.
	*/
	public function canAccess($record)
	{
		if (!$this->canView($record))
			return false;


		return true;
	}

	/**
	* Check if the user is admin or manager.
	*
	* @access	public
	*
	* @return	boolean	True if user can admin all items.
	*/
	public function canAdmin()
	{
		$acl = JformsHelper::getActions($this->getId(),$this->view_list);

		if ($acl->get('core.admin'))
			return true;

		return false;
	}

	/**
	* Method to check if the item is free of checkout.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed. False if checkedout
	*/
	public function canCheck($record)
	{
		if ($this->isCheckedIn($record))
		{			
		//	$this->setError(JText::_("JFORMS_TASK_RESULT_THE_USER_CHECKING_OUT_DOES_NOT_MATCH_THE_USER_WHO_CHECKED_OUT_THE_ITEM"));    // TO DO
			return false;			
		}

		return true;
	}

	/**
	* Check if the user can create a new item.
	*
	* @access	public
	*
	* @return	boolean	True if allowed.
	*/
	public function canCreate()
	{
		//Facultative : Check Admin
		if ($this->canAdmin())
			return true;

		$acl = JformsHelper::getActions();

		//Authorizated to create
		if ($acl->get('core.create'))
			return true;

		return false;
	}

	/**
	* Method to test whether a record can be deleted.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed to delete the record. Defaults to the permission for the component.
	*/
	public function canDelete($record)
	{
		//Check if already edited
		if ($this->isCheckedIn($record))
			return false;

		//Facultative : Check Admin
		if ($this->canAdmin())
			return true;

		$acl = JformsHelper::getActions($this->getId(),$this->view_list);

		//Authorizated to delete
		if($this->isAccessible($record) AND $acl->get('core.delete')){ //Facultative : Check accesslevel
			return true;
		}

		//Author can delete
		if(!$acl->get('core.delete.own')) return false;
		
		if($this->isAuthor($record))	return true;

		// check the guest user has a last saved/edited item in the session
		if($this->isValidSessionId($record)) return true;
		
		return false;
	}

	/**
	* Check if the user can edit the item.
	*
	* @access	public
	* @param	object	$record	A record object.
	* @param	boolean	$testNew	Check canCreate() in case of new element.
	* @param	string	$pk	Primary key name.
	*
	* @return	boolean	True if allowed.
	*/
	public function canEdit($record, $testNew = true, $pk = 'id')
	{
		//Create instead of Edit if new item
		if($testNew && empty($record->$pk))
			return self::canCreate();
		
		//Check if already edited
		if (!$this->canCheck($record))
			return false;

		//Facultative : Check Admin
		if ($this->canAdmin())
			return true;

		$acl = JformsHelper::getActions($this->getId(),$this->view_list);

		//Authorizated to edit
		if ($this->isAccessible($record) AND $acl->get('core.edit')){ //Facultative : Check accesslevel
			return true;
		}

		//Author can edit
		if (!$acl->get('core.edit.own')) return false;
		
		
		if ($this->isAuthor($record)) return true;

		// check the guest user has a last saved/edited item in the session
		if($this->isValidSessionId($record)) return true;
		
		return false;
	}

	/**
	* Check if the user can set default the item.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed.
	*/
	public function canEditDefault($record)
	{
		//Uses the same ACL than edit state
		return $this->canEditState();
	}

	/**
	* Check if the user can edit he published state of this item.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed.
	*/
	public function canEditState($record)
	{
		//Check if already edited
		if ($this->isCheckedIn($record))
			return false;

		//Facultative : Check Admin
		if ($this->canAdmin())
			return true;

		$acl = JformsHelper::getActions($this->getId(),$this->view_list);

		//Authorizated to change publish state
		if (!$acl->get('core.edit.state'))
			return false;

		//Facultative : Check accesslevel
		if (!$this->isAccessible($record))
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
	public function canView($record)
	{

		$acl = JformsHelper::getActions($this->getId(),$this->view_list);


		//Check publish state
		if ($this->isVisible($record) && $acl->get('core.view.item')){ 
			return true;
		}

		//Not allowed to access to own item
		if (	!$acl->get('core.view.own')
			&& 	!$acl->get('core.edit.own')
			&& 	!$acl->get('core.delete.own')){
			return false;
		}

		//Author can view
		if ($this->isAuthor($record))	return true;

		// check the guest user has a last saved/edited item in the session
		if($this->isValidSessionId($record)) return true;

		return false;
	}
	
	public function isValidSessionId($record){
		if(empty($this->sessionLastItemVar)) return false;
		
		$session = JFactory::getSession();
		
		// check referer, if the user left the website, this access is not allowed
		$referer = parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST);
		$thisWebsite = parse_url(JURI::root(),PHP_URL_HOST);
		
		if($referer != $thisWebsite){
			// let's remove any stored last id....just in case
			$session->set($this->sessionLastItemVar,null);			
			return false;
		}

		$lastId = $session->get($this->sessionLastItemVar,false);
	
		if(!empty($lastId) AND $record->id == $lastId) return true;
		
		return false;
	}

	/**
	* Clean the cache
	*
	* @access	protected
	* @param	string	$group	The cache group.
	* @param	integer	$client_id	The ID of the client.
	* @return	void
	*
	* @since	12.2
	*/
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache($group, $client_id);

		$pk = $this->getState($this->getName() . '.id');
		//Clean current item cache (Called when save succeed)
		$this->_item[$pk] = null;
	}

	/**
	* Delete the files assiciated to the items
	*
	* @access	public
	* @param	array	$pks	Ids of the items to delete the images
	* @param	array	$fileFields	Images indexes fields of the table where to find the images paths.
	*
	* @return	boolean	True on success
	*/
	public function deletefiles($pks)
	{
		if (empty($pks)) return true;
		
		// get form
		$form = $this->getForm();
		$fsets = $form->getFieldsets();
		
		// detect filefields
		$fileFields = array();
		foreach($fsets as $fname => $fset){
			// check the fieldset is NOT: repeatable, filters, joomla repeatable
			if((isset($fset->repeatable) AND $fset->repeatable === 'true') OR strpos($fname,'.filters') !== false OR strpos($fname,'_modal') !== false){
				continue;
			}
			
			$fileFields = array_merge($fileFields,$this->getFileFields($form,$fset));			
		}

		if(empty($fileFields)) return true;
		
		$fields = array();
		foreach($fileFields as $key => $fi){
		
			// subitems grouped
			if(strpos($key,'.') !== false){
				$key = explode('.',$key);
				$key = $key[0];
			}
			
			$fields[$key] = $key;
		}

		JArrayHelper::toInteger($pks);
		$db = JFactory::getDBO();

		$errors = array();
		$table = $this->getTable();

		//Get all indexes for all fields
		$query = "SELECT id, " . qn($db, implode(qn($db, ', '), $fields))
			. " FROM " . qn($db, $table->getTableName())
			. ' WHERE id IN ( '.implode(', ', $pks) .' )';
		$db->setQuery($query);
		$files = $db->loadObjectList();

		$config	= JComponentHelper::getParams( 'com_jforms' );
		$checkIsUniqueFileOnThisItem = $config->get('checkIsUniqueFileOnThisItem',true);
		$checkIsUniqueFileOnAllItems = $config->get('checkIsUniqueFileOnAllItems',true);

		foreach($fileFields as $fieldName => $op)
		{
			$groups = array();
			$mainField = $fieldName;
			if(strpos($fieldName,'.') !== false){
				$groups = explode('.',$fieldName);
				$mainField = $groups[0];
			}

			$dir = $config->get('upload_dir_' . $this->view_list . '_' . $mainField, '[COM_SITE]' .DS. 'files' .DS. $this->view_list . '_' . $mainField);			
			$fPaths = array();
			
			$valuePath = implode('.',$groups);
			foreach($files as $fileObj){
				if(count($groups)>1){
					$subObject = json_decode($fileObj->$mainField);
					if(empty($subObject)){
						continue;
					}
					
					if($groups[1] == '{ID}'){
						$items = (array)$subObject;
						
						$field = $groups[2];
						foreach($items as $it){
							if(empty($it->$field)){
								continue;
							}
							$isUniqueFileOnThisItem = true;
							$isUniqueFileOnAllItems = true;
								
							if($checkIsUniqueFileOnThisItem){
								// check if this file is ONLY used by this subitem
								$isUniqueFileOnThisItem = ByGiroHelper::isValUniqueInArray($items,$field,$it->$field, $it->id);
							}
							
							if($isUniqueFileOnThisItem AND $checkIsUniqueFileOnAllItems){
								// check if this file is ONLY used by this subitem (checking all items in DB)
								$isUniqueFileOnAllItems = ByGiroHelper::isValUniqueInDBItems($table->getTableName(),$mainField,$field,$it->$field,$fileObj->id);
							}
							
							if($isUniqueFileOnAllItems AND $isUniqueFileOnThisItem){
								$fPaths[] = $it->$field;
							}
						}
					} else {
						$fPath = trim(ByGiroHelper::array_path_value($fileObj, $valuePath));
						if(empty($fPath)){
							continue;
						}
						$fPaths[] = $fPath;
					}
				} else {
					$fPath = trim($fileObj->$mainField);
					if(empty($fPath)){
						continue;
					}
					$fPaths[] = $fPath;
				}
				
				unset($filePath);
				foreach($fPaths as $filePath){				
					if (!JformsClassFile::deleteFile($filePath, $op)){
						$errors[] = true;
					}
				}
			}
			
		}

		$errors = array_unique($errors);
		
		return !(count($errors) == 1 AND $errors[0]);

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
	* Method to get the form.
	*
	* @access	public
	* @param	array	$data	An optional array of data for the form to interrogate.
	* @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	* @param	string	$control	The name of the control group.
	*
	* @return	JForm	A JForm object on success, false on failure
	*
	* @since	11.1
	*/
	public function getForm($data = array(), $loadData = true, $control = 'jform')
	{
		$form = $this->loadForm($this->context, $this->view_item, array('control' => $control,'load_data' => $loadData));
		if (empty($form))
			return false;

		$form->addRulePath(JPATH_ADMIN_JFORMS .DS. 'models' .DS . 'rules');
		$form->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules'); 

		$id = (int)$this->getState($this->getName() . '.id');
		$item = new stdClass;
		if(isset($this->_item[$id])){
			$item = $this->_item[$id];
		}

		$this->populateParams($item);
		$this->populateObjects($item);

		return $form;
	}

	/**
	* Method to get the id.
	*
	* @access	public
	*
	* @return	int	The item id. Null if no item loaded.
	*
	* @since	11.1
	*/
	public function getId()
	{
		if (isset($this->_item))
			return $this->getState($this->getName() . '.id');

		return 0;
	}

	/**
	* Method to get an item data.
	*
	* @access	public
	* @param	integer	$pk	The primary id key of the item
	*
	* @return	mixed	Item data object on success, false on failure.
	*/
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try
			{
				if (empty($pk))
					$data = new stdClass();
				else
				{
					//Increment the hits if needed
					$this->hit();


					$db = $this->getDbo();
					$query = $db->getQuery(true);

					//Preparation of the query
					$this->prepareQuery($query, $pk);

					$db->setQuery($query);

					$data = $db->loadObject();

					if ($error = $db->getErrorMsg()) {
						throw new Exception($error);
					} else {
						$last_db_query = $db->getQuery();
					}
				}

				if (empty($data)) {
					$this->setError(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
					return;
				}

				$this->populateParams($data);
				$this->populateObjects($data);

				$this->_item[$pk] = $data;

			}
			catch (JException $e)
			{
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	* Returns the alias of the list model.
	*
	* @access	public
	* @return	void
	*
	* @since	Cook 2.0
	*/
	public function getNameList()
	{
		return $this->viewList;
	}

	/**
	* A protected method to get a set of ordering conditions.
	*
	* @access	protected
	* @param	JTable	$table	A JTable object.
	*
	* @return	mixed	An array of conditions or a string to add to add to ordering queries.
	*
	* @since	12.2
	*/
	protected function getReorderConditions($table)
	{
		return array();
	}

	/**
	* Method to increment hits when necessary (check session and layout)
	*
	* @access	public
	* @param	array	$layouts	List of authorized layouts for hitting the object
	*
	* @return	boolean	Null if skipped. True when incremented. False if error.
	*/
	public function hit($layouts = null)
	{
		//Not been overrided in this model (no hit function)
		if (!$layouts)
			return;

		$name = $this->getName();
		$context = $this->getState('context');

		//Search if this item is requested from an item layout
		$found = false;
		foreach($layouts as $layout)
			if ($context == ($name . '.' . $layout))
				$found = true;

		//This layout is not an item layout context
		if (!$found)
			return;

		//Search if the user already loaded this item.
		$id = $this->getState($name . '.id');

		$app = JFactory::getApplication();
		$hits = $app->getUserState($this->context . '.hits', array());


		//This item has already been seen during this session
		if (in_array($id, $hits))
			return;

		$hits[] = $id;

		//Increment the hits
		$table = $this->getTable();
		if (!$table->hit($id))
			return false;

		$app->setUserState($this->context . '.hits', $hits);

		return true;
	}

	/**
	* Method to cascad delete items.
	*
	* @access	public
	* @param	string	$key	The foreign key which relate to the cids.
	* @param	array	$cid	The deleted ids of foreign table.
	*
	* @return	boolean	True on success
	*/
	public function integrityDelete($key, $cid = array())
	{
		if (count( $cid ))
		{
			$db = $this->_db;
			$table = $this->getTable();
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'SELECT id FROM ' . qn($db, $table->getTableName())
				. " WHERE `" . $key . "` IN ( " . $cids . " )";
			$db->setQuery($query);
			$list = $db->loadObjectList();

			$cidsDelete = array();
			if (count($list) > 0)
				foreach($list as $item)
					$cidsDelete[] = $item->id;

			//using the model, the integrities can be chained.
			return $this->delete($cidsDelete);

		}

		return true;
	}

	/**
	* Method to reset foreign keys.
	*
	* @access	public
	* @param	string	$key	The foreign key which relate to the cids.
	* @param	array	$cid	The deleted ids of foreign table.
	*
	* @return	boolean	True on success
	*/
	public function integrityReset($key, $cid = array())
	{
		if (count( $cid ))
		{
			$db = $this->_db;
			$table = $this->getTable();

			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'UPDATE ' . qn($db, $table->getTableName())
				.	' SET ' . qn($db, $key) . ' = 0'
				. ' WHERE ' . qn($db, $key) . ' IN ( ' . $cids . ' )';
			$db->setQuery( $query );

			if(!$db->query()) {
				JError::raiseWarning(1100, $db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	* Method to check accesslevel.
	*
	* @access	public
	* @param	object	$record	A record object.
	* @param	string	$accessKey	The access level field name.
	*
	* @return	boolean	True if allowed.
	*/
	public function isAccessible($record, $accessKey = 'access')
	{
		//Accesslevels are not instancied
		if (!property_exists($record, $accessKey))
			return true;

		//User group affiliations permits to access
		$groupsByUser = JAccess::getGroupsByUser(JFactory::getUser()->id);
		
		if(is_array($record->$accessKey)){
			if(count(array_intersect($record->$accessKey, $groupsByUser)) > 0){
				return true;
			}		
		} else {
			if (in_array($record->$accessKey, $groupsByUser)){
				return true;
			}
		}

		return false;
	}

	/**
	* Method to check is the current user is the author (or can be the author).
	*
	* @access	public
	* @param	object	$record	A record object.
	* @param	string	$authorKey	The authoring field name.
	*
	* @return	boolean	True if allowed.
	*/
	public function isAuthor($record, $authorKey = 'created_by')
	{
		//Authoring is not used
		if (!property_exists($record, $authorKey))
			return true;

		//Author is not defined
		if (empty($record->$authorKey))
			return false;

		//Current user is author
		if ($record->$authorKey == JFactory::getUser()->get('id'))
			return true;

		return false;
	}

	/**
	* Method to check if item has already been opened.
	*
	* @access	public
	* @param	object	$record	A record object.
	* @param	string	$checkedKey	The check out field name.
	*
	* @return	boolean	True if allowed.
	*/
	public function isCheckedIn($record, $checkedKey = 'checked_out')
	{
		if (	property_exists($record, $checkedKey)
			&& 	!empty($record->$checkedKey)
			&& 	$record->$checkedKey != JFactory::getUser()->get('id')){
			return true;
		}

		return false;
	}

	/**
	* Method to check if then item can be seen, basing on publish state.
	*
	* @access	public
	* @param	object	$record	A record object.
	* @param	string	$publishKey	The publish state field name.
	*
	* @return	boolean	True if allowed.
	*/
	public function isPublished($record, $publishKey = 'published')
	{
		//Published states are not instancied
		if (!property_exists($record, $publishKey))
			return true;

		$acl = JformsHelper::getActions($this->getId(),$this->view_list);

		//Who can change state can always see all.
		if ($acl->get('core.edit.state'))
			return true;

		//Published state is not defined
		if ($record->$publishKey === null)
			return true;

		//Published item
		if ($record->$publishKey == 1)
			return true;

		return false;
	}

	/**
	* Method to check the visibility of the item.
	*
	* @access	public
	* @param	object	$record	A record object.
	*
	* @return	boolean	True if allowed.
	*/
	public function isVisible($record)
	{
		if (!$this->isAccessible($record))
			return false;

		if (!$this->isPublished($record))
			return false;

		return true;
	}

	/**
	* Method to get a form object.
	*
	* @access	protected
	* @param	string	$name	The name of the form.
	* @param	string	$source	The form source. Can be XML string if file flag is set to false.
	* @param	array	$options	Optional array of options for the form creation.
	* @param	boolean	$clear	Optional argument to force load a new form.
	* @param	string	$xpath	An optional xpath to search for the fields.
	*
	* @return	mixed	returnDesc.
	*
	* @since	12.2
	*/
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear)
		{
			return $this->_forms[$hash];
		}

		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		
		JForm::addRulePath(JPATH_COMPONENT . '/models/rules'); 		
		JForm::addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields'); 
		JForm::addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules'); 

		try
		{
			$form = JForm::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}

	/**
	* Load a N:x relation list to objects array in the item.
	*
	* @access	public
	* @param	object	&$item	The item to populate.
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
	public function loadXref(&$item, $objectField, $xrefTable, $on, $key, $states = array(), $context = 'object.default')
	{
		$db = JFactory::getDbo();

		if ($this->getState('xref.' . $objectField))
		{
			$model = CkJModel::getInstance($xrefTable, 'jformsModel');
	
			// Prepare the fields to load, trough a context profile
			$model->setState('context', $context);
	
			// Filter on the origin
			$model->addWhere(qn($db, $on) . '='. (int)$item->$key);

			// Cascad objects states
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

	/**
	* Method to set default to the item.
	*
	* @access	public
	* @param	int	$id	Id of the item to become default.
	* @param	varchar	$field	Default field name.
	* @param	string	$where	Distinct the defaulting basing on this condition.
	*
	* @return	boolean	True on success. False if error.
	*/
	public function makeDefault($id, $field = 'default', $where = '')
	{
		$table = $this->getTable();

		if (!$table->load($id))
			return false;

		if (!$this->canEditDefault($table))
			return false;

		$pk = $table->getKeyName();

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->update(qn($db, $table->getTableName()));
		$query->set(qn($db, $field) . ' = (' . qn($db, $pk) . ' = ' . (int)$id . ' )');

		if (trim($where) != '')
			$query->where($where);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	* Prepare some additional derivated objects.
	*
	* @access	public
	* @param	object	&$item	The object to populate.
	* @return	void
	*/
	public function populateObjects(&$item)
	{
		if(isset($item->access) AND is_string($item->access)){
			$item->access = explode(',',$item->access);
		}
	}

	/**
	* Prepare some additional important values.
	*
	* @access	public
	* @param	object	&$item	The object to populate.
	* @return	void
	*/
	public function populateParams(&$item)
	{
		if (!$item)
			return;

		$item->params = new JObject();

		if ($this->canView($item))
			$item->params->set('access-view', true);

		if ($this->canEdit($item))
			$item->params->set('access-edit', true);

		if ($this->canDelete($item))
			$item->params->set('access-delete', true);

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
		// Load id from array from the request.
		$jinput = JFactory::getApplication()->input;

		//1. First read the state var
		//2. Then read from Request
		//3. Finally search if cid is an array var (in request)
		$id = $this->state->get($this->getName() . '.id', 
			$jinput->get('id', 
				$jinput->get('cid', null, 'ARRAY')
				, 'ARRAY'));

		if (is_array($id))
			$id = $id[0];

		//assure compatibility when cid is received instead of id
		$jinput->set('id', $id);

		parent::populateState($ordering, $direction);

		if (defined('JDEBUG'))
			$_SESSION["Jforms"]["Model"][$this->getName()]["State"] = $this->state;

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
		$acl = JformsHelper::getActions($this->getId(),$this->view_list);

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
			    $groups	= implode(',', JFactory::getUser()->getAuthorisedViewLevels());
				$whereAccess = $table . '.access IN ('.$groups.')';
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
	* Method to allow derived classes to preprocess the form.
	*
	* @access	protected
	* @param	JForm	$form	A JForm object.
	* @param	mixed	$data	The data expected for the form.
	* @param	string	$group	The name of the plugin group to import (defaults to "content").
	* @return	void
	*
	* @since	12.2
	*/
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		$baseFolder = JPATH_COMPONENT .DS. 'fork' .DS. 'models' .DS. 'forms';
		$formFile = $baseFolder .DS. $this->view_item .'.xml';
		if (file_exists($formFile))
		{		
			$xml = simplexml_load_file($formFile);
			$form->load($xml, true);			
		}


		$form->addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields'); 
		$form->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules'); 
		// manage REQUIRED extended
		$isNew = !($this->getId() > 0);
		$reqAttr = 'newRequired';
		if(!$isNew){
			$reqAttr = 'editRequired';
		}

		$fieldSets = $form->getFieldsets();
		foreach($fieldSets as $fSet){
			$fSetFields = $form->getFieldset($fSet->name);
			
			foreach($fSetFields as $fi){
				$required = $fi->required;
				$required_extended = $form->getFieldAttribute($fi->fieldname,$reqAttr,null,$fi->group);
			
				if(!$required AND !empty($required_extended)){			
					if($required_extended === 'true' || $required_extended == 'required'){
						$required = true;
					} else if(class_exists('ByGiroHelper')){ // check ACL
						$required = ByGiroHelper::canAccess($required_extended);
					}
				}
				
				if($required){
					$form->setFieldAttribute($fi->fieldname,'required',true,$fi->group);
				}
			}			
		}


		parent::preprocessForm($form, $data, $group);
	}

	/**
	* Saves the manually set order of records.
	*
	* @access	public
	* @param	array	$pks	An array of primary key ids.
	* @param	integer	$order	+1 or -1
	* @param	string	$where	The stringified condifions for ordering.
	*
	* @return	boolean	True on success.
	*
	* @since	12.2
	*/
	public function saveorder($pks = null, $order = null, $where = null)
	{
		$table = $this->getTable();
		$conditions = array();

		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			// Access checks.
			if (!$this->canEdit($table))
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
			}

			elseif (isset($order[$i]) && $table->ordering != $order[$i])
			{
		
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}

				if ($where)
					$condition = array($where);
				else
					$condition = $this->getReorderConditions($table);


				$found = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$key = $table->getKeyName();
					$conditions[] = array($table->$key, $condition);
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
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
		return $this->state->set($property, $value);
	}

	/**
	* Method to toggle a value, including integer values
	*
	* @access	public
	* @param	string	$fieldName	The field to increment.
	* @param	integer	$pk	The id of the item.
	* @param	integer	$max	Max possible values (modulo). Reset to 0 when the value is superior to max.
	*
	* @return	boolean	True when changed. False if error.
	*/
	public function toggle($fieldName, $pk = null, $max = 1)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		$table = $this->getTable();
		if (!$table->toggle($fieldName, $pk, $max))
		{
			JError::raiseWarning(1106, JText::sprintf("JFORMS_MODEL_IMPOSSIBLE_TO_TOGGLE", $fieldName));
			return false;
		}

		return true;
	}

	/**
	* Method to validate the form data. 
	*  This override handle the inputs of files types, (Joomla issue when they
	* are required)
	*
	* @access	public
	* @param	object	$form	The form to validate against.
	* @param	array	$data	The data to validate.
	* @param	string	$group	The name of the field group to validate.
	*
	* @return	mixed	Array of filtered data if valid, false otherwise.
	*/
	public function validate($form, $data, $group = null, $cleanData = array(), $postSave = false)
	{	
		$config	= JComponentHelper::getParams( 'com_jforms' );
		$data_keys = array_keys($data);
		$this->errors = array();
		
		if(!empty($data['SAVE_AS_ID'])){
			$currentData = $this->getItem($data['SAVE_AS_ID']);
			unset($data['SAVE_AS_ID']);
			$currentData->id = 0;
		} else {
			$currentData = $this->getItem();
		}

		$fileInput = new JInput($_FILES);
		$data['tmp_files'] = $fileInput->get('jform', null, 'array');
		
		if(empty($cleanData)){
			$cleanData = array();
		}
		$fieldsets = $form->getFieldsets();

		foreach($fieldsets as $fname => $fset){
			// check the form is repeatable
			if((isset($fset->repeatable) AND $fset->repeatable === 'true') OR strpos($fname,'.filters') !== false OR strpos($fname,'_modal') !== false){
				continue;
			}
			$this->validateFieldset($form,$fset,$currentData,$cleanData,$data, null, $postSave);
		}

		// restore the ID
		$cleanData['id'] = $data['id'];
		
		unset($data['tmp_files']);
		
		if(!empty($this->errors)){
			return false;
		}
		
		if(isset($cleanData['access']) AND is_array($cleanData['access'])){
			$cleanData['access'] = implode(',',$cleanData['access']);
		}
		
		return $cleanData;
	}
		
	public function validateFieldset($form,&$fset,$currentData,&$cleanData,$data,$customGroup = null, $postSave = false, $remove_it = false){
		static $config;
		
		if(!$config){
			$config	= JComponentHelper::getParams( 'com_jforms' );
		}

		$fname = $fset->name;		
		foreach($form->getFieldset($fname) as $field){
			if($field->type == 'ckspacer' OR $field->type == 'spacer'){
				continue;
			}
		
			while (strpos($customGroup, '..') !== false) {
				$customGroup = str_replace('..', '.', $customGroup);
			}
			$customGroup = trim($customGroup,'.');

			if($customGroup == null){
				$customGroup = $field->group;
			}
			
			$fieldName = $field->fieldname;
			$field->eleXML = $this->extendField($form,$field);
			if($field->type == 'ckfieldset'){
				// get data from POST
				$items = ByGiroHelper::array_path_value($data, $customGroup .'.'. $fieldName);
				$removeAll = ByGiroHelper::array_path_value($data, $customGroup .'.'. $fieldName .'_remove_all');
				
				if($postSave){
					// fix array keys = items keys for already stored data
					$cleanItems = ByGiroHelper::array_path_value($cleanData, $customGroup .'.'. $fieldName);
					$cleanItems = ByGiroHelper::groupArrayByValue((array)$cleanItems, 'id', false);
					$cleanData = ByGiroHelper::set_array_path_value($cleanData, $customGroup .'.'. $fieldName, $cleanItems);				
				}

				// recursive on validateFieldset
				// check XML subitems fields
				$subForm = ByGiroHelper::getSubForm($field->eleXML);
				if(!empty($subForm) AND $subForm instanceof JForm){
					$form = $subForm;
				}
				
				$fsetName = $form->getFieldAttribute($fieldName,'fieldsetName',null, $field->group);				
				$subfset = $form->getFieldsets();
				$subfset = $subfset[$fsetName];

				// check a couple of things
				$subfset = $this->extendFieldset($form,$subfset);

				
				// get data from CURRENT data
				$oldItems = ByGiroHelper::array_path_value($currentData, $customGroup .'.'. $fieldName);					
				if(empty($oldItems)){
					$oldItems = array();
				}
				$oldItems = ByGiroHelper::groupArrayByValue((array)$oldItems, 'id', false);						
				
				$itemsToRemove = array();
				if($removeAll){
					$itemsToRemove = array_keys($oldItems);
					$itemsToRemove = array_combine($itemsToRemove,$itemsToRemove);
				}				
				
				foreach($items as $fieldset_key => $it){
					$remove_item = 0;					
					if(empty($fieldset_key)){
						continue;
					}
					
					// get the remove_item value
					$remove_item =  intVal($it['remove_item']);
						
					if($remove_item > 0){
						// add the id to the list of items to remove later
						$itemsToRemove[$fieldset_key] = $fieldset_key;	
						continue;
					} else {
						unset($itemsToRemove[$fieldset_key]);
					}
					
					$fieldGroup = trim($customGroup .'.'. $fieldName .'.'. $fieldset_key,'.') ;
			
					if(!$postSave){
						$cleanData = ByGiroHelper::set_array_path_value($cleanData, $fieldGroup .'.id', $fieldset_key);
					} else {
						// check the item has been saved and exists, to avoid upload of files
						$thisItem = ByGiroHelper::array_path_value($currentData, $customGroup);
						if($thisItem === null){
							continue;
						}
					}

					$this->validateFieldset($form,$subfset,$currentData,$cleanData,$data,$fieldGroup, $postSave, $remove_item);
				}

				$cleanItems = ByGiroHelper::array_path_value($cleanData, $customGroup .'.'. $fieldName);
				
				$mergedItems = (array)$cleanItems + (array)$oldItems;
			
				// remove elements with NO ID
				unset($mergedItems['']);
				if($postSave){
					$table = $this->getTable();
					$tableName = $table->getTableName();

					foreach($itemsToRemove as $id){
						// remove files
						foreach($subfset->fileFields as $fi){
							$element = $field->eleXML;
							$fiName = $fi->fieldname;
							@$filePath = $mergedItems[$id]->$fiName;
							
							if(empty($filePath)){
								continue;
							}
							$checkIsUniqueFileOnThisItem = $config->get('checkIsUniqueFileOnThisItem',false);
							$checkIsUniqueFileOnAllItems = $config->get('checkIsUniqueFileOnAllItems',false);
							
							$isUniqueFileOnThisItem = true;
							$isUniqueFileOnAllItems = true;
							
							// check if this file is ONLY used by this subitem
							if($checkIsUniqueFileOnThisItem){
								$isUniqueFileOnThisItem = ByGiroHelper::isValUniqueInArray($mergedItems,$fiName,$filePath,$mergedItems[$id]->id);
							}
							
							if($isUniqueFileOnThisItem AND $checkIsUniqueFileOnAllItems){
								// check if this file is ONLY used by this subitem (checking all items in DB) - TO DO use the path group
								$isUniqueFileOnAllItems = ByGiroHelper::isValUniqueInDBItems($tableName,$fieldName,$fiName,$filePath,$currentData->id);
							}
							
							if(!$isUniqueFileOnAllItems OR !$isUniqueFileOnThisItem){
								continue;
							}
							
							$dir = $config->get('upload_dir_' . $this->view_list . '_' . $fieldName, '[COM_SITE]' .DS. 'files' .DS. $this->view_list . '_' . $fieldName);
							$root = (string)$element['root'];
							if($root != ''){
								$dir = $root;
							}

							if (!preg_match("/\[.+\]/", $filePath)){
								$filePath = $dir .DS. $filePath;
							}
							
							JformsClassFile::deleteFile($filePath, 'delete');
						}
						
						// remove item
						unset($mergedItems[$id]);
					}
		
					// assign ordering if found
					if($subfset->orderingField){
						// TO DO: controllare con nested repeatable fieldsets
						$ordering = ByGiroHelper::array_path_value($data, $customGroup .'.'. $fieldName .'_list_ordering');
						$ordering = JFilterInput::getInstance()->clean($ordering, 'string');				

						$ordering = explode(',',$ordering);				
						foreach($ordering as $k => $id){
							if(isset($mergedItems[$id])){
								$mergedItems[$id]->{$subfset->orderingField} = $k;
							}
						}
						// normalize elements type, TO DO: improve or fix this
						$mergedItems = (array)json_decode(json_encode($mergedItems));
						
						$mergedItems = ByGiroHelper::sort_on_field($mergedItems, $subfset->orderingField, 'ASC');
					}				
				}
				
				// assign the updated list of items
				$cleanData = ByGiroHelper::set_array_path_value($cleanData, $customGroup .'.'. $fieldName, array_values($mergedItems));

			} else if($field->type == 'ckconfig'){
				$subForm = ByGiroHelper::getSubForm($field->eleXML);
				if(!empty($subForm) AND $subForm instanceof JForm){
					$form = $subForm;
				} else {
					continue;
				}
				
				$cleanFieldData = array();
				$groups = ByGiroHelper::getUserGroups();

				$subfsets = $form->getFieldsets();
				foreach($subfsets as $subfset){
					$fields = $form->getFieldset($subfset->name);
					unset($fi);
					foreach($fields as $fi){
						$fakeForm = '<?xml version="1.0" encoding="UTF-8"?>
						<form>
							 <fieldset name="'. $subfset->name .'">
								<field
									name="'. $fi->fieldname .'_inherit"
									type="cktext"
									filter="BOOL"/>
							 </fieldset>
						</form>';
						$form->load($fakeForm);						
					}
				}
	
				unset($gr);
				foreach($groups as $gr){					
					foreach($subfsets as $subfset){
						// check a couple of things
						$subfset = $this->extendFieldset($form,$subfset);
					
						$fieldGroup = trim($customGroup .'.'. $fieldName .'.'. $gr->value,'.');					
						$this->validateFieldset($form,$subfset,$currentData,$cleanData,$data,$fieldGroup, $postSave);					
					}
				}
			} else {
				if($remove_it){
					continue;
				}
				
				$element = $field->eleXML;
				@$multilanguage = (string)$element['multilanguage'];
				$multilanguage = (!empty($multilanguage)) ? true : false;
				$ml_fields = array();
				$ml_fields[] = $field;
				
				if($multilanguage OR (isset($fset->multilanguage) AND $fset->multilanguage != '')){
					@$attributes = array(
						'root'=> (string)$element['root'],
						'rename'=> (string)$element['rename']
					);
					$modField = clone $field;					
					$xmlString = $field->eleXML->asXML();							
					$modField->eleXML = new SimpleXmlElement($xmlString);					
					
					// get all languages
					$languages = ByGiroHelper::getInstalledLanguages();					
					foreach($languages as $lang){
						// WTF!!!!!! object is GONE!!!!
						if(empty($modField) OR !method_exists($modField,'__set')){
							$modField = clone $field;
							$xmlString = $field->eleXML->asXML();
							$modField->eleXML = new SimpleXmlElement($xmlString);
						}					
						$modField->__set('fieldname',$modField->fieldname . $lang->postfix);

						if($modField->type == 'ckfile'){
							foreach($attributes as $att => $val){
								$lang_code = $lang->lang_code;	
								$modElement = $modField->eleXML;
								
								if(strpos($val,'{LANG}') !== false){
									$modField->__set($att,str_replace('{LANG}',$lang_code, $val));
									
									unset($modElement->attributes()->{$att});
									$newAttVal = str_replace('{LANG}',$lang_code, $val);
									$modElement->addAttribute($att,$newAttVal);
									$modField->eleXML = $modElement;								
								}
							}
						}
						// remove required for multilanguage field
						$modField->required = false;						
						$modField->eleXML['required'] = false;
						$modField->eleXML['newRequired'] = false;
						$modField->eleXML['editRequired'] = false;
						
						$ml_fields[] = $modField;
						$modField = null;
						unset($modField);
					}
				}
				
				foreach($ml_fields as $ml_fi){			
					if($postSave){					
						if($ml_fi->type != 'ckfile'){
							continue;
						}
						
						$value = $this->uploadFile($form,$ml_fi,$customGroup,$data);

					} else {				
						$value = $this->cleanValue($form,$ml_fi,$currentData,$cleanData,$data, $customGroup);
						if($ml_fi->type == 'ckcaptcha'){
							continue;
						}

						if($ml_fi->type == 'ckfile'){
							if($this->uploadFile($form,$ml_fi,$customGroup,$data,true) === false){
								$this->setError('File upload error');
							}
						}
						
					}
					
					//if it's not new, skip field if it's NOT IN FORM (in this way the old prevoous value will be used)
					if(!empty($data['id']) AND !$postSave){
						$jinputField = ByGiroHelper::array_path_value($data, trim($customGroup .'.'. $ml_fi->fieldname,'.'));
						if($jinputField === null){
							continue;
						}
					}
					
					$cleanData = ByGiroHelper::set_array_path_value($cleanData, trim($customGroup .'.'. $ml_fi->fieldname,'.'), $value);
				}
			}
		}
	}

	protected function extendFieldset($form,$fset){
	
		$fileFields = array();
		foreach($form->getFieldset($fset->name) as $field){
			$fieldName = $field->fieldname;
			
			if($field->type == 'ckordering'){
				$fset->orderingField = $fieldName;
			}
			
			if($field->type == 'ckfile'){
				$fileFields[] = $field;
			}
		}

		$fset->fileFields = $fileFields;
		
		return $fset;
	}
	
	protected function cleanValue($form,$field,$currentData,$cleanData,$data,$fieldGroup = ''){
		$input = new JRegistry($data);
		$fieldName = $field->fieldname;
		$value = null;
		
		// get data value considering the groups tree
		if($fieldGroup != ''){
			$value = ByGiroHelper::array_path_value($data, $fieldGroup .'.'. $fieldName);
		} else if(isset($data[$fieldName])){
			$value = $data[$fieldName];
		}
		
		//Missing in $_POST and required
		if (($field->required OR $fieldName == 'created_by') && ($value === null OR $value == '')){
			if($fieldGroup != ''){
				$value = ByGiroHelper::array_path_value($currentData, $fieldGroup .'.'. $fieldName);
			} else {
				//Insert the current object value. (UPDATE)
				@$value = $currentData->$fieldName;
			}
		}
	
		if(is_array($value)){
			$valOk = array();
			foreach($value as $v){			
				// clean the value
				$vOk = $this->filterField($form, $field, $v);
					
				// PHP, validate the field.
				$valid = $this->validateField($field, $fieldGroup, $vOk, $input, $form);
				
				// Check for an error.
				if ($valid instanceof Exception){				
					$this->errors[] = (string)$valid;					
				} else {
					if($vOk != ''){
						$valOk[] = $vOk;
					}
				}
			}					
		} else {						
			// clean the value
			$valOk = $this->filterField($form, $field, $value);
			
			// PHP, validate the field.
			$valid = $this->validateField($field, $fieldGroup, $valOk, $input, $form);

			// Check for an error.
			if ($valid instanceof Exception){
				$this->errors[] = (string)$valid;
				$valOk = '';
			}
		}

		$jFix = $this->joomlaFixTypeField($field, $valOk, $fieldGroup, $data, $cleanData, $currentData, $form);
		
		return $valOk;
	}
	
	protected function uploadFile($form,&$field, $fieldGroup,$data, $test = false){
		$value = null;
		$element = $field->eleXML;
		
		// upload files and eventually modify the filename
		$options = array(
			'allowedExtensions' => '',
			'indirect' => '',
			'thumbnails' => '',
			'attrs' => '',
			'root' => '',
			'rename' => '',
			'maxSize' => '',
			'overwrite' => ''
		);
		
		foreach($options as $key => $opt){
			@$options[$key] = (string)$element[$key];
		}
		
		$options['test'] = $test;
		
		// if editable -> if changed -> write the file
		@$editable = (string)$element['editable'];
		if(!empty($editable)){
			$obj = ByGiroHelper::array_path_value($data, $fieldGroup);

			if($obj[$field->fieldname .'-raw_changed']){
				$options['content'] = $obj[$field->fieldname .'-raw_editor'];
			}
		}	
		
		$dir = $options['root'];
		$options['group'] = $fieldGroup;	

		$options['rename'] = str_replace('{LANG}','default',$options['rename']);
		if($options['root'] != ''){
			$dir = str_replace('{LANG}','default',$options['root']);
		}
		
		$exts = $options['allowedExtensions'];
		$extensions = explode(',',strtolower(str_replace(array('*.',' '),'',$exts)));

		//UPLOAD FILE
		$value = $this->_upload($field->fieldname, $extensions, $options, $dir);
		
		if(!empty($value) AND is_string($value) AND $value[0] != '['){
			$value = $options['root'] .'/'. $value;
			$value = trim(preg_replace("#/+#", "/", str_replace('\\','/',$value)),'/');
		}
		
		return $value;
	}
	
	protected function extendField($form,$field){
		if(method_exists($field,'getXML')){
			$element = $field->getXML();
		} else {
			$element = new SimpleXMLElement("<field/>");
			$attributes = array('root','rename','maxSize','overwrite','message','actions','onDelete',
			'allowedExtensions','indirect','thumbnails','attrs','filter','type','autoGenerate','unique','relative','class','required','newRequired','editRequired',
			'validate','label','alias','name','default','placeholder','printable','multilanguage','forceAbsoluteUrl',
			'editable'
			);
			
			foreach($attributes as $at){
				$attr = $form->getFieldAttribute($field->fieldname,$at,null, $field->group);
				$element->addAttribute($at,$attr);
			}
		}
		
		if((string)$element['type'] == 'ckcaptcha'){
			unset($element->attributes()->validate);
			$element->addAttribute('validate','recaptcha');
		}
		
		return $element;	
	}

	protected function joomlaFixTypeField(&$field, &$value, $fieldGroup, &$data, &$cleanData, $currentData, $form){	
		$valid = true;
		$element = $field->eleXML;		
		
		switch($field->type)
		{
			//JOOMLA FIX : Reformate the date/time format comming from the post
			case 'ckcalendar':

				//cimport('helpers.dates');

				if ($value && (string)$field->format && !JformsHelperDates::isNull((string)$value) )
				{
					$time = JformsHelperDates::getSqlDate($value, array($field->format));
					if ($time === null){
						$this->setError(JText::sprintf('JFORMS_VALIDATOR_WRONG_DATETIME_FORMAT_FOR_PLEASE_RETRY', $field->label));
						$valid = false;
					} else {
						$value = JformsHelperDates::toSql($time);
					}
				}
				break;


			//JOOMLA FIX : Apply a null value if the field is in the form
			case 'ckcheckbox':
				if (!$value){
					$value = 0;
				}
				break;
				

			case 'ckfile':
				// JOOMLA FIX : always missing file names in $_POST -> issue when required
				//Get the posted files if this model is concerned by files submission
				$value = ByGiroHelper::array_path_value($data['tmp_files']['name'], $fieldGroup .'.'. $field->fieldname);
				$value = JFilterInput::getInstance()->clean($value, 'string');
			
				// ADD the CURRENT and REMOVE fields to the $cleanData
				$suffixes = array('-current','-remove');
				foreach($suffixes as $suff){
					if(empty($data[$field->fieldname . $suff])){
						continue;
					}
					if($fieldGroup != ''){						
						$val = ByGiroHelper::array_path_value($data, $fieldGroup .'.'. $field->fieldname . $suff);
						$val = JFilterInput::getInstance()->clean($val, 'string');
						$cleanData = ByGiroHelper::set_array_path_value($cleanData, $fieldGroup .'.'. $field->fieldname . $suff,$val);
					} else {
						$cleanData[$field->fieldname . $suff] = JFilterInput::getInstance()->clean($data[$field->fieldname . $suff], 'string');
					}
				}
				break;				
		}
		
		return $valid;
	}
	
	protected function filterField($form, $field, $value)
	{
		$fieldname = $field->fieldname;
		
		if(isset($field->eleXML)){
			$ele = $field->eleXML;
			@$filter = (string)$ele['filter'];
			@$type = (string)$ele['type'];
			@$relative = (string)$ele['relative'];
			@$forceAbsoluteUrl = (string)$ele['forceAbsoluteUrl'];
		} else {
			$filter = $form->getFieldAttribute($fieldname,'filter',null, $field->group);
			$type = $form->getFieldAttribute($fieldname,'type', null, $field->group);
			$relative = $form->getFieldAttribute($fieldname,'relative', null, $field->group);
			$forceAbsoluteUrl = $form->getFieldAttribute($fieldname,'forceAbsoluteUrl', null, $field->group);
		}

		// Process the input value based on the filter.
		$return = null;
		switch (strtoupper($filter))
		{
			// Access Control Rules.
			case 'RULES':
				$return = array();
				foreach ((array) $value as $action => $ids)
				{
					// Build the rules array.
					$return[$action] = array();
					foreach ($ids as $id => $p)
					{
						if ($p !== '')
						{
							$return[$action][$id] = ($p == '1' || $p === 'true') ? true : false;
						}
					}
				}
				break;

			// Do nothing, thus leaving the return value as null.
			case 'UNSET':
				break;

			// No Filter.
			case 'RAW':
				$return = $value;
				break;

			// Filter the input as an array of integers.
			case 'INT_ARRAY':
				// Make sure the input is an array.
				if (is_object($value))
				{
					$value = get_object_vars($value);
				}
				$value = is_array($value) ? $value : array($value);

				JArrayHelper::toInteger($value);
				$return = $value;
				break;

			// Filter safe HTML.
			case 'SAFEHTML':
				$return = JFilterInput::getInstance(null, null, 1, 1)->clean($value, 'string');
				break;

			// Convert a date to UTC based on the server timezone offset.
			case 'SERVER_UTC':
				if ((int) $value > 0)
				{
					// Get the server timezone setting.
					$offset = JFactory::getConfig()->get('offset');

					// Return an SQL formatted datetime string in UTC.
					$return = JFactory::getDate($value, $offset)->toSql();
				}
				else
				{
					$return = '';
				}
				break;

			// Convert a date to UTC based on the user timezone offset.
			case 'USER_UTC':
				if ((int) $value > 0)
				{
					// Get the user timezone setting defaulting to the server timezone setting.
					$offset = JFactory::getUser()->getParam('timezone', JFactory::getConfig()->get('offset'));

					// Return a MySQL formatted datetime string in UTC.
					$return = JFactory::getDate($value, $offset)->toSql();
				}
				else
				{
					$return = '';
				}
				break;

			// Ensures a protocol is present in the saved field. Only use when
			// the only permitted protocols require '://'. See JFormRuleUrl for list of these.

			case 'URL':
				if (empty($value))
				{
					return false;
				}

				// This cleans some of the more dangerous characters but leaves special characters that are valid.
				$value = JFilterInput::getInstance()->clean($value, 'html');
				$value = trim($value);

				// <>" are never valid in a uri see http://www.ietf.org/rfc/rfc1738.txt.
				$value = str_replace(array('<', '>', '"'), '', $value);

				// Check for a protocol
				$protocol = parse_url($value, PHP_URL_SCHEME);

				// If there is no protocol and the relative option is not specified,
				// we assume that it is an external URL and prepend http://.
				if (($type == 'url' && !$protocol &&  !$relative)
					|| (!$type == 'url' && !$protocol))
				{
					$protocol = 'http';

					// If it looks like an internal link, then add the root.
					if (substr($value, 0) == 'index.php')
					{
						$value = JUri::root() . $value;
					}

					// Otherwise we treat it is an external link.
					// Put the url back together.
					$value = $protocol . '://' . $value;
				}

				// If relative URLS are allowed we assume that URLs without protocols are internal.
				elseif (!$protocol && $relative)
				{
					$host = JUri::getInstance('SERVER')->gethost();

					// If it starts with the host string, just prepend the protocol.
					if (substr($value, 0) == $host)
					{
						$value = 'http://' . $value;
					}
					// Otherwise prepend the root.
					else
					{
						$value = JUri::root() . $value;
					}
				}

				$value = JStringPunycode::urlToPunycode($value);
				$return = $value;
				break;

			case 'TEL':
				$value = trim($value);

				// Does it match the NANP pattern?
				if (preg_match('/^(?:\+?1[-. ]?)?\(?([2-9][0-8][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/', $value) == 1)
				{
					$number = (string) preg_replace('/[^\d]/', '', $value);
					if (substr($number, 0, 1) == 1)
					{
						$number = substr($number, 1);
					}
					if (substr($number, 0, 2) == '+1')
					{
						$number = substr($number, 2);
					}
					$result = '1.' . $number;
				}
				// If not, does it match ITU-T?
				elseif (preg_match('/^\+(?:[0-9] ?){6,14}[0-9]$/', $value) == 1)
				{
					$countrycode = substr($value, 0, strpos($value, ' '));
					$countrycode = (string) preg_replace('/[^\d]/', '', $countrycode);
					$number = strstr($value, ' ');
					$number = (string) preg_replace('/[^\d]/', '', $number);
					$result = $countrycode . '.' . $number;
				}
				// If not, does it match EPP?
				elseif (preg_match('/^\+[0-9]{1,3}\.[0-9]{4,14}(?:x.+)?$/', $value) == 1)
				{
					if (strstr($value, 'x'))
					{
						$xpos = strpos($value, 'x');
						$value = substr($value, 0, $xpos);
					}
					$result = str_replace('+', '', $value);

				}
				// Maybe it is already ccc.nnnnnnn?
				elseif (preg_match('/[0-9]{1,3}\.[0-9]{4,14}$/', $value) == 1)
				{
					$result = $value;
				}
				// If not, can we make it a string of digits?
				else
				{
					$value = (string) preg_replace('/[^\d]/', '', $value);
					if ($value != null && strlen($value) <= 15)
					{
						$length = strlen($value);

						// If it is fewer than 13 digits assume it is a local number
						if ($length <= 12)
						{
							$result = '.' . $value;

						}
						else
						{
							// If it has 13 or more digits let's make a country code.
							$cclen = $length - 12;
							$result = substr($value, 0, $cclen) . '.' . substr($value, $cclen);
						}
					}
					// If not let's not save anything.
					else
					{
						$result = '';
					}
				}
				$return = $result;

				break;
			default:
				// Check for a callback filter.
				if (strpos($filter, '::') !== false && is_callable(explode('::', $filter)))
				{
					$return = call_user_func(explode('::', $filter), $value);
				}
				// Filter using a callback function if specified.
				elseif (function_exists($filter))
				{
					$return = call_user_func($filter, $value);
				}
				// Filter using JFilterInput. All HTML code is filtered by default.
				else
				{
					$return = JFilterInput::getInstance()->clean($value, $filter);
				}
				break;
		}

		if(!empty($forceAbsoluteUrl)){
			$rootURL = JUri::root();
			$return = preg_replace("#(?:\s(href|src)\s*=\s*[\"'])(?!http://)(.*?)(?:[\"'])#i", " $1=\"". $rootURL ."$2\"", $return);
		}
		
		return $return;
	}	

	/**
	 * Method to validate a JFormField object based on field data.
	 *
	 * @param   SimpleXMLElement  $element  The XML element object representation of the form field.
	 * @param   string            $group    The optional dot-separated form group path on which to find the field.
	 * @param   mixed             $value    The optional value to use as the default for the field.
	 * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate
	 *                                      against the entire form.
	 *
	 * @return  mixed  Boolean true if field value is valid, Exception on failure.
	 *
	 * @since   11.1
	 * @throws  InvalidArgumentException
	 * @throws  UnexpectedValueException
	 */
	protected function validateField(&$field, $group = null, &$value = null, JRegistry $input = null, JForm &$form = null)
	{
		$element = $field->eleXML;	
		if (!($element instanceof SimpleXMLElement)){
			return false;
		}		
		
		$valid = true;
		$type = (string)$element['type'];

		// Check if the field is required.
		@$required = (string)$element['required'];
		$required = (!empty($required) AND ($required === 'true' || $required == 'required'));
		
		// Check if the field is required EXTENDED
		$isNew = !($this->getId() > 0);
		$reqAttr = 'newRequired';
		if(!$isNew){
			$reqAttr = 'editRequired';
		}
		@$required_extended = (string)$element[$reqAttr];
		if(!$required AND !empty($required_extended)){			
			if($required_extended === 'true' || $required_extended == 'required'){
				$required = true;
			} else if(class_exists('ByGiroHelper')){ // check ACL
				$required = ByGiroHelper::canAccess($required_extended);
			}
		}
		
		if ($required AND $type != 'ckcaptcha')
		{
			// If the field is required and the value is empty return an error message.
			if (($value === '') || ($value === null))
			{
				if ($element['label'])
				{
					$message = JText::_($element['label']);
				}
				else
				{
					$message = JText::_($element['name']);
				}

				$message = JText::sprintf('JLIB_FORM_VALIDATE_FIELD_REQUIRED', $message);
				$this->setError($message);
				
				return new RuntimeException($message);
			}
		}

		// Get the field validation rule.
		if (@$type = (string) $element['validate'])
		{
			// Load the JFormRule object for the field.
			$rule = JFormHelper::loadRuleType($type);
			// If the object could not be loaded return an error message.
			if ($rule === false)
			{
				$message = sprintf('%s::validateField() rule `%s` missing.', get_class($this), $type);
				$this->setError($message);
				throw new UnexpectedValueException($message);
			}

			// Run the field validation rule test.
			$valid = $rule->test($element, $value, $group, $input, $form);

			// Check for an error in the validation test.
			if ($valid instanceof Exception)
			{
				return $valid;
			}
		}

		// Check if the field is valid.
		if ($valid === false)
		{
			// Does the field have a defined error message?
			if (@$message = (string) $element['message'])
			{
				$message = JText::_($element['message']);
				$this->setError($message);
				return new UnexpectedValueException($message);
			}
			else
			{
				$message = JText::_($element['label']);
				$message = JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', $message);
				$this->setError($message);
				return new UnexpectedValueException($message);
			}
		}

		return true;
	}

	public function addWhere($where)
	{
		$this->addQuery('where', $where);
	}
	
	/**
	* Method to delete item(s).
	*
	* @access	public
	* @param	array	&$pks	Ids of the items to delete.
	*
	* @return	boolean	True on success.
	*/
	public function delete(&$pks)
	{
		if (!count( $pks ))
			return true;

		$app = JFactory::getApplication();
				
		// delete files
		//Integrity : delete the files associated to this deleted item
		try {
			$result = $this->deletefiles($pks);
		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(),'error');
			return false;
		}
		if (!$result){
			$app->enqueueMessage(JText::_("JFORMS_ALERT_ERROR_ON_DELETE_FILES"),'error');
			return false;
		}
		
		if (!parent::delete($pks))
			return false;



		return true;
	}
	
	public function getFileFields($form,$fset,$customGroup = null)
	{
		$fileFields = array();
		if(!($form instanceof JForm) OR empty($fset)){
			return $fileFields;
		}
		

		$fname = $fset->name;		
		foreach($form->getFieldset($fname) as $field){
			if($field->type != 'ckfile' AND $field->type != 'ckfieldset'){
				continue;
			}
		
			while (strpos($customGroup, '..') !== false) {
				$customGroup = str_replace('..', '.', $customGroup);
			}
			$customGroup = trim($customGroup,'.');

			if($customGroup == null){
				$customGroup = $field->group;
			}
			
			$fieldName = $field->fieldname;
			$field->eleXML = $this->extendField($form,$field);
			if($field->type == 'ckfieldset'){
							
				// recursive on validateFieldset
				$fsetName = $form->getFieldAttribute($fieldName,'fieldsetName',null, $field->group);				
				$subfset = $form->getFieldsets();
				$subfset = $subfset[$fsetName];

				$fieldGroup = trim($customGroup .'.'. $fieldName .'.{ID}','.');
				
				$fileFields = array_merge($fileFields,$this->getFileFields($form,$subfset,$fieldGroup));

			} else {				
				$element = $field->eleXML;
				$multilanguage = ((string)$element['multilanguage'] != '') ? true : false;
				$ml_fields = array();
				$ml_fields[] = $field;
				
				if($multilanguage OR (isset($fset->multilanguage) AND $fset->multilanguage != '')){
					$attributes = array(
						'root'=> (string)$element['root'],
						'rename'=> (string)$element['rename']
					);
					$modField = clone($field);
					$xmlString = $field->eleXML->asXML();							
					$modField->eleXML = new SimpleXmlElement($xmlString);					
					
					// get all languages
					$languages = ByGiroHelper::getInstalledLanguages();					
					foreach($languages as $lang){
						// WTF!!!!!! object is GONE!!!!
						if(!method_exists($modField,'__set')){
							$modField = clone($field);
							$xmlString = $field->eleXML->asXML();
							$modField->eleXML = new SimpleXmlElement($xmlString);
						}					
						$modField->__set('fieldname',$modField->fieldname . $lang->postfix);						
						$ml_fields[] = $modField;
						$modField = null;
						unset($modField);
					}
				}
				
				foreach($ml_fields as $ml_fi){
					$element = $ml_fi->eleXML;
					$behaviour = 'delete';
					if(isset($element['onDelete']) AND (string)$element['onDelete'] != ''){
						$behaviour = (string)$element['onDelete'];
					}
					$fileFields[trim($customGroup .'.'. $ml_fi->fieldname,'.')] = $behaviour;
				}
			}
		}
		
		return $fileFields;
	}
	
	function processEmails($eventType,$data){
		if(!is_array($data)){
			$data = (array)$data;
		}
		$jForm = $data['jforms_snapshot'];
		$replacerOpts = array(
			'form' => $jForm->form,
			'jdomOptions' => array('indirect' => false)
		);

		$formParts = JformsHelper::buildFormParts($jForm->form,$jForm,$data['form_data'],$replacerOpts);
		
		$config	= JComponentHelper::getParams( 'com_jforms' );
		$emails_dir = $config->get('upload_dir_forms_emails', JPATH_COMPONENT .DS. 'files' .DS. 'forms_emails');
		$emails_dir = JformsHelper::getDirectory($emails_dir);
		$lang = JFactory::getLanguage();		
		$jForm_emails = ByGiroHelper::objectToArray($jForm->emails);
	
	
		$allData = array_merge($data,$formParts);
		$emails = array();
		$pdf = $data['pdf'];
		foreach($jForm_emails as $em){
			if(!is_object($em)){
				$em = (object)$em;
			}
			
			if(!$em->enabled){
				continue;
			}
			
			if($em->language != $lang->getTag() AND $em->language != '*'){
				continue;
			}
			
			if(!isset($em->event)){
				$em->event = 'on_after_save';
			}
			
			$conditionToProceed = $em->event == $eventType;
			if(is_array($eventType)){
				$conditionToProceed = in_array($em->event,$eventType);
			}
			
			if(!$conditionToProceed){
				continue;
			}

			$skip_fields = array('enabled','html','attach_pdf_submitted_form','attachment_file');
			foreach($em as $k => $v){
				if(in_array($k,$skip_fields)){
					continue;
				}				

				// replace steps
				JformsHelper::replaceLayoutSteps($jForm,$jForm->form,$v);

				// replace fieldsets
				JformsHelper::replaceLayoutFieldsets($jForm,$jForm->form,$v);
				
				// replace all jforms tags and language tags
				$em->$k = JformsHelper::replacer($v,$allData, true,'JformsHelper::replaceField', $replacerOpts);
			}
			
			$from = explode(',',$em->from);
			
			$vars = array(
				'recipients'=>'to',
				'recipients_cc'=>'cc',
				'recipients_bcc'=>'bcc',
				'recipients_reply_to'=>'reply_to'
			);
			
			$email = new stdClass;
			
			foreach($vars as $key => $var){
				$r_emails = array();
				$r_names = array();
				$recipients = explode(';',$em->$var);
				foreach($recipients as $r){
					$r = trim($r);
					$r = explode(',',$r);
					
					if(count($r) > 2){
						continue;
					}
					
					$r[0] = trim($r[0]);
					$r[1] = trim($r[1]);
					if($r[0] == ''){
						continue;
					}
					
					$r_emails[] = $r[0];
					$r_names[] = $r[1];				
				}
				
				if(count($r_emails) OR count($r_names)){
					$email->$key = array($r_emails,$r_names);
				}
			}
			
			$email->sender = array($from[0], $from[1]);
			$email->subject = $em->subject;
			$email->body = $em->body;
			$email->html = $em->html;
			
			$email->attachment = array();
			if(!empty($em->attachment_file)){
				$fileURL = $emails_dir .'/'. $em->attachment_file;
				if(strpos($em->attachment_file,'[') !== false){
					$fileURL = JformsHelper::getDirectory($em->attachment_file);
				}
				$filePath = JPath::clean(JPATH_SITE .DS. $fileURL);
				$email->attachment[] = $filePath;
			}

			if($em->attach_pdf_submitted_form){
				if(empty($pdf)){
					$pdf = $pdf_generated = $this->printpdf($data);
				}
				
				if($pdf != ''){
					$email->attachment[] = $pdfFilePath = JPath::clean(JPATH_SITE . DS . JformsHelper::getDirectory($pdf)); // PDF of the form
				}
			}			
			
			$emails[] = $email;
		}

		// remove the file if we don't save the data in the DB
		if(!empty($pdf_generated)){
			unlink($pdfFilePath);
		}

		try {
			// send all emails
			ByGiroHelper::sendEmails($emails);		
		} catch (Exception $e) {
			$errors = $e->getMessage();
		}
	}

}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsClassModelItem')){ class JformsClassModelItem extends JformsCkClassModelItem{} }

