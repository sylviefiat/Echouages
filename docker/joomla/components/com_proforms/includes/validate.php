<?PHP
/**
* @name MOOJ Proforms 
* @version 1.0
* @package proforms
* @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
* @author Dipl. Inf.(FH) Fahrettin Kutyol
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Please note that some Javascript files are not under GNU/GPL License.
* These files are under the mad4media license
* They may edited and used infinitely but may not repuplished or redistributed.  
* For more information read the header notice of the js files.
**/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class M4J_validate {


	function email($email){
			$email = trim($email);
			return (preg_match('/^([a-zA-Z0-9!#?^_`.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,6})$/', $email)) ;
	}


	function multipleEmail($email)
		{
		if(substr_count($email,";") == 0 && substr_count($email,",") == 0) return $this->email($email);
		else
			{
			$emails = preg_split("/[;,]+/", $email);
			
			$isMail = true;
			
			foreach($emails as $mail)
				{
				if($mail != "" && !$this->email($mail)) $isMail = false;
				}
			return $isMail;
			}
		
		
		}


	function url($url)
		{
		$url = trim($url);
		$regex = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,6}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
		if (preg_match ($regex, $url)) return true;
		else return false;
		}

	function checkRegEx($pattern,$value){
		if (preg_match($pattern,$value)) return true;
		else return false;
	}
	
	function notAlphabetic($value){
		return $this->checkRegEx('/[^A-Za-z\\s]/',$value);
	}
	
	function notAlphanumeric($value){
		return $this->checkRegEx('/[^A-Za-z0-9\\s]/',$value);
	}
	
	function notNumeric($value){
		return $this->checkRegEx('/[^0-9\.]/',$value);
	}

	function notInteger($value){
		return $this->checkRegEx('/[^0-9]/',$value);
	}	
	
	
	// check Access	
	public static function access($required = 0, $plattform = "1.5"){
		
		if(class_exists('ProformsHelper')){
			return ProformsHelper::access($required, $plattform);
		}
		
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
	
	
	public static function & getInstance(){
		static $instance;
		if(empty($insance)){
			$instance = new M4J_validate();
		}
		return $instance;
	}
	
	
}//EOF Class Validate

/**
 *  Create a global validate object (Legacy)
 *  @deprecated	Use singleton for future.
 */
$validate = M4J_validate::getInstance();
$GLOBALS['validate'] = & $validate;
