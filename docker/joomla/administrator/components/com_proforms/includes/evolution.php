<?PHP
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

// These are the parameters and function which are needed for dual development

$temp = new JConfig;
foreach (get_object_vars($temp) as $k => $v) {
	$name = 'm4jConfig_'.$k;
	$GLOBALS[$name] = $v;
}

$lang = JFactory::getLanguage();
$langTag = explode("-", $lang->getTag());

$GLOBALS['m4jConfig_live_site']	= (getenv('HTTPS') == 'on') ? substr_replace( str_replace("http://", "https://", JURI::root() ), '', -1, 1) : substr_replace(JURI::root(), '', -1, 1);
$GLOBALS['m4jConfig_lang'] = trim($langTag[0]);
$GLOBALS['database'] =  JFactory::getDBO();
DEFINE( "_M4J_NOTRIM", 0x0001 );
DEFINE( "_M4J_ALLOWHTML", 0x0002 );
DEFINE( "_M4J_ALLOWRAW", 0x0004 );

function m4jGetParam( &$arr, $name, $def=null, $mask=0 )
{
	// Static input filters for specific settings
	static $noHtmlFilter	= null;
	static $safeHtmlFilter	= null;

	$var = JArrayHelper::getValue( $arr, $name, $def, '' );

	// If the no trim flag is not set, trim the variable
	if (!($mask & 1) && is_string($var)) {
		$var = trim($var);
	}

	// Now we handle input filtering
	if ($mask & 2) {
		// If the allow html flag is set, apply a safe html filter to the variable
		if (is_null($safeHtmlFilter)) {
			$safeHtmlFilter = & JFilterInput::getInstance(null, null, 1, 1);
		}
		$var = $safeHtmlFilter->clean($var, 'none');
	} elseif ($mask & 4) {
		// If the allow raw flag is set, do not modify the variable
		$var = $var;
	} else {
		// Since no allow flags were set, we will apply the most strict filter to the variable
		if (is_null($noHtmlFilter)) {
			$noHtmlFilter = & JFilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
		}
		$var = $noHtmlFilter->clean($var, 'none');
	}
	return $var;
}

function MEditorArea($name = null, $content = null, $hiddenField = null, $width = "100%", $height = "100%", $col = null, $row = null){
	echo GetMEditorArea($name, $content,  $hiddenField, $width, $height, $col, $row);
}

function GetMEditorArea($name = null, $content = null, $hiddenField = null, $width = "100%", $height = "100%", $col = null, $row = null){
	jimport( 'joomla.html.editor' );
	
	// only for old Joomla 1.5
	if(!  _M4J_IS_J16 ){
		$editor = JFactory::getEditor();
		return $editor->display($hiddenField, $content, $width, $height, $col, $row);
	}
	
	$conf = JFactory::getConfig();	
	$editorName = $conf->get('editor');
	
	if( defined("M4J_EDITOR") && trim(M4J_EDITOR)){
		MDebug::pre("EDITOR PROCESS");
		$db = JFactory::getDbo();
		$editorName = $db->escape(trim(M4J_EDITOR));
		$db->setQuery("SELECT COUNT(*) AS is_editor FROM `#__extensions` WHERE `type` = 'plugin' AND `folder` = 'editors' AND `enabled` = 1 AND `element` = '$editorName';");
		$edt = $db->loadObject();
		if($edt->is_editor){
			$editorName = trim(M4J_EDITOR);
		}else{
			$editorName = JFactory::getConfig()->get('editor');
		}
	}
	
	$editor = JEditor::getInstance($editorName);
	return  $editor->display($name, $content, $width, $height, $col, $row) . '<div class="m4jCLR"></div>';
}


function m4jRedirect( $url, $msg='' ) {	
	$app = new JApplication();
	$app->redirect($url,$msg);

}

function m4jCreateMail( $from='', $fromname='', $subject, $body ) {

	$mail =JFactory::getMailer();

	$mail->From 	= $from ? $from : $mail->From;
	$mail->FromName = $fromname ? $fromname : $mail->FromName;
	$mail->Subject 	= $subject;
	$mail->Body 	= $body;

	return $mail;
}

function dbEscape($sql = null){
	static $db = null;
	if(!$db) $db = JFactory::getDbo();
	return _M4J_IS_J30 ? $db->escape($sql) : $db->getEscaped($sql);
}

function articleButton($text= null, $onClick = null){
	if(_M4J_IS_J30){
		return '<a style="margin-top:3px;" class="btn" onclick="javascript: '.$onClick.'">'.$text.'</a>';
	}else{
		return '<div class="button2-left">
					<div class="blank">
						<a onclick="javascript: '.$onClick.'">'.$text.'</a>
					</div>
				</div>';
	}
}

function if30($code = null, $else = null){
	return (_M4J_IS_J30) ? $code : $else;
}

function ifnot30($code = null, $else = null){
	return (_M4J_IS_J30) ? $else : $code;
}

 if (!function_exists('mb_strlen')){
 	
 	// create mb_strlen like function if mb_strlen doesn't exist
	 function mb_strlen($str, $iso = "UTF-8"){
	 	
	 	if(strtoupper($iso) == "UTF-8" || strtoupper($iso) == "UTF8"  ){
	 	$count = 0;
	    for($i = 0; $i < strlen($str); $i++){
	        $value = ord($str[$i]);
	        if($value > 127){
	            if($value >= 192 && $value <= 223)
	                $i++;
	            elseif($value >= 224 && $value <= 239)
	                $i = $i + 2;
	            elseif($value >= 240 && $value <= 247)
	                $i = $i + 3;
	            else
	                return strlen($str);
	            }
	      
	        $count++;
	        }
	  
	    return $count;
	    }else return strlen($str);
	 }
 	
 }

if (!function_exists('mb_substr')){

	 // create mb_substr like function if mb_substr doesn't exist
	 function mb_substr($str,$from = 0, $len = 0, $iso = "UTF-8"){
		 if(strtoupper($iso) == "UTF-8" || strtoupper($iso) == "UTF8"  ){
			return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $from .'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $len .'}).*#s','$1', $str);
		 }else return substr($str,$from,$len);
	 }//EOF function 
}//EOF mb_substr doesn't exist

global $m4jConfig_lang,$m4jConfig_live_site,$database;

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

