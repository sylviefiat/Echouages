<?php
/*
* @name			ByGiroHelper.php is a class with useful PHP functions
* @version		0.0.6
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		GNU GPL v3 or later
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class ByGiroHelper {
    
    var $db;
    var $comAlias;

	function __construct($comAlias = '') {
		$this->comAlias = $comAlias;
		// $this->db = JFactory::getDBO();
	}
	
	public static function getInstalledLanguages(){
		static $languages;
		
		if(isset($language)){
			return $languages;
		}
		$db = JFactory::getDBO();
		
		$sql = "SELECT *, LOWER(REPLACE(lang_code,'-','')) as lang_tag  FROM #__languages WHERE published = 1";
		$db->setQuery(  $sql );
		$languages = $db->loadObjectList();

		foreach($languages as &$lang){
			$lang->postfix = $lang->lang_tag;
			if($lang->lang_tag != ''){
				$lang->postfix = '_'. $lang->lang_tag;
			}
			
			$lang->img_url = '';
			if($lang->lang_code != ''){
				$lang->img_url = JURI::root() .'media/mod_languages/images/'. $lang->image .'.gif';
			}
		}
		
		return $languages;
	}

	public static function getMlFields($item,$fields){
		$isObj = is_object($item);
		
		if($isObj){
			$item = (array)$item;
		}

		$lang = JFactory::getLanguage();
		$lang_tag = strtolower(str_replace('-','', $lang->getTag()));
		if($lang_tag != ''){
			$lang_tag = '_'. $lang_tag;
		}
	
		foreach($fields as $fi){
			$newKey = $fi .'_ml';
			$langKey = $fi . $lang_tag;
			
			if(!empty($item[$langKey]) AND is_string($item[$langKey])){
				$item[$newKey] = $item[$langKey];
			} else if(!empty($item[$fi])){
				$item[$newKey] = $item[$fi];
			}
		}
		
		if($isObj){
			$item = (object)$item;
		}
		
		return $item;
	}
	
	public static function get_ip_address(){
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
						return $ip;
					}
				}
			}
		}
	}
	
	/**
	 * Set value of an array by using "root.branch.leaf" notation
	 *
	 * @param array $array Array to affect
	 * @param string $path Path to set
	 * @param mixed $value Value to set the target cell to
	 * @return void
	 */
	public static function set_array_path_value($array, $path, $value, $delimiter = '.', $multi = false)
	{
		if(is_string($path)){
			$path = explode($delimiter, $path);
		}
		
		foreach($path as $i => $p){
			$path[$i] = trim($path[$i]);
			if(empty($path[$i])){
				unset($path[$i]);
			}
		}
	 
		if(empty($path)){
			if(is_array($array)){
				$array[] = $value;
			}
			return $array;
		}
	 
		// initially point to the root of the array
		$pointer =& $array;

		// loop through each part and ensure that the cell is there
		foreach ($path as $part) {
			// fail if the part is empty
			if ($part == '') {			
				continue;
				// throw new Exception('Invalid path specified: ' . $path);
			}
	 
			// create the cell if it doesn't exist
			if(is_object($pointer) AND !isset($pointer->$part)){
				$pointer->$part = new stdClass;
			} else if (is_array($pointer) AND !isset($pointer[$part])) {
				$pointer[$part] = array();
			}
	 
			// redirect the pointer to the new cell
			if(is_object($pointer)){
				$pointer =& $pointer->$part;
			} else if (is_array($pointer)) {
				$pointer =& $pointer[$part];
			}
			
		}
	 
		// set value of the target cell
		if($multi){
			if(!is_array($pointer)){
				$pointer = array();
			}
			$pointer[] = $value;
		} else {
			$pointer = $value;
		}
		
		return $array;
	}
	
	public static function unset_array_path_value($array, $path, $valToCompare = false, $delimiter = '.')
	{	 
		if(is_string($path)){
			$path = explode($delimiter, $path);
		}
		
		foreach($path as $i => $p){
			$path[$i] = trim($path[$i]);
			if(empty($path[$i])){
				unset($path[$i]);
			}
		}
	 
		// fail if the path is empty
		if (empty($path)) {
			return $array;
			// throw new Exception('Path cannot be empty');
		}
	 
		// use current array as the initial value
		$value = $array;
		
		$realPath = '$array';
		// loop through each part and extract its value
		$count_parts = count($path);
		foreach ($path as $k => $part) {
			if(is_array($value) AND isset($value[$part])){
				$realPath .= '[$parts['. $k .']]';
				// replace current value with the child
				$value = $value[$part];
			} else if(is_object($value) AND isset($value->$part)){
				$realPath .= '->{$parts['. $k .']}';
				// replace current value with the child
				$value = $value->$part;
			}
		}
	
		if($valToCompare){
			$phpString = "if($realPath == '$valToCompare'){ unset($realPath); }";
		} else {
			$phpString = "unset($realPath);";
		}
		
		eval($phpString);
	 
		return $array;
	}
	
	/**
	 * Get value of an array by using "root.branch.leaf" notation
	 *
	 * @param array $array   Array to traverse
	 * @param string $path   Path to a specific option to extract
	 * @param mixed $default Value to use if the path was not found
	 * @return mixed
	 */
	public static function array_path_value($array, $path, $default = null, $delimiter = '.')
	{		
		if(is_string($path)){
			$path = explode($delimiter, $path);
		}
		
		foreach($path as $i => $p){
			$path[$i] = trim($path[$i]);
			if(empty($path[$i])){
				unset($path[$i]);
			}
		}
		
		// fail if the path is empty
		if (empty($path)) {
			return $array;
			// throw new Exception('Path cannot be empty');
		}
	 
	 
		// use current array as the initial value
		$value = $array;
	 
		// loop through each part and extract its value
		foreach ($path as $part) {
			if(is_array($value) AND isset($value[$part])){
				// replace current value with the child
				$value = $value[$part];
			} else if(is_object($value) AND isset($value->$part)){
				// replace current value with the child
				$value = $value->$part;
			} else {
				// key doesn't exist, fail
				return $default;
			}
		}
	 
		return $value;
	}
	
	public static function sendEmails($emails){		
		$result = array();
		
		$vars = array(
			'addReplyTo' => 'reply_to',
			'addRecipient'=>'recipients',
			'addCC'=>'recipients_cc',
			'addBCC'=>'recipients_bcc'
		);
		foreach($emails as $email){
			// get Mailer
			$mailer = JFactory::getMailer();		
			$mailer->setSender($email->sender);	
		
			foreach($vars as $k => $v){
				if(!isset($email->$v)){
					continue;
				}
				$mailer->$k($email->{$v}[0],$email->{$v}[1]);
			}
			
			$mailer->addAttachment($email->attachment);
			$mailer->setSubject($email->subject);
			
			$body = $email->body;
		
			$mailer->isHTML(true);
			$isHTML = $email->html;
			if($isHTML > 0){
				$mailer->isHTML(true);
			} else {
				$mailer->isHTML(false);
				
				// convert HTML body to plain text body
				$HtmlText = new HtmlText($body);
				$body = $HtmlText->get_text();
			}

			$mailer->Encoding = 'base64';
			$mailer->setBody($body);
			
			// send email
			$send = $mailer->Send();
			if ( $send !== true ) {
				$result[] = 'ERROR: '. $send->get('message');
			} else {
				$result[] = 'OK';
			}
		}		
	}
	
	public static function objectToArray($object, $recursive = false)
	{		
		$array=array();
		if(!is_object($object) AND !is_array($object)){
			return $object;
		}		
		
		foreach($object as $key => $value)
		{
			if($recursive AND (is_object($value) OR is_array($value))){
				$value = self::objectToArray($value, true);
			}			
			$array[$key] = $value;
		}
		return $array;
	}

	public static function arrayToObject($array, $recursive = false)
	{
		$object = new stdClass;
		if(!is_array($array) AND !is_object($array)){
			return $array;
		}
		
		foreach($array as $key => $value)
		{
			if($recursive AND (is_object($value) OR is_array($value))){
				$value = self::ArrayToObject($value, true);
			}
			$object->$key = $value;
		}
		return $object;
	}
		
	public static function generateRandomString($length = 5) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	public static function escapeJsonString($value) {
		# list from www.json.org: (\b backspace, \f formfeed)    
		$escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}
	
    /**
     * Checks whether a string is valid json.
     *
     * @param string $string
     * @return boolean
     */
    function is_json($string)
    {
		$result = false;
        try
        {
            // try to decode string
            $result = json_decode($string);
        }
        catch (ErrorException $e)
        {
            // exception has been caught which means argument wasn't a string and thus is definitely no json.
            return false;
        }

        // check if error occured
        return (json_last_error() == JSON_ERROR_NONE) ? false : $result;
    }
		
	public static function groupArrayByValue($array, $keyName, $multiple = true, $strtolower = false){
		if(!is_array($array)){
			$array = (array)$array;
		}
		
		$newArray = array();
		foreach($array as $key => $it){
			if(empty($it)) continue;
			
			$isObj = is_object($it);			
			if($isObj){
				@$it_keyName = $it->$keyName;
			} else {
				@$it_keyName = $it[$keyName];
			}
			
			if($strtolower){
				$it_keyName = strtolower($it_keyName);
			}
			

			if($multiple){
				$newArray[$it_keyName][$key] = $it;
			} else {
				$newArray[$it_keyName] = $it;
			}
		}

		return $newArray;
	}

	public static function sort_on_field($array,$on = array(), $direction = 'ASC'){
		if(empty($array) OR !is_array($array)){
			return $array;
		}
		
		if(!is_array($on)){
			$on = array($on => $direction);
		}
		
		try{
		// check we have objects or arrays
		@$isObj = (is_object(array_shift(array_values((array)$array))));
		
		$sortingArrays = array();
		unset($row);
		foreach ($array as $key => $row) {
			if($isObj){
				if(!is_object($row)) $row = (object)$row;
			} else {
				if(!is_array($row)) $row = (array)$row;
			}
				
			unset($vf);
			foreach($on as $kf => $vf){
				if(!isset($sortingArrays[$kf])){
					$sortingArrays[$kf] = array();
				}
				if($isObj){
					@$val = $row->{$kf};
				} else {
					@$val = $row[$kf];
				}
				$sortingArrays[$kf][$key] = $val;
			}
		}
				
		$args = array();
		unset($vf);
		foreach($on as $kf => $vf){
			$args[] = &$sortingArrays[$kf];
			
			$v = SORT_ASC;
			if(strtolower($vf) == 'desc'){
				$v = SORT_DESC;
			}
			
			$args[] = &$v;
		}
		
		$args[] = &$array;
		
		call_user_func_array('array_multisort', $args);
		} catch(Exception $e){
			$e->getMessage();
		}
		
		return $array;
	}

	public static function getUniquePath($filePath){
		$fileName = basename($filePath);
		$dir = dirname($filePath);
		while(file_exists($filePath)){
			$rand = self::generateRandomString(5);
			$fileNameParts = explode('.',$fileName);						
			$fileNameParts[(count($fileNameParts) -2)] = $fileNameParts[(count($fileNameParts) -2)] .'_'. $rand;
			$filePath = $dir .DS. implode('.',$fileNameParts);
		}
		
		return $filePath;
	}
	
	public static function loginFirstly(){
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		if($user->guest){
			$u = JURI::getInstance();
			$currentURL = $u->toString();		

			$redirectUrl = urlencode(base64_encode($currentURL));
			$redirectUrl = '&return='.$redirectUrl;
			$joomlaLoginUrl = 'index.php?option=com_users&view=login';
			$Itemid = self::getItemId(array($joomlaLoginUrl));
			$joomlaLoginUrl .= '&Itemid='. $Itemid;
			
			$app->redirect(JRoute::_($joomlaLoginUrl . $redirectUrl, false), JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 'warning');			
			return false;
		}

		return true;
	}

