<?php
/**
 *
 * @package         jForms
 * @version         0.0.1
 *
 * @author          Girolamo Tomaselli
 * @email			girotomaselli@gmail.com
 * @link            http://bygiro.com
 * @copyright       Copyright © 2014 Girolamo Tomaselli All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

defined('DS') or define('DS',DIRECTORY_SEPARATOR);
defined('BR') or define("BR", "<br />");
defined('LN') or define("LN", "\n");

class plgSystemJforms extends JPlugin
{
	function __construct(&$subject, $config)
	{
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			return false;
		}
		
		$file = JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jforms'. DS . 'jforms.php';
		if(!file_exists($file)){
			return false;
		}		
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jforms'.DS.'helpers'.DS.'loader.php');
		
		$lang = JFactory::getLanguage();
		$lang->load('com_jforms', JPATH_SITE);		
		
		$this->_pass = 0;
		parent::__construct($subject, $config);
	}
	
	function onAfterRoute()
	{
		$this->_pass = 0;

		// load the admin language file
		$app = JFactory::getApplication();
		$base = JPATH_SITE;
		if ($app->isAdmin()){
			$base = JPATH_ADMINISTRATOR;
		}
			
		$lang = JFactory::getLanguage();
		if ($lang->getTag() != 'en-GB')
		{
			// Loads English language file as fallback (for missing lang string in other language file)
			$lang->load('plg_system_jforms', JPATH_ADMINISTRATOR, 'en-GB');
			$lang->load('com_jforms', $base, 'en-GB');
		}

		$lang->load('plg_system_jforms', JPATH_ADMINISTRATOR, null, 1);
		$lang->load('com_jforms', $base, null, 1);
		
		$this->option = JFactory::getApplication()->input->get('option');
		
		$params = $this->params;
		
		// jforms block
		$this->params->tag_open = 'jforms';
		$this->params->tag_close = '/jforms';
			
		$this->params->regex =
			'(\{' . $this->params->tag_open
			. '(?:\:([0-9]+)+)?'
			. '(.*?)?'
			. '\})'
			. '(.*?)?'
			. '\{' . $this->params->tag_close .'\}';						
			
		$this->params->regex_simple = 
			'\{' . $this->params->tag_open
			. '(?:\:([0-9]+)+)?'
			. '(.*?)?'
			. '\}';
			
		// Include the Helper
		require_once JPATH_PLUGINS . '/system/jforms/helper.php';
		$this->helper = new plgSystemJformsHelper($params);

		$this->_pass = 1;
	}

	function onContentPrepare($context, &$article)
	{
		if (!$this->_pass){
			return;
		}
		
		if (isset($article->text))
		{
			$this->helper->replace($article->text);
		}
		if (isset($article->description))
		{
			$this->helper->replace($article->description);
		}
		if (isset($article->title))
		{
			$this->helper->replace($article->title);
		}
		if (isset($article->author))
		{
			if (isset($article->author->name))
			{
				$this->helper->replace($article->author->name);
			}
			else if (is_string($article->author))
			{
				$this->helper->replace($article->author);
			}
		}
	}

	function onAfterDispatch()
	{
		if (!$this->_pass){
			return;
		}
		
		// FEED
		if ((JFactory::getDocument()->getType() == 'feed' || $this->option == 'com_acymailing') && isset(JFactory::getDocument()->items))
		{
			for ($i = 0; $i < count(JFactory::getDocument()->items); $i++)
			{
				$this->onContentPrepare(JFactory::getDocument()->items[$i]);
			}
		}

		// PDF
		if (JFactory::getDocument()->getType() == 'pdf')
		{
			$buffer = JFactory::getDocument()->getBuffer('component');
			if (is_array($buffer))
			{
				if (isset($buffer['component'], $buffer['component']['']))
				{
					if (isset($buffer['component']['']['component'], $buffer['component']['']['component']['']))
					{
						$this->helper->replace($buffer['component']['']['component'][''], 0);
					}
					else
					{
						$this->helper->replace($buffer['component'][''], 0);
					}
				}
				else if (isset($buffer['0'], $buffer['0']['component'], $buffer['0']['component']['']))
				{
					if (isset($buffer['0']['component']['']['component'], $buffer['0']['component']['']['component']['']))
					{
						$this->helper->replace($buffer['component']['']['component'][''], 0);
					}
					else
					{
						$this->helper->replace($buffer['0']['component'][''], 0);
					}
				}
			}
			else
			{
				$this->helper->replace($buffer);
			}
			JFactory::getDocument()->setBuffer($buffer, 'component');
			return;
		}

		$buffer = JFactory::getDocument()->getBuffer('component');
		if (!empty($buffer))
		{
			JFactory::getDocument()->setBuffer($buffer, 'component');
		}
	}
		
	function onAfterRender()
	{
		if (!$this->_pass){
			return;
		}
		
		// not in pdf's
		if (JFactory::getDocument()->getType() == 'pdf')
		{
			return;
		}

		$doc = JFactory::getDocument();
		// save current DOC head elements
		$savedDoc = clone($doc);
		
		$skipThis = array('template','baseurl','params','_file','language','direction','_charset','_mime');
		foreach($doc as $k => $ele){
			if(in_array($k,$skipThis)){
				continue;
			}
			
			if(is_array($doc->$k)){
				$doc->$k = array();
			} else if(is_object($doc->$k)){
				$doc->$k = new stdClass;
			} else if(is_string($doc->$k)){
				$doc->$k = '';
			} else {
				$doc->$k = null;
			}
			
		}
		
		$html = JResponse::getBody();	
		$this->helper->replace($html);

		
		// rebuild the head
		$renderer = new JDocumentRendererHead($doc);
		$head = $renderer->render(null);
		
		// remove title
		$regex = '#(<title[^>]*>)(.*?)(</title>)#is';
		$head = preg_replace($regex, '', $head);

		// merge new data - just in case some other plugins need to process the HEAD
		@$savedDoc->_script['text/javascript'] .= $doc->_script['text/javascript'];
		@$savedDoc->_style['text/css'] .= $doc->_style['text/css'];
		@$savedDoc->_scripts = array_merge($savedDoc->_scripts,$doc->_scripts);
		@$savedDoc->_styleSheets = array_merge($savedDoc->_styleSheets,$doc->_styleSheets);

		// restore the original $doc OBJ
		$doc = clone($savedDoc);
		$html = preg_replace('#</head>#i', $head . '</head>', $html, 1);

		JResponse::setBody($html);
	}
}
