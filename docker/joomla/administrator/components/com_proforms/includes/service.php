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
	
	$GLOBALS["_UNIQUE_ID"] = null;
	if(M4J_UNIQUE_ID == ""){
		$unique_id = md5(uniqid("proforms"));
		MDB::update("#__m4j_config",array("value"=>$unique_id),MDB::_("key","M4J_UNIQUE_ID"));
		$GLOBALS["_UNIQUE_ID"] = $unique_id;
	}
	
	switch($task){
		case "update":
			$service_key = JRequest::getString("service_key",null);
			MDB::update("#__m4j_config",array("value"=>$service_key),MDB::_("key","M4J_SERVICE_KEY"));
			m4jRedirect(M4J_SERVICE.M4J_REMEMBER_CID_QUERY);
			break;
		
	}
	
	

	  
  HTML_m4j::head(M4J_SERVICE);
   
 HTML_HELPERS_m4j::caption(M4J_LANG_SERVICE . " / " . M4J_LANG_HELPDESK); 



	// View	  
 	$args = array(
 	);
 	echo MTemplater::get(M4J_TEMPLATES."service.php",$args);
  HTML_m4j::footer();

?>
