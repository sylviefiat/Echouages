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

	require_once M4J_INCLUDE_TEMPLATER;
	
	remember_cid();
	$error = null;

	$name = JRequest::getString('name');
	$desc = JRequest::getString('description');
	$use_help = JRequest::getInt('use_help',null);
	$json = json_decode( JRequest::getString('json', null) );
	if(isset($json->orderToSlot)){
	    settype($json->orderToSlot, "array");
	}
	$data = bEnc(serialize($json));
// 	MDebug::_("JSON DEFAULT: ");
// 	MDebug::pre($json);


	/* @var $model  ProformsAdminModelResponsive */
	$model = ProformsAdminModel::getInstance("responsive");
	
	switch($task){
	
		case 'new':
			if($name!=null && $name!="")
			{
				$query = "INSERT INTO #__m4j_forms"
						. "\n ( `name`, `description`, `use_help`, `responsive`,`responsive_data`  )"
								. "\n VALUES"
										. "\n ( '".dbEscape($name)."', '".dbEscape($desc)."', '".$use_help."', 1 , '".dbEscape($data)."')";
				$database->setQuery($query);
				if (!$database->query()) HTML_HELPERS_m4j::dbError($database->getErrorMsg());
					
	
				m4jRedirect(M4J_FORM_ITEMS.M4J_HIDE_BAR.M4J_REMEMBER_CID_QUERY.'&id='.$database->insertid());
	
			}
			else $error= M4J_LANG_TEMPLATE_NAME_REQUIRED;
			break;
			//EOF NEW
	
		default:
		case 'edit':
	
			if($id>=0)
			{
				$database->setQuery( "SELECT * FROM #__m4j_forms WHERE fid = '$id' LIMIT 1" );
				$row = $database->loadObject();
				$name = MReady::_($row->name);
				$desc = MReady::_($row->description);
				$use_help = $row->use_help;
				$json = unserialize( bDec($row->responsive_data) ) ;
			}
			break;
			//EOF EDIT
	
		case 'update':
		case 'updateproceed':
	
			$editID = JRequest::getInt("editID", null);
	
			if($name!=null && $name!="" && $editID>=0){
				
				/*
			 * CHECK FOR SLOT CHANGES HERE FIRST!
			 */
               
			 $model->setEditParams($editID, $json);
			 $model->performSlotChanges();

// 			 break;
			$result = MDB::update('#__m4j_forms',
									array(
										"name" =>  dbEscape ( $name ),
										"description" =>  dbEscape ( $desc ),
										"use_help" =>  dbEscape ( $use_help ),
										"responsive_data" =>  dbEscape ( $data )
									),
									MDB_('fid', $editID));	
	
			if (! $result ) HTML_HELPERS_m4j::dbError($database->getErrorMsg());
					
			if ($task=='update'){
				$url = (bool) JRequest::getInt('apply',0) ? M4J_FORM_RESPONSIVE.M4J_REMEMBER_CID_QUERY.M4J_EDIT.M4J_HIDE_BAR.'&id='.$editID : M4J_FORMS.M4J_REMEMBER_CID_QUERY ;
				// UPDATE REDIRECT
				m4jRedirect($url);
			}else {
				// UPDATE AND PROCEED REDIRECT
				m4jRedirect(M4J_FORM_ITEMS.M4J_REMEMBER_CID_QUERY.M4J_HIDE_BAR.'&id='.$editID);
				}
			}else
			{
				$error= M4J_LANG_TEMPLATE_NAME_REQUIRED;
				define("M4J_EDITFLAG",1);
				if($editID) $id = $editID;
			}
			break;
			//EOF UPDATE
	}
	
	
	
	
	
	$elementCount2Slot =  $model->getElementCounts($json, $id);
	$document = JFactory::getDocument();
	$document->addScriptDeclaration('var elementCount2Slot = '. json_encode($elementCount2Slot). ';');
  HTML_m4j::head(M4J_FORM_RESPONSIVE,$error);
	
	if(M4J_EDITFLAG==1) HTML_HELPERS_m4j::caption(M4J_LANG_EDIT_RESPONSIVELAYOUT,null,M4J_LANG_TEMPLATES.' > '.M4J_LANG_EDIT);
	else HTML_HELPERS_m4j::caption(M4J_LANG_NEW_RESPONSIVE_TEMPLATE,null,M4J_LANG_TEMPLATES.' > '.M4J_LANG_NEW_RESPONSIVE_TEMPLATE_SHORT);			

  	$args = array(
  		"name"=>$name, 
  		"desc"=>$desc,
  		"id"=>$id,
  		"use_help"=>$use_help,
  		"json"=> ( $json ? json_encode( $json ) : null )
  	);
 	echo MTemplater::get(M4J_TEMPLATES."responsivelayout.php",$args);
 	 
  HTML_m4j::footer();