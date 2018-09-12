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
* HTML View class for the Jforms component
*
* @package	Jforms
* @subpackage	Form
*/
class JformsCkViewForm extends JformsClassView
{
	protected $view = 'form';
	
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
		if (!in_array($layout, array('form')))
			JError::raiseError(0, $layout . ' : ' . JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'));

		$fct = "display" . ucfirst($layout);

		$this->addForkTemplatePath();
		$this->$fct($tpl);			
		$this->_parentDisplay($tpl);
	}

	/**
	* Execute and display a template : Form
	*
	* @access	protected
	* @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	*
	* @return	mixed	A string if successful, otherwise a JError object.
	*
	* @since	11.1
	*/
	protected function displayForm($tpl = null)
	{
		// Initialiase variables.
		$this->model	= $model	= $this->getModel();
		$this->state	= $state	= $this->get('State');
		$this->params 	= $state->get('params');
		$state->set('context', 'form.form');
		$this->item		= $item		= $this->get('Item');
		$this->form		= $form		= $this->get('Form');
		$this->canDo	= $canDo	= JformsHelper::getActions($model->getId());
		$lists = array();
		$this->lists = &$lists;

		// Define the default title
		$this->params->def('title', JText::_('JFORMS_LAYOUT_FORM'));

		$this->_prepareDocument();

		// Deprecated var : use $this->params->get('page_heading')
		$this->title = $this->params->get('page_heading');
		
		if(isset($this->item->fieldsets)){
			$this->item->fieldsets = ByGiroHelper::sort_on_field($this->item->fieldsets, 'ordering', 'ASC');
		}
		
		if(isset($this->item->emails)){
			$this->item->emails = ByGiroHelper::sort_on_field($this->item->emails, 'ordering', 'ASC');
		}
		
		if(isset($this->item->fields)){
			$this->item->fields = ByGiroHelper::sort_on_field($this->item->fields, 'ordering', 'ASC');
		}
		
		if(isset($this->item->events)){
			$this->item->events = ByGiroHelper::sort_on_field($this->item->events, 'ordering', 'ASC');
		}
		$this->user = $user		= JFactory::getUser();
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

	
		$config	= JComponentHelper::getParams( 'com_jforms' );
		$files_dir = $config->get('upload_dir_forms_fieldsets', JPATH_SITE_JFORMS .DS. 'files' .DS. 'forms_fieldsets');

		$fake_form = false;
		if(!$isNew){
			$formModel = CkJModel::getInstance('form', 'JformsModel');
			$jForm = $formModel->getItem($item->id);
			
			$jForm = JformsHelper::getjFieldsets($jForm);
			// load language files in JOOMLA
			$jForm = JformsHelper::getjFormLanguageFiles($jForm, true);
			$ml_fields = JformsHelper::getMultilangTables();
			$jForm = ByGiroHelper::getMlFields($jForm,$ml_fields['forms']);
		
			$jForm->fields = (array)$jForm->fields;
			$fset_fields = ByGiroHelper::groupArrayByValue($jForm->fields, 'fieldset');
			
			unset($fields);
			foreach($fset_fields as $fset_key => $fields){
				$fields = ByGiroHelper::groupArrayByValue($fields, 'enabled');
				
				// enabled fields
				if(!empty($fields[1])){
					$fields = ByGiroHelper::sort_on_field($fields[1], 'ordering', 'ASC');
				} else {
					$fields = array();
				}
				$fset_fields[$fset_key] = $fields;
			}
			
			$jFieldsets = !empty($jForm->fieldsets) ? $jForm->fieldsets : array();
			foreach($jFieldsets as $fset){
				if(($fset->enabled != 'true' AND $fset->enabled != 1) OR empty($fset->form)){
					continue;
				}
				
				// integrate forms
				if(!($fake_form instanceof JForm)){
					if(!empty($fset->form_file_content)){
						$fake_form = JForm::getInstance('com_jforms.main', $fset->form_file_content, array('control'=>'jform'));
						$fake_form->addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields');
						$fake_form->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules');
					}
				} else {
					// merge this form with the main form
					$fake_form->load($fset->form_file_content, true);
				}
			}
			
			// add the fields
			$jForm->fields = ByGiroHelper::groupArrayByValue($jForm->fields, 'id', false);
			$fitofo = new fitofo($fset_fields,$jForm);
			$fitofo->setMainForm($fake_form);
			$jForm = $fitofo->getForm();
			$fake_form = $fitofo->_mainForm;
		
			$this->item->jForm = $jForm;
		}

		$this->item->fake_form = $fake_form;
		
		
		
		//Toolbar initialization

		JToolBarHelper::title(JText::_('JFORMS_LAYOUT_FORM'), 'jforms_forms');
		// Save
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::apply('form.apply', "JFORMS_JTOOLBAR_SAVE");
		// Save & Close
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::save('form.save', "JFORMS_JTOOLBAR_SAVE_CLOSE");
		// Save & New
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::save2new('form.save2new', "JFORMS_JTOOLBAR_SAVE_NEW");
		// Save to Copy
		if (($isNew && $model->canCreate()) || (!$isNew && $item->params->get('access-edit')))
			CkJToolBarHelper::save2copy('form.save2copy', "JFORMS_JTOOLBAR_SAVE_TO_COPY");
		// Trash
		if (!$isNew && $model->canEditState($item) && ($item->published != -2))
			CkJToolBarHelper::trash('forms.trash', "JFORMS_JTOOLBAR_TRASH", false);
		// Archive
		if (!$isNew && $model->canEditState($item) && ($item->published != 2))
			CkJToolBarHelper::custom('forms.archive', 'archive', 'archive',  "JFORMS_JTOOLBAR_ARCHIVE", false);


		// Delete
		if (!$isNew && $item->params->get('access-delete'))
			JToolbar::getInstance('toolbar')->appendButton('Confirm', JText::_('JFORMS_JTOOLBAR_ARE_YOU_SURE_TO_DELETE'), 'delete', "JFORMS_JTOOLBAR_DELETE", 'form.delete', false);

		// Publish
		if (!$isNew && $model->canEditState($item) && ($item->published != 1))
			CkJToolBarHelper::publish('forms.publish', "JFORMS_JTOOLBAR_PUBLISH");
		// Unpublish
		if (!$isNew && $model->canEditState($item) && ($item->published != 0))
			CkJToolBarHelper::unpublish('forms.unpublish', "JFORMS_JTOOLBAR_UNPUBLISH");
		// Cancel
		CkJToolBarHelper::cancel('form.cancel', "JFORMS_JTOOLBAR_CANCEL");
		$lists['enum']['forms.layout_type'] = JformsHelper::enumList('forms', 'layout_type');

		//Layout type
		$lists['select']['layout_type'] = new stdClass();
		$lists['select']['layout_type']->list = $lists['enum']['forms.layout_type'];
		$lists['select']['layout_type']->value = $item->layout_type;
		
		
		// layouts select list
		$layoutsFields = array('pdf','pre_form','form','edit_form','fly');
		$layouts = ByGiroHelper::groupArrayByValue($item->layouts, 'type');

		unset($lFi);
		foreach($layoutsFields as $lFi){			
			$li = array();
			if(isset($layouts[$lFi])){
				foreach($layouts[$lFi] as $l){
					$li[$l->id] = array(
						'value' => $l->id,
						'text' => $l->name
					);
				}
			}			
			
			$fieldN = 'layout_'. $lFi;
			@$lFi_select = (object)array(
				'list' => $li,
				'value' =>  $item->options->$fieldN
			);
			
			$lists['select'][$fieldN] = $lFi_select;
		}
		
		// mirror select lists
		// get all forms (fieldsets,events,emails,layouts)
		$db = JFactory::getDBO();
		$query = "SELECT id,name,fieldsets,events,emails,layouts FROM #__jforms_forms WHERE 1 ORDER BY ordering";
		$db->setQuery($query);
		$allForms = $db->loadObjectList();
		
		$mirrorFields = array(
			'fieldsets' => array(),
			'events' => array(),
			'emails' => array(),
			'layouts' => array()
		);

		$langs = ByGiroHelper::getInstalledLanguages();
		$langs = ByGiroHelper::groupArrayByValue($langs, 'lang_code',false);

		foreach($allForms as $fo){
			$fo = ByGiroHelper::stringToJsonFields($fo);
	
			foreach($mirrorFields as $mFi => $values){
				$list = array();			
				
				if(empty($fo->$mFi)){
					continue;
				}
				
				$k = 0;
				foreach($fo->$mFi as $it){
					$k++;
					$eleValue = array(
						'mainId' => $fo->id,
						'subId' => $it->id,
					);
					$eleValue = json_encode($eleValue);
					
					switch($mFi){
						case 'emails':
							$title = $it->language;
							if(isset($langs[$it->language]) AND isset($langs[$it->language]->title)){
								$title = $langs[$it->language]->title;
							}
							
							$info = (!empty($it->language)) ? ' ('. $title .')' : '';
							$text = $k . ' - '. $fo->name . ': '. $it->subject . $info;
							break;
							
						case 'events':
							$info = (!empty($it->event)) ? ' ('. JText::_('JFORMS_FIELDS_'. strtoupper($it->event)) .')' : '';
							$text = $k . ' - '. $fo->name . ': '. $it->name . $info;
							break;
							
						default:
							$text = $k . ' - '. $fo->name . ': '. $it->name;
							break;
					}
					
					$list[] = (object)array(
						'value' => $eleValue,
						'text' => $text,
						'form_id' => $fo->id,
						'form_name' => $fo->name
					);					
				}
				
				$mirrorFields[$mFi] = array_merge($mirrorFields[$mFi],$list);
			}
		}
		
		unset($values);
		unset($mFi);
		foreach($mirrorFields as $mFi => $values){
			$lists['select'][$mFi]['mirror'] = $values;
		}

		
		// lists for fields and repeatable
		$fieldsets = array();
		if(!empty($this->item->fieldsets)){
			foreach($this->item->fieldsets as $it_fset){
				if(empty($it_fset->id)) continue;
				$fieldsets[] = array(
					'value' => $it_fset->id,
					'text' => $it_fset->name
				);
			}
		}
		$lists['select']['fields']['fieldset'] = $fieldsets;
		
		
		$skipTypes = array('ckfile','ckcalendar','ckmedia','ckrepeatable');
		$fields = array();
		$condFields = array();
		if(!empty($this->item->fields)){
			foreach($this->item->fields as $it_fi){
				if(empty($it_fi->id)) continue;
				
				if($it_fi->type == 'ckrepeatable') continue;
				$condFields[] = array(
					'value' => $it_fi->id,
					'text' => '#jform_'. $it_fi->name
				);
				
				if(in_array($it_fi->type,$skipTypes)) continue;
				$fields[] = array(
					'value' => $it_fi->id,
					'text' => $it_fi->name
				);
				
			}
		}

		$lists['select']['fields']['repeatable_fields'] = $fields;
		$lists['select']['fields']['condRules'] = array();
		$lists['select']['fields']['condRules']['trigger'] = $condFields;
		
		$lists['select']['fields']['integrations'] = array();
		
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsViewForm')){ class JformsViewForm extends JformsCkViewForm{} }

