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
* @subpackage	Submission
*/
class JformsCkViewSubmission extends JformsClassView
{
	protected $view = 'submission';
	
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
		if (!in_array($layout, array('submission', 'submissiondetails', 'editsubmission', 'finish')))
			JError::raiseError(0, $layout . ' : ' . JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'));

		$fct = "display" . ucfirst($layout);

		$this->addForkTemplatePath();
		$this->$fct($tpl);			
		$this->_parentDisplay($tpl);
	}


	/**
	* Execute and display ajax queries
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displayAjax($tpl = null)
	{	
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		
		
		$jinput = new JInput;
		$render = $jinput->get('render', null, 'CMD');

		$data = $jinput->get('frmData',array(),'ARRAY');

		$this->model	= $model	= CkJModel::getInstance('submission', 'JformsModel');
		$db = JFactory::getDBO();
		
		switch($render)
		{
			case 'savestep':
				break;
		}		
		
		jexit();
	}
	


	/**
	* Execute and display a template : Edit submission
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displayEditsubmission($tpl = null)
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$jinput = $app->input;
		// Initialiase variables.
		$this->model	= $model	= $this->getModel();
		$this->state	= $state	= $this->get('State');
		$this->params 	= $state->get('params');
		$state->set('context', 'submission.editsubmission');
		$this->item		= $item		= $this->get('Item');
		
		$user		= JFactory::getUser();
		$isNew		= ($model->getId() == 0);
		
		// submission by passphrase?
		$passphrase = $jinput->get('pp', null, 'STRING');
		$storedPassphrase = $session->get('jFormsSubmissionByPassphrase', false);
		
		if($isNew AND (empty($passphrase) OR $passphrase != $storedPassphrase)){
			if($user->guest){
				ByGiroHelper::loginFirstly();			
			} else {
				$db = JFactory::getDBO();
				$db->setQuery("SELECT id FROM #__jforms_submissions WHERE created_by =". $user->id ." ORDER BY creation_date DESC LIMIT 0, 1");
				$lastSubmission_id = $db->loadResult();
				
				if(!empty($lastSubmission_id)){
					$this->item = $item = $model->getItem($lastSubmission_id);
					$state->set('submission.id',$lastSubmission_id);
					$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
					$isNew		= ($model->getId() == 0);
				}
			}
		}
		
		if($isNew){
			$link = 'index.php';
			$msg = JText::_("JFORMS_MISSING_SUBMISSION_ID");
			$app->redirect($link, $msg, $msgType='error');
			return false;
		}
		$this->frm = $form_id = $item->form_id;
		$this->state->set('jforms.form',$form_id);
		
		$state->set('jforms.form', $form_id);

		$this->form		= $form		= $this->get('Form');
		$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
		$lists = array();
		$this->lists = &$lists;

		$text = JText::_("JFORMS_LAYOUT_NEW_SUBMISSION");
		if(!$isNew){
			$text = JText::_("JFORMS_LAYOUT_EDIT_SUBMISSION") .' ID:'. $this->item->id;
		}

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');
		if(empty($this->title)){
			$this->title = $form->jForm->name_ml .' <span style="font-style: italic; font-size: 70%;">('. $text .')</span>';
		}
		$this->description = $form->jForm->description_ml;
		
		$this->frm = $form_id;
		$this->submission_id = $this->item->id;
						
		// Define the default title
		$this->params->def('title', $this->title);

		$this->_prepareDocument();


		
		$user		= JFactory::getUser();
		$isNew		= ($model->getId() == 0);

		//Check ACL before opening the form (prevent from direct access)
		if (!$model->canEdit($item, true))
			$model->setError(JText::_('JERROR_ALERTNOAUTHOR'));

		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			JError::raiseError(500, implode(BR, array_unique($errors)));
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		//Hide the component menu in item layout
		$jinput->set('hidemainmenu', true);

		//Toolbar initialization


		// Save
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::apply('submission.apply', "JFORMS_JTOOLBAR_SAVE");
		// Save & Close
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::save('submission.save', "JFORMS_JTOOLBAR_SAVE_CLOSE");
		// Cancel
		CkJToolBarHelper::cancel('submission.cancel', "JFORMS_JTOOLBAR_CANCEL");
		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');

		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');


	}

	/**
	* Execute and display a template : Finish
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displayFinish($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$this->result = $jinput->get('result', null, 'INT');
		
		// Initialiase variables.
		$this->model	= $model	= $this->getModel();
		$this->state	= $state	= $this->get('State');
		$this->params 	= $state->get('params');
		$state->set('context', 'submission.finish');
		$this->item		= $item		= $this->get('Item');
		$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
		$lists = array();
		$this->lists = &$lists;

		// Define the default title
		$this->params->def('title', JText::_('JFORMS_LAYOUT_FINISH'));

		$this->_prepareDocument();

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');

		$user		= JFactory::getUser();
		$isNew		= ($model->getId() == 0);

		$session = JFactory::getSession();
		$checkoutProcess = $session->get('jforms.checkout.process',false);
		
		if($isNew) $model->setError(JText::_('JERROR_ALERTNOAUTHOR'));	
		
		//Check ACL before opening the view (prevent from direct access)
		if(
			!(
				($checkoutProcess == $item->id) OR 
				$model->canAccess($item)
			)
		){
			$model->setError(JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			JError::raiseError(500, implode(BR, array_unique($errors)));
			return false;
		}

		//Hide the component menu in item layout
		$jinput->set('hidemainmenu', true);

		//Toolbar initialization



		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');

		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');

		$fields = array(
			'thank_you',
			'failed_payment',
		);
		$item->jforms_snapshot->options = ByGiroHelper::getMlFields($item->jforms_snapshot->options,$fields);
		
		$messages = array(
			'thank_you' => $item->jforms_snapshot->options->thank_you_ml,
			'failed_payment' => $item->jforms_snapshot->options->failed_payment_ml
		);

		$this->message = $messages['failed_payment'];
		if($this->result OR $item->payment_status == 'completed'){
			$this->message = $messages['thank_you'];
		}
		
		if(!is_array($item)){
			$data = (array)$item;
		}
		$jForm = $data['jforms_snapshot'];
		$replacerOpts = array(
			'form' => $jForm->form,
			'jdomOptions' => array('indirect' => false)
		);

		$formParts = JformsHelper::buildFormParts($jForm->form,$jForm,$data['form_data'],$replacerOpts);
		$allData = array_merge($data,$formParts);
		
		
		// replace steps
		JformsHelper::replaceLayoutSteps($jForm,$jForm->form,$this->message);

		// replace fieldsets
		JformsHelper::replaceLayoutFieldsets($jForm,$jForm->form,$this->message);
		
		// replace all jforms tags and language tags
		$this->message = JformsHelper::replacer($this->message,$allData, true,'JformsHelper::replaceField', $replacerOpts);

	}

	/**
	* Execute and display a template : Submission
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displaySubmission($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$form_id = $jinput->get('frm', null, 'INT');
		// Initialiase variables.
		$this->model	= $model	= CkJModel::getInstance('submission', 'JformsModel');
		$this->state	= $state	= $this->get('State');
		$this->params 	= $state->get('params');
		if(!$form_id){
			$state->set('jforms.form', null);
			
			$link = 'index.php';
			$msg = JText::_("JFORMS_MISSING_FORM_ID");
			$app->redirect($link, $msg, $msgType='error');
			return false;
		}		
		
		$state->set('jforms.form', $form_id);
		
		$state->set('context', 'submission.submission');
		$this->item		= $item		= $this->get('Item');
		$this->form		= $form		= $this->get('Form');
		$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
		$lists = array();
		$this->lists = &$lists;



		$user		= JFactory::getUser();
		$isNew		= ($model->getId() == 0);

		$this->form->preform = $jinput->get('pre', false, 'BOOL');
		
		if(!($this->form instanceof JForm)){
			$app->redirect(JRoute::_('index.php?option=com_jforms&view=forms'), JText::_("JFORMS_FORM_EMPTY"), 'error');
			return false;
		}

		// check we can access to this form
		$error = array();
		if(class_exists('ByGiroHelper')){
			if(!ByGiroHelper::canAccess($form->jForm->access)){
				$error[] = true;
				$app->enqueueMessage(JText::_("JFORMS_YOU_ARE_NOT_AUTHORIZED"), 'error');
			}
			
			// check MAX submissions for this user
			if(!JformsHelper::checkMaxSubmissions($form->jForm->id, $form->jForm->acl)){
				$error[] = true;
				$app->enqueueMessage(JText::_("JFORMS_YOU_REACHED_THE_MAX_NUMBER_OF_SUBMISSIONS"), 'error');
			}
		}
		
		if(!empty($error)){
			$app->redirect(JRoute::_('index.php?option=com_jforms&view=forms', false));
			return false;		
		}
	
	
		$this->frm = $this->state->get('jforms.form');
		$this->submission_id = $this->state->get('submission.id');
		
		$data = $app->getUserState('com_jforms.edit.submission.data',array());

		if(!empty($data)){
			$this->page_title = $data['page_title'];
			$this->page_url = $data['page_url'];
		}
		
		$this->title = $form->jForm->name_ml;
		$this->description = $form->jForm->description_ml;

		$active_menu = $app->getMenu()->getActive();
		if(!empty($active_menu)){
			$page_title = $active_menu->params->get("page_title");
		}
		
		if(empty($page_title)){
			$page_title = $this->title;
		}
		$this->title = $page_title;
		
		// Define the default title
		$this->params->def('title', $page_title);

		$this->_prepareDocument();

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');

	}

	/**
	* Execute and display a template : Submission details
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displaySubmissiondetails($tpl = null)
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$config = JComponentHelper::getParams('com_jforms');
		$jinput = $app->input;
		$form_id = $jinput->get('frm', null, 'INT');
		$passphrase = $jinput->get('pp', null, 'STRING');
		$this->isPdf = $jinput->get('isPdf', false, 'CMD');
		$this->layout_pdf = $jinput->get('layout_pdf', '', 'STRING');
		$this->paymentType = $paymentType = $jinput->get('pg', null, 'STRING');
		// Initialiase variables.
		$this->model	= $model	= $this->getModel();
		$this->state	= $state	= $this->get('State');
		$this->params 	= $state->get('params');
		$state->set('context', 'submission.submissiondetails');
		$this->item		= $item		= $this->get('Item');
		$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
		$lists = array();
		$this->lists = &$lists;



		$user		= JFactory::getUser();
		$isNew		= ($model->getId() == 0);

		$this->checkoutProcess = $checkoutProcess = $session->get('jforms.checkout.process',false);
		$lastSubmission_id = $session->get($model->get('sessionLastItemVar'),false);

		if($isNew AND ($user->id OR $checkoutProcess OR $lastSubmission_id)){
			if(!$lastSubmission_id){
				$lastSubmission_id = $checkoutProcess;
			}

			if($user->id AND !$lastSubmission_id){
				// let's try to get the last submission
				$db = JFactory::getDBO();
				$db->setQuery("SELECT id FROM #__jforms_submissions WHERE created_by =". $user->id ." ORDER BY creation_date DESC LIMIT 0, 1");
				$lastSubmission_id = $db->loadResult();
			}
			
			if(!empty($lastSubmission_id)){
				$this->item = $item = $model->getItem($lastSubmission_id);
				$state->set('submission.id',$lastSubmission_id);
				$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
				$isNew		= ($model->getId() == 0);
			}
		}

		if($isNew){
			if($form_id){
				$app->redirect(JRoute::_('index.php?option=com_jforms&view=submission&layout=submission&frm='. $form_id));
			} else {
				$app->redirect(JRoute::_('index.php'), JText::_("JFORMS_MISSING_SUBMISSION_ID"), 'error');
			}
			return false;
		}
		
		$pdfCreation = false;
		if($this->isPdf){		
			$pdfCreation = $session->get('jforms.printing.process',false);
		}

		$this->item->jforms_snapshot = JformsHelper::getjFieldsets($this->item->jforms_snapshot,false);
		$this->item->jforms_snapshot = JformsHelper::getjFormLanguageFiles($this->item->jforms_snapshot, false, true);
		$ml_fields = JformsHelper::getMultilangTables();
		$this->item->jforms_snapshot = ByGiroHelper::getMlFields($this->item->jforms_snapshot,$ml_fields['forms']);

		$this->item->jforms_snapshot = JformsHelper::getMainForm($this->item->jforms_snapshot);
		
		$page_title = $this->item->jforms_snapshot->name_ml;
		$this->description = $this->item->jforms_snapshot->description_ml;
		
		// Define the default title
		$this->params->def('title', $page_title);

		$this->_prepareDocument();

		$this->title = $page_title;
		/*
		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');
		if(empty($this->title)){
		}
		*/
		
		// get current jForm if exists
		if(!empty($this->item->jforms_snapshot->id)){
			$formModel	= CkJModel::getInstance('form', 'JformsModel');
			$this->currentjForm = $formModel->getItem($this->item->jforms_snapshot->id);
			
			// fallback if current form doesn't exists anymore
			if(empty($this->currentjForm)){
				$this->currentjForm = $this->item->jforms_snapshot;
			} else {
				$this->currentjForm = ByGiroHelper::getMlFields($this->currentjForm,$ml_fields['forms']);
			}
			
			if(!empty($this->currentjForm->layouts)){
				$this->currentjForm->layouts = ByGiroHelper::groupArrayByValue($this->currentjForm->layouts, 'type');
				
				foreach($this->currentjForm->layouts as $type => $lays){
					$this->currentjForm->layouts[$type] = ByGiroHelper::groupArrayByValue($lays, 'id',false);
				}
			}
		}		
	
		// conditions to access the submission	
		if(
			!(
			$pdfCreation // we are in PDF creation process
			OR
			$checkoutProcess == $item->id // we are in checkout process
			OR
			(!empty($this->currentjForm->options->passphrase) AND !empty($item->passphrase) AND $passphrase == $item->passphrase) // submission by passphrase?
			OR
			$model->canAccess($item) //Check ACL before opening the view (prevent from direct access)		
			)
		){
			$model->setError(JText::_('JERROR_ALERTNOAUTHOR'));
		}
	
		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			JError::raiseError(500, implode(BR, array_unique($errors)));
			return false;
		}

		
		// remove view authorization
		$session->set('jforms.printing.process',null);
		
		
		//Hide the component menu in item layout
		$jinput->set('hidemainmenu', true);

		//Toolbar initialization



		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');

		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');
	
		// default counting
		$this->tax = $item->jforms_snapshot->options->tax;
		$this->subTotal = $item->jforms_snapshot->options->price;
		if($item->jforms_snapshot->options->amount_type != 'fixed'){
			$calculation = JformsHelper::calculateSubTotal($item);
			$this->subTotal = $calculation->subTotal;
			$this->calcDetails = $calculation->details;
		}
		$this->currency = $config->get("currency", "USD");
				
		$paymentStatuses = array('na','canceled','failed','not_created');
		if($this->currentjForm->options->payment AND (empty($item->payment_status) OR in_array($item->payment_status, $paymentStatuses))){
			$this->checkoutProcess = $checkoutProcess = $item->id;
		} else {
			$this->checkoutProcess = $checkoutProcess = false;
			$session->set('jforms.checkout.process',false);			
			
			if(!empty($item->payment_details)){
				$payment_details = ByGiroHelper::objectToArray($item->payment_details,true);
				
				@$transactions = $payment_details['transactions'];
				$last_transaction = null;
				if(!empty($transactions)){
					@$last_transaction = array_pop(array_values($transactions));
					$data_sent = $last_transaction['data_sent'];
				}
				
				if(empty($data_sent)){
					$data_sent = $payment_details['data_to_send'];
				}
				
				if(!empty($data_sent)){
					$this->tax = $data_sent['order']['original_tax'];
					$this->subTotal = $data_sent['order']['original_subtotal'];
					$this->currency = $data_sent['order']['original_currency'];
				}
			}
		}

		if(!$checkoutProcess){
			return true;
		}
		
		// Check the payment plugin exists or return false
		$this->paymentPlugins = $plugins = JformsHelper::getPaymentPlugins();
		
		// default instruction for client
		$fields = array(
			'payment_instructions'
		);
		$this->currentjForm->options = ByGiroHelper::getMlFields($this->currentjForm->options,$fields);		
		@$this->paymentInstructions = $this->currentjForm->options->payment_instructions_ml;
		
		if(count($this->paymentPlugins) == 1){
			@$firstPayment = array_shift(array_values($this->paymentPlugins));
			$this->paymentType = $paymentType = $firstPayment->name;
		}
		
		if(empty($paymentType)){
			return true;
		}
		
		$found = false;
		if(!empty($plugins)){
			foreach($plugins as $plugin) {
				if($plugin->name == $paymentType) {
					$found = true;
					$validCurrency = false;
					if(in_array($plugin->currency, $plugin->currencies)){
						$validCurrency = $plugin->currency;
					}
					break;
				}
			}
		}
		
		if(!$found OR empty($validCurrency)){
			$this->paymentType = null;
			return true;
		}

		// create order data
		$subTotal = $this->subTotal;
		// convert currency using the google API, if it's not supported by the payment gateway
		if(!empty($this->subTotal) AND $this->currency != $validCurrency){
			$converter = new Converter;
			$subTotal = $converter->convert($this->subTotal, $this->currency, $validCurrency);
			$subTotal = round($subTotal,2);
			
			if(empty($subTotal)){
				$this->paymentType = null;
				return false;
			}
		}

		$order = array(
			'id' => $item->id,
			'title' => $this->title .' - '. JText::_( "JFORMS_SUBMISSION_ID" ) .': '. $item->id,
			'original_subtotal' => $this->subTotal,
			'original_tax_percentage' => $this->tax,
			'original_currency' => $this->currency,
			'subtotal' => $subTotal,
			'tax' => round($subTotal * $this->tax / 100, 2),
			'currency' => $validCurrency,
		);

		// create client data
		$clientFields = array(
			'name','lastname','address','zip','city','state',
			'country','phone_number','mobile_number','email'
		);

		$client = array();
		unset($key);		
		foreach($clientFields as $key){
			@$val = $item->jforms_snapshot->options->{'client_'. $key};
			if(!empty($val) AND !empty($item->form_data->$val)){
				$client[$key] = $item->form_data->$val;
			}
		}
		
		// call the payment plugin
		$this->paymentForm = '';
		$payment_params = array();
		$vendor = array();

		JPluginHelper::importPlugin( 'jformspayments' );		
		$version = new JVersion();
		// Joomla! 1.6 - 1.7 - 2.5
		if (version_compare($version->RELEASE, '2.5', '<=')){	
			$dispatcher = JDispatcher::getInstance();
		} else {
			$dispatcher = JEventDispatcher::getInstance();
		}
		$jResponse = $dispatcher->trigger('onNewPayment',array(
				$paymentType,
				$vendor,
				$order,
				$client,
				$payment_params
		));
		
		foreach($jResponse as $response) {
			if(empty($response))continue;

			$this->paymentForm = $response['form'];
			$data_to_send = $response['data_to_send'];
			break;
		}
				
		// integrate the original subtotal, tax, currency to keep track of the currency conversion
		$data_to_send['order']['original_subtotal'] 		= $this->subTotal;
		$data_to_send['order']['original_tax_percentage'] 	= $this->tax;
		$data_to_send['order']['original_currency'] 		= $this->currency;
		
		// save data sent for future processing and verification
		$payment_details = !empty($item->payment_details) ? (array)$item->payment_details : array();
		$payment_details['data_to_send'] = $data_to_send;
		
		$item->payment_details = $payment_details;
		$toStore = (array)ByGiroHelper::jsonFieldsToString($item);
		$table = $model->getTable();
		$table->bind($toStore);
		$table->store();		
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsViewSubmission')){ class JformsViewSubmission extends JformsCkViewSubmission{} }

