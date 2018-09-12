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
	* Method to add an element.
	*
	* @access	public
	* @return	void
	*/
	public function add()
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$this->_result = $result = parent::add();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$jinput = $app->input;
		
		//Define the redirections
		switch($this->getLayout() .'.'. $this->getTask())
		{
			case 'default.add':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
					'frm' => $jinput->get('frm', null, 'INT')
				));
				break;

			case 'modal.add':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
					'frm' => $jinput->get('frm', null, 'INT')
				));
				break;

			default:
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
					'frm' => $jinput->get('frm', null, 'INT')
				));
				break;
		}
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

			case 'newsubmission.cancel':
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
	* Method to delete an element.
	*
	* @access	public
	* @return	void
	*/
	public function delete()
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$this->_result = $result = parent::delete();
		$model = $this->getModel();

		//Define the redirections
		switch($this->getLayout() .'.'. $this->getTask())
		{
			case 'submission.delete':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submissions.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.delete':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submissions.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.delete':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submissions.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'modal.delete':
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
	* Method to edit an element.
	*
	* @access	public
	* @return	void
	*/
	public function edit()
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$this->_result = $result = parent::edit();
		$model = $this->getModel();

		//Define the redirections
		switch($this->getLayout() .'.'. $this->getTask())
		{
			case 'default.edit':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
			
				));
				break;

			case 'default.edit':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
			
				));
				break;

			case 'modal.edit':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
			
				));
				break;

			default:
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				));
				break;
		}
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
			return 'newsubmission';

		if ($default)
			return 'newsubmission';

		$jinput = JFactory::getApplication()->input;
		return $jinput->get('layout', 'newsubmission', 'CMD');
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
		
		$savedItem = (array)$model->getItem();
		$isNew = (empty($recordId));

		$validData = $model->validate($form, $data, null, $validData, true);
	
		unset($validData['id']);
		$savedItem['form_data'] = $validData;
		
		$jForm = $savedItem['jforms_snapshot'];
	
		if($isNew){
			$savedItem['jforms_snapshot'] = JformsHelper::getjFieldsets($savedItem['jforms_snapshot'],false);
			$savedItem['jforms_snapshot'] = JformsHelper::getMainForm($savedItem['jforms_snapshot'],false);
		
			$jForm = $savedItem['jforms_snapshot'];
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
		
		$jform = $item->jforms_snapshot;
		
		$form_id = $jform->id;
		
		if(empty($form_id)){
			$jinput = $app->input;
			$form_id = $jinput->get('frm', null, 'INT');
		}

		//Define the redirections
		switch($this->getLayout() .'.'. $this->getTask())
		{
			case 'newsubmission.apply':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
					'cid[]' => $model->getState('submission.id'),
					'frm' => $form_id
				));
				break;

			case 'newsubmission.save':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submissions.default'
				), array(
					'cid[]' => null,
					'frm' => $form_id
				));
				break;

			case 'newsubmission.save2new':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
					'cid[]' => null,
					'frm' => $form_id
				));
				break;

			case 'newsubmission.save2copy':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.submission.newsubmission'
				), array(
					'cid[]' => $model->getState('submission.id'),
					'frm' => $form_id
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


	public function printpdf($layout = null)
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$result = false;

		// Get the model.
		$model = $this->getModel();
		
		$this->_result = $result = $model->printpdf();

		//Redirect to list
		$this->applyRedirection(1, array(1 => 'stay'));

		return $result;
	}

	public function deletepdf($layout = null)
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$result = false;

		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', array(), 'ARRAY');
		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->deletepdf($cid))
			{
				$result = true;
				$this->setMessage(JText::plural('JFORMS_ITEMS_PDF_SUCCESSFULLY_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->_result = $result;
		//Redirect to list
		$this->applyRedirection(1, array(1 => 'stay'));

		return $result;
	}

}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsControllerSubmission')){ class JformsControllerSubmission extends JformsCkControllerSubmission{} }