/*
*
*	function by AKEEBA BACKUP
*
*/
	
	public static function colorise($file, $onlyLast = false)
	{
		$ret = '';
		
		$lines = @file($file);
		if(empty($lines)) return $ret;
		
		array_shift($lines);
		
		foreach($lines as $line) {
			$line = trim($line);
			if(empty($line)) continue;
			$type = substr($line,0,1);
			switch($type) {
				case '=':
					continue;
					break;
					
				case '+':
					$ret .= "\t".'<li class="changelog-added"><i class="changelog-icon-added"></i>'.htmlentities(trim(substr($line,2)))."</li>\n";
					break;
				
				case '-':
					$ret .= "\t".'<li class="changelog-removed"><i class="changelog-icon-removed"></i>'.htmlentities(trim(substr($line,2)))."</li>\n";
					break;
				
				case '~':
					$ret .= "\t".'<li class="changelog-changed"><i class="changelog-icon-changed"></i>'.htmlentities(trim(substr($line,2)))."</li>\n";
					break;
				
				case '!':
					$ret .= "\t".'<li class="changelog-important"><i class="changelog-icon-important"></i>'.htmlentities(trim(substr($line,2)))."</li>\n";
					break;
				
				case '#':
					$ret .= "\t".'<li class="changelog-fixed"><i class="changelog-icon-fixed"></i>'.htmlentities(trim(substr($line,2)))."</li>\n";
					break;
				
				default:
					if(!empty($ret)) {
						$ret .= "</ul>";
						if($onlyLast) return $ret;
					}
					if(!$onlyLast) $ret .= "<h3 class=\"changelog\">$line</h3>\n";
					$ret .= "<ul class=\"changelog\">\n";
					break;
			}
		}
		
		return $ret;
	}	


	public static function safeAlias($str, $toCase = 'lower', $stripSomeOther = true)
	{
		//ACCENTS
		$accents = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$replacements = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		$str = str_replace($accents, $replacements, $str);

		//SPACES
		$str = preg_replace("/\s+/", "-", strip_tags($str));

		//OTHER CHARACTERS
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "=", "+", "[", "{", "]",
					   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
					   "—", "–", ",", "<", ".", ">", "/", "?");
		$str = trim(str_replace($strip, "", $str));
		
		if($stripSomeOther){
			$strip = array("(", ")", "_", "—", "–");
			$str = trim(str_replace($strip, "", $str));
		}
		
		switch($toCase)
		{
			case 'lower':
				if(function_exists('mb_strtolower')){
					$str = mb_strtolower($str, 'UTF-8');
				} else {
					$str = strtolower($str);
				}
				break;

			case 'upper':
				if(function_exists('mb_strtoupper')){
					$str = mb_strtoupper($str, 'UTF-8');
				} else {
					$str = strtoupper($str);
				}
				break;

			case 'ucfirst':
				$str = ucfirst($str);
				break;

			case 'ucwords':
				$str = ucwords($str);
				break;

			default:
				break;

		}

		return $str;
	}
	
	public static function isKeyValsUniqueInArray($array,$k){
		static $keys;
		
		if(!is_array($keys)){
			$keys = array();
		}
		
		$arrMD5 = md5(serialize($array));		
		if(isset($keys[$arrMD5][$k.$v]) AND $keys[$arrMD5][$k.$v] !== null){
			return $keys[$arrMD5][$k.$v];
		}
		
		$keys[$arrMD5][$k.$v] = null;
		$tmp = array();
		foreach ($array as $item) {
			if(is_object($item)){
				$val = $item->$k;
			} else if(is_array($item)) {
				$val = $item[$k];
			} else {
				$val = $item;
			}
			
			$tmp[] = $val;
		}

		$keys[$arrMD5][$k] = $result = (count($tmp) !== count(array_unique($tmp)));
		
		return $result;
	}
	
	public static function isValUniqueInArray($array,$k,$v,$id = null){
		$found = 0;
		$itemID = null;
		foreach ($array as $item) {
			if(is_object($item)){
				$val = $item->$k;
				if(isset($item->id)){
					$itemID = $item->id;
				}
			} else if(is_array($item)) {
				$val = $item[$k];
				if(isset($item['id'])){
					$itemID = $item['id'];
				}
			} else {
				$val = $item;
			}
			
			if($val === $v){
				$found++;
				if($itemID AND $id AND $itemID != $id){
					$found++;
				}
			}
			
			if($found >= 2){
				return false;
			}
		}
		
		return true;
	}
	
	public static function isValUniqueInDBItems($tableName,$dataField,$fieldName,$value, $thisId){
		$db = JFactory::getDBO();
		
		$searchString = $dataField ." LIKE '%\"". $fieldName ."\":\"". $value ."\"%' AND id <> ". $thisId;
		
		$query = "SELECT COUNT(*) FROM ". $tableName ." WHERE ". $searchString ;
		$db->setQuery( $query );
		$count = $db->loadResult();
		
		return ($count == 0);
	}
	
	public static function textInBetween($string, $startTag, $endTag, $regex = false){
		$matches = array();
		
		if($regex){
			$delimiter = '#';
			$regex = $delimiter . preg_quote($startTag, $delimiter) 
								. '(.*?)' 
								. preg_quote($endTag, $delimiter) 
								. $delimiter 
								. 's';
			preg_match($regex,$string,$matches);		
		}
		$startsAt = strpos($string, $startTag) + strlen($startTag);
		$endsAt = strpos($string, $endTag, $startsAt);
		$result[] = substr($string, $startsAt, $endsAt - $startsAt);

		return $result;
	}
	
	public static function sortPathsList(&$paths){
		//sort folders first, then by type, then alphabetically
		usort ($paths, create_function ('$a,$b', '
			return	is_dir ($a)
				? (is_dir ($b) ? strnatcasecmp ($a, $b) : -1)
				: (is_dir ($b) ? 1 : (
					strcasecmp (pathinfo ($a, PATHINFO_EXTENSION), pathinfo ($b, PATHINFO_EXTENSION)) == 0
					? strnatcasecmp ($a, $b)
					: strcasecmp (pathinfo ($a, PATHINFO_EXTENSION), pathinfo ($b, PATHINFO_EXTENSION))
				))
			;
		'));
	}
	
	public static function getMenuLinks($strings = array()){ // example: 'option=com_mycomponent'
		static $list;
		
		if(!is_array($list)){
			$list = array();			
		}
		$strings = (array)$strings;
		
		$hash = md5(serialize($strings));
		
		if(isset($list[$hash])){
			return $list[$hash];
		}
		
		$lang = JFactory::getLanguage();
		$current_lang = $lang->getTag();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$db = JFactory::getDBO();
		
		$extra = '';
		if(count($strings)){			
			$ands = array();
			foreach($strings as $st){
				$ands[] = "link LIKE '%". $st ."%'";
			}
			
			$extra = '('. implode(' OR ',$ands) .') AND ';
		}
		
		$query = "SELECT id,link FROM #__menu WHERE `type`='component' AND published=1 AND ". $extra ."client_id=0 AND (language='*' OR language='".$current_lang."') AND access IN (".$groups.")";
		$db->setQuery($query);

		$list[$hash] = $db->loadObjectList();
		
		return $list[$hash];
	}
	
	public static function getItemsByfields($tableName, $where = array(), $listItems = false, $columns = array(), $key = null, $loadResult = false)
	{
		if(empty($where)){
			$extra = ' WHERE 1';
		} else {
			$extra = ' WHERE '. implode(' AND ', $where);	
		}

		$db = JFactory::getDBO();
		
		$select = '*';
		if(!empty($columns)){
			$select = implode(',', $columns);
		}
		
		//Get all items
		$query = "SELECT ". $select
			. " FROM `". $tableName ."`"
			. $extra;
		$db->setQuery($query);
	
		if($loadResult){
			$result = $db->loadResult();
		} else {
			if($listItems){
				if($key){
					$result = $db->loadObjectList($key);
				} else {
					$result = $db->loadObjectList();
				}
			}else {
				if($key){
					$result = $db->loadObject($key);
				} else {
					$result = $db->loadObject();
				}
			}
		}
			
		return $result;
	}	

	
	public static function addItems($items, $tableName, $method = 'REPLACE'){	

		// check if we have ONE item (object or associative array) OR we have many items
		if(is_object($items)){
			$items = array($items);
		} else if(is_array($items)){
			@$firstItem = array_shift(array_values($items));
			if(!is_object($firstItem) AND !is_array($firstItem)){
				$items = array($items);
			}
		}
		
		if(count($items) <= 0){
			return 'No items to ADD';
		}
		
		$db= JFactory::getDBO();
		// get keys from FIRST item assuming all the other items are the same
		@$firstItem = array_shift(array_values($items));
		$keys = array();
		unset($value);
		foreach($firstItem as $key => $value){
			$keys[] = "`". $key ."`";
		}
		
		$itemsValues = array();
		unset($item);
		foreach($items as $item){
			// json_encode
			$cleanItem = self::jsonFieldsToString($item);
			
			$values = array();
			unset($value);
			foreach($cleanItem as $key => $value){			
				$values[] = $db->quote($value);
			}
			
			$itemsValues[] = '('. implode(',',$values) .')';
		}

		$query = $method ." INTO `". $tableName ."` (". implode(',',$keys) .")"
				. " VALUES ". implode(',', $itemsValues) ;
		$db->setQuery($query);

		try {
			if(!$db->query()){
				return $db->getErrors();
			}	
		} catch (Exception $e) {
			return $e->getMessage();
		}
		return 1;
	}
	
	protected function replacerHelper($matches,$str, $data, $labels){
		foreach ($matches as $match) {
			$found = true;
			$value = $data;
			if(is_object($value)){
				$value = (array)$value;
			}
			$var = $match['1'];
			
			$variables = explode(':',$match['1']);		
			// nested variables
			if(count($variables) > 1){
				foreach($variables as $var){
					if(is_object($value)){
						$value = (array)$value;
					}
					
					if(!isset($value[$var])){
						break;
						$found = false;
						$value = '';
					}
					$value = $value[$var];
				}							
			} else {
				if(isset($value[$var])){
					$value = $value[$var];
				} else {
					$value = '';
				}
			}

			if (!$found){
				continue;
			}

			if(is_array($value) OR is_object($value)){
				$html = '<ul>';
				foreach($value as $v){
					$v = (string)$v;
					if(isset($labels[$var])){
						$v = JText::_($labels[$var][$v]);
					}
				
					$html .= '<li>'. $v .'</li>';
				}
				$html .= '</ul>';							
				$value = $html;
			} else {
				if(isset($labels[$var])){
					$value = JText::_($labels[$var][$value]);
				}
			}						
			
			$str = str_replace($match['0'], $value, $str);
		}

		return $str;
	}
	
	
	public static function replacer($str, $data, $labels = array()){		
		foreach($data as $key => $details){
			if(is_string($details)){

				// extra little things
				switch($key){
					case 'creation_date':
					case 'modification_date':
						$regex_simple = '[['. $key .':';
						$regex = '#\[\['. $key .'\:([^\]]+)\]\]#';
					
						if (!(strpos($str, $regex_simple) === false)) {
							if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0){
								foreach ($matches as $match) {
									$str = str_replace($match['0'], date ($match['1'],$details), $str);
								}
							}
						}
						break;
						
					default:					
						break;
				}
				
				$regex_simple = '[['. $key .']]';
				$str = str_replace($regex_simple,$details,$str);
				
			} else {
				$regex_simple = '[['. $key .':';
				$regex = '#\[\['. $key .'\:([^\]]+)\]\]#';
			
				if (!(strpos($str, $regex_simple) === false)) {
					if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0) {				
						$str = self::replacerHelper($matches,$str, $details, $labels);
					}
				}
			}	
		}

		if (preg_match_all('#\{([^\}]+)}#', $str, $matches, PREG_SET_ORDER) > 0) {	
			foreach ($matches as $match) {				
				$str = str_replace($match['0'], JText::_(strtoupper($match['1'])), $str);
			}
		}
		
		return $str;
	}
	

	public static function getMessages()
	{
		// Initialise variables.
		$lists = array();

		$app = JFactory::getApplication();
		
		// Get the message queue
		$messages = $app->getMessageQueue();

		// Build the sorted message list
		if (is_array($messages) && !empty($messages))
		{
			foreach ($messages as $msg)
			{
				if (isset($msg['type']) && isset($msg['message']))
				{
					$lists[$msg['type']][] = $msg['message'];
				}
			}
		}
		
		$buffer = null;
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/message.php';
		$itemOverride = false;

		if (file_exists($chromePath))
		{
			include_once $chromePath;
			if (function_exists('renderMessage'))
			{
				$itemOverride = true;
			}
		}

		$buffer = ($itemOverride) ? renderMessage($lists) : self::renderDefaultMessage($lists);

		return $buffer;
	}


	/**
	 * Render the system message if no message template file found
	 *
	 * @param   array  $msgList  An array contains system message
	 *
	 * @return  string  System message markup
	 *
	 * @since   12.2
	 */
	private static function renderDefaultMessage($msgList)
	{
		// Build the return string
		$buffer = '';

		// If messages exist render them
		if (is_array($msgList))
		{
			foreach ($msgList as $type => $msgs)
			{
				$buffer .= "\n<div class=\"alert alert-" . $type . "\">";

				// This requires JS so we should add it trough JS. Progressive enhancement and stuff.
				$buffer .= "<a class=\"close\" data-dismiss=\"alert\">×</a>";

				if (count($msgs))
				{
					$buffer .= "\n<h4 class=\"alert-heading\">" . JText::_($type) . "</h4>";
					$buffer .= "\n<div>";
					foreach ($msgs as $msg)
					{
						$buffer .= "\n\t\t<p>" . $msg . "</p>";
					}
					$buffer .= "\n</div>";
				}
				$buffer .= "\n</div>";
			}
		}

		return $buffer;
	}
	
	public static function renderLayout($layoutPath, $options = array()){
		static $version;
		
		if(!isset($version)){
			$version = new JVersion();
		}
		
		if($layoutPath == ''){
			return;
		}
			
		// Joomla! 1.6 - 1.7 - 2.5
		if (version_compare($version->RELEASE, '2.5', '<='))
		{
			$displayData = $options['data'];
			
			ob_start();
			include(JPath::clean(JPATH_SITE . DS . $layoutPath));
			$output = ob_get_contents();
			ob_end_clean();			
		} else {			
			$layout = new JLayoutFile(pathinfo($layoutPath, PATHINFO_FILENAME), JPath::clean(JPATH_SITE . DS . dirname($layoutPath)));
			$data = $options['data'];		
			$output = $layout->render($data);
		}
		
		return $output;
	}
	
	public static function generatePdf($html, $extension = '', $download = false, $filename = 'print.pdf', $css_files = array(), $preview = false){
		$pdf_library = JPATH_SITE .DS.'libraries'.DS.'librariesbygiro'.DS.'dompdf'.DS.'dompdf_config.inc.php';

		if(file_exists($pdf_library)){
			require_once($pdf_library);
		} else {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_("LibrariesByGiro not installed"), 'error');

			return false;
		}

		// add CSS files for better rendering
		$css_files[] = (object)array(
			'type' => 'file',
			'content' => JPATH_SITE .DS.'libraries'.DS.'jdom'.DS.'jform'.DS.'assets'.DS.'css'.DS.'jform.css',
		);
		
		$css_files[] = (object)array(
			'type' => 'file',
			'content' => JPATH_SITE .DS.'components'.DS. 'com_' . $extension .DS.'css'.DS.'pdf.css',
		);

		$stylesheet = '';
		foreach($css_files as $css){
			if($css->type != 'file'){
				$stylesheet .= $css->content;
			} else {
				if(file_exists($css->content)){
					$stylesheet .= file_get_contents($css->content);
				}
			}
		}
		$stylesheet = '<style>'. $stylesheet .'</style>' ;

		// for testing
		if($preview){
			echo $stylesheet . $html;
			jexit();
		}
		
		
		// clean output
		ob_clean();
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($stylesheet . $html);
		$dompdf->render();		 
		
		// add the page number
		$canvas = $dompdf->get_canvas();
		$font = Font_Metrics::get_font("helvetica", "bold");
		$canvas->page_text(560,770, "{PAGE_NUM} / {PAGE_COUNT}", $font, 9, array(0,0,0));
		
		if($download){
			$dompdf->stream($filename);
			return;
		}
		
		$content = $dompdf->output();
		return $content;
	}
	
	public static function updateItems($tableName, $values, $where){	
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		if(!is_array($values)){
			$values = (array)$values;
		}
		
		$setValues = array();
		foreach($values as $key => $val){
			$setValues[] = "`". $key .'` = '. $db->quote($values[$key]);
		}

		//Update all items
		$query = "UPDATE `". $tableName ."` SET ". implode(', ', $setValues)
				. " WHERE ". implode(' AND ', $where);
		$db->setQuery($query);

		if(!$db->query()){
			return $db->getErrors();
		}	
		return $db->getAffectedRows();
	}
	
	public static function checkExtensionExists($extension){
	
		$file = JPATH_ADMINISTRATOR .DS.'components'.DS.'com_'. $extension . DS . $extension .'.php';
		if(!file_exists($file)){
			return false;
		}

		return true;
	}
	
	public static function getParentsPath($items, $id = 'id', $parent_id = 'parent_id'){
		// group items by id
		$items = self::groupArrayByValue($items, $id, false);
		
		foreach($items as &$it){		
			$path = array();
			
			$pid = $it->$parent_id;
			while($pid > 0){
				$path[] = $pid;
				if(is_array($items[$pid]->parents)){
					$path = array_merge($path,$items[$pid]->parents);
					$pid = 0;
				} else {
					$pid = $items[$pid]->parent_id;
				}
			}
			
			$it->parents = $path;
		}
		
		return $items;
	}
	
	public static function getUserAclConfig($aclConfig = null, $loggedUser = null){		
		$userConfig = array();
		
		if(empty($aclConfig)){
			return (object)$userConfig;
		}
		
		$aclConfig = self::objectToArray($aclConfig);
		
		if(!($loggedUser instanceof JUser)){
			$userId = null;
			if(!empty($loggedUser)){
				$userId = (int)$loggedUser;
			}

			$loggedUser = JFactory::getUser($userId);
		}
		$groupsByUser = array_reverse(JAccess::getGroupsByUser($loggedUser->id));

		$publicConfig = $aclConfig[1];
		foreach($publicConfig as $key => $val){
			foreach($groupsByUser as $grId){
				if(isset($aclConfig[$grId]->$key)){
					$userConfig[$key] = $aclConfig[$grId]->$key;
					break;
				}
			}
			
			if(!isset($userConfig[$key])) $userConfig[$key] = $val;
		}
		
		return $userConfig;
	}
	
	public static function canAccess($accessValues, $loggedUser = null){
		static $groups;
		static $groupsByTitle;
		static $groupsByUser;
		
		if(empty($accessValues)){
			return true;
		}
		
		if(is_string($accessValues)){
			$accessValues = explode(',',str_replace(' ','',strtolower($accessValues)));
		}

		if(!$groups){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('a.id')
				->select('LOWER(REPLACE(a.title, " ", "")) AS text')
				->select('a.parent_id')
				->select('COUNT(DISTINCT b.id) AS level')
				->from('#__usergroups as a')
				->join('LEFT', '#__usergroups  AS b ON a.lft > b.lft AND a.rgt < b.rgt')
				->group('a.id, a.title, a.lft, a.rgt')
				->order('a.lft ASC');
			$db->setQuery($query);			
		
			$groups = $db->loadObjectList();
			$groupsByTitle = self::groupArrayByValue($groups, 'text', false);
			$groups = self::getParentsPath($groups);
		}

				
		// check the accessgroups values are the IDS
		foreach($accessValues as $k => $av){
			if(!is_numeric($av)){
				if(isset($groupsByTitle[$av])){
					$accessValues[$k] = $groupsByTitle[$av]->id;
				} else {
					unset($accessValues[$k]);
				}
			}
		}		
		
		
		if(!($loggedUser instanceof JUser)){
			$userId = null;
			if(!empty($loggedUser)){
				$userId = (int)$loggedUser;
			}

			$loggedUser = JFactory::getUser($userId);
		}
		$groupsByUser = JAccess::getGroupsByUser($loggedUser->id);


		// check user groups and relative parents
		if(count(array_intersect($groupsByUser, $accessValues)) > 0){
			return true;
		}
		
		// check parents of groups
		foreach($groupsByUser as $g){
			if(isset($groups[$g]->parents) AND count(array_intersect($groups[$g]->parents, $accessValues)) > 0){
				return true;
			}
		}

		return false;
	}		


	public static function getItemId($url = array()){ //returns the itemid associated with a component if a link to this component is available in the menus table OR the MAIN itemId
		if(!is_array($url)){
			$url = (array)$url;
		}
		
		$lang = JFactory::getLanguage();		
		$db		= JFactory::getDBO();
		
		// prepare the conditions
		$conditions = array();
		$conditions[] = "home = 1";
		
		$orderingConditions = array();
		foreach($url as $k => $u){
			$conditions[] = "link LIKE '%". $u ."%'";
			$orderingConditions[] = "WHEN link LIKE '%". $u ."%' THEN ". $k;
		}		
		
		if(!empty($url)){
			$k++;
			$orderingConditions[] = "ELSE ". $k;
			
			$orderingConditions = ", CASE ". implode(' ', $orderingConditions) ." END";
		}
		
		$query	= "SELECT id FROM #__menu WHERE "
			.	"(". implode(' OR ',$conditions) .") AND "
			.	"published=1 AND menutype <> 'main' AND "
			.	"(language = '*' OR language = '". $lang->getTag() ."') "
			.	"ORDER BY home ASC". $orderingConditions;

		$db->setQuery( $query );
		$id = $db->loadResult();
		return $id;		
	}
	
	public static function cors() {

		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			exit(0);
		}

		echo "You have CORS!";
	}
	
	public static function normalizeArray($array, $recursive = true){
		// check array itself
		if(is_array($array) AND self::is_assoc($array)){
			$array = (object)$array;
		}
		
		if($recursive){
			foreach($array as &$it){
				if(is_array($it) OR is_object($it)){
					$it = self::normalizeArray($it);
				}
			}
		}
		
		return $array;
	}
	
	public static function jsonFieldsToString($data){
		$isObj = is_object($data);
		
		$cleanData = array();
		// check fields for JSON data to store
		unset($val);
		foreach($data as $key => $val){
			if(is_array($val) OR is_object($val)){
				// check recursively for associatives arrays and convert them to objects
				$val = self::normalizeArray($val);
		
				// remove CURRENT and REMOVE file input
				$val = json_encode(self::removeSysInput($val, JSON_NUMERIC_CHECK));
			}
			$cleanData[$key] = $val;
		}
		
		if($isObj){
			$cleanData = (object)$cleanData;
		}
		
		return $cleanData;
	}
	
	
	public static function removeSysInput($array){
		$remove_fields = array('remove','current','view','raw_changed','raw_editor');
		$isObj = is_object($array);
		
		$cleanArray = array();
		unset($val);
		foreach($array as $key => $val){
			$tmp_key = explode('-',$key);
			
			if(in_array(end($tmp_key),$remove_fields)){				
				continue;
			}
			
			$valIsObj = is_object($val);
			$valIsArr = is_array($val);
			$cleanVal = $val;
			if($valIsArr OR $valIsObj){
				$cleanVal = self::removeSysInput($val);
			}
			
			$cleanArray[$key] = $cleanVal;
		}
		
		if($isObj){
			$cleanArray = (object)$cleanArray;
		}
		
		return $cleanArray;
	}

	
	public static function stringToJsonFields($data){
		$isObj = is_object($data);
		
		$cleanData = array();
		unset($val);
		foreach($data as $key => $val){
			if(is_string($val) AND !empty($val) AND 
				(
					(@$val[0] == '{' AND $val[(strlen($val)-1)] == '}') OR
					(@$val[0] == '[' AND $val[(strlen($val)-1)] == ']')
				) OR (
					$val == '{}' OR $val == '[]'				
				)				
			){
				$val = json_decode($val);
			}

			$cleanData[$key] = $val;
		}
		
		if($isObj){
			$cleanData = (object)$cleanData;
		}
		
		return $cleanData;
	}
	
	public static function is_assoc($array) {
		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}
	
	public static function array_replace_i(){
		$numargs = func_num_args();
		
		if(empty($numargs)){
			return null;
		}
		
		$isRecursive = false;
		if(is_bool(func_get_arg($numargs-1))){
			$numargs--;
			$isRecursive = func_get_arg($numargs);
		}
		
		$removeEmpty = false;
		if(is_bool(func_get_arg($numargs-1))){
			$numargs--;
			$removeEmpty = func_get_arg($numargs);
		}
		
		$first = func_get_arg(0);
		for($i=0;$i<$numargs;$i++){
			$first = self::array_replace_recurse($first,func_get_arg($i),$isRecursive, $removeEmpty);
		}
		
		return $first;
	}
	
	protected static function array_replace_recurse($ar1,$ar2,$recursive = false, $removeEmpty = false){
		$isObj1 = is_object($ar1);
		$isObj2 = is_object($ar2);
		
		if(!empty($ar1)){
			$ar1 = (array)$ar1;
		} else {
			$ar1 = array();
		}
		
		if(!empty($ar2)){
			$ar22 = (array)$ar2;
		} else {
			$ar22 = array();
		}
		
		if($removeEmpty){
			$ar22 = self::removeEmptyElements($ar22,false);
		}
		
		foreach($ar22 as $key => $val){
			if(is_object($val) OR is_array($val)){
				$val = self::array_replace_recurse($ar1,$ar22,$recursive,$removeEmpty);
			}
			
			$ar1[$key] = $val;
		}
		
		if($isObj1){
			$ar1 = (object)$ar1;
		}
		
		return $ar1;
	}
	
	public static function removeEmptyElements($item,$recursive = false){
		$isObj = is_object($item);
		
		$newItem = array();
		foreach($item as $key => $it){
			if(empty($it) AND $it !== 0){
				continue;
			}
			
			if(is_object($it) OR is_array($it) AND $recursive){
				$it = self::removeEmptyElements($it,$recursive);
			}
			
			$newItem[$key] = $it;
		}
		
		if($isObj){
			$newItem = (object)$newItem;
		}
			
		return $newItem;
	}
	
	public static function getHtmlField($html,$field,$format = 'input',$form = null){
		$format = ($format == 'input') ? $format : 'output';
		
		// replace label
		$html = str_replace('<%label%>',self::renderField($field,'label', $form),$html);
		
		// replace input
		$html = str_replace('<%input%>',self::renderField($field,$format, $form),$html);

		return $html;
	}
	
	public static function renderField($field,$format = 'input', $form = null){
		$html = '';

		switch($format){
			case 'input':
				if (!method_exists($field, 'getInputI')){
					if($form instanceof JForm){
						$html .= $form->getInput($field->fieldname);
					} else {
						$html .= $field->input;
					}
				} else {
					$html .= $field->getInputI($field->postfix);
				}
				break;
				
			case 'output':
				if (method_exists($field, 'getOutput')){
					$html .= $field->getOutput();
				} else {
					$html .= (!empty($field->jdomOptions['dataValue'])) ? $field->jdomOptions['dataValue'] : $field->value;
				}
				break;
				
			case 'label':
				if (method_exists($field, 'getLabel')){
					$reflection = new ReflectionMethod($field, 'getLabel');
					if (!$reflection->isPublic()) {
						if($form instanceof JForm){
							$html .= $form->getLabel($field->fieldname);
						} else {
							$html .= $field->label;
						}
					} else {
						$html .= $field->getLabel();
					}
				}			
				break;
				
			case 'value':
			default:
				$html .= (!empty($field->jdomOptions['dataValue'])) ? $field->jdomOptions['dataValue'] : $field->value;
				break;
		}		
		
		return $html;
	}
	
	public static function buildRoute($route = array(), $getCurrentBasicRoute = true){	
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$vars = array_keys($route);
		if(!is_array($vars)){
			$vars = (array)$vars;
		}
		
		if($getCurrentBasicRoute){
			$vars = array_merge($vars, array('option', 'view', 'layout', 'task', 'cid[]'));
		}
		$vars = array_merge($vars, array('tmpl', 'lang', 'Itemid'));
		
		$queryVars = array();
	
		foreach($vars as $var)
		{
			if (isset($route[$var])){
				if (!empty($route[$var])){
					$queryVars[$var] = $route[$var];
				}
			} else {
				$value = $jinput->get($var, null, 'STRING');
				if ($value !== null){
					$queryVars[$var] = $value;
				}
			}
		}
		
		$parts = array();
		if (count($queryVars)){
			foreach($queryVars as $key => $value){
				$parts[] = $key . '=' . $value;
			}
		}
			
		$url = "index.php?" . implode("&", $parts);

		return $url;			
		
	}
	
	/**
	 * Method to return a list of actions for which permissions can be set given a component and section.
	 *
	 * @param	string	$component	The component from which to retrieve the actions.
	 * @param	string	$section	The name of the section within the component from which to retrieve the actions.
	 *
	 * @return	array	List of actions available for the given component and section.
	 * @since	11.1
	 */
	public static function getActions($component, $section = 'component')
	{
		$getAll = $section == '*';
		if(strpos($section, ',') !== false){
			$section = explode(',',$section);
		}
		
		if(!is_array($section)){
			$section = array($section);
		}
		
		$actions = array();

		if (is_file(JPATH_ADMINISTRATOR.'/components/'.$component.'/access.xml')) {
			$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/'.$component.'/access.xml');

			foreach ($xml->children() as $child)
			{
				$sectionTitle = (string)$child['title'];
				$sectionTitle = !empty($sectionTitle) ? $sectionTitle : (string) $child['name'];
				if ($getAll OR in_array((string) $child['name'],$section)) {
					foreach ($child->children() as $action) {
						$actions[] = (object) array(
							'name' => (string) $action['name'],
							'title' => (string) $action['title'],
							'description' => (string) $action['description'],
							'section' => (string) $child['name'],
							'sectionTitle' => $sectionTitle,
							'sectionDescription' => (string) $child['description']
						);
					}
				}
			}
		}

		return $actions;
	}
	
	public static function triggerEvents($comAlias = null,$type,&$data, $events = array()){
		static $version;
		$errors = array();
		if(empty($comAlias)){
			return;
		}
		
		if(empty($version)){
			$version = new JVersion();
		}
		
		if(!is_array($data)){
			$data = (array)$data;
		}
		
		foreach($events as $event){
			if(!$event->enabled OR $event->event != $type){
				continue;
			}
			
			$file = null;
			if(!empty($event->file)){
				$file = JPATH_SITE .DS. self::getDirectory($event->file);
			}
		
			if(is_file($file)){
				// detect the type of file
				switch(JFile::getExt($file)){
					case 'js':
						$url = preg_replace( '/\\\\+/', '/', JURI::root() . $event->file);
						echo '<script src="'. $url .'">';
						break;
						
					case 'css':
						$url = preg_replace( '/\\\\+/', '/', JURI::root() . $event->file);
						echo '<link rel="stylesheet" type="text/css" href="'. $url .'">';					
						break;
						
					default:
						// include the file
						try {
							include $file;
						} catch (Exception $e) {
							$errors[] = $e->getMessage();
						}
						
						break;
				}
			}

			if(!empty($event->script)){
				$script = $event->script;
				try {
					eval("?> $script <?php ");
				} catch (Exception $e) {
					$errors[] = $e->getMessage();
				}
			}
			
		}

		// trigger joomla plugins
		JPluginHelper::importPlugin( $comAlias );
		
		// Joomla! 1.6 - 1.7 - 2.5
		if (version_compare($version->RELEASE, '2.5', '<='))
		{	
			$dispatcher = JDispatcher::getInstance();
		} else {
			$dispatcher = JEventDispatcher::getInstance();
		}
		
		$dispatcher->trigger( $type, $data );
	}
	
	/**
	 * Method to load and return a view object. This method first looks in the
	 * current template directory for a match and, failing that, uses a default
	 * set path to load the view class file.
	 *
	 * Note the "name, prefix, type" order of parameters, which differs from the
	 * "name, type, prefix" order used in related public methods.
	 *
	 * @param   string  $name    The name of the view.
	 * @param   string  $prefix  Optional prefix for the view class name.
	 * @param   string  $type    The type of view.
	 * @param   array   $config  Configuration array for the view. Optional.
	 *
	 * @return  mixed  View object on success; null or error result on failure.
	 *
	 * @since   12.2
	 * @note    Replaces _createView.
	 * @throws  Exception
	 */
	public static function createView($component, $name, $frontend = true, $config = array(), $type = 'html')
	{
		// Clean the view name
		$component = preg_replace('/[^A-Z0-9_]/i', '', strtolower($component));
		$name = preg_replace('/[^A-Z0-9_]/i', '', strtolower($name));
		$type = preg_replace('/[^A-Z0-9_]/i', '', strtolower($type));

		// Build the view class name
		$viewClass = ucfirst($component) .'CkView'. ucfirst($name);
		
		if (!class_exists($viewClass))
		{
			$baseRoot = JPATH_SITE;
			if(!$frontend){
				$baseRoot = JPATH_SITE .DS. 'administrator';
			}
			
			$viewFilepath = $baseRoot . DS . 'components' .DS. 'com_' . $component .DS. 'views' .DS. $name;
			$viewFilename = 'view.'. $type .'.php';
			$path = $viewFilepath .DS. $viewFilename;

			if (is_file($path))
			{
				require_once $path;

				if (!class_exists($viewClass))
				{
					throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_CLASS_NOT_FOUND', $viewClass, $path), 500);
				}
			}
			else
			{
				return null;
			}
		}

		return new $viewClass($config);
	}

	function callStack(){
		$stacktrace = debug_backtrace();
		$html = str_repeat("=", 50) ."\n";
		$i = 1;
		foreach($stacktrace as $node) {
			$html .= "$i. ".basename($node['file']) .":" .$node['function'] ."(" .$node['line'].")\n";
			$i++;
		}
		$html .= str_repeat("=", 50) ."\n";
		
		return $html;
	}
	
	public static function formatDateTimeMicroSeconds($microseconds){
		$mil = round($microseconds * 1000);
		$seconds = floor($mil / 1000);
		$fraction = substr($mil, -3);
		$showdate = date('d M Y H:i:s',$seconds) . ".$fraction";
		
		return $showdate;
	}
	
	public static function writeFile($content, $opts = array(), $comAlias = null){
		if(empty($comAlias)) return false;
				
		// upload setting
		$options = array(
			'allowedExtensions' => 'jpg,png,jpeg,gif',
			'root' => '[DIR_FILES]' .DS. 'temp',
			'rename' => '{BASE}.{EXT}',
			'maxSize' => null,
			'indirect' => 'false',
			'attrs' => '',
			'overwrite' => 'suffix',
			'remote' => false
		);

		$options = array_merge($options, (array)$opts);
	
		$helper = $comAlias .'Helper';		
		if(!empty($options['filepath'])){
			$destFilePath = $options['filepath'];
		} else {
			$destFilePath = $options['root'] .DS. self::generateRandomString(15) .'.jpg';
		}
		// change the DIRECTORIES placeholder to the right value
		$newFolder = $helper::getDirectory($destFilePath);	

		$extensions = explode(',',$options['allowedExtensions']);
		
		// create the folder/subfolders if it doesn't exist
		$uploadClassName = $comAlias .'ClassFileUpload';
		$uploadClass = new $uploadClassName(dirname($newFolder));
		$dir = $uploadClass->uploadFolder;
		$mime_types = $uploadClass->getMimeTypes();
	
		$ext_array = array();
		foreach($extensions as $ext){
			$ext = trim($ext);
			$mime = $mime_types[$ext];
			$ext_array[$mime][] = $ext;
			$ext_array['application/force-download'][] = $ext;
		}
		
		foreach($ext_array as $key => $val){
			$ext_array[$key] = implode(',',$val);
		}
		
		$uploadClass->setAllowed($ext_array);

		$uploadFile = array(
			'tmp_name' => '',
			'size' => 0,
			'content' => $content,
			'name' => basename($newFolder)
		);
		$value = $uploadClass->uploadFile($uploadFile, $options);
		$filename = $value->filename;
	
		if($filename != ''){
			$filename = dirname($destFilePath) .'/'. $filename;
			$filename = trim(preg_replace("#/+#", "/", str_replace('\\','/',$filename)),'/');
			
			return $filename;
		}

		return false;
	}
	
	
	public static function getSubForm($element){	
		// Initialize variables.
		if(!($element instanceof SimpleXMLElement)){
			return false;
		}

		$xml = $element->children()->asXML();		
		if(empty($xml)){
			return;
		}

		$fieldname = (string)$element->attributes()->name;
		
		// replace first occurrence
		$xml = preg_replace('/fset/', 'fieldset', $xml, 1);
				
		// replace last occurrence
		$xml = substr_replace($xml, 'fieldset', strrpos($xml, 'fset'), strlen('fset'));
		
		// protect subfieldsets
		self::protect($xml, 'fset', '<protected>','</protected>');
				
		$xml = str_replace(array('<fs ','</fs>'), array('<fields ','</fields>'), $xml);
		$xml = str_replace(array('<fi ','</fi>'), array('<field ','</field>'), $xml);

		// unprotect subfieldsets
		self::unprotect($xml, '<protected>','</protected>');
	
		
		$xml = '<?xml version="1.0" encoding="UTF-8"?><form>'
			.	$xml
			.	'</form>';

		$subForm = new JForm($fieldname);
		$subForm->load($xml);

		if(!($subForm instanceof JForm)){
			return false;
		}
		
		return $subForm;
	}
	
	/**
	 * replace any protected text to original
	 */
	public static function unprotect(&$str,$protect_a = '<!-- >> PROTECTED >> -->', $protect_b = '<!-- << PROTECTED << -->')
	{		
		$regex = '#' . preg_quote($protect_a, '#') . '(.*?)' . preg_quote($protect_b, '#') . '#si';
		while (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match){
				$str = str_replace($match['0'], base64_decode($match['1']), $str);
			}
		}
	}

	public static function protect(&$str, $tag, $protect_a = '<!-- >> PROTECTED >> -->', $protect_b = '<!-- << PROTECTED << -->')
	{
		$regex = '#<' . preg_quote($tag, '#') . '[\s>](.*?)</' . preg_quote($tag, '#') . '>#si';
		if (preg_match_all($regex, $str, $matches,PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$xml = $match[0];
				// test for nested pattern
				if(strpos($match[1],'<'.$tag .' ') !== false){

					$random = self::generateRandomString(20);
					// replace first tag ONLY
					$xml = preg_replace("/". $tag ."/", $random, $xml, 1);
					
					self::protect($xml, $tag, $protect_a, $protect_b);
					$xml = str_replace($random, $tag, $xml);
					
					$str = str_replace($match[0], $xml, $str);					
					self::protect($str, $tag, $protect_a, $protect_b);
				} else {				
					$protected = $protect_a . base64_encode($xml) . $protect_b;
					$str = str_replace($match[0], $protected, $str);
				}
			}
		}
	}
	
	public static function str_replace_last( $search , $replace , $str ) {
		if( ( $pos = strrpos( $str , $search ) ) !== false ) {
			$search_length  = strlen( $search );
			$str    = substr_replace( $str , $replace , $pos , $search_length );
		}
		return $str;
	}
	
	public static function testXML($xml, $errorMsg, $writeFullXmlInLog = false){
		$app = JFactory::getApplication();
		
		if(empty($errorMsg)){
			$errorMsg = 'XML errors! check the log file in your log folder.';
		}
		
		$logMsg = "\n\n---------------XML DATA START---------------\n";
		$logMsg .= $writeFullXmlInLog ? $xml : substr($xml,0,300).'...';
		$logMsg .= "\n----------------XML DATA END----------------\n";
		
		libxml_use_internal_errors(true);
		$doc_test = simplexml_load_string($xml);
		$xml_test = explode("\n", $xml);
		
		if (!$doc_test) {
			$errors = libxml_get_errors();

			$msg = array();
			foreach ($errors as $error) {
				$msg[] = self::display_xml_error($error, $xml_test);
			}

			JLog::add($errorMsg ."\n". $logMsg . implode("\n",$msg), JLog::WARNING, 'bygirohelper');
			$app->enqueueMessage($errorMsg, 'error');
			libxml_clear_errors();
			return false;
		}
		
		return true;
	}
	
	public static function display_xml_error($error, $xml)
	{
		$return  = $xml[$error->line - 1] . "\n";
		$return .= str_repeat('-', $error->column) . "^\n";

		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code: ";
				break;
			 case LIBXML_ERR_ERROR:
				$return .= "Error $error->code: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code: ";
				break;
		}

		$return .= trim($error->message) .
				   "\n  Line: $error->line" .
				   "\n  Column: $error->column";

		if ($error->file) {
			$return .= "\n  File: $error->file";
		}

		return "$return\n\n--------------------------------------------\n\n";
	}
	
	/**
	 * Get a list of the user groups.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public static function getUserGroups()
	{
		static $options;
		
		if(!empty($options)) return $options;
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level, a.parent_id')
			->from('#__usergroups AS a')
			->join('LEFT', '`#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft, a.rgt, a.parent_id')
			->order('a.lft ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}
}
?>
