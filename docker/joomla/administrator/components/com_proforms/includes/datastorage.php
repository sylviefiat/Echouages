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

remember_cid();

$error ="";
$formName = "";
$id = 0;
$optionArray = array();
$count = 0; 
$limit= 0;
$limitstart = 0;

$alias= JRequest::getInt("alias",0);

define("M4J_SHOW_ALIAS", $alias);

HTML_m4j::head(M4J_DATASTORAGE,$error);
HTML_HELPERS_m4j::caption(M4J_LANG_ONLYPRO_DESC,null,M4J_LANG_FORMS.' > '.M4J_LANG_STORAGES.MReady::_($formName));

// HTML_m4j::dataStorageSearch($id,$optionArray,$count,$limit,$limitstart);

echo '<center><span style="display:inline-block; margin-top: 40px; font-size: 48px; color:red;">' . M4J_LANG_ONLYPRO .'</span></center>';
HTML_m4j::footer();

