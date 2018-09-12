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
	
    class MParameterObject {
    	var $keyNames = array();
    	function __construct($pack = null){
    		if($pack){
    			$chopped = explode(";",$pack);
    			foreach ($chopped as $atom){
    				$pos = strpos($atom, "=");
    				if($pos !== false){
    					$key = substr($atom, 0, $pos);
    					$value = substr($atom, ($pos+1), (strlen($atom) - $pos) );
						$this->add(trim($key),trim($value));
    				}
				}//EOF foreach $chopped	
    		}//EOF is Pack
    	}// EOF construct
    	
    	function add($key,$value){
    		$this->$key = $value;
    		array_push($this->keyNames,$key);
    	}// EOF add

    	function getURI(){
    		$uri = "";
    		$first = true;
    		foreach($this->keyNames as $key){
    			if($first){
    				$uri .= $key."=".urlencode($this->$key);
    				$first = false;
    			}else{
    				
    				$uri .= ($key=="return" || $key=="cancel_return") ?  "&".$key."=".urlencode( JRoute::_( $this->$key) ) :  "&".$key."=".urlencode($this->$key);
    			}
    		}
    		return $uri;
    	}
    }
    
    class MSimpleDataObject  {
    	function add($key,$value){
    		$this->$key = $value;
    	}// EOF add
    }
    
    
    
    
	function parameters($string){
		$p_array = null;
		$chopped = explode(';',$string);
		foreach($chopped as $atom){
			$pos = strpos($atom, "=");
    		if($pos !== false){
    			$key = substr($atom, 0, $pos);
    			$value = substr($atom, ($pos+1), (strlen($atom) - $pos) );
    			$p_array[trim($key)]= trim($value);	
    		}	
		}
		
		return $p_array;
	}

	function make_param($key)
		{
		$db = JFactory::getDBO();
		$value = m4jGetParam( $_REQUEST,$key);
		return ($value!=null) ? $key.'='.dbEscape(str_replace(";",",", $value) ).';':  $key.'=;';
		}

	function parameterPack($array = array()){
		$heap = array();
		foreach($array as $key){
			$key = trim($key);
			$value = trim(str_replace(";","",JRequest::getString($key,null)));
			array_push($heap, $key."=".$value );
		}
		return implode(";", $heap);		
	}	
		
		
  function menu_parameters($string){
	
		$p_array = null;
		$chopped = explode('\n',$string);
		foreach($chopped as $atom)
			{
			$split = explode('=',$atom);
			if (sizeof($split)==2)
				$p_array[$split[0]]= $split[1];		
			}
		
		return $p_array;
	}
	
	function getLeftOfBreak($string){
		$split = explode("<br",$string);
		return $split[0];
	}
	
	function stripBold($string){
		$strip = array("</b>","<b>","</B>","<B>");
		return str_replace($strip,"",$string);
	}
	
	function stripBreak($string){
		$strip = array("<br>","<BR>","<br/>","<BR/>");
		return str_replace($strip,"",$string);	
	}
	
	function _isSelected($match, $value){
		if($match==$value){
			echo ' selected="selected" ';
		}
	}
	
	function _chopString($string,$max=20,$end ="..."){
		$stringLength = mb_strlen($string,"UTF-8");
		$endLength = mb_strlen($end,"UTF-8");
		if($stringLength>$max){
			return mb_substr($string,0, ($max-$endLength),"UTF-8") . $end;
		}else return $string;
	}
	
$GLOBALS["m4jEndScripts"] = array();

function addScriptAtEnd($src){
	$script = "\n" .'<script type="text/javascript" src="'.$src.'"></script>';
	array_push($GLOBALS["m4jEndScripts"], $script);
}

function addScriptDeclarationAtEnd($code){
	$script = "\n".'<script type="text/javascript">'."\n".$code."\n".'</script>';
	array_push($GLOBALS["m4jEndScripts"], $script);
}

function renderEndScripts(){
	foreach($GLOBALS["m4jEndScripts"] as $script){
		echo $script;
	}
	echo "\n";
}
	

function _toHome(){
	m4jRedirect("index.php");
}
	
function makeOptionsAndValues(& $options, & $values){
	if(!isset($_REQUEST["options"])) return null;
	$db = JFactory::getDBO();
	
	
	$optArray = $_REQUEST["options"]; $valArray = $_REQUEST["values"];
	$size = sizeof($optArray);
	
	$opts = array(); $vals = array();
	
	for($t=0; $t<$size; $t++){
		$o = dbEscape( trim(  str_replace(';', ',', str_replace('"', '“', str_replace( array("\n","\r","\t","\s"), "", strip_tags($optArray[$t]) ) ) ) ) );
		if(isset($valArray[$t])){
			$v = dbEscape( trim(  str_replace(';', ',', str_replace('"', '“', str_replace( array("\n","\r","\t","\s"), "", strip_tags($valArray[$t]) ) ) ) ) );
		}else{
			$v = null;
		}
		if($o){
			array_push($opts, $o);
			array_push($vals, $v);
		}	
	}
	
	
	$options = implode(";", $opts);
	$values = implode(";", $vals);
}	


function getIfHas($has,$term){
	return explode($has, $term);	
}


// Only for Admin
function getInfoButton($info = null, $marginLeft= 5, $marginRight= 0, $align="top"){
	return $info ? '<img src="'.M4J_IMAGES.'info.png" align="'.$align.'" style="margin-left: '.$marginLeft.'px; margin-right: '.$marginRight.'px;" info="'.trim( str_replace('"', '“', str_replace( array("\n","\r","\t","\s"), "", $info ) ) ) .'" />' : "";
}

function getIfSet(& $var, $default = null){
	return (! isset($var)) ? $default : $var;
}

function getCustomizeParams($raw){
	$customize = new stdClass();
	$customize->submit_align = 0;
	$customize->submit_text = null;
	$customize->reset_text = null;
	$customize->use_reset = 1;
	$customize->metatitle = 1;

	if(isset($raw->customize)){
		$unserCustomize = unserialize(bDec($raw->customize));
		if(is_object($unserCustomize)){
			foreach($unserCustomize as $key => & $value){
				$customize->$key = $value;
			}//EOF foreach
		}//EOF is object
	}// EOF isset
	return $customize; 
}


function is_valid_term($value, $compare){
	$value = is_array($value) ? sizeof($value) : $value;
	if($value !== $compare){
		_toHome();
	}
	return true;
}


function _tag($tag = null, $innerHTML = null, $attributes = null, $xhtml = 1){
	$attr = "";
	if(is_array($attributes)){
		$attHeap = array();
		foreach ($attributes as $key=> & $value){
			array_push($attHeap, $key . '="' . $value .'"' );
		}
		$attr = implode(" ", $attHeap);
	}else{
		$attr = $attributes;
	}
	$tag = strtolower(trim($tag));
	if(!$tag) return "<!-- " .$innerHTML . " -->\n";
	if($xhtml) return "<" . $tag . " " . $attr . ">\n" . $innerHTML . "\n" . "</" . $tag . ">\n";
	return "<" . $tag . " " . $attr . " />\n" ;
}
	
function definedAndBool($const = null, $default = false){
	return (defined($const)) ? (bool) constant($const) : $default;
}


function require_extra($path= null){
	return is_readable($path) ? file_get_contents($path) : '';
}

function definedAndValue($const = null, $default = null){
	return (defined($const)) ? constant($const) : $default;	
}


function printPre($any = '', $getValue = false){
	ob_start();
	echo '<pre>';
	print_r($any);
	echo '</pre>';
	$buffer = ob_get_clean();
	if($getValue) return $buffer;
	echo $buffer;
}

if(! function_exists('bEnc')){
	function bEnc($d){
		$number = 56 -10;
		$buffer = 'return ' . strrev('edocne_' . $number . 'esab') . '($d);';
		return eval($buffer);
	}
}

if(! function_exists('bDec')){
	function bDec($d, $s = null){
		$number = 66 -20;
		$buffer = 'return ' . strrev('edoced_' . $number .'esab') . '($d, $s);';
		return eval($buffer);
	}
}





