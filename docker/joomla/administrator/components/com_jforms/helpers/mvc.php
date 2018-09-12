<?php
/**                               ______________________________________________
*                          o O   |                                              |
*                 (((((  o      <     JDom Class - Cook Self Service library    |
*                ( o o )         |______________________________________________|
* --------oOOO-----(_)-----OOOo---------------------------------- www.j-cook.pro --- +
* @version		0.4.4
* @package		Cook Self Service
* @subpackage	JDom
* @license		GNU General Public License
* @author		Jocelyn HUARD
*
*             .oooO  Oooo.
*             (   )  (   )
* -------------\ (----) /----------------------------------------------------------- +
*               \_)  (_/
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


jimport('joomla.version');
$version = new JVersion();


// Joomla! 1.0 - 1.5
if (version_compare($version->RELEASE, '1.6', '<'))
{
	exit('Not Supported');	
}
		// Joomla! 1.6 - 1.7 - 2.5
else if (version_compare($version->RELEASE, '3.0', '<'))
{
	
	// Joomla 1.6
	if (version_compare($version->RELEASE, '1.7', '<'))
	{
		if (!class_exists('JInput'))
			CkJLoader::register('JInput', JPATH_ADMIN_JFORMS .DS.'legacy' .DS. 'input' .DS. 'input.php');
			CkJLoader::load('JInput');  // needed to load the class, apparently it's not enough to register the class, at least with j1.6
	}
	// Joomla 1.7
	else if (version_compare($version->RELEASE, '2.5', '<'))
	{
		jimport('joomla.application.input');
	}
	
	// Instance JInput in application when missing
	if (empty($app->input))
		$app->input = new JInput;


	// MVC
	if (!class_exists('CkJController'))
	{
		jimport('joomla.application.component.controller');		
		class CkJController extends JController{}
	}
	
	// MVC
	if (!class_exists('CkJModel'))
	{
		jimport('joomla.application.component.model');		
		class CkJModel extends JModel{}
	}
	
	// MVC
	if (!class_exists('CkJView'))
	{
		jimport('joomla.application.component.view');
		class CkJView extends JView{}
	}

	if (!class_exists('CkJSession'))
	{
		class CkJSession extends JSession
		{
			public static function checkToken($method = 'post')
			{
				return JRequest::checkToken($method);
			}	
		}
	}
	
}




//Joomla! 3.0 and later
else if ($version->isCompatible('3.0'))
{
	if (!class_exists('CkJController'))
	{	
		jimport('legacy.controller.legacy');
	
		class CkJController extends JControllerLegacy{}
	}

	if (!class_exists('CkJModel'))
	{
		jimport('legacy.model.legacy');		
		class CkJModel extends JModelLegacy{}
	}
	
	if (!class_exists('CkJView'))
	{
		if (!class_exists('JViewLegacy', false))
			jimport('legacy.view.legacy');
			
		class CkJView extends JViewLegacy{}
	}
	
	if (!class_exists('CkJSession'))
	{	
		class CkJSession extends JSession{}
	}

}


//Additional legacy classes
if (!class_exists('CkJToolbarHelper')){
	if ($version->isCompatible('1.7')){
		class CkJToolbarHelper extends JToolbarHelper{}		
	}
	else{
		//Some toolbar buttons are missing in 1.6
		if (!class_exists('JformsLegacyHtmlToolbar'))
			CkJLoader::register('JformsLegacyHtmlToolbar', JPATH_ADMIN_JFORMS .DS.'legacy' .DS. 'html' .DS. 'toolbar.php');
			CkJLoader::load('JformsLegacyHtmlToolbar');  // needed to load the class, apparently it's not enough to register the class, at least with j1.6

		class CkJToolbarHelper extends JformsLegacyHtmlToolbar{}	
	}
}

//JDatabase::quoteName does not exist in 1.6
if (!function_exists('qn'))
{
	if ($version->isCompatible('1.7')){
		function qn($db, $s){return $db->quoteName($s);}
	}
	else{
		//1.6
		function qn($db, $s){return $db->nameQuote($s);}	
	}
}
