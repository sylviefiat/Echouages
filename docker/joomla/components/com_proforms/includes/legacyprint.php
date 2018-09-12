<?php
/**
 * @name MOOJ Proforms
 * @version 1.5
 * @package proforms
 * @copyright Copyright (C) 2008-2013 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


class ProformsLegacyPrint {
	
	
	public function __construct(){
		return;
	}
	
	public function add_error($error = null){
		return ProformsHelper::errorTag($error);
	}
	
	
	
	
	public static function & getInstance(){
		static $instance;
		if(empty($instance)){
			$instance = new ProformsLegacyPrint();
		}
		return $instance;
	}
	
}

/* @var $GLOBALS['print'] ProformsLegacyPrint  */
$GLOBALS['print'] = ProformsLegacyPrint::getInstance();

function endsWith($H, $N){ return substr($H, strlen($N)*-1) == $N;}