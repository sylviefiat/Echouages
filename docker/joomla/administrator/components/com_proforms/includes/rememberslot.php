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

class RememberSlot{
	protected  static $fid = 0;
	protected static $mainKey = 'ProformsRememberSlot4Fid_';
	public static function set($slot = 1){
		$key = self::$mainKey . self::$fid ;
		$app = JFactory::getApplication();
		$app->setUserState($key, $slot);
	}

	public static function get($default = null){
		$key = self::$mainKey . self::$fid ;
		$app = JFactory::getApplication();
		return $app->getUserState( $key , $default);
	}

	public static function setFid($fid = 0){
		self::$fid = (int) $fid;
	}

}