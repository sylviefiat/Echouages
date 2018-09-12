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
* Jforms Item Model
*
* @package	Jforms
* @subpackage	Classes
*/
class JformsCkModelSubmission extends JformsClassModelItem
{
	/**
	* List of all fields files indexes
	*
	* @var array
	*/
	protected $fileFields = array('pdf');

	/**
	* View list alias
	*
	* @var string
	*/
	protected $view_item = 'submission';

	/**
	* View list alias
	*
	* @var string
	*/
	protected $view_list = 'submissions';

	/**
	* Constructor
	*
	* @access	public
	* @param	array	$config	An optional associative array of configuration settings.
	* @return	void
	*/
	public function __construct($config = array())
	{
		parent::__construct();
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
	
	
		// prepare data for trigger
		$model_list = CkJModel::getInstance($this->view_list, 'JformsModel');
		$model_list->addWhere('a.id IN (' . implode(',',$pks) .')');
		$model_list->setState('context','all');
		$submissions = $model_list->getItems();
		
		foreach($submissions as $sub){
			JformsHelper::triggerEvents('on_before_delete',$sub);
		}
		

		// delete all the files with the specified ID		
		$config = JComponentHelper::getParams('com_jforms');
		$attached_files_folder = $config->get("upload_dir_submissions_attached_files", JPATH_SITE .DS. "components" .DS. "com_jforms" .DS. "files" .DS. "submissions_attached_files");
		
		if(file_exists($attached_files_folder)){
			try {
				// get all files in the submission files folder
				$iterator = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator($attached_files_folder),
					RecursiveIteratorIterator::SELF_FIRST
				);
			} catch (Exception $e) {
				$iterator = array();
			}
		}
		
		foreach ($iterator as $file)
		{
			$fileName = $file->getFilename();

			if (!$file->isFile()){
				continue;
			}
			
			$file_id = explode('_',$fileName);
			$file_id = $file_id[0];
			if(in_array($file_id,$pks)){
				unlink($file->getPath() . '/' . $fileName);
			}
		}		
		
		if (!parent::delete($pks))
			return false;


		foreach($submissions as $sub){
			JformsHelper::triggerEvents('on_after_delete',$sub);
			$this->processEmails('on_after_delete',$sub);
		}


		return true;
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
		return $jinput->get('layout', 'submissiondetails', 'STRING');
	}

	/**
	* Returns a Table object, always creating it.
	*
	* @access	public
	* @param	string	$type	The table type to instantiate.
	* @param	string	$prefix	A prefix for the table class name. Optional.
	* @param	array	$config	Configuration array for model. Optional.
	*
	* @return	JTable	A database object
	*
	* @since	1.6
	*/
	public function getTable($type = 'submission', $prefix = 'JformsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	* Method to increment hits (check session and layout)
	*
	* @access	public
	* @param	array	$layouts	List of authorized layouts for hitting the object.
	*
	* @return	boolean	Null if skipped. True when incremented. False if error.
	*
	* @since	11.1
	*/
	public function hit($layouts = null)
	{
		return parent::hit(array('submissiondetails', 'finish'));
	}

	/**
	* Method to get the data that should be injected in the form.
	*
	* @access	protected
	*
	* @return	mixed	The data for the form.
	*/
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jforms.edit.submission.data', array());

		if (empty($data)) {
			//Default values shown in the form for new item creation
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('submission.id') == 0)
			{
				$jinput = JFactory::getApplication()->input;

				$data->id = 0;
				$data->created_by = $jinput->get('filter_created_by', $this->getState('filter.created_by'), 'INT');
				$data->form_id = $jinput->get('filter_form_id', $this->getState('filter.form_id'), 'INT');
				$data->creation_date = null;
				$data->ip_address = null;
				$data->session_id = null;
				$data->form_data = null;
				$data->jforms_snapshot = null;
				$data->pdf = null;
				$data->passphrase = null;
				$data->status = $jinput->get('filter_status', $this->getState('filter.status'), 'STRING');
				$data->payment_status = $jinput->get('filter_payment_status', $this->getState('filter.payment_status'), 'STRING');
				$data->payment_details = null;

			}
		}
		return $data;
	}

	/**
	* Prepare some additional derivated objects.
	*
	* @access	public
	* @param	object	&$item	The object to populate.
	* @return	void
	*
	* @since	Cook 2.0
	*/
	public function populateObjects(&$item)
	{
		
		$item = ByGiroHelper::stringToJsonFields($item);
		

		
		parent::populateObjects($item);
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
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$acl = JformsHelper::getActions();



		parent::populateState($ordering, $direction);
	}

	/**
	* Preparation of the query.
	*
	* @access	protected
	* @param	object	&$query	returns a filled query object.
	* @param	integer	$pk	The primary id key of the submission
	* @return	void
	*/
	protected function prepareQuery(&$query, $pk)
	{

		$acl = JformsHelper::getActions();

		//FROM : Main table
		$query->from('#__jforms_submissions AS a');



		//IMPORTANT REQUIRED FIELDS
		$this->addSelect(	'a.id,'
						.	'a.created_by');

		switch($this->getState('context', 'all'))
		{
			case 'submission.submission':

				//BASE FIELDS
				$this->addSelect(	'a.form_id,'
								.	'a.passphrase');

				break;

			case 'submission.submissiondetails':
			case 'submission.editsubmission':
			case 'submission.finish':
			case 'all':
				//SELECT : raw complete query without joins
				$query->select('a.*');
				break;
		}

		//WHERE : Item layout (based on $pk)
		$query->where('a.id = ' . (int) $pk);		//TABLE KEY

		//FILTER - Access for : Root table


		//SELECT : Instance Add-ons
		foreach($this->getState('query.select', array()) as $select)
			$query->select($select);

		//JOIN : Instance Add-ons
		foreach($this->getState('query.join.left', array()) as $join)
			$query->join('LEFT', $join);
	}

	/**
	* Prepare and sanitise the table prior to saving.
	*
	* @access	protected
	* @param	JTable	$table	A JTable object.
	*
	* @return	void	
	* @return	void
	*
	* @since	1.6
	*/
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();


		if (empty($table->id))
		{
			//Creation date
			if (empty($table->creation_date))
				$table->creation_date = $date->toUnix();


			//Defines automatically the author of this element
			$table->created_by = JFactory::getUser()->get('id');
		}
		else
		{

		}

	}

	/**
	* Save an item.
	*
	* @access	public
	* @param	array	$data	The post values.
	*
	* @return	boolean	True on success.
	*/
	public function save($data)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$form = $this->getForm();
		$jForm = $form->jForm;

		//Convert to unix Format (creation_date)
		if (isset($data['creation_date']))
			$data['creation_date'] = JformsHelperDates::getUnixTimestamp($data['creation_date'], array('d-m-Y H:i:s', 'Y-m-d H:i:s'));
		
		$eventType = 'on_before_edit';
		if(empty($data['id'])){
			$isNew = true;
			
			// check MAX submissions for this user
			if(!JformsHelper::checkMaxSubmissions($jForm->id, $jForm->acl)){
				$app->enqueueMessage(JText::_("JFORMS_YOU_REACHED_THE_MAX_NUMBER_OF_SUBMISSIONS"), 'error');
				return false;
			}			
			
			$eventType = 'on_before_save';
			
			$data['ip_address'] = ByGiroHelper::get_ip_address();
			$otherVars = $jinput->getArray(array(
				'jform' => array(
					'page_url' => 'STRING',
					'page_title' => 'STRING'
				)
			));
			$otherVars = $otherVars['jform'];
			$data['page_url'] = $otherVars['page_url'];
			$data['page_title'] = $otherVars['page_title'];

			// generate password to access the form without login
			$data['password'] = ByGiroHelper::generateRandomString(6);
		}

		$formData = $data;

		$data = array();
		
		if(!$isNew){
			$data['id'] = $formData['id'];
			$data['created_by'] = $formData['created_by'];
			$data['creation_date'] = $formData['creation_date'];
			$data['passphrase'] = $formData['passphrase'];
		}
		unset($formData['id']);
		
		$data['ip_address'] = $formData['ip_address'];
		$data['form_id'] = $jForm->id;
		$data['form_data'] = $formData;
		$data['jforms_snapshot'] = $jForm;

		JformsHelper::triggerEvents($eventType,$data);

		// remove forms from jFieldsets
		@$fieldsets = (!empty($data['jforms_snapshot']->fieldsets)) ? $data['jforms_snapshot']->fieldsets : array();
		foreach($fieldsets as $k => $v){
			unset($data['jforms_snapshot']->fieldsets[$k]->form);
		}
		
		// check fields for JSON data to store
		$data = ByGiroHelper::jsonFieldsToString($data);
		

		//Some security checks
		$acl = JformsHelper::getActions();

		//Secure the author key if not allowed to change
		if (isset($data['created_by']) && !$acl->get('core.edit'))
			unset($data['created_by']);
			
		if($jForm->save_data_in_db){
			$saved = parent::save($data);
		} else {
			$saved = true;
		}

		if(!$saved){
			return false;
		}
		
		return true;
	}
	
	public function preprocessForm(JForm $form, $data, $group = 'content', $form_id = null)
	{
		$baseFolder = JPATH_COMPONENT .DS. 'fork' .DS. 'models' .DS. 'forms';
		$formFile = $baseFolder .DS. $this->view_item .'.xml';
		if (file_exists($formFile))
		{		
			$xml = simplexml_load_file($formFile);
			$form->load($xml, true);			
		}

		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		// check we are NOT in DELETE task
		$task = $jinput->get('task', null, 'CMD');
		if(strtolower($task) == 'delete'){
			parent::preprocessForm($form, $data, $group);
			return;
		}

		$item = $this->getItem();
		$isNew = empty($item->id);
		if(!$form_id AND $isNew){
			$form_id = $jinput->get('frm', '', 'INT');	
		} else {
			$form_id = $item->id;
		}
		
		$jForm = new stdClass;
		if(!empty($form_id)){
			
			if($isNew){
				// load jforms items
				$formModel = CkJModel::getInstance('form', 'JformsModel');
				$jForm = $formModel->getItem($form_id);

				// get mirror elements
				JformsHelper::addMirrorElements($jForm);
			} else {
				$jForm = $item->jforms_snapshot;
			}

			$jForm = JformsHelper::getjFieldsets($jForm);

			// load language files in JOOMLA
			$jForm = JformsHelper::getjFormLanguageFiles($jForm, $isNew, true);
			$ml_fields = JformsHelper::getMultilangTables();
			$jForm = ByGiroHelper::getMlFields($jForm,$ml_fields['forms']);

			// ordering emails
			if(isset($jForm->emails)){
				ByGiroHelper::sort_on_field($jForm->emails, 'ordering', 'ASC');
			}
			
			// ordering events
			if(isset($jForm->events)){
				ByGiroHelper::sort_on_field($jForm->events, 'ordering', 'ASC');
			}
			
			
			$jFieldsets = !empty($jForm->fieldsets) ? $jForm->fieldsets : array();
			foreach($jFieldsets as $fset){
				if(($fset->enabled != 'true' AND $fset->enabled != 1) OR empty($fset->form)){
					continue;
				}
				
				// integrate forms
				if(!empty($fset->form_file_content)){
					if(!($form instanceof JForm)){
						$form = JForm::getInstance('com_jforms.main', $fset->form_file_content, array('control'=>'jform'));
						$form->addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields');
						$form->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules');			
					} else {
						// merge this form with the main form					
						$form->load($fset->form_file_content, true);
					}
				}
			}
		}

		$form->jForm = $jForm;

		parent::preprocessForm($form, $data, $group);
	}
	
	public function printpdf($item = null, $download_pdf = null, $layout_pdf = null, $preview = null){		
		$app = JFactory::getApplication();
		
		$jinput = $app->input;
		
		if($item === null){
			$item = $this->getItem();
		} else {
			$item = (object)$item;
			$this->_item[$item->id] = (object)$item;
		}
		
		if($preview === null){
			$preview = $jinput->get('preview',false,'BOOL');
		}

		if(!empty($layout_pdf)){
			$layout_pdf = $jinput->set('layout_pdf',$layout_pdf);
		}		
		
		if($download_pdf === null){
			$download_pdf = $jinput->get('download_pdf',false,'BOOL');
		}		
		
		$jinput->set('layout', 'submission');
		$jinput->set('isPdf', true);
		
		$controller = CkJController::getInstance('jforms');
		$view = $controller->getView('submission','html');

		$view->setModel($this, true);		
		$view->setLayout('submissiondetails');

		// set authorization to view the layout for pdf creation process
		$session = JFactory::getSession();		
		$session->set('jforms.printing.process',true);		
		
		// clean output
		ob_clean();
		
		// Start capturing output into a buffer
		ob_start();

		$view->display();

		$_output = ob_get_contents();
		ob_end_clean();		

		$style[] = (object)array(
			'type' => 'file',
			'content' => JPATH_SITE .DS.'components'.DS. 'com_jforms' .DS.'css'.DS.'jforms.css',
		);
		
		$now = date ("d-m-Y__H_i_s", $item->creation_date);
		$extra = 'jForm_submission';
		if(!empty($item->created_by)){
			@$extra = $item->_created_by_username;
		}
		$filename = $extra .'_'. $now .'.pdf';
		
		$pdf_content = ByGiroHelper::generatePdf($_output, 'jforms', $download_pdf, $filename, $style, $preview);
		if(empty($pdf_content)){
			return false;
		}

		$pdf_dir = JformsHelper::getDirectory('[DIR_SUBMISSIONS_PDF]');
				
		try {
			// create the folder/subfolders if it doesn't exist
			$jformUploadClass = new JformsClassFileUpload($pdf_dir);
			$pdf_dir = $jformUploadClass->uploadFolder;
			
			// save PDF file
			$pdf_filename = ByGiroHelper::generateRandomString(15) .'.pdf';
			file_put_contents($pdf_dir .DS. $pdf_filename,$pdf_content);
			
			return '[DIR_SUBMISSIONS_PDF]/'. $pdf_filename;	
		} catch (Exception $e) {
			$errors = $e->getMessage();
		}
		
		return;
	}

	/**
	 * Runs a payment callback
	 */
	public function runCallback()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$config = JComponentHelper::getParams('com_jforms');
		$payment_type = $jinput->get('pg', 'none', 'CMD');	
	
		$rawDataPost = JRequest::get('POST', 2);
		$rawDataGet = JRequest::get('GET', 2);
		$data_received = array_merge($rawDataGet, $rawDataPost);	
		
		JPluginHelper::importPlugin( 'jformspayments' );		
		$version = new JVersion();
		// Joomla! 1.6 - 1.7 - 2.5
		if (version_compare($version->RELEASE, '2.5', '<=')){	
			$dispatcher = JDispatcher::getInstance();
		} else {
			$dispatcher = JEventDispatcher::getInstance();
		}		
		
		$pResponse = $dispatcher->trigger('getOrderId',array(
			$payment_type,
			$data_received
		));	
		
		// something went wrong
		if(empty($pResponse)) return false;
		
		$order_id = -1;
		foreach($pResponse as $response){
			if($response > 0){
				$order_id = $response;
			}
		}
		
		// order id not found
		if($order_id <= 0) return false;
		
		$item = $this->getItem($order_id);
		$payment_details = ByGiroHelper::objectToArray($item->payment_details,true);
		@$data_sent = $payment_details['data_to_send'];
		
		if(empty($data_sent)){
			return false;
		}

		$pResponse = $dispatcher->trigger('onPaymentCallback',array(
			$payment_type,
			$data_sent,
			$data_received
		));		

/*
		// TEST
		ob_start();
		echo '<pre>';
print_r($data_sent);
print_r($data_received);
print_r($pResponse);
		echo '</pre>';
		$output = ob_get_contents();
		ob_end_clean();	
	
		$original_filename = $filename = 'test_p';
		$counter = 0;
		while(is_file(JPATH_SITE .DS. 'tmp' .DS. $filename .'.html')){
			$counter++;
			$filename = $original_filename .'_'. $counter;
		}
		file_put_contents(JPATH_SITE .DS. 'tmp' .DS. $filename .'.html',$output);
		
		return true;
		// TEST
*/

		// something went wrong
		if(empty($pResponse)) return false;
		foreach($pResponse as $response){
			if(isset($response['result'])){
				break;
			}
		}

		if(empty($response['result'])) return false;
		
		$transaction_data = array(
			'creation_date' => time(),
			'payment_type' => $paymentmethod,
			'data_sent' => $data_sent,
			'callback_data' => $data_received
		);
		
		$transaction_data = array_merge($transaction_data,$response);
		if(empty($payment_details['transactions'])){
			$payment_details['transactions'] = array();
		}
		$payment_details['transactions'][] = $transaction_data;
		
		$item->payment_details = $payment_details;
		
		$oldPaymentStatus = $item->payment_status;
		$item->payment_status = $transaction_data['payment_status'];
		
		$eventType = array();
		$oldStatus = $item->status;
		switch($item->payment_status){
			case 'completed':
				$item->status = 'paid';
				$eventType[] = 'on_successfull_payment';
				break;
				
			case 'failed':
				$eventType[] = 'on_failed_payment';
				break;
				
			case 'na':
			case 'pending':
			case 'canceled':
			case 'refunded':
			case 'not_created':
			case 'to_be_checked':
			default:
				break;
		}
		
		$toStore = (array)ByGiroHelper::jsonFieldsToString($item);
		$table = $this->getTable();
		$table->bind($toStore);
		$table->store();

		// check payment status changed event
		if($oldPaymentStatus != $item->payment_status){
			$eventType[] = 'on_payment_status_changed';
		}

		// check submission status changed event
		if($oldStatus != $item->status){
			$eventType[] = 'on_submission_status_changed';
		}
		
		// trigger other plugins and sending emails
		if(!empty($eventType)){
			$savedItem = $table->getProperties();
			JformsHelper::triggerEvents($eventType,$savedItem);
			$this->processEmails($eventType,$savedItem);
		}
		
		return true;
	}

}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsModelSubmission')){ class JformsModelSubmission extends JformsCkModelSubmission{} }

