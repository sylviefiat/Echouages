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
  include_once (M4J_INCLUDE_EDIT_AREA);
  $GLOBALS['editArea'] = new EditArea();
  
  //if($id==-1 && ($task=='update' || $task=='edit')) m4jRedirect(M4J_JOBS.M4J_REMEMBER_CID_QUERY);
  
  require_once(M4J_INCLUDE_VALIDATE);
  require_once(M4J_INCLUDE_FUNCTIONS);

  
  require_once(M4J_INCLUDE_COUNTRIES);
  
  $error= null;
  $tab = JRequest::getInt("tab",0);
  $title = JRequest::getString('title',null);
  $alias = trim(JRequest::getString('alias',null));
  $email = JRequest::getString('email',null);
  $access = JRequest::getInt('access',0);
  $introtext =  JRequest::getString('introtext', null, 'default', JREQUEST_ALLOWRAW);
  $maintext =  JRequest::getString('maintext', null, 'default', JREQUEST_ALLOWRAW);
  $hidden = JRequest::getString('hidden', null, 'default', JREQUEST_ALLOWRAW);
  $active =  JRequest::getInt('active',1); 
  $captcha =  JRequest::getInt('captcha',1); 
  $process =  JRequest::getInt('process',0); 
  
  $subject = JRequest::getString('subject',null);
  $confirmation = JRequest::getInt('confirmation',0);  
  $code1 =  "";
  $code2 =  "";
  $text_confirm_only = 0;
  
  $aftersending = JRequest::getInt('aftersending',0);
  $redirect = JRequest::getString('redirect',null);
  $custom_text = JRequest::getString('custom_text', null, 'default', JREQUEST_ALLOWRAW);

 
  $data_listing_confirmation = 1;
  $data_listing = JRequest::getInt('data_listing',1);
   

  //Customizing 
  /**
   * +++++++++++++++++++++++++++++++++++++
   * If adding new params you need to change
   * also the params in admin > 
   * includes/functions.php > getCustomizeParams();
   * +++++++++++++++++++++++++++++++++++++
   */
  $customize = new stdClass();
  $customize->submit_align = JRequest::getInt("submit_align",0);
  $customize->submit_text = JRequest::getString("submit_text",null);
  $customize->reset_text = JRequest::getString("reset_text",null);
  $customize->use_reset = JRequest::getInt("use_reset",1);
  $customize->metatitle = JRequest::getInt("metatitle",1);
  $customize->form_width = JRequest::getString("form_width",null);
  $customize->form_alignment = JRequest::getInt("form_alignment",0);
  
  //Serialize the customize object
  $serCustomize = bEnc(serialize($customize));
  
  // EOF customize
  
  
  // New FID
  if(isset($_REQUEST["fid"])){
  	$fid = intval( $_REQUEST["fid"] );
  }else $fid = null;
    

  
  
  
  
  
  if($active==null) $active = 1;
  $cid = m4jGetParam($_REQUEST, 'cid');
  $m4j_category = m4jGetParam($_REQUEST, 'm4j_category');
  if($m4j_category) $cid = $m4j_category;
  if ($cid==-2) $cid=-1;
  
  
  $legal_email = $validate->multipleEmail($email);

  $max_sort = null;
		$query = "SELECT MAX(sort_order) AS max_sort FROM #__m4j_jobs WHERE cid=".$cid;

		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		$max_sort = $rows[0]->max_sort;
  
$apply = 0;		
if("apply_new" == $task){
	$task = "new";
	$apply = 1;
}else if("apply" == $task){
	$task = "update";
	$apply = 1;
}		
  
