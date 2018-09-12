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


class MDebug{
	protected static $buffer = '';
	
	public static function _($args){
		$args = func_get_args();
		for ($i = 0; $i < func_num_args(); $i++) {
			if(is_object($args[$i]) || is_array($args[$i])){
				self::pre($args[$i]);
			}else self::$buffer .= $args[$i]."<br>";
		}
	}

	public static function pre($args){
		
		self::$buffer.= '<pre>';
		ob_start();
		$args = func_get_args();
		
		for ($i = 0; $i < func_num_args(); $i++) {
			print_r($args[$i]);
			echo "\n";
		}
		self::$buffer .= wordwrap( htmlspecialchars(ob_get_clean()) , 110, "\n", true);
		self::$buffer .= '</pre><br>';
	}

	public static function multiPre($args){
	   $args = func_get_args();
	   
	   for ($i = 0; $i < func_num_args(); $i++) {
	       self::pre($args[$i] );
        }
	}
	
	
	public static function preSpecialChars($obj){
		self::$buffer.= '<pre>';
		ob_start();
		print_r($obj);
		self::$buffer .= htmlspecialchars ( ob_get_clean());
		self::$buffer .= '</pre><br>';
	}

	public static function xhrOut(){

		$buffer = str_replace(array('<pre>'), "\n" , html_entity_decode(self::$buffer) );
		$buffer = str_replace(array( '</pre>'), "\n" . '------------------------------------' . "\n", $buffer );
		$buffer = str_replace(array('<br>', '<br/>'), "\n", $buffer);
		return 'XHR DEBUG:' . "\n" . $buffer;
	}
	
	
	public static function out($echo = false){
		$out = '<div style="clear:both;"></div>'."\n";
		$out .= '<center><div style="display:block;width:90%; text-align:left; padding:10px; color:black; border:1px solid #343434; background-color: #efefef; margin:10px;">'."\n";
		$out .= '<h3>Debug:</h3>'."\n";
		$out .= self::$buffer;
		$out .= '</div></center>'."\n";
		if($echo) echo $out;
		else return $out;
	}

}


class MReady{

	public static function _($string){
		return str_replace('"',"&quot;",stripslashes($string) );
	}

	public static function change(& $string){
		$string = str_replace('"',"&quot;",stripslashes($string) );
	}
}