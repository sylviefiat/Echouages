<?php
/** @name MOOJ Proforms
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

defined('_JEXEC') or die;


class MVersion{
	
	public static $major = 1;
	public static $minor = 6;
	public static $patch = 0;
	public static $stage = "";
	
	public static $build = 150;
	
	public static function get(){
		return self:: $major . "." . self::$minor . "." .  self::$patch . ( self::$stage ? ' '.self::$stage : '');
	}
	
	public static function getFull(){
		return self:: $major . "." . self::$minor . "." .  self::$patch . ( self::$stage ? ' '.self::$stage : '') . " | Build ".self::$build;
	}
		
	public static function thisReleaseDate(){
		return '2016-08-31';
	}
	
	public static function getAuthor(){
		return 'Dipl. Informatiker(FH) Fahrettin Kutyol';
	}
	
	public static function getCopyright(){
		return '&copy; 2008 - '.date("Y") . ' <a href="http://www.mad4media.de">' ."Mad4Media</a>";
	}
	
	public static function getFirstRelease(){
		return '2008-08';
	}
	
	public static function getLicense(){
		return 'GNU/GPL V2.0 &amp; GPL Friendly Media License (GFML)';
	}
}


?>