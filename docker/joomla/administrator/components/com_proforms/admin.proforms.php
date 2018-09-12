<?PHP
/**
* @name MOOJ Proforms 
* @version 1.0
* @package proforms
* @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
* @author Dipl. Inf.(FH) Fahrettin Kutyol
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
* Please note that some Javascript files are not under GNU/GPL License.
* These files are under the mad4media license
* They may edited and used infinitely but may not repuplished or redistributed.  
* For more information read the header notice of the js files.
**/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

	// Joomla version detection
		$jVersion = new JVersion;
		$j = $jVersion->getShortVersion();
		$jsub = floatval(substr($j,0,3));
		if($jsub == 1.5 ){
			define("_M4J_IS_J16" ,0);
			define("_M4J_IS_J30" ,0);
			define("_M4J_IS_J32" ,0);
		}else if($jsub > 1.5 && $jsub <3.0){
			define("_M4J_IS_J16" ,1);
			define("_M4J_IS_J30" ,0);
			define("_M4J_IS_J32" ,0);
		}else if($jsub > 2.6 && $jsub <3.2){
			define("_M4J_IS_J16" ,1);
			define("_M4J_IS_J30" ,1);
			define("_M4J_IS_J32" ,0);
		}else{
			define("_M4J_IS_J16" ,1);
			define("_M4J_IS_J30" ,1);
			define("_M4J_IS_J32" ,1);
		}
    
	// Must route downloads directly after start. This is an exception
	if(JRequest::getString('section')=== 'download') include_once (JPATH_ROOT . '/administrator/components/com_proforms/includes/get.php');	

	//*ABSOLUTE PATH
	define("M4J_ABS", JPATH_ROOT);
	//proforms internal debug
	define("_M4J_DEBUG",0);
	
	// get version class
	require_once(M4J_ABS . '/administrator/components/com_proforms/includes/version.php');
	
	if(! _M4J_IS_J30){
		jimport("joomla.utilities.simplexml");
	}
	//JSimpleXML Legacy
	if(! class_exists("JSimpleXML")){
		require_once(M4J_ABS . '/administrator/components/com_proforms/includes/simplexml.php');
	}
	// get evolution functions	
	require_once(M4J_ABS . '/administrator/components/com_proforms/includes/evolution.php');
	// get defines
	require_once(M4J_ABS . '/administrator/components/com_proforms/defines.proforms.php');
	// load view ( Based on  J1.0x ) or something like this :).
	require_once(M4J_ABS . '/administrator/components/com_proforms/admin.proforms.html.php');
	// load admin helper
	require_once(M4J_INCLUDE_ADMINHELPER);
	// load the easy form maker class (new to proforms)
	require_once(M4J_INCLUDE_MFORM);
	// load the curl emulation for servers without curl
//	require_once(M4J_INCLUDE_LIBCURL);
	// a helper class for working with templates 
	require_once(M4J_INCLUDE_TEMPLATER);
	// get the sef builder class
	require_once(M4J_INCLUDE_MSEF);
	// get the layout and the layout list class
	require_once(M4J_INCLUDE_LAYOUT);
	// get proforms's db helpers
	require_once(M4J_INCLUDE_MDB);
	// get Javscript language text helper
	require_once (M4J_INCLUDE_JSTEXT);
	// get the toolbar class
 	require_once(M4J_INCLUDE_TOOLBAR);
	// the main admin model class
 	require_once(M4J_INCLUDE_ADMIN_MODEL);
	
	// get the file class
	jimport('joomla.filesystem.file'); 
	// get the folder class
	jimport('joomla.filesystem.folder'); 
	// get the path class
	jimport('joomla.filesystem.path'); 	
	
	// get the configuration
	if(file_exists(M4J_INCLUDE_CONFIGURATION)) require_once(M4J_INCLUDE_CONFIGURATION);
	else require_once(M4J_INCLUDE_RESET_CONFIGURATION);
	// fix me . this is the old way to remember the category at the forms section.
	require_once(M4J_INCLUDE_REMEMBER);
	// the old way to include languages. if a certain language doesn't exist, the english files will be loaded
	if(M4J_FORCE_ADMIN_LANG) $m4jConfig_lang = M4J_FORCE_ADMIN_LANG;
	if(file_exists(M4J_LANG.$m4jConfig_lang.'.php')) include_once(M4J_LANG.$m4jConfig_lang.'.php');
	else include_once(M4J_LANG.'en.php');
	
	$GLOBALS["m4j_lang_elements"] = $m4j_lang_elements;
	require_once M4J_INCLUDE_LANGMANAGER;
	
	// append stlesheets and javascript to the head
	$document=JFactory::getDocument();
	$document->addStyleSheet(M4J_CSS);	
	if(_M4J_IS_J30) $document->addStyleSheet(M4J_CSS_30);	
	$document->addScript(M4J_JS.'proforms.js');	
	$document->addScript(M4J_FRONTEND_JS_DOJO);	
	$document->addScript(M4J_JS_MWINDOW);	
	$document->addScript(M4J_JS_MODERNIZR);
	$document->addScript(M4J_JS . 'topinfo.js');
		
	//append main language variables to js
	HTML_m4j::jsText();
	

	// Get own Code Mirror class
	require_once( M4J_INCLUDE_CODEMIRROR );
	
	
	// get the variables
	$section = JRequest::getString('section','jobs');
	$task = JRequest::getString('task',null);
	$id = JRequest::getInt('id', -1);
	$GLOBALS['id'] = $id;
	$GLOBALS['task'] = $task;
	$GLOBALS['section'] = $section;
	
	if( JRequest::getInt("nobar", 0) == 1 ) define("M4J_NOBAR",1);
	
	$editFlag = ($task =='edit') ? 1 : 0;
	define("M4J_EDITFLAG",$editFlag);
		
	$location = "";
	switch($section){
			
		default:
		case 'jobs':
		$location = M4J_LANG_FORM;
		require_once(M4J_INCLUDE_JOBS);
		break;
		
		case 'jobs_new':
		$location = M4J_LANG_FORM;
		require_once(M4J_INCLUDE_JOBS_NEW);
		break;
		
		case 'forms':
		$location = M4J_LANG_TEMPLATE;
		require_once(M4J_INCLUDE_FORMS);
		break;
		
		case 'form_new':
		$location = M4J_LANG_TEMPLATE;
		require_once(M4J_INCLUDE_FORM_NEW);
		break;

		case 'responsivelayout':
		$location = M4J_LANG_RESPONSIVE_LAYOUT;
		require_once(M4J_INCLUDE_RESPONSIVE_LAYOUT);
		break;		
		
		case 'datastorage':
		$location = M4J_LANG_DATABASE;
		require_once(M4J_INCLUDE_DATASTORAGE);
		break;		

		case 'apps':
		$location = M4J_LANG_APPS;
		require_once(M4J_INCLUDE_APPS);
		break;		
		
		case 'storage_view':
		require_once(M4J_INCLUDE_STORAGE_VIEW);
		break;	
		
		case 'storage_config':
		require_once(M4J_INCLUDE_STORAGE_CONFIG);
		break;	
		
		case 'storage_mail':
		require_once(M4J_INCLUDE_STORAGE_MAIL);
		break;
		
		case 'formelements':
		$location = str_replace(":","", M4J_LANG_TEMPLATE_ELEMENTS);
		require_once(M4J_INCLUDE_FORM_ELEMENTS);
		break;
		
		case 'formitems':
		$location = str_replace(":","", M4J_LANG_TEMPLATE_ELEMENTS);
		require_once(M4J_INCLUDE_FORM_ITEMS);
		break;
		
		
		case 'element':
		$location = str_replace(":","", M4J_LANG_TEMPLATE_ELEMENTS);
		require_once(M4J_INCLUDE_ELEMENT);
		break;

		case 'field':
		require_once(M4J_INCLUDE_FIELD);
		break;
		
		
		case 'category':
		$location = M4J_LANG_CATEGORY;
		require_once(M4J_INCLUDE_CATEGORY);
		break;

		case 'category_new':
		$location = M4J_LANG_CATEGORY;
		require_once(M4J_INCLUDE_CATEGORY_NEW);
		break;
				
		case 'config':
		$location = M4J_LANG_CONFIG;
		require_once(M4J_INCLUDE_CONFIG);
		break;
				
		case 'applist':
		$location = M4J_LANG_APPS;
		require_once(M4J_INCLUDE_APPLIST);
		break;
				
		case 'adminapps':
		$location = M4J_LANG_APPS;
		require_once(M4J_INCLUDE_ADMINAPPS);
		break;
		
		case 'backup':
		$location = M4J_LANG_BACKUP;
		require_once(M4J_INCLUDE_BACKUP);
		break;
		
		case 'help':
		$location = M4J_LANG_HELP;
		define('M4J_NOBAR',0);	
		require_once(M4J_INCLUDE_HELP);
		break;
		
		case 'service':
		$location = M4J_LANG_HELPDESK;
		define('M4J_NOBAR',0);	
		require_once(M4J_INCLUDE_SERVICE);
		break;		
		
		case 'link':
		require_once(M4J_INCLUDE_LINK);
		break;

		case 'preview':
		require_once(M4J_INCLUDE_PREVIEW);
		break;		
		
		case 'xhr':
		require_once(M4J_INCLUDE_XHR);
		exit();
		break;
		
		case 'test':
		require_once(M4J_INCLUDE_TEST);
		break;
			
	}

	$app = JFactory::getApplication();
	/* @var $app JApplicationAdministrator */
	$app->JComponentTitle = '<h1 class="page-title"><span class="icon-heart"></span>Proforms'. ($location ? ": ".$location : "") . '</h1>';
	
	
	JSText::render();	
	
