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
	
	 $error= null;
	 $name = m4jGetParam($_REQUEST, 'name');
	 $desc = m4jGetParam($_REQUEST, 'description');
	 $qwidth =  m4jGetParam($_REQUEST, 'qwidth');
	 $awidth =  m4jGetParam($_REQUEST, 'awidth');
	 $use_help =  m4jGetParam($_REQUEST, 'use_help');
	 $layout_name = 'layout01';
	 $slots = 1;
	 $use_fieldset = m4jGetParam($_REQUEST, 'use_fieldset');
	 $legend = m4jGetParam($_REQUEST, 'legend');
	 $width = m4jGetParam($_REQUEST, 'width');
	 $height = m4jGetParam($_REQUEST, 'height');
	 $left = m4jGetParam($_REQUEST, 'left');
	 $right = m4jGetParam($_REQUEST, 'right');
	 
	 $data = "";
	 for ($t=1; $t< ($slots+1);$t++){
	 	
	 		$data .= "use_fieldset=".(int) $use_fieldset[$t].";";
	 		$data .= "legend=".strip_tags($legend[$t]).";";
	 		$data .= "width=".(int) $width[$t].";";
	 		$data .= "height=".(int) $height[$t].";";
	 		$data .= "left=".(int) $left[$t].";";
	 		$data .= "right=".(int) $right[$t].";";
	 		$data .= "|";
	 }
	 
	 
	
	switch($task){
		
		case 'new':
		if($name!=null && $name!="") 
			{			
			$query = "INSERT INTO #__m4j_forms"
				. "\n ( `name`, `description`, `question_width`, `answer_width`, `use_help`, `layout`, `layout_data`, `responsive`,`responsive_data`  )"
				. "\n VALUES"
				. "\n ( '".dbEscape($name)."', '".dbEscape($desc)."', '".$qwidth."', '".$awidth."', '".$use_help."', 'layout01', '".dbEscape($data)."', 0, NULL )";
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
			$query = "SELECT * FROM #__m4j_forms WHERE fid = ".$id;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
				$name = MReady::_($rows[0]->name);
				$desc = MReady::_($rows[0]->description);			
				$qwidth = $rows[0]->question_width;
				$awidth = $rows[0]->answer_width;	
				$use_help = $rows[0]->use_help;
				$layout_name = $rows[0]->layout;
				$data = MReady::_($rows[0]->layout_data);			
		
		}
		break;
		//EOF EDIT
		
		case 'update':
		case 'updateproceed':
				
		$editID = m4jGetParam($_REQUEST, 'editID');
		
		
		
		if($name!=null && $name!="" && $editID>=0){
			
			$oldLayout = MLayoutList::getLayoutById($editID);
			if($oldLayout->getName() != $layout_name){
				$newLayout = MLayoutList::get($layout_name);
				$oldSlots = (int) $oldLayout->getParameter("positions");
				$newSlots = (int) $newLayout->getParameter("positions");
				if($oldSlots>$newSlots){
					$count = (int) MDB::getMax("#__m4j_formelements", "`fid`='".$editID."' AND slot='1'", 1);
					
					$query = "SELECT `eid` FROM #__m4j_formelements WHERE `fid` = '".$editID."' AND `slot` > '".$newSlots."' ORDER BY `slot`,`sort_order`";
					$database->setQuery($query);
					$list = $database->loadObjectList();		
					
					foreach($list as $item){
						$query = "UPDATE #__m4j_formelements"
								. "\n SET"
								. "\n `slot` = '1', "
								. "\n `sort_order` = '".$count++."'"
								. "\n WHERE `eid` = ".$item->eid;
						$database->setQuery($query);
						$database->query(); 
					}// EOF forech list
				}// EOF SLot mismatch	
			}// EOF Layout has changed						
			
			$query = "UPDATE #__m4j_forms"
				. "\n SET"
				. "\n name = '".dbEscape($name)."', "
				. "\n description = '".dbEscape($desc)."', "
				. "\n question_width = '".$qwidth."', "
				. "\n answer_width = '".$awidth."', "
				. "\n use_help = '".$use_help."', "
				. "\n layout = 'layout01', "
				. "\n layout_data = '".dbEscape($data)."', "
				. "\n responsive = 0 "
				. "\n WHERE fid = ".$editID;
				$database->setQuery($query);
			 if (!$database->query()) HTML_HELPERS_m4j::dbError($database->getErrorMsg()); 
			
			 if ($task=='update'){
			 	$url = (bool) JRequest::getInt('apply',0) ? M4J_FORM_NEW.M4J_REMEMBER_CID_QUERY.M4J_EDIT.M4J_HIDE_BAR.'&id='.$editID : M4J_FORMS.M4J_REMEMBER_CID_QUERY ;
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


	
  HTML_m4j::head(M4J_FORM_NEW,$error);
	
	if(M4J_EDITFLAG==1) HTML_HELPERS_m4j::caption(M4J_LANG_EDIT_NAME,null,M4J_LANG_TEMPLATES.' > '.M4J_LANG_EDIT);
	else HTML_HELPERS_m4j::caption(M4J_LANG_NEW_TEMPLATE_LONG,null,M4J_LANG_TEMPLATES.' > '.M4J_LANG_NEW_TEMPLATE);			

  //HTML_m4j::new_form($name,$desc,$id,$qwidth,$awidth,$use_help); 
  	$args = array(
  		"name"=>$name, 
  		"desc"=>$desc,
  		"id"=>$id,
  		"qwidth"=>$qwidth,
  		"awidth"=>$awidth,
  		"use_help"=>$use_help,
  		"layout_name"=>$layout_name
  	);
 	echo MTemplater::get(M4J_TEMPLATES."new_form.php",$args);
 	 
  HTML_m4j::footer();

