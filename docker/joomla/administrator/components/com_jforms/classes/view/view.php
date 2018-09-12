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

jimport('joomla.application.component.view');


/**
* HTML View class for the Jforms component
*
* @package	Jforms
* @subpackage	Class
*/
class JformsCkClassView extends CkJView
{
	/**
	* Call the parent display function. Trick for forking overrides.
	*
	* @access	protected
	* @param	string	$tpl	Template.
	* @return	void
	*
	* @since	Cook 2.0
	*/
	protected function _parentDisplay($tpl)
	{
	
		JformsHelper::headerDeclarations();
		JHtml::addIncludePath(JPATH_ADMIN_JFORMS.'/helpers/html');
		//Load the formvalidator scripts requirements.
		JHtml::_('behavior.framework');
		JDom::_('html.toolbar');
		JHtml::_('behavior.tooltip');
	
	
		parent::display($tpl);
	}

	/**
	* Prepares the document.
	*
	* @access	protected
	* @return	void
	*
	* @since	Cook 2.6.5
	*/
	protected function _prepareDocument()
	{
		$this->document = $document	= JFactory::getDocument();
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu()->getActive();


		// Define the default page heading. Uses the site name as default
		$pageHeading = $this->params->get('title', $app->getCfg('sitename'));
		$showHeading = $this->params->get('show_page_heading', 1);

		// Encapsulate the default title in the component prefix / suffix
		$pageTitle = $document->titlePrefix . $pageHeading . $document->titleSuffix;


		// Can reuse the item title to use in the page title
		if ($itemTitle = $this->params->get('item_title'))
			$pageTitle .= ' - ' . $itemTitle;

		// Override the document using the menu configuration
		if ($menu)
		{
			$pageHeading = $menu->params->get('page_heading', $menu->title);
			$pageTitle = $menu->params->get('page_title', $menu->title);
			$showHeading = $menu->params->get('show_page_heading', 1);
		}

		// Set the heading in params. So the template can use it.
		$this->params->def('page_heading', $pageHeading);
		$this->params->def('show_page_heading', $showHeading);


		// Set the page title
		$this->document->setTitle($pageTitle);
	}

	/**
	* Manage a template override in the fork directory
	*
	* @access	protected
	*
	* @return	void	
	* @return	void
	*
	* @since	Cook 2.0
	*/
	protected function addForkTemplatePath()
	{
		$this->addTemplatePath(JPATH_COMPONENT .DS. 'fork' .DS. 'views' .DS. $this->getName() .DS. 'tmpl');
	}

	/**
	* Convert a custom table to a JSON object string.
	*
	* @access	public static
	* @param	array	$headers	Defines the fields to include in the SQL select query.
	* @return	void
	*
	* @since	Cook 2.6.3
	*/
	public static function jsonList($headers = array())
	{
		// Get the datas
		$jinput = JFactory::getApplication()->input;

		$view = $jinput->get('view');
		$states = $jinput->get('__states', array(), 'array');

		$model = CkJModel::getInstance($view, 'JformsModel');

		$model->setState('context', '');

		if (count($states))
			foreach($states as $var => $value)
				$model->setState($var, $value);

		// Apply the headers
		if (count($headers))
			$model->prepareQueryHeaders($headers);

		$data = $model->getItems();

		$ajax = new JformsClassAjax();		
		$ajax->responseJson(array(
			'data' => $data,
			'headers' => $headers,
			'renderExceptions' => 'html',
		));
	}

	/**
	* Renders the fieldset form.
	*
	* @access	public
	* @param	array	$fieldset	Fielset. array of fields.
	*
	* @return	string	Rendered fields.
	*
	* @since	Cook 2.6.1
	*/
	public function renderFieldset($fieldset, $form = null) 
	{
		$html = '';

		// Iterate through the fields and display them.
		foreach($fieldset as $field)
		{
			//Check ACL
		    if ((method_exists($field, 'canView')) && !$field->canView())
		    	continue;
	
			$classes = '';
			$canView = '';
			$canEdit = '';
			if(($form instanceof JForm)){
				$classes = $form->getFieldAttribute($field->fieldname,'class',null,$field->group);
				$canView = $form->getFieldAttribute($field->fieldname,'canView',null,$field->group);
				$canEdit = $form->getFieldAttribute($field->fieldname,'canEdit',null,$field->group);
			}

			// check ACL
			if($canView){
				$canView = JformsHelper::canAccess($canView);
			} else {
				$canView = true;
			}
			
			if($canEdit){
				$canEdit = JformsHelper::canAccess($canEdit);
			} else {
				$canEdit = true;
			}

			if(!$canView){
				continue;
			}
			
			if(!$canEdit){
				try{
					$field->disabled = 1;
				} catch (Exception $e){
					$error = $e->getMessage();
				}
				
				$selectors = $field->jdomOptions['selectors'];
				if(is_array($selectors)){
					$selectors['disabled'] = 'disabled';
				} else {
					$selectors .= ' disabled="disabled"';
				}
				
				$field->jdomOptions = array_merge($field->jdomOptions, array(
					'selectors' => $selectors
						));
			}

			$hidden = (empty($field->hidden)?$field->hidden:null);
			$id = (empty($field->id)?$field->id:null);
			$responsive = (empty($field->responsive)?$field->responsive:null);
			$type = (empty($field->type)?$field->type:null);
			$label = (empty($field->label)?$field->label:null);
			$input = (empty($field->input)?$field->input:null);
		
			if ($hidden)
			{
				$html .= $field->input;
				continue;
			}
			
			if ($field->type == 'ckspacer' OR $field->type == 'spacer')
			{
				
				$html .= '<div class="control-group field-' . $field->id . $field->responsive . ' '. $classes .'">';
				$html .= $field->label;
				$html .= '</div>';
				
				continue;
			}
			$selectors = (($type == 'Editor' || $type == 'Textarea') ? ' style="clear: both; margin: 0;"' : '');

			$html .= '<div class="control-group field-' . $id . $responsive . '">';

			$html .= '<div class="control-label">' 
					. $label
					. '</div>';

			$html .= '<div class="controls"' . $selectors . '>'
					. $field->input
					. '</div>';

			$html .= '</div>';
		}
		return $html;
	}

	/**
	* Renders the error stack and returns the results as a string
	*
	* @access	public
	* @param	string	$format	Possible output formats : HTML, TEXT, null (return array).
	*
	* @return	mixed	Rendered messages. Or array if format is null.
	*
	* @since	Cook 2.0
	*/
	public function renderMessages($format = 'HTML')
	{
		jimport('joomla.version');
		$version = new JVersion();


		// Deprecated (JDocumentRendererMessage)
		if ((version_compare($version->RELEASE, '3.0', '<')) && ($format == 'HTML'))
		{
			$document = JFactory::getDocument();
			$renderer = $document->loadRenderer('message');
			return $renderer->render(null, array(), '');
		}

		// Initialize the variables
		$msgList = array();
		$rawMessages = array();

		$app = JFactory::getApplication();
		
		// Get the message queue
		$messages = $app->getMessageQueue();

		// Build the sorted message list
		if (is_array($messages) && !empty($messages))
		{
			foreach ($messages as $msg)
			{
				if (isset($msg['type']) && isset($msg['message']))
				{
					$msgList[$msg['type']][] = $msg['message'];

					//Prepare raw
					if ($format == 'TEXT')
						$rawMessages[] = strtoupper($msg['type']) . ': ' . $msg['message'];
				}
			}
		}

		// Return the sorted array
		if ($format == null)
			return $msgList;

		// When stack list is empty, does not return anything
		if (!count($msgList))
			return '';


		// Use a layout
		if ($format == 'HTML')
		{

			return JLayoutHelper::render('joomla.system.message', array(
				'msgList' => $msgList,
				'name' => null,
				'params' => array(),
				'content' => null
			));
		}


		// Output the messages in a raw text format (for alert boxes)
		if ($format == 'TEXT')
			return implode("\n", $rawMessages );
	}

	/**
	* Renders the toolbar.
	*
	* @access	public
	* @param	array	$items	List of items. Used in few cases
	*
	* @return	string	Rendered toolbar.
	*
	* @since	Cook 2.6.2
	*/
	public function renderToolbar($items = null)
	{
		$render = true;

		$app = JFactory::getApplication();
		if ($app->isAdmin())
		{
			//Toolbar is handled by the administrator template
			$render = false;
	
			//Need to render it in case of modal view, or template less
			if ($app->input->get('tmpl') == 'component')
				$render = true;
		}

		if (!$render)
			return '';

		$html = JDom::_('html.toolbar', array(
			"bar" => JToolBar::getInstance('toolbar'),
			'list' => $items
		));

		return $html;
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsClassView')){ class JformsClassView extends JformsCkClassView{} }

