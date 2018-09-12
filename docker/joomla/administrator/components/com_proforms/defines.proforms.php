<?php
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
	
	//* Proforms' general path defines
	define("PROFORMS_PATH_ADMIN", JPATH_ROOT . '/administrator/components/com_proforms/');
	define("PROFORMS_INCLUDES", PROFORMS_PATH_ADMIN . 'includes/');
	define("PROFORMS_PATH_SITE", JPATH_ROOT . '/components/com_proforms/');
	
	define("PROFORMS_FORMLIB", PROFORMS_PATH_SITE . 'formlib/');
	
	//* Apps Folders	
	define("M4J_APPS_BASE", PROFORMS_PATH_SITE . 'apps/');
	define("M4J_APPS_PARAMS", PROFORMS_INCLUDES . 'appsparamdisplay.php');
	define("M4J_HTTP_APPS", $m4jConfig_live_site . '/components/com_proforms/apps/');
	
	//*Frontend Folder
	define("M4J_JS_CALNEDAR", PROFORMS_PATH_SITE . 'js/calendar/');
	define("M4J_FRONTEND_STYLESHEET", PROFORMS_PATH_SITE . 'css/stylesheet.css');
	define("M4J_FRONTEND_STYLESHEET_RESPONSIVE", PROFORMS_PATH_SITE . 'css/responsive.css');
	define("M4J_TMP", PROFORMS_PATH_SITE . 'tmp/');
	
	//* Frontend Includes
	define("M4J_INCLUDE_CALENDAR", PROFORMS_PATH_SITE . 'includes/calendar.php');
	define("M4J_INCLUDE_VALIDATE", PROFORMS_PATH_SITE . 'includes/validate.php');
	define("M4J_INCLUDE_MSELECTION", PROFORMS_PATH_SITE . 'includes/selections.php');
	
	//* Fontend HTTP
	define("M4J_FRONTEND", $m4jConfig_live_site . '/components/com_proforms/');
	define("M4J_FRONTEND_LINK", $m4jConfig_live_site . '/index.php?option=com_proforms');
	define("M4J_FRONTEND_CALENDAR", M4J_FRONTEND . 'js/calendar/');
	define("M4J_FRONTEND_JS_DOJO", M4J_FRONTEND . 'js/dojo/dojo.js');

	//* Frontend App MVC
	define("M4J_INCLUDE_CONTROLLER", PROFORMS_PATH_SITE . 'includes/mcontroller.php');
	define("M4J_INCLUDE_VIEW", PROFORMS_PATH_SITE . 'includes/mview.php');
	define("M4J_INCLUDE_MODEL", PROFORMS_PATH_SITE . 'includes/mmodel.php');

	//* Folders
	define("M4J_LANG", PROFORMS_PATH_ADMIN . 'language/');
	define("M4J_TEMPLATES", PROFORMS_PATH_ADMIN . 'templates/');
	define("M4J_PATH_JS", PROFORMS_PATH_ADMIN . 'js/');
	define("M4J_LAYOUT", PROFORMS_PATH_ADMIN . 'layout/');
	define("M4J_XHR", PROFORMS_PATH_ADMIN . 'xhr/');
	
	//* Include Constants
	define("M4J_INCLUDE_CONFIGURATION", PROFORMS_PATH_ADMIN . 'config.proforms.php');
	define("M4J_INCLUDE_RESET_CONFIGURATION", PROFORMS_INCLUDES . 'reset_config.php');
	
	define("M4J_INCLUDE_FUNCTIONS", PROFORMS_INCLUDES . 'functions.php');	
	define("M4J_INCLUDE_FORMFACTORY", PROFORMS_INCLUDES . 'formfactory.php');	
	define("M4J_INCLUDE_REMEMBER", PROFORMS_INCLUDES . 'remember_cid.php');

	define("M4J_INCLUDE_XHR", PROFORMS_INCLUDES . 'xhr.php');
	define("M4J_INCLUDE_JOBS", PROFORMS_INCLUDES . 'jobs.php');
	define("M4J_INCLUDE_JOBS_NEW", PROFORMS_INCLUDES . 'jobs_new.php');
	define("M4J_INCLUDE_DATASTORAGE", PROFORMS_INCLUDES . 'datastorage.php');
	define("M4J_INCLUDE_APPS", PROFORMS_INCLUDES . 'apps.php');
	define("M4J_INCLUDE_APPTEXT", PROFORMS_INCLUDES . 'apptext.php');
	define("M4J_INCLUDE_APPHELPER", PROFORMS_INCLUDES . 'apphelper.php');
	define("M4J_INCLUDE_APPDB", PROFORMS_INCLUDES . 'appdb.php');
	define("M4J_INCLUDE_TOOLBAR", PROFORMS_INCLUDES . 'toolbar.php');
	define("M4J_INCLUDE_FORMS", PROFORMS_INCLUDES . 'forms.php');
	define("M4J_INCLUDE_FORM_NEW", PROFORMS_INCLUDES . 'form_new.php');
	define("M4J_INCLUDE_FORM_ELEMENTS", PROFORMS_INCLUDES . 'form_elements.php');	
	define("M4J_INCLUDE_FORM_ITEMS", PROFORMS_INCLUDES . 'form_items.php');
	define("M4J_INCLUDE_ELEMENT", PROFORMS_INCLUDES . 'element.php');	
	define("M4J_INCLUDE_CATEGORY", PROFORMS_INCLUDES . 'category.php');	
	define("M4J_INCLUDE_CATEGORY_NEW", PROFORMS_INCLUDES . 'category_new.php');	
	define("M4J_INCLUDE_CONFIG", PROFORMS_INCLUDES . 'config.php');	
	define("M4J_INCLUDE_APPLIST", PROFORMS_INCLUDES . 'applist.php');	
	define("M4J_INCLUDE_ADMINAPPS", PROFORMS_INCLUDES . 'admin.apps.php');
	define("M4J_INCLUDE_BACKUP", PROFORMS_INCLUDES . 'backup.php');	
	define("M4J_INCLUDE_HELP", PROFORMS_INCLUDES . 'help.php');
	define("M4J_INCLUDE_SERVICE", PROFORMS_INCLUDES . 'service.php');
	define("M4J_INCLUDE_LINK", PROFORMS_INCLUDES . 'link.php');
	define("M4J_INCLUDE_EDIT_AREA", PROFORMS_INCLUDES . 'editarea.php');
	define("M4J_INCLUDE_MFORM", PROFORMS_INCLUDES . 'mform.php');
	define("M4J_INCLUDE_LIBCURL", PROFORMS_PATH_ADMIN . 'libcurl/libcurlemu.inc.php');
	define("M4J_INCLUDE_TEMPLATER", PROFORMS_INCLUDES . 'templater.php');
	define("M4J_INCLUDE_PREVIEW", PROFORMS_INCLUDES . 'preview.php');
	define("M4J_INCLUDE_MSEF", PROFORMS_INCLUDES . 'sef.php');
	define("M4J_INCLUDE_LAYOUT", PROFORMS_INCLUDES . 'layout.php');
	define("M4J_INCLUDE_MDB", PROFORMS_INCLUDES . 'mdb.php');
	define("M4J_INCLUDE_DOWNLOAD", PROFORMS_INCLUDES . 'get.php');
	define("M4J_INCLUDE_JSTEXT", PROFORMS_INCLUDES . 'jstext.php');
	define("M4J_INCLUDE_CSV_COMMA", PROFORMS_INCLUDES . 'csv_comma.php');
	define("M4J_INCLUDE_CSV_SEMICOLON", PROFORMS_INCLUDES . 'csv_semicolon.php');
	define("M4J_INCLUDE_XLS", PROFORMS_INCLUDES . 'xls.php');
	define("M4J_INCLUDE_XLS_HELPER", PROFORMS_INCLUDES . 'xls_helper.php');
	define("M4J_INCLUDE_STORAGE_CONFIG", PROFORMS_INCLUDES . 'storage_config.php');
	define("M4J_INCLUDE_STORAGE_VIEW", PROFORMS_INCLUDES . 'storage_view.php');
	define("M4J_INCLUDE_STORAGE_MAIL", PROFORMS_INCLUDES . 'storage_mail.php');
	define("M4J_INCLUDE_DB_CONFIG", PROFORMS_INCLUDES . 'db_config.php');
	define("M4J_INCLUDE_ELEMENT_HELPER", PROFORMS_INCLUDES . 'element_helper.php');
	define("M4J_INCLUDE_OPT", PROFORMS_INCLUDES . 'opt.php');
	define("M4J_INCLUDE_INSTALL", PROFORMS_INCLUDES . 'install.php');
	define("M4J_INCLUDE_UNINSTALL", PROFORMS_INCLUDES . 'uninstall.php');
	define("M4J_INCLUDE_COUNTRIES", PROFORMS_INCLUDES . 'countries.php');
	define("M4J_INCLUDE_CODEMIRROR", PROFORMS_INCLUDES . 'codemirror.php');
	define("M4J_INCLUDE_ADMINHELPER", PROFORMS_INCLUDES . 'adminhelper.php');
	define("M4J_INCLUDE_TEST", PROFORMS_INCLUDES . 'test.php');
	define("M4J_INCLUDE_RESPONSIVE_LAYOUT", PROFORMS_INCLUDES . 'responsivelayout.php');
	define("M4J_INCLUDE_ADMIN_MODEL", PROFORMS_INCLUDES . 'adminmodel.php');
	define("M4J_INCLUDE_LANGMANAGER", PROFORMS_INCLUDES . 'langmanager.php');
	define("M4J_INCLUDE_FIELDMANAGER", PROFORMS_INCLUDES . 'fieldmanager.php');
	define("M4J_INCLUDE_REMEMBERSLOT", PROFORMS_INCLUDES . 'rememberslot.php');
	define("M4J_INCLUDE_FIELD", PROFORMS_INCLUDES . 'field.php');
	
	$tmpl = (JRequest::getString("tmpl")=="component")? "&tmpl=component" : "";
	//* HTTP Contstants
	define("M4J_HOME",$m4jConfig_live_site .'/administrator/index.php?option=com_proforms'.$tmpl);
	define("M4J_JOBS", M4J_HOME.'&section=jobs');	
	define("M4J_JOBS_NEW", M4J_HOME.'&section=jobs_new');	
	define("M4J_DATASTORAGE", M4J_HOME.'&section=datastorage');	
	define("M4J_APPS", M4J_HOME.'&section=apps');	
	define("M4J_FORMS", M4J_HOME.'&section=forms');
	define("M4J_FORM_NEW", M4J_HOME.'&section=form_new');
	define("M4J_FORM_RESPONSIVE", M4J_HOME.'&section=responsivelayout');
	define("M4J_FORM_ELEMENTS", M4J_HOME.'&section=formelements');
	define("M4J_FORM_ITEMS", M4J_HOME.'&section=formitems');
	define("M4J_ELEMENT", M4J_HOME.'&section=element');
	define("M4J_CATEGORY", M4J_HOME.'&section=category');
	define("M4J_CATEGORY_NEW", M4J_HOME.'&section=category_new');
 	define("M4J_CONFIG", M4J_HOME.'&section=config'); 
 	define("M4J_APPLIST", M4J_HOME.'&section=applist'); 
 	define("M4J_ADMINAPPS", M4J_HOME.'&section=adminapps');
 	define("M4J_BACKUP", M4J_HOME.'&section=backup'); 
	define("M4J_HELP",  M4J_HOME.'&section=help');
	define("M4J_SERVICE",  M4J_HOME.'&section=service'); 
	define("M4J_LINK",  M4J_HOME.'&section=link');
	define("M4J_PREVIEW",  M4J_HOME.'&section=preview&format=raw&fid=');
	define("M4J_LOAD_XHR",  M4J_HOME.'&section=xhr&xhr=');
	define("M4J_DOWNLOAD",  M4J_HOME.'&section=download&format=raw&stiid=');
	define("M4J_STORAGE_CONFIG",  M4J_HOME.'&section=storage_config&id=');
	define("M4J_STORAGE_VIEW",  M4J_HOME.'&section=storage_view&stid=');
	define("M4J_STORAGE_MAIL",  M4J_HOME.'&section=storage_mail&id=');
	define("M4J_FIELD", M4J_HOME.'&section=field');
	
	define("M4J_HTTP_LAYOUT", $m4jConfig_live_site . '/administrator/components/com_proforms/layout/');
	define("M4J_IMAGES", $m4jConfig_live_site . '/administrator/components/com_proforms/images/');
	define("M4J_CSS", $m4jConfig_live_site . '/administrator/components/com_proforms/css/admin.stylesheet.css'); 
	define("M4J_CSS_30", $m4jConfig_live_site . '/administrator/components/com_proforms/css/admin.stylesheet30.css');
	define("M4J_CSS_RESPONSIVE_WHIZZARD", $m4jConfig_live_site . '/administrator/components/com_proforms/css/admin.responsivewhizzard.css');
	define("M4J_JS", $m4jConfig_live_site . '/administrator/components/com_proforms/js/');
	define("M4J_THICKBOX", M4J_JS. 'thickbox/');
	define("M4J_EDIT_AREA", M4J_JS. 'editarea/');
	define("M4J_JS_INFO", M4J_JS. 'info.js?BASIC=120');
	define("M4J_JS_PREVIEW", M4J_JS. 'preview.js?BASIC=120');
	define("M4J_JS_LAYOUT_SLOT", M4J_JS. 'layoutslot.js?BASIC=120');
	define("M4J_JS_NEW_FORM", M4J_JS. 'newform.js?BASIC=120');
	define("M4J_JS_NEW_JOB", M4J_JS. 'newjob.js?BASIC=120');
	define("M4J_JS_SERVICE", M4J_JS. 'service.js?BASIC=120');
	define("M4J_JS_MWINDOW", M4J_JS. 'mwindow.js?BASIC=120');
	define("M4J_JS_APPS", M4J_JS. 'apps.js?BASIC=120');
	define("M4J_JS_MODERNIZR", M4J_JS. 'modernizr-proforms.js?BASIC=120');
	
	//* ACTIONS
	define("M4J_HIDE_BAR",'&nobar=1');
	define("M4J_NEW",'&task=new');	
	define("M4J_EDIT",'&task=edit');
   	define("M4J_DELETE",'&task=delete');	
	define("M4J_UPDATE",'&task=update');
	define("M4J_SAVE",'&task=save');
	define("M4J_UP",'&task=up');	
	define("M4J_DOWN",'&task=down');	
	define("M4J_PUBLISH",'&task=publish');		
	define("M4J_UNPUBLISH",'&task=unpublish');
	define("M4J_REQUIRED",'&task=required');		
	define("M4J_NOT_REQUIRED",'&task=not_required');
	define("M4J_COPY",'&task=copy');	
	define("M4J_RESET",'&task=reset');	
	define("M4J_MENUTYPE",'&menutype=');
	
	define('M4J_BACK2LAYOUT_FID', '&id=' . JRequest::getInt('id', null));
	
	
	// Service Connect
	define('M4J_SERVICE_CONNECT','http://www.mad4software.com/packages/proforms.html');
	
	// Ident
	define("M4J_IDENTIFIER","MPFM");
