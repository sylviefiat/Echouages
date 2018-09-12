<?PHP 
/**
* @name MOOJ Proforms 
* @version 1.0
* @package proforms
* @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
* @author Dipl. Inf.(FH) Fahrettin Kutyol
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Please note that some Javascript files are not under GNU/GPL License.
* These files are under the mad4media license
* They may edited and used infinitely but may not repuplished or redistributed.  
* For more information read the header notice of the js files.
**/
defined("_JEXEC") or define( '_JEXEC', 1 );
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

include_once('../../../configuration.php');


	// Joomla 1.5.x
	define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../..' ));
	define( 'DS',DIRECTORY_SEPARATOR);
	
	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	$mainframe =JFactory::getApplication('site');
	$mainframe->initialise();
	$db = JFactory::getDBO();
	$query = "SELECT `value` FROM `#__m4j_config` WHERE `key` = 'M4J_UNIQUE_ID'";
	$db->setQuery($query);
	$info = $db->loadObject();
	echo $info->value;
exit();


