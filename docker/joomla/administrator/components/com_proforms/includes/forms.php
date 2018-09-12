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
    define('M4J_NOBAR',0);	
	remember_cid();
	include_once(M4J_INCLUDE_FORMFACTORY);
	include_once(M4J_INCLUDE_FUNCTIONS);
	$error= null;
	
	switch($task){
		case 'delete':
			if($id){
									
					$query = "SELECT COUNT(*) as summe FROM #__m4j_forms";
					$database->setQuery( $query );
					$rows = $database->loadObjectList();
					if( $rows[0]->summe >1)
						{
							
							$query = "DELETE FROM #__m4j_forms WHERE fid = ".$id;
							$database->setQuery($query);
							if (!$database->query()) HTML_HELPERS_m4j::dbError($database->getErrorMsg()); 
							
							$query = "DELETE FROM #__m4j_formelements WHERE fid = ".$id;
							$database->setQuery($query);
							if (!$database->query()) HTML_HELPERS_m4j::dbError($database->getErrorMsg());		
		
							//FIX ME
							$query = "SELECT `fid`,`jid` FROM #__m4j_jobs";
							$database->setQuery( $query );
							$jobs = $database->loadObjectList();
							MDebug::pre($jobs);
							foreach ($jobs as $job){
								
								if($job->fid){
									MDebug::_("FID: ".$job->fid);
									$fids = explode(";",$job->fid);
									MDebug::_("FIDS:");
									MDebug::pre($fids);
									
//									if(sizeof($fids)==1){
//										
//									}else{
//										unset($fids[array_search($id, $fids)]);
//									}
									if(array_search($id, $fids) === false){
										// Do nothing
									}else{
										unset($fids[array_search($id, $fids)]);										
									}
									MDebug::_("NEW FIDS:");
									MDebug::pre($fids);
									$newFid = implode(";",$fids);
									if($newFid != $job->fid){
										MDB::update("#__m4j_jobs",array( "fid"=>$newFid ),MDB::_("jid",$job->jid));
									}
									
								}//EOF if $job								
							}//EOF foreach
							
//							$query = "DELETE FROM #__m4j_jobs WHERE fid = ".$id;
//							$database->setQuery($query);
//							if (!$database->query()) HTML_HELPERS_m4j::dbError($database->getErrorMsg()); 		
						}
					else $error = M4J_LANG_AT_LEAST_ONE;
					}
		 break;
		 
		case'copy':
		if($id>=0){
			$tbl = "#__m4j_forms";
			$fid = MDB::copy( $tbl , MDB::_("fid",$id));
			MDB::markCopied($tbl,"name", MDB::_("fid",$fid));
				
			$query = "SELECT `eid` FROM #__m4j_formelements WHERE fid = '".$id."' ORDER BY `slot`, `sort_order`" ;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			
			$tbl = "#__m4j_formelements";
			foreach ($rows as $element){
				$eid = MDB::copy( $tbl , MDB::_("eid",$element->eid));
				MDB::update( $tbl , array( "fid"=>$fid ), MDB::_("eid",$eid));
				MDB::refactorFE($element->eid,$eid);
			}				
		}
		break;
		 
		 
	}//EOF switch task	

HTML_m4j::head(M4J_FORMS,$error);
  HTML_HELPERS_m4j::caption(M4J_LANG_TEMPLATES);

  $head = array( M4J_LANG_NAME,M4J_LANG_SHORTDESCRIPTION,'fid','','','',M4J_LANG_ITEMS );
  HTML_HELPERS_m4j::init_table($head);
  
  	// DB Query
	$query = "SELECT `name`, `description`, `fid`, `responsive` FROM `#__m4j_forms` ";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	$even=true;
	
	foreach($rows as $row){	
		$editLayoutURL = $row->responsive ? M4J_FORM_RESPONSIVE : M4J_FORM_NEW ;
		$elementsURL = $row->responsive ? M4J_FORM_ITEMS : M4J_FORM_ELEMENTS;
		
		$elementsURL = M4J_FORM_ITEMS;
		
	    $widthArray = array('','475px','24px','16px','16px','16px','150px');
	    $total = MDB::count("#__m4j_formelements", MDB::_("fid",$row->fid));
		$rowArray = array (
			HTML_HELPERS_m4j::edit_by_name(MReady::_($row->name) , $editLayoutURL ,$row->fid,M4J_LANG_EDIT_MAIN_DATA),
			MReady::_($row->description),
			$row->fid,
			HTML_HELPERS_m4j::copy_button(M4J_FORMS,$row->fid,M4J_LANG_DO_COPY),
			HTML_HELPERS_m4j::delete_button(M4J_FORMS,$row->fid,M4J_LANG_TEMPLATE_DELETE_CAUTION),
			HTML_HELPERS_m4j::edit_button($editLayoutURL ,$row->fid,M4J_LANG_EDIT_MAIN_DATA),
			HTML_HELPERS_m4j::link($elementsURL.M4J_HIDE_BAR.M4J_EDIT."&id=".$row->fid,$total.' '.M4J_LANG_EDIT_TEMPLATE_ITEMS)		
		);
		
		HTML_HELPERS_m4j::table_row($rowArray,$even,$widthArray);
		$even = !$even; 
	} //EOF foreach
	//EOF DB Query	
	
  HTML_HELPERS_m4j::close_table();
    if(M4J_SHOW_LEGEND) HTML_m4j::legend('forms');	
HTML_m4j::footer();
?>
