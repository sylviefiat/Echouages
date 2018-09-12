<?php
/**
 *
 * @package         jForms
 * @version         0.0.4
 *
 * @author          Girolamo Tomaselli
 * @email			girotomaselli@gmail.com
 * @link            http://bygiro.com
 * @copyright       Copyright © 2014 Girolamo Tomaselli All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class plgSystemJformsHelper
{	
	function __construct(&$params)
	{		
		$this->params = $params;

	}

	function replace(&$str)
	{
		if (empty($str))
		{
			return;
		}
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->replace($val);
			}
		}
		else
		{	
			$this->replaceVariables($str);
		}
	}
	
	function replaceVariables(&$str)
	{		
		if (strpos($str, '{' . $this->params->tag_open) === false){
			return $str;
		}

		JformsHelper::protect($str);

		$matches = JformsHelper::getJformsTags($str,'#'. $this->params->regex .'#is');
		foreach($matches as $ma){		
			// check nested jforms tags
			while(strpos($ma[4],'{' . $this->params->tag_open .':') !== false){
				// remove the first tag found
				$tempStr = str_replace($ma[1],'',$ma[0]);
				$tempMatches = JformsHelper::getJformsTags($tempStr,'#'. $this->params->regex .'#is');
				
				if(empty($tempMatches)){
					break;	
				}
				
				$ma = $tempMatches[0];
			}

			$match = $ma[0];
			$headTag = $ma[1];
			$id = $ma[2];
			$options = $ma[3];
			$content = $ma[4];

			$output = $this->renderForm($id,$options,$content);		
			$str = str_replace($match,$output,$str);
		}
		
		// manage simple tags
		$matches = JformsHelper::getJformsTags($str,'#'. $this->params->regex_simple  .'#is');		
		unset($ma);
		foreach ($matches as $ma){
			$match = $ma[0];
			$id = $ma[1];
			@$options = $ma[2];

			$output = $this->renderForm($id,$options);
			$str = str_replace($match,$output,$str);
		}		
		
		JformsHelper::unprotect($str);
		return;		
	}
	
	function getForm($formId,$overrides = array()){
		static $forms;
		static $forms_loaded;

		natsort($overrides);
		
		$hash = MD5($formId . serialize($overrides));
		if(!isset($forms)){
			$forms = array();
		}
		
		if(!isset($forms_loaded)){
			$forms_loaded = array();
		}
		
		if(isset($forms_loaded[$hash])){
			return $forms_loaded[$hash];
		}
		
		if(isset($forms[$formId])){
			$jForm = $forms[$formId];
		} else {		
			// load the jForm item
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jforms'.DS.'tables');
			$row = JTable::getInstance('form', 'JformsTable');
			$searchFor = array('id'=>$formId);
			$row->load($searchFor);

			$jForm = ByGiroHelper::stringToJsonFields((object)$row->getProperties());
			$forms[$formId] = $jForm;
		}
		
		if(empty($jForm->id)){
			$forms_loaded[$hash] = null;
			return null;
		}
		
		$jForm = (object)array_merge((array)$jForm,(array)$overrides);
		
		$form = new JForm('jForms.submission',array('control'=>'jform'));
		// Get the form.		
		JForm::addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields');
		JForm::addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules');
		
		$jForm = JformsHelper::getjFieldsets($jForm);
		
		// load language files in JOOMLA
		$jForm = JformsHelper::getjFormLanguageFiles($jForm, true, true);
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
			$jForm->fields[$fset_key] = $fields;
		}		
		
		$jFieldsets = (!empty($jForm->fieldsets)) ? $jForm->fieldsets : array();
		foreach($jForm->fieldsets as $fset){
			if(($fset->enabled != 'true' AND $fset->enabled != 1) OR empty($fset->form)){
				continue;
			}
			
			// integrate forms
			if(!empty($fset->form_file_content)){
				if(!($form instanceof JForm)){
						$form = JForm::getInstance('com_jforms.'. $hash, $fset->form_file_content, array('control'=>'jform'));
						$form->addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields');
						$form->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules');
				} else {
					// merge this form with the main form					
					$form->load($fset->form_file_content, true);
				}
			}
		}
		
		// add the fields
		$jForm->fields = ByGiroHelper::groupArrayByValue($jForm->fields, 'id', false);
		$fitofo = new fitofo($fset_fields,$jForm);
		$fitofo->setMainForm($form);
		$jForm = $fitofo->getForm();
		$form = $fitofo->_mainForm;		
		
		$form->jForm = $jForm;
		
		$forms_loaded[$hash] = $form;	
		return $form;
	}
	
	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 *
	 * @return  string  The output of the the template script.
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function loadTemplate($tpl = null,$layout = 'submission',$view = 'submission', $layoutTemplate = '_')
	{
		$_output = '';
		
		$template = JFactory::getApplication()->getTemplate();
		$_path_template = array();
		$_path_template[] = JPATH_SITE .'/components/com_jforms/views/' . $view . '/tmpl/';
		$_path_template[] = JPATH_THEMES .'/'. $template . '/html/com_jforms/' . $view . '/';
		
		// Clean the file name
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		
		// Load the language file for the template
		$lang = JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);

		// Change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template){
			$_path_template = str_replace($template, $layoutTemplate, $_path_template);
		}

		// Load the template script
		jimport('joomla.filesystem.path');
		$_template = JPath::find($_path_template, $file .'.php');
		
		// If alternate layout can't be found, fall back to default layout
		if ($_template == false){
			$file = 'default' . (isset($tpl) ? '_' . $tpl : $tpl);
			$_template = JPath::find($_path_template, $file .'.php');			
		}

		if ($_template != false){
			// Unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Start capturing output into a buffer
			ob_start();

			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $_template;

			// Done with the requested template; get the buffer and
			// clear it.
			$_output = ob_get_contents();
			ob_end_clean();
		}
		
		return $_output;
	}
	
	function loadHeader(){
		static $addHeaderDeclarations;
		// add header declaration just ONCE
		if(isset($addHeaderDeclarations)){
			return;
		}
		
		JformsHelper::headerDeclarations();
		JHtml::addIncludePath(JPATH_ADMIN_JFORMS.'/helpers/html');
		//Load the formvalidator scripts requirements.
		JHtml::_('behavior.framework');
		JDom::_('html.toolbar');
		JHtml::_('behavior.tooltip');
		
		$addHeaderDeclarations = true;
	}
	
	function renderForm($jfid,$jfopts = '',$content = null){		
		// if missing form id, remove the block
		if(empty($jfid)){
			return '';
		}
		
		$overrides = JformsHelper::parseOptions($jfopts);
		
		// get formData
		$form = $this->getForm($jfid,$overrides);	
		// check we can access to this form
		if(empty($form) OR class_exists('ByGiroHelper') AND !ByGiroHelper::canAccess($form->jForm->access)){
			return '';
		}
		
		self::loadHeader();
		
		// check we have a custom layout to render
		$this->layout = null;
		if(!empty($content)){
			$this->layout = $content;
		}
	
		// prepare data for rendering
		@$form->preform = $form->jForm->preform;
		$this->item = new stdClass;
		@$this->title = $form->jForm->name_ml;
		@$this->description = $form->jForm->description_ml;
		$this->form = $form;
		$this->submission_id = 0;
		$this->frm = $jfid;
		
		// render custom layout output
		$output = $this->loadTemplate();
		JformsHelper::protect($output);
		return $output;
	}
}