switch($task){	
	case 'new':
		if($title!=null && ($legal_email|| $email==null) ){
			$insertId = MDB::insert("#__m4j_jobs",array(
				"title"=>dbEscape($title),
				"alias"=>dbEscape($alias),
				"hidden"=>dbEscape($hidden),
				"introtext"=>dbEscape($introtext),
				"maintext"=>dbEscape($maintext),
				"active"=>$active,
				"fid"=>$fid,
				"cid"=>$cid,
				"email"=>$email,
				"captcha"=>$captcha,
				"access"=>$access,
				"sort_order"=>($max_sort+1),
				"process"=>$process,
				"subject"=>dbEscape($subject),
				"text_confirm_only"=>$text_confirm_only,
				"confirmation"=>$confirmation,
				"code1"=>"",
				"code2"=>"",
				"aftersending"=> $aftersending,
			    "redirect"=> dbEscape($redirect),
				"custom_text"=> dbEscape($custom_text),
				"is_paypal"=>0,
				"is_sandbox"=>0,
				"paypal"=> "",
				"data_listing_confirmation"=>$data_listing_confirmation,
				"data_listing"=>$data_listing,
				"is_optin"=> 0,
				"optin_params"=> "",
				"customize" => $serCustomize	
			));
			// Create SEF URL
			$sef = new MSEF($title,$alias,$insertId,$cid);
			$sef->insert();
			// Redirect
			if($apply){
				m4jRedirect(M4J_JOBS_NEW."&id=".$insertId."&cid=".$cid.M4J_REMEMBER_CID_QUERY.M4J_HIDE_BAR.M4J_EDIT."&tab=".$tab);
			}else{
				m4jRedirect(M4J_JOBS.M4J_REMEMBER_CID_QUERY);
			}
		}else{
			if(!$legal_email && $email!=null) $error = M4J_LANG_NONE_LEGAL_EMAIL;
			if(!$title) $error .= M4J_LANG_ERROR_NO_TITLE;
		}	
	break;
	//EOF NEW

	case 'edit':
		if($id>=0)
		{
			$query = "SELECT * FROM `#__m4j_jobs` WHERE `jid` = '".$id."' ";
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			
			$title = MReady::_($rows[0]->title) ;
			$alias = MReady::_($rows[0]->alias) ;
  			$email = $rows[0]->email ;
	  	    $introtext =  $rows[0]->introtext ;
			$maintext =  $rows[0]->maintext ;
			$hidden = $rows[0]->hidden ;
			$fid = $rows[0]->fid ;
			$active = $rows[0]->active ;
			$cid = $rows[0]->cid ;
			$captcha = $rows[0]->captcha;
			$access = $rows[0]->access;
			$process = $rows[0]->process;
			$subject = MReady::_($rows[0]->subject);
			$text_confirm_only = $rows[0]->text_confirm_only;
	  	    $confirmation = $rows[0]->confirmation; 
		    $code1 = "";
		    $code2 = ""; 
		    $aftersending = $rows[0]->aftersending;
		    $redirect = $rows[0]->redirect;
		    $custom_text = $rows[0]->custom_text;
		    $is_paypal = 0;
		    $is_sandbox = 0;
		    $payPalPack = null; 
  			$data_listing_confirmation = $rows[0]->data_listing_confirmation;
  			$data_listing = $rows[0]->data_listing;
  			  			
  			// unserialise for customize
  			$unserCustomize =  unserialize(bDec($rows[0]->customize)) ; 
  			if(is_object($unserCustomize)){			
  				foreach($unserCustomize as $key => & $value){
  					$customize->$key = $value;
  				}
  			}
  			
  			
		}
	break;
	//EOF EDIT

	case 'update':
		$former_cid =  JRequest::getInt("former_cid",0);
		
		if( trim($title) && ($legal_email || ! trim($email) ) ){				
			$database->setQuery("SELECT `title`,`alias` FROM `#__m4j_jobs` WHERE `jid` = '".(int) $id."' LIMIT 1");
			$old = $database->loadObject();	
			$newSef = false;
			if($alias){
				if($alias != $old->alias)$newSef = true;
			}else{
				if($title != $old->title)$newSef = true;
		}
		
		// CID EQUALS FORMER CID	
		if($former_cid==$cid){		
			MDB::update("#__m4j_jobs",array(
				"title"=>dbEscape($title),
				"alias"=>dbEscape($alias),
				"hidden"=>dbEscape($hidden),
				"introtext"=>dbEscape($introtext),
				"maintext"=>dbEscape($maintext),
				"active"=>$active,
				"fid"=>$fid,
				"cid"=>$cid,
				"email"=>$email,
				"captcha"=>$captcha,
				"access"=>$access,
				"process"=>$process,
				"subject"=>dbEscape($subject),
				"text_confirm_only"=>$text_confirm_only,
				"confirmation"=>$confirmation,
				"code1"=>"",
				"code2"=>"",
				"aftersending"=> $aftersending,
			    "redirect"=> dbEscape($redirect),
				"custom_text"=> dbEscape($custom_text),
				"is_paypal"=>0,
				"is_sandbox"=>0,
				"paypal"=>"",
				"data_listing_confirmation"=>$data_listing_confirmation,
				"data_listing"=>$data_listing,
				"is_optin"=> 0,
				"optin_params"=> "",
				"customize" => $serCustomize	
			),MDB::_("jid",$id));
			
			// Update SEF URL by changes
			if($newSef){
				$sef = new MSEF($title,$alias,$id,$cid);
				$sef->update();
			}
			
			// Redirect
			if($apply){
				m4jRedirect(M4J_JOBS_NEW."&id=".$id."&cid=".$cid.M4J_REMEMBER_CID_QUERY.M4J_HIDE_BAR.M4J_EDIT."&tab=".$tab);
			}else{
				m4jRedirect(M4J_JOBS.M4J_REMEMBER_CID_QUERY);
			}
		}else{
			MDB::update("#__m4j_jobs",array(
				"title"=>dbEscape($title),
				"alias"=>dbEscape($alias),
				"hidden"=>dbEscape($hidden),
				"introtext"=>dbEscape($introtext),
				"maintext"=>dbEscape($maintext),
				"active"=>$active,
				"fid"=>$fid,
				"cid"=>$cid,
				"email"=>$email,
				"captcha"=>$captcha,
				"access"=>$access,
				"sort_order"=>($max_sort+1),
				"process"=>$process,
				"subject"=>dbEscape($subject),
				"text_confirm_only"=>$text_confirm_only,
				"confirmation"=>$confirmation,
				"code1"=>"",
				"code2"=>"",
				"aftersending"=> $aftersending,
			    "redirect"=> dbEscape($redirect),
				"custom_text"=> dbEscape($custom_text),
				"is_paypal"=>0,
				"is_sandbox"=>0,
				"paypal"=>"",
				"data_listing_confirmation"=>$data_listing_confirmation,
				"data_listing"=>$data_listing,
				"is_optin"=> 0,
				"optin_params"=> "",
				"customize" => $serCustomize	
			),MDB::_("jid",$id));	
				
			// Refactor sort_order of Old Category Items
			MDB::refactorOrder("#__m4j_jobs", MDB::_("cid",$former_cid), "jid");
			
			// Update SEF URL
			$sef = new MSEF($title,$alias,$id,$cid);
			$sef->update();
			
			// Update link
			MDB::update("#__menu",array(
			"link"=>"index.php?option=com_proforms&jid=".$id."&cid=".$cid
			), MDB::_("link","index.php?option=com_proforms&jid=".$id."&cid=".$former_cid));
			
			
		}
		// Redirect	
		m4jRedirect(M4J_JOBS.M4J_REMEMBER_CID_QUERY);
	}else{
	  if(!$legal_email && $email!=null) $error = M4J_LANG_NONE_LEGAL_EMAIL;
	  if(!$title) $error .= M4J_LANG_ERROR_NO_TITLE;
	  define("M4J_EDITFLAG",1);
	}
	break;
	//EOF UPDATE
}	

  	   
HTML_m4j::head(M4J_JOBS_NEW,$error);
  
	if(M4J_EDITFLAG==1){
		$heading = M4J_LANG_EDIT_FORM;
	  	$right=null;
	  	$breadcrumbs = M4J_LANG_FORMS.' > '.M4J_LANG_EDIT;
	}else{
	  	$heading = M4J_LANG_NEW_FORM;
	  	$right=null;
	  	$breadcrumbs = M4J_LANG_FORMS.' > '.M4J_LANG_NEW_FORM;
	}
  
  	// Get Category Names
	$query = "SELECT cid,name FROM #__m4j_category ORDER BY sort_order ASC";
	$database->setQuery( $query );
	$categories = $database->loadObjectList();
			
	// Get Template Names
	$query = "SELECT `fid`,`name`,`description` FROM #__m4j_forms  ORDER BY name ASC";
	$database->setQuery( $query );
	$templates = $database->loadObjectList(); 
	
	// Create FID Array !
	if($fid){
		$fidArray = explode(";",$fid);
	
		foreach($fidArray as $f){
			if($f == 0 ){
				unset($f);
				continue;
			}
		}
	}//EOF if $fid 
	else{
		$fidArray = array();
	}		
			

		$newTemplates = array();
		foreach($templates as $template){
				$simple = new MSimpleDataObject();
				$simple->add("fid",$template->fid);
				$simple->add("name",MReady::_($template->name));
				$simple->add("description",MReady::_($template->description));
				
				$usermail = 0;
				if(MDB::hasUserMail((int) $template->fid) == 1){
					$usermail = 1;
				}
				$simple->add("usermail",$usermail);
				array_push($newTemplates,$simple);
			}		
	

     
   $args = array(
   		"tab"=>$tab,
	   	"editID"=>$id,
   		"heading"=>$heading,
   	    "feedback"=>$right,
   	 	"breadcrumbs"=>$breadcrumbs,
	   	"categories"=>$categories,
	   	"templates"=>$newTemplates,
	   	"title"=>$title,
	   	"alias"=>$alias,
	   	"email"=>$email,
   		"subject"=>$subject,
   		"text_confirm_only"=>$text_confirm_only,
	   	"active"=>$active,
	   	"captcha"=>$captcha,
   		"access"=>$access,
	   	"introtext"=>$introtext,
	   	"maintext"=>$maintext,
	   	"cid"=>$cid,
	   	"fid"=>$fidArray,
	   	"hidden"=>$hidden,
	   	"process"=>$process,
   		"confirmation"=> $confirmation,
   		"code1"=> null,
   		"code2"=> null,
   		"aftersending"=> $aftersending,
   		"redirect"=> $redirect,
   		"custom_text"=> $custom_text,
   		"is_paypal"=>0,
   		"is_sandbox"=>0,
   		"paypal"=>null,
		"data_listing_confirmation"=>$data_listing_confirmation,
		"data_listing"=>$data_listing,
   		"customize" => $customize
   
   );
   
   echo MTemplater::get(M4J_TEMPLATES."new_job.php",$args);

HTML_m4j::footer();
