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
// Display config	  
HTML_m4j::head(M4J_BACKUP);
 	$args = array("heading"=>M4J_LANG_BACKUP, "error"=>"" , "sql"=>"", "message" => NULL);
 	echo MTemplater::get(M4J_TEMPLATES."backup.php",$args);
HTML_m4j::footer();
