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
require_once M4J_INCLUDE_FUNCTIONS;

require_once JPATH_ROOT . '/components/com_proforms/formlib/init.php';
require_once JPATH_ROOT . '/components/com_proforms/includes/validate.php';

$fid = JRequest::getInt("fid",null);


function §($string){
	echo $string."\n";
}

if(!$fid) die();
// 	$lang =JFactory::getLanguage();
// 	$lang_code = substr($lang->getTag(),0,2);

// 	if(! file_exists(JPATH_ROOT . '/components/com_proforms/js/calendar/lang/calendar-'.$lang_code.'.js') || M4J_FORCE_CALENDAR) $lang_code = "en";


§('<div id="preview" style="display:none;">
		<h2>'.	M4J_LANG_PREVIEW.'</h2>	'.
		'<div style="width:100%;" id="proforms_proforms" class="m4j_form_wrap proformsFormWrap">
				<div class="proformsInnerFormWrap" style="width: 100%;">
					<form id="m4jForm" class="ProformsForm" action="" enctype="multipart/form-data" method="post" name="m4jForm">');

$layout = MLayoutList::getLayoutById($fid);


$eidHeap = array();
// DB Query Drawing the Table
$query = "SELECT * FROM #__m4j_formelements WHERE `fid` = '".$fid."' AND `active` = '1' ORDER BY `slot`,`sort_order` ASC";
$database->setQuery( $query );
$formElements = $database->loadObjectList();

// printPre($formElements);

foreach($formElements as $element){
	$_element = MFormFactory::create($element->form, $element, 'default');
	if($_element->getDisplayOnly()){
		$layout->addHTMLRow(
				$_element->getSlot(),
				(string) $_element,
				$_element->getEid(),
				$_element);
	}else{
		$layout->addRow($_element->getSlot(),
				$_element->getQuestion(),
				(string) $_element ,
				$_element->getRequired(),
				$_element->getHelp(),
				$_element->getAlign(),
				$_element->getUsermail(),
				$_element->getEid(),
				$_element->getIsHidden(),
				$_element);
	}
		
		
}//EOF foreach loop
$layout->render(true);
§('</form></div></div></div>');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head><meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script type="text/javascript"><?php echo "\t var pfmFields = [" .implode(",", $eidHeap)."];\n"; ?></script>
</head>
<body style="margin:0; padding:0; width:100%; height: 100%;">
<iframe frameborder="0" src="<?php echo $m4jConfig_live_site?>/index.php?option=com_proforms&dummy=1" width="100%"  style="height: 100%; overflow: visible;" name="dummy"></iframe>
</body>
</html>


