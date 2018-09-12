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





class PFormGroup extends stdClass{
	protected $identifier = '';
	public $title = "";
	public $desc = "";
	public $image = null;
	
	public function __construct($ident = '', $title = "", $desc = "", $image = null){
		$this->identifier = $ident;
		$this->title = $title;
		$this->desc = $desc;
		$this->image = $image;
	}
}


class PText{
	public static $formElements = array();
	
	public static $formGroups = array();
	
	
	protected static function createGroup($ident = null, $title = null, $desc = null, $image = null){
		$ident = strtolower(trim($ident));
		if(!$ident) return false;
		self::$formGroups[$ident] = new PFormGroup($ident, $title, $desc, $image);
	}
	
	/**
	 * 
	 * @param string $ident
	 * @param string $default
	 * @return PFormGroup
	 */
	public static function group($ident = null, $default = null){
		$ident = strtolower(trim($ident));
		return isset(self::$formGroups[$ident]) ? self::$formGroups[$ident] : new PFormGroup($ident, $default, $default);
	}
	
	public static function field($id = 0, $default = null){
		$id = (int) $id;
		return isset(self::$formElements[$id]) ? self::$formElements[$id] : $default;
	}
	
	
	public static function init($formElements = array()){
		self::$formElements = $formElements;
		
		self::createGroup('switch', M4J_LANG_CHECKBOX, M4J_LANG_CHECKBOX_DESC, '');
		self::createGroup('date', M4J_LANG_DATE, M4J_LANG_DATE_DESC, '');
		self::createGroup('textfield', M4J_LANG_TEXTFIELD, M4J_LANG_TEXTFIELD_DESC, '');
		self::createGroup('options', M4J_LANG_OPTIONS, M4J_LANG_OPTIONS_DESC, '');
		self::createGroup('attachment', M4J_LANG_ATTACHMENT, M4J_LANG_ATTACHMENT_DESC, '');
		self::createGroup('displayonly', M4J_LANG_DISPLAYONLY, M4J_LANG_DISPLAYONLY_DESC, '');

	}
	
	
	
}

PText::init($GLOBALS["m4j_lang_elements"]);