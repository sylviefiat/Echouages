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
* Jforms Submission Controller
*
* @package	Jforms
* @subpackage	Submission
*/
class JformsCkControllerSubmission extends JformsClassControllerItem
{
	/**
	* The context for storing internal data, e.g. record.
	*
	* @var string
	*/
	protected $context = 'submission';

	/**
	* The URL view item variable.
	*
	* @var string
	*/
	protected $view_item = 'submission';

	/**
	* The URL view list variable.
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
		parent::__construct($config);
		$app = JFactory::getApplication();

	}

	/**
	* Override method when the author allowed to delete own.
	*
	* @access	protected
	* @param	array	$data	An array of input data.
	* @param	string	$key	The name of the key for the primary key; default is id..
	*
	* @return	boolean	True on success
	*/
	protected function allowDelete($data = array(), $key = id)
	{
		return parent::allowDelete($data, $key, array(
		'key_author' => 'created_by'
		));
	}

	/**
	* Override method when the author allowed to edit own.
	*
	* @access	protected
	* @param	array	$data	An array of input data.
	* @param	string	$key	The name of the key for the primary key; default is id..
	*
	* @return	boolean	True on success
	*/
	protected function allowEdit($data = array(), $key = id)
	{
		return parent::allowEdit($data, $key, array(
		'key_author' => 'created_by'
		));
	}

	/**
	* Method to cancel an element.
	*
	* @access	public
	* @return	void
	*/
	public function cancel()
	{
		$this->_result = $result = parent::cancel();
		$model = $this->getModel();

		//Define the redirections
		switch($this->getLayout() .'.'. $this->getTask())
		{
			case 'submission.cancel':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submissions.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'editsubmission.cancel':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submissions.default'
				), array(
					'cid[]' => null
				));
				break;

