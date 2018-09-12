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

class MTemplater{

	public static function get($filePath,$arg){
		if (!file_exists($filePath) || is_dir($filePath)){
			if(isset( $arg['content']) && $arg['content']) return $arg['content'];			
			else return null;
		} else{
			foreach($arg as $key=>$value){
				$$key = $value;
			}
			ob_start();
			include($filePath);
			return ob_get_clean();			
		}
	}//EOF get
}//EOF Class MTemplater

?>