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
defined( '_JEXEC' ) or die( 'Restricted access' );

// Some usefull constants
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
if(!defined('BR')) define("BR", "<br />");
if(!defined('LN')) define("LN", "\n");

//Joomla 1.6 only
if (!defined('JPATH_PLATFORM')) define('JPATH_PLATFORM', JPATH_SITE .DS. 'libraries');

// Main component aliases
if (!defined('COM_JFORMS')) define('COM_JFORMS', 'com_jforms');
if (!defined('JFORMS_CLASS')) define('JFORMS_CLASS', 'Jforms');

// Component paths constants
if (!defined('JPATH_ADMIN_JFORMS')) define('JPATH_ADMIN_JFORMS', JPATH_ADMINISTRATOR . DS . 'components' . DS . COM_JFORMS);
if (!defined('JPATH_SITE_JFORMS')) define('JPATH_SITE_JFORMS', JPATH_SITE . DS . 'components' . DS . COM_JFORMS);

// JQuery use
if(!defined('JQUERY_VERSION')) define('JQUERY_VERSION', '1.8.2');


$app = JFactory::getApplication();
jimport('joomla.version');
$version = new JVersion();

// Load the component Dependencies
require_once(dirname(__FILE__) .DS. 'helper.php');

if (!class_exists('CkJLoader')){
	require_once(dirname(__FILE__) .DS. '..' .DS. 'classes' .DS. 'loader.php');
}

CkJLoader::setup(false, false);
CkJLoader::discover('Jforms', JPATH_ADMIN_JFORMS, false, true);

// Some helpers
CkJLoader::register('JToolBarHelper', JPATH_ADMINISTRATOR .DS. "includes" .DS. "toolbar.php", true);
CkJLoader::register('JSubMenuHelper', JPATH_ADMINISTRATOR .DS. "includes" .DS. "toolbar.php", true);

// Handle cross compatibilities
require_once(dirname(__FILE__) .DS. 'mvc.php');

// Set the models directory
if ($app->isSite())
CkJController::addModelPath(JPATH_SITE_JFORMS .DS.'models');
else
CkJController::addModelPath(JPATH_ADMIN_JFORMS .DS.'models');


//Instance JDom
if (!isset($app->dom))
{
	jimport('jdom.dom');
	if (!class_exists('JDom'))
		JError::raiseError(null, JText::_("JFORMS_JDOM_NOT_INSTALLED"));

	JDom::getInstance();	
}


// check for libraries by giro, by simply checking the folder libraries
if (!file_exists(JPATH_SITE .DS. 'libraries' .DS. 'librariesbygiro') AND $app->isAdmin()){
	$app->enqueueMessage(JText::_("JFORMS_LIBRARIES_BY_GIRO_NOT_INSTALLED"), 'error');
}