			default:
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submissions.default'
				));
				break;
		}
	}

	/**
	* Method to download an element.
	*
	* @access	public
	* @return	void
	*/
	public function download()
	{

	}

	/**
	* Return the current layout.
	*
	* @access	protected
	* @param	bool	$default	If true, return the default layout.
	*
	* @return	string	Requested layout or default layout
	*/
	protected function getLayout($default = null)
	{
		if ($default === 'edit')
			return 'submission';

		if ($default)
			return 'submissiondetails';

		$jinput = JFactory::getApplication()->input;
		return $jinput->get('layout', 'submissiondetails', 'CMD');
	}

	/**
	* Function that allows child controller access to model data after the data
	* has been saved.
	*
	* @access	protected
	* @param	JModel	&$model	The data model object.
	* @param	array	$validData	The validated data.
	* @return	void
	*/
	protected function postSaveHook(&$model, $data = array(), $form = null, $validData = array(), $recordId = null)
	{
		$this->model = $model;
		$config = JComponentHelper::getParams('com_jforms');

		$savedItem = (array)$model->getItem();
		
		// set last item edited
		$sessionVar = $model->get('sessionLastItemVar');
		$session = JFactory::getSession();
		$session->set($sessionVar,$savedItem['id']);
			
		
		$isNew = (empty($recordId));

		$validData = $model->validate($form, $data, null, $validData, true);
	
		unset($validData['id']);
		$savedItem['form_data'] = $validData;
		
		$jForm = $savedItem['jforms_snapshot'];
	
		if($isNew){
			$savedItem['jforms_snapshot'] = JformsHelper::getjFieldsets($savedItem['jforms_snapshot'],false);			
			$savedItem['jforms_snapshot'] = JformsHelper::getMainForm($savedItem['jforms_snapshot'],false);
		
			$jForm = $savedItem['jforms_snapshot'];
			
			$savedItem['payment_status'] = 'na';
			if(!empty($jForms->options->payment)){
				$savedItem['payment_status'] = 'not_created';
			}
			
			$defaultSubmissionStatus = $config->get("default_new_submission_status", 'confirmed');
			$savedItem['status'] = $defaultSubmissionStatus;
		}
	
		if($jForm->generate_pdf){
			// remove old PDF if ANY
			if(!empty($savedItem['pdf'])){
				if($savedItem['pdf'][0] != '['){
					$savedItem['pdf'] = '[DIR_SUBMISSIONS_PDF]/'. $savedItem['pdf'];
				}
				$oldPdf = JPath::clean(JPATH_SITE . DS . JformsHelper::getDirectory($savedItem['pdf']));
				unlink($oldPdf);
			}

			$savedItem['pdf'] = $model->printpdf($savedItem);
		}
				
		$item2save = ByGiroHelper::jsonFieldsToString($savedItem);

		$row = $model->getTable('submission');
		$row->bind($item2save);		
		if($row->store()){
			// force to reload item WE NEED IT
			$model->_item[$row->id] = null;
				
			// send emails
			$user = JFactory::getUser($savedItem['created_by']);
			$savedItem['user'] = $user;

			$eventType = 'on_after_edit';
			if($isNew){
				$eventType = 'on_after_save';
			}
			
			if(!$jForm->save_data_in_db){
				$dispatcher = JEventDispatcher::getInstance();
				$dispatcher->trigger('onContentAfterSave', array('com_jforms.submission', $row, $isNew));
			}
			
			JformsHelper::triggerEvents($eventType,$savedItem);
			$jForm = $savedItem['jforms_snapshot'];
			
			if(!empty($jForm->message_after_submit_ml)){
				$this->setMessage($jForm->message_after_submit_ml, 'notice');
			}

			$model->processEmails($eventType,$savedItem);
		}
		
		$this->model = $model;
	}

	/**
	* Method to save an element.
	*
	* @access	public
	* @return	void
	*/
	public function save()
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		//Check the ACLs
		$model = $this->getModel();
		$item = $model->getItem();
		$isNew = !empty($item->id); 
		
		$result = false;
		if ($model->canEdit($item, true))
		{
			$result = parent::save();
			//Get the model through postSaveHook()
			if ($this->model)
			{
				$model = $this->model;
				$item = $model->getItem();	
			}
		}
		else
			JError::raiseWarning( 403, JText::sprintf('ACL_UNAUTORIZED_TASK', JText::_('JFORMS_JTOOLBAR_SAVE')) );

		$this->_result = $result;
		
		$app = JFactory::getApplication();
		
		$jForm = $item->jforms_snapshot;
		
		$form_id = $jForm->id;
		if(empty($form_id)){
			$jinput = $app->input;
			$form_id = $jinput->get('frm', null, 'INT');
		}
		
		$data = array(
					'cid[]' => null,
					'frm' => $form_id
				);
		switch($jForm->options->redirect){
			case 'submissions':
				$redirectTo = 'com_jforms.submissions.default';
				break;
				
			case 'submitted_data':
				$redirectTo = 'com_jforms.submission.submissiondetails';
				$data = array(
					'cid[]' => $model->getState('submission.id')
				);
				break;
				
			case 'form':
				// return to previous page
				$redirectTo = 'stay';
				if(!empty($item->page_url)){
					$redirectTo = $item->page_url;
				}
				break;
			
			case 'custom':
				$redirectTo = 'index.php';
				if(!empty($jForm->redirect_after_submit)){
					$redirectTo = $jForm->redirect_after_submit;
				}
				break;
				
			default:
				$redirectTo = 'index.php';
				if(!isset($jForm->options->redirect)){
					if(!empty($jForm->redirect_after_submit)){
						$redirectTo = $jForm->redirect_after_submit;
					}
				}
				break;
		}	

		$paymentStatuses = array('na','canceled','failed','not_created');
		if($jForm->options->payment AND (empty($item->payment_status) OR in_array($item->payment_status, $paymentStatuses))){
			$redirectTo = 'com_jforms.submission.submissiondetails';
			$data['cid[]'] = $model->getState('submission.id');
			
			// set access to payment page
			$session = JFactory::getSession();
			$session->set('jforms.checkout.process',$data['cid[]']);
		}
	
		//Define the redirections
		switch($this->getLayout() .'.'. $this->getTask())
		{
			case 'submission.save':
				$this->applyRedirection($result, array(
					'stay',
					$redirectTo
				), $data);
				break;

			case 'editsubmission.apply':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.editsubmission'
				), array(
					'cid[]' => $model->getState('submission.id'),
					'frm' => $form_id
				));
				break;

			case 'editsubmission.save':
				$this->applyRedirection($result, array(
					'stay',
					$redirectTo
				), $data);
				break;

			default:
				$this->applyRedirection($result, array(
					'stay',
					$redirectTo
				), $data);
				break;
		}
	}

	/**
	* Method to save an element.
	*
	* @access	public
	* @return	void
	*/
	public function forward()
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		
		$app = JFactory::getApplication();
		
		//Check the ACLs
		$model = $this->getModel();
		$item = $model->getItem();
		$result = false;
		if ($model->canEdit($item, true))
		{
			$jinput = $app->input;
			$allData = $jinput->getArray(array());
			
			if(!empty($allData['jform'])){			
				$app->setUserState('com_jforms.edit.submission.data', $allData['jform']);
			}
			
			$vars = array(
				'task'=>'',
				'frm'=>$jinput->get('frm',null,'INT')
			);
			
			$url = ByGiroHelper::buildRoute($vars);
			$app->redirect(JRoute::_($url, false));
		}
		else
			JError::raiseWarning( 403, JText::sprintf('ACL_UNAUTORIZED_TASK', JText::_('JFORMS_JTOOLBAR_SAVE')) );

	}
	
	public function printpdf($layout = null)
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$result = false;
		
		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		$layout = $jinput->get('layout',null,'CMD');
		$view = $jinput->get('view','submissions','CMD');
		
		if($layout === null){
			if($view == 'submissions'){
				$layout = 'default';
			} else {
				$layout = 'submissiondetails';
			}
		}
		// Get the model.
		$model = $this->getModel();

		$savedItem = (array)$model->getItem();
		
		if(empty($savedItem['id'])){
			return false;
		}
		$jForm = $savedItem['jforms_snapshot'];		
		
		if($jForm->generate_pdf){
			// remove old PDF if ANY
			if(!empty($savedItem['pdf'])){
				if($savedItem['pdf'][0] != '['){
					$savedItem['pdf'] = '[DIR_SUBMISSIONS_PDF]/'. $savedItem['pdf'];
				}
				$oldPdf = JPath::clean(JPATH_SITE . DS . JformsHelper::getDirectory($savedItem['pdf']));
				unlink($oldPdf);
			}
			
			$savedItem['pdf'] = $model->printpdf($savedItem);
		}

		$savedItem = ByGiroHelper::jsonFieldsToString($savedItem);

		$row = $model->getTable('submission');
		$row->bind($savedItem);		
		if($row->store()){
			$this->_result = $result = true;
		}

		$vars = array(
			'option'=> 'com_jforms',
			'view'=> $view,
			'layout'=> $layout,
			'task'=>'',
			'cid[0]'=> $savedItem['id'],
		);
		
		$url = ByGiroHelper::buildRoute($vars);
		$app->redirect(JRoute::_($url, false));

		return $result;
	}

}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsControllerSubmission')){ class JformsControllerSubmission extends JformsCkControllerSubmission{} }

