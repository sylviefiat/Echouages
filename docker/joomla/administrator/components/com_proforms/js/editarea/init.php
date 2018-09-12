<?php
/**
* @name MOOJ Proforms 
* @version 1.0
* @package proforms
* @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
* @author Dipl. Inf.(FH) Fahrettin Kutyol
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Please note that some Javascript files are not under GNU/GPL License.
* These files are under the mad4media license
* They may edited and used infinitely but may not repuplished or redistributed.  
* For more information read the header notice of the js files.
**/
	defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 
	
	$id = (isset($_REQUEST['id']))? addslashes(strip_tags($_REQUEST['id'])):null;
	$syntax = (isset($_REQUEST['syntax']))? addslashes(strip_tags($_REQUEST['syntax'])):null;
	$lang = (isset($_REQUEST['lang']))? addslashes(strip_tags($_REQUEST['lang'])):'en';
	if( !$id || !$syntax) exit;
?>
editAreaLoader.init({
			id : "<?php echo $id; ?>"		
			,syntax: "<?php echo $syntax; ?>"
			,allow_resize: false
			,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
			,language: "<?php echo $lang; ?>"
			,start_highlight: true		
			});

addEditArea('<?php echo $id; ?>');