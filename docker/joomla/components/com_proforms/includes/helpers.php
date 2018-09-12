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

class ProformsHelper {
	
	public static function cleanPath($filePath = null){
		return ltrim( preg_replace('/\.[\.]+/', '', trim((string) $filePath)), '\.') ;
	}
	
	public static function cleanFilenameForUpload($filePath){
		$fileName = self::cleanPath( urldecode($filePath) );
		$fileName = preg_replace('/[^\w\.\[\]\^\+=\-_! @\#\$%&*\(\)]/', '_', $fileName );
		return $fileName;
	}

	public static function spambottrap1(){		
		$spambot_trap = "3c646976207374796c653d22646973706c61793a6e6f6e653b223e47656e6572617465642077697468204d4f4f4a2050726f666f726d732042617369632056657273696f6e20312e363c2f6469763e";
		$sec = ''; for($i=0;$i<strlen($spambot_trap );$i+=2){ $sec.=chr(hexdec(substr($spambot_trap ,$i,2))); }
		echo stripslashes($sec);
	}
	
	public static function getLang(){
		static $lang;
		if(empty($lang)){
			$_lang = JFactory::getLanguage();
			$langTagArr = explode("-", $_lang->getTag());
			if( $l = trim($langTagArr[0])) $lang = strtolower($l);
			else $lang = 'en';
		}
		return $lang;
	}
	
	public static function access($required = 0, $plattform = "1.5"){
		if(_M4J_IS_J16) $plattform = "1.6";
		if(_M4J_IS_J30) $plattform = "3.0";
		$user = JFactory::getUser();
		switch($plattform){
			default:
			case "1.5":
				if($required == 0) return true;
				if($user->usertype){
					return true;
				}else{
					return false;
				}
				break;
	
			case "1.6":
				$groups = $user->authorisedLevels();
				foreach ($groups as $group){
					if($group == $required) return true;
				}
				return false;
				break;
	
	
			case "3.0":
				$groups = $user->getAuthorisedViewLevels();
				foreach ($groups as $group){
					if($group == $required) return true;
				}
				return false;
				break;
		}//EOF switch
	}//EOF access
	
	 
	public static function validateType($value = null, $type = 0, & $errorMessage = ''){
		
		$validate = M4J_validate::getInstance();
		switch ($type){
			case 1:
				if($validate->notAlphabetic($value)){
					$errorMessage = M4J_LANG_ALPHABETICAL.": ";
				}
				break;
	
			case 2:
				if($validate->notNumeric($value)){
					$errorMessage = M4J_LANG_NUMERIC.": ";
				}
				break;
	
			case 3:
				if($validate->notInteger($value)){
					$errorMessage = M4J_LANG_INTEGER.": ";
				}
				break;
	
			case 4:
				if(!$validate->email($value)){
					$errorMessage = M4J_LANG_EMAIL.": ";
				}
				break;
	
			case 5:
				if(!$validate->url($value)){
					$errorMessage = M4J_LANG_URL.": ";
				}
				break;
	
			case 6:
				if($validate->notAlphanumeric($value)){
					$errorMessage = M4J_LANG_ALPHANUMERIC.": ";
				}
				break;	
		}
	}
	/**
	 * Return the remote ip of the user
	 * @return	string
	 */
	public static function getRemoteIp(){
		$ip = $_SERVER['REMOTE_ADDR'];
		if(! preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip)){
			$ip = "UNKNOWN";
		}
		return $ip;
	}
	
	
	public static function redirect($url = null, $msg = null, $inIFrame = false){
		if(! $inIFrame){
			$app = new JApplication();
			$app->redirect($url,$msg);
		}else{
			ob_get_clean();
			die("<html>\n<head>\n".
					'<script type="text/javascript"> window.top.location = "'.$url.'"; </script>'."\n".
					"</head>\n<body></body>\n</html>\n");
		}
	}
	
	public static function healEmail($str) {
		$injections = array('/(\n+)/i',
				'/(\r+)/i',
				'/(\t+)/i',
				'/(%0A+)/i',
				'/(%0D+)/i',
				'/(%08+)/i',
				'/(%09+)/i'
		);
		$str= preg_replace($injections,'',$str);
		return $str;
	}
	
	/**
	 * 
	 * @param JMail $mailObject
	 * @param string $adresses
	 */
	public static function addMultipleMailAddress(& $mailObject = null, $adresses = null){ 
		$multiples =  ( is_array($adresses) || is_object($adresses) ) ? $adresses : preg_split("/[;,]+/", (string) $adresses );
		foreach ($multiples as $mailAddress){
			$mailAddress = self::healEmail($mailAddress);
			$mailObject->AddAddress($mailAddress);
		}
	}
	
	/**
	 * 
	 * @param bool $forceDataReturn  Allways return the user data string even if not allowed by admin
	 * @param bool $useTable	Use table tag around.
	 * @return string
	 */
	public static function serverData($forceDataReturn = false, $useTable = false){
		
		if( defined('M4J_SHOW_USER_INFO') && ! M4J_SHOW_USER_INFO && ! $forceDataReturn ) return '';
		
		$isHTMLMail = defined('M4J_HTML_MAIL') ? (bool) M4J_HTML_MAIL : true;
		
		$br ="\n";
		$head = "----------------------------------------------------------------------------\n";
		$footer = "----------------------------------------------------------------------------\n";

		if($isHTMLMail)  
		{
			
			if($useTable){
				$br ='</td>'. "\n" .'</tr>'. "\n" .'<tr>'. "\n" .'<td style="font-size:11px;">'. "\n" ;
				$head = '<table cellspacing="0" cellpading="0" border="0" width="100%" style="font-family: Arial; font-size:11px;">' . "\n" . '<tbody>' . "\n" .
						'<tr>'. "\n" .'<td height="18" >'. "\n" .'<hr/>'. "\n" .'</td>'. "\n" .'</tr>'. "\n" .'<tr>'. "\n" .
						'<td style="font-family: Arial; font-size:11px;">'. "\n";
				$footer ='</td>'. "\n" .'</tr>'. "\n" . '</tbody>' . "\n" . '</table>' . "\n";
			}else{
				
				$br ='</td>'. "\n" .'</tr>'. "\n" .'<tr>'. "\n" .'<td colspan="3" style="font-size:11px;">'. "\n" ;
				$head ='<tr>'. "\n" .'<td colspan="3" height="18" >'. "\n" .'<hr/>'. "\n" .'</td>'. "\n" .'</tr>'. "\n" .'<tr>'. "\n" .
						'<td colspan="3" style="font-family: Arial; font-size:11px;">'. "\n";
				$footer ='</td>'. "\n" .'</tr>'. "\n";
			}
		}

		
		
		

		$user = JFactory::getUser();
		$userData = "User: ";
		if($user->guest==1){
			$userData .= " Guest";
		}else{
			$userData .= $user->username.$br."Real Name: ".$user->name.$br."Email: ". '<a href="mailto:'.$user->email.'">'.$user->email.'</a>'.$br;
		}
		
		$userAgent = self::healEmail( strip_tags( array_key_exists('HTTP_USER_AGENT',$_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : "unknow") ) ;
		$host = self::healEmail( strip_tags( array_key_exists('REMOTE_NAME',$_SERVER) ? $_SERVER['REMOTE_NAME'] : @ gethostbyaddr($_SERVER['REMOTE_ADDR'])) );
		$remoteAddress = strip_tags( array_key_exists('REMOTE_ADDR',$_SERVER) ? $_SERVER['REMOTE_ADDR'] : "") ;
		$remoteAddress = (preg_match(
				'/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',
				$remoteAddress)) ? $remoteAddress : "CATCHED IP ADDRESS INFILTRATION";
		$remotePort = intval( array_key_exists('REMOTE_PORT',$_SERVER) ? $_SERVER['REMOTE_PORT'] : 0) ;
		
		return  $head.
				'Sending Time: '.date("Y-m-d H:i:s",time()). $br .
				'User Agent: '.$userAgent. $br .
		  	    'Host: '. $host . $br .
		   		'IP: '.$remoteAddress . $br .
				'PORT: '.$remotePort . $br . 
				$userData .
				$footer; 

	}//EOF serverData
	
	public static function errorTag($errorMessage = null){
		return "\n" .  '<span class="'.M4J_CLASS_ERROR.'">'.$errorMessage.'</span>' ;
	}
	
	public static function cleanBacktrace($debug = true, $maxSteps = 0){
		$ignoreFirst = true;
		$count = 1;
		$arr = debug_backtrace();
	
		$ret = '<h3>CLEAN BACKTRACE:</h3>';
		$allow = array('file', 'line', 'class' , 'function' );
		foreach($arr as $station){
			if($ignoreFirst){
				$ignoreFirst = false;
				continue;
			}
			$buffer = "\n";
			foreach($station as $key => $info){
				$buffer .= (in_array($key, $allow)) ? strtoupper($key) . ": " . $info . "\n" : '';
			}
			$ret .= '<b>STEP: '. $count . "</b><br/><pre>" . $buffer . "</pre>\n";
			if($maxSteps === $count++) break;
		}
		if($debug) MDebug::_($ret);
		else return $ret;
	}
	
}