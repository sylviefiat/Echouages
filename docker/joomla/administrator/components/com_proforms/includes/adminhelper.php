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

class ProformsStyleObject{
	
	protected $_styles = array();
	
	public function __construct($string = null){
		$this->_string2Object($string);
	}
	
	
	protected function _string2Object($string = null){
		if(!  $string = trim($string)) return;
		$styles = explode(";", $string);
		foreach ($styles as $item){
			$split = explode(":", trim($item) );
			if(sizeof($split) === 2 ){
				$this->_styles[trim($split[0])] = trim($split[1]);
			}
		}
	}
	
	public function reset($string = null){
		$this->_styles = array();
		$this->_string2Object($string);		
	}
	
	public function get($styleName = false, $default = null){
		if($styleName === false && $default === null){
			$return = $this->_styles;
			settype($return, 'object');
			return $return;
		}
		
		return isset($this->_styles[trim($styleName)]) ? $this->_styles[trim($styleName)] : $default;
	}
	
	public function set($styleName = false , $value = null){
		if(!$styleName === false) return;
		if( is_array($styleName)){
			foreach ($styleName as $style => $val){
				$this->set($style, $val);
			}
			return;
		}
		$this->_styles[trim($styleName)] = $value;
	}
	
	
	public function addString($styleName = false , $value = null){
		if(!$styleName === false) return;
		if(isset($this->_styles[trim($styleName)] ) ) {
			$this->_styles[trim($styleName)] .= $value;	
		}
	}
	
	public function getFormatted($styleName = false, $default = null){
		return isset($this->_styles[trim($styleName)]) ? trim($styleName).':'.$this->_styles[trim($styleName)] . ";" : $default;
	}
		
	public function __toString(){
		return empty($this->_styles) ? '' : implode(";", $this->_styles) . ';' ;
	}
	
	/**
	 * 
	 * @param string $string
	 * @return ProformsStyleObject
	 */
	public static function getInstance($string = null){
		return new ProformsStyleObject($string);
	}
	
}

/**
 * The main admin helper
 */

class ProformsAdminHelper{
	protected static $loaded = array();
	
	
	public static function loadColorPicker(){
		
		if(self::isLoaded('loadColorPicker')) return;
		
		$document=JFactory::getDocument();
		$document->addScript(M4J_JS.'jscolor/jscolor.js');
	}
	
	protected static function isLoaded($namespace = null){
		if(!$namespace || isset(self::$loaded[$namespace])) return true;
		self::$loaded[$namespace] = 1;
		return false;
	}
	
	public static function xhrExit( $buffer = null){
		ob_get_clean();
		ob_get_clean();
		die($buffer);
	}
	
	public static function setComponentView(){
		JRequest::setVar('tmpl', 'component');
		
	}
	
}






