<?php
/**
 * @name MOOJ Proforms
 * @version 1.5
 * @package proforms
 * @copyright Copyright (C) 2008-2013 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$isXHR = false;
$buffer = '';
remember_cid();

require_once M4J_INCLUDE_FIELDMANAGER;
require_once M4J_INCLUDE_REMEMBERSLOT;
RememberSlot::setFid($id);



/* @var $model ProformsAdminModelFormitems */
$model = ProformsAdminModel::getInstance("formitems");
define('M4J_IS_RESPONSIVE_LAYOUT', $model->isResponsive  );

$reloadURL = M4J_FORM_ITEMS . M4J_HIDE_BAR . "&id=$id" . M4J_REMEMBER_CID_QUERY; 

$eid = JRequest::getInt("eid", null);
$batch = (bool) JRequest::getInt("batch",0);
$batchVerify = JRequest::getString("batch_verify", null);

if($batch){
	$batchVerify = json_decode($batchVerify, TRUE);
	if(! is_array($batchVerify)){
		$task = null;
		m4jRedirect($reloadURL);
	}else{
		foreach($batchVerify as & $_eid){
			$_eid = (int) $_eid;
		}
	}
}

// $model->refactorSlotSortOrder(1);
$slot = JRequest::getInt("slot", null);

$showSlot = $slot !== null ? $slot : RememberSlot::get();
RememberSlot::set($showSlot);

$selection = null;
if(isset($_REQUEST["selection"])){
	foreach($_REQUEST["selection"] as $key => & $sel){
		$sel = (int) $sel;
		if($batch && ! in_array($sel, $batchVerify)){
			unset($_REQUEST["selection"][$key]);
		}
	}
	$selection = $_REQUEST["selection"];
}


switch ($task) {
	
	case "reloadmarked":
		
		if($selection){
			$app = JFactory::getApplication();
			$app->setUserState('ProformsFormItemsRememberSelection_' . $id, $selection);
		}
		m4jRedirect($reloadURL);
		
		break;
	
	
	case "remember": 
		die(1);
		break;
	
	
	case "unpublish":
		if($batch){
			foreach ($selection as $_eid){
				$model->setActive(0, $_eid);
			}
			m4jRedirect($reloadURL);
			break;
		}
		
		$result = $model->setActive(0, $eid);
		$val = $result !== false ? $result : 1;
		$isXHR =true;
		$buffer =  HTML_HELPERS_m4j::active_button(M4J_FORM_ITEMS,$id,$val,1,$eid, "return FormItems.setActive(this);");
	;
	break;
	
	case "publish":
		if($batch){
			foreach ($selection as $_eid){
				$model->setActive(1, $_eid);
			}
			m4jRedirect($reloadURL);
			break;
		}
		
		$result = $model->setActive(1, $eid);
		$val = $result !== false ? $result : 0;
		$isXHR =true;
		$buffer =  HTML_HELPERS_m4j::active_button(M4J_FORM_ITEMS,$id,$val,1,$eid, "return FormItems.setActive(this);");
		;
		break;
		
		
	case "not_required":
		if($batch){
			foreach ($selection as $_eid){
				$model->setRequired(0, $_eid);
			}
			m4jRedirect($reloadURL);
			break;
		}
		
		$result = $model->setRequired(0, $eid);
		$val = $result !== false ? $result : 1;
		$isXHR =true;
		$buffer =  HTML_HELPERS_m4j::required_button(M4J_FORM_ITEMS,$id,$val,1,$eid, "return FormItems.setRequired(this);");
		;
		break;
	
	case "required":
		if($batch){
			foreach ($selection as $_eid){
				if(! $model->isDisplayOnly($_eid)) $model->setRequired(1, $_eid);
			}
			m4jRedirect($reloadURL);
			break;
		}
		
		$result = $model->setRequired(1, $eid);
		$val = $result !== false ? $result : 0;
		$isXHR =true;
		$buffer =    HTML_HELPERS_m4j::required_button(M4J_FORM_ITEMS,$id,$val,1,$eid, "return FormItems.setRequired(this);");
		;
		break;
		
	case "copy":
		$_selection = ( !$batch && $eid ) ? array($eid) : $selection;
		$result = $model->copyElements($_selection, $slot);
		m4jRedirect($reloadURL);
		
		break;
		
	case "delete":
		$model->remove($selection, $eid, $slot);
		$model->reload();		
		break;
	
	case "move": 
		$destination = JRequest::getInt("destination", null);
		$responsiveDestination = JRequest::getInt("responsive_destination", null);
			
		if($destination && $slot){
		    
			$model->moveToSlot($destination, $selection, $responsiveDestination);
			$model->refactorSlotSortOrder($slot);
			
		}
		$destination = (int) $destination;
		RememberSlot::set($destination);
		m4jRedirect($reloadURL);		
		break;
		
	case "sort":
		$destination = JRequest::getInt("destination", null);
		$model->movePosition($slot,$selection, $destination);				
		$isXHR =true;
		$buffer= MDebug::xhrOut();
		break;
	
	default:
		;
	break;
}





if(! $isXHR){
	// Rendering the template
	ob_start();
	include M4J_TEMPLATES . "form_items.php";
	$buffer = ob_get_clean();
	echo $buffer;
}else ProformsAdminHelper::xhrExit($buffer);
