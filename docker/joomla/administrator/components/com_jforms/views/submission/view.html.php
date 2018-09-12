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
		if (!in_array($layout, array('submission', 'newsubmission')))
			JError::raiseError(0, $layout . ' : ' . JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'));

		$fct = "display" . ucfirst($layout);

		$this->addForkTemplatePath();
		$this->$fct($tpl);			
		$this->_parentDisplay($tpl);
	}

	/**
	* Execute and display a template : New submission
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displayNewsubmission($tpl = null)
	{
	
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$form_id = $jinput->get('frm', null, 'INT');

		$document	= JFactory::getDocument();

		// Initialiase variables.
		$this->model	= $model	= $this->getModel();
		$this->state	= $state	= $this->get('State');
		$this->params 	= $state->get('params');
		$state->set('context', 'submission.newsubmission');
		$this->item		= $item		= $this->get('Item');

		$isNew		= ($model->getId() == 0);
		if(!$isNew){
			$form_id = $this->item->form_id;
		}
		
		if(empty($form_id)){
			$state->set('jforms.form', null);

			$msg = JText::_("JFORMS_MISSING_FORM_ID");
			$app->redirect('index.php?option=com_jforms&view=submissions', $msg, $msgType='error');
			return false;
		}
		
		$state->set('jforms.form', $form_id);

		$this->form		= $form		= $this->get('Form');
		$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
		$lists = array();
		$this->lists = &$lists;

		$user		= JFactory::getUser();
		
		$text = JText::_("JFORMS_LAYOUT_NEW_SUBMISSION");
		if(!$isNew){
			$text = JText::_("JFORMS_LAYOUT_EDIT_SUBMISSION") .' ID:'. $this->item->id;
		} else {
			// check MAX submissions for this user
			if(!JformsHelper::checkMaxSubmissions($form->jForm->id, $form->jForm->acl)){
				$app->enqueueMessage(JText::_("JFORMS_YOU_REACHED_THE_MAX_NUMBER_OF_SUBMISSIONS"), 'error');
				return false;
			}
		}
		
		$this->title = $form->jForm->name_ml .' <span style="font-style: italic; font-size: 70%;">('. $text .')</span>' ;
		$document->title = $document->titlePrefix . $this->title . $document->titleSuffix;
		$this->description = $form->jForm->description_ml;
		
		$this->frm = $form_id;
		$this->submission_id = $this->item->id;

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
		JToolBarHelper::title($this->title, 'jforms_submissions');
		// Save
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::apply('submission.apply', "JFORMS_JTOOLBAR_SAVE");
		// Save & Close
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::save('submission.save', "JFORMS_JTOOLBAR_SAVE_CLOSE");
		// Save & New
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::save2new('submission.save2new', "JFORMS_JTOOLBAR_SAVE_NEW");
		// Save to Copy
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::save2copy('submission.save2copy', "JFORMS_JTOOLBAR_SAVE_TO_COPY");
		// Cancel
		CkJToolBarHelper::cancel('submission.cancel', "JFORMS_JTOOLBAR_CANCEL");
		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');

		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');

		$model_created_by = CkJModel::getInstance('ThirdUsers', 'JformsModel');
		$model_created_by->addGroupOrder("a.username");
		$lists['fk']['created_by'] = $model_created_by->getItems();

		//Status
		$lists['select']['status'] = new stdClass();
		$lists['select']['status']->list = $lists['enum']['submissions.status'];
		$lists['select']['status']->value = $item->status;

		//Payment status
		$lists['select']['payment_status'] = new stdClass();
		$lists['select']['payment_status']->list = $lists['enum']['submissions.payment_status'];
		$lists['select']['payment_status']->value = $item->payment_status;
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
		$config = JComponentHelper::getParams('com_jforms');
		
		$this->isPdf = $jinput->get('isPdf', false, 'CMD');
		$this->layout_pdf = $jinput->get('layout_pdf', '', 'STRING');
		
		// Initialiase variables.
		$this->model	= $model	= $this->getModel();
		$this->state	= $state	= $this->get('State');
		$this->params 	= $state->get('params');
		$state->set('context', 'submission.submission');
		$this->item		= $item		= $this->get('Item');
		$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
		$lists = array();
		$this->lists = &$lists;

		$user		= JFactory::getUser();
		$isNew		= ($model->getId() == 0);


		$session = JFactory::getSession();
		if($this->isPdf){		
			$pdfCreation = $session->get('jforms.printing.process',false);
		}
		
		
		//Check ACL before opening the view (prevent from direct access)
		if (empty($pdfCreation) AND !$model->canAccess($item))
			$model->setError(JText::_('JERROR_ALERTNOAUTHOR'));

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

		JToolBarHelper::title(JText::_('JFORMS_LAYOUT_SUBMISSION'), 'jforms_submissions');
		// Delete
		if (!$isNew && $item->params->get('access-delete'))
			JToolbar::getInstance('toolbar')->appendButton('Confirm', JText::_('JFORMS_JTOOLBAR_ARE_YOU_SURE_TO_DELETE'), 'delete', "JFORMS_JTOOLBAR_DELETE", 'submission.delete', false);

		if ($model->canEdit($item))
			CkJToolBarHelper::custom('submission.edit', 'edit', "JFORMS_JTOOLBAR_EDIT", "JFORMS_JTOOLBAR_EDIT", false);
			
		// Print pdf
		if (!$isNew){
			// Print client pdf
			CkJToolBarHelper::custom( 'submission.printpdf', 'print', 'JFORMS_JTOOLBAR_PRINT', 'JFORMS_JTOOLBAR_PRINT', false);
		}
		
		// Cancel
		CkJToolBarHelper::cancel('submission.cancel', "JFORMS_JTOOLBAR_CANCEL");
		$lists['enum']['submissions.status'] = JformsHelper::enumList('submissions', 'status');

		// get jform snapshot data
		$this->item->jforms_snapshot = JformsHelper::getjFieldsets($this->item->jforms_snapshot,false);
		$this->item->jforms_snapshot = JformsHelper::getjFormLanguageFiles($this->item->jforms_snapshot, false, true);
		$ml_fields = JformsHelper::getMultilangTables();
		$this->item->jforms_snapshot = ByGiroHelper::getMlFields($this->item->jforms_snapshot,$ml_fields['forms']);

		$this->item->jforms_snapshot = JformsHelper::getMainForm($this->item->jforms_snapshot);
		
		$document	= JFactory::getDocument();		
		$this->title = $this->item->jforms_snapshot->name_ml;
		$this->description = $this->item->jforms_snapshot->description_ml;

		$document->title = $document->titlePrefix . $this->title . $document->titleSuffix;

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

		// default counting
		$this->tax = $item->jforms_snapshot->options->tax;
		$this->subTotal = $item->jforms_snapshot->options->price;
		if($item->jforms_snapshot->options->amount_type != 'fixed'){
			$calculation = JformsHelper::calculateSubTotal($item);
			$this->subTotal = $calculation->subTotal;
			$this->calcDetails = $calculation->details;
		}
		$this->currency = $config->get("currency", "USD");		
		
		$lists['enum']['submissions.payment_status'] = JformsHelper::enumList('submissions', 'payment_status');


	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsViewSubmission')){ class JformsViewSubmission extends JformsCkViewSubmission{} }

