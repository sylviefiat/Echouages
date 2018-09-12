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

$stiid = JRequest::getInt('stiid',null);
if(!$stiid) die();

JResponse::clearHeaders();


$db = JFactory::getDBO();

$query = "SELECT si.`content` AS `filename`, s.`root_dir` , s.`tmp_dir` AS `dir`  ".
		 "FROM #__m4j_storage_items AS si ".
		 "LEFT JOIN #__m4j_storage AS s ON (si.stid = s.stid ) "  .
		 "WHERE `stiid` = '".$stiid."' LIMIT 1";  
$db->setQuery( $query );
$download = $db->loadObject();
$rootDir = $download->root_dir ? $download->root_dir : JPATH_ROOT . '/components/com_proforms/tmp/';


$fileName = $rootDir.$download->dir.DS.$download->filename;	
		
		if(file_exists($fileName)){
//		header('Content-type: application');
		header("Content-Type: application/force-download");	
		header("Content-Disposition: attachment; filename=\"".basename($fileName)."\"");
		readfile($fileName);
		exit;	
		}else{
			die("File not found: ".$fileName);	
		}
	   

?>