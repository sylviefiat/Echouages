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


// get the file class
jimport('joomla.filesystem.file');
// get the folder class
jimport('joomla.filesystem.folder');
// get the path class
jimport('joomla.filesystem.path');

if( ! class_exists('MDB')){
	require_once JPATH_ROOT . '/administrator/components/com_proforms/includes/mdb.php';
}

if( ! class_exists('ProformsHelper')){
	require_once JPATH_ROOT . '/components/com_proforms/includes/helpers.php' ;
}

if( ! class_exists('ProformsView')){
	require_once JPATH_ROOT . '/components/com_proforms/includes/proformsview.php' ;
}

if( ! class_exists('ProformsModel')){
	require_once JPATH_ROOT . '/components/com_proforms/includes/proformsmodel.php' ;
}


class Proforms {
		
	protected static $send;
	protected static $token;
	
	protected $automaticProcess = false;
	
	protected $view = 'form';
	
	protected $cid = false,
			  $jid = false,
			  $app = false,
			  $opt = false,
			  $dummy = false;
			  

	protected $Itemid = null;
	
	/**
	 *
	 * @var string
	 */
	protected $includePath = null;
	
	/**
	 *
	 * @var string
	 */
	protected $viewsPath = null;
	
	/**
	 * 
	 * @var string
	 */
	protected $buffer = '';
	/**
	 * 
	 * @var ProformsView
	 */
	protected $viewObject = null;
	
	public function __construct($view = 'automatic', $jid = 0, $cid= 0){
		$this->_joomlaVersion();
		$this->Itemid = JRequest::getInt( 'Itemid', NULL );
		$view = trim($view);
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		$this->includePath = dirname(__FILE__);		
		$this->viewsPath = JPATH_ROOT . '/components/com_proforms/views';
		$this->includeConfiguration();
		$this->includeLanguageFiles();
		$this->includeLegacy();		
		
		$this->cid = JRequest::getInt('cid', 0);
		$this->jid = JRequest::getInt('jid', 0);
		$app = strtolower( JRequest::getString("app",null) );
		$this->app = $app ? $app : false;	
		$this->dummy = (bool) JRequest::getInt('dummy', null);
		
		// Automatic processing means the Proforms class does automatically route the view, starts the view and renders the view
		if($view == 'automatic'){
		    
			if($cpta = JRequest::getInt('cpta',false)){
				$this->captcha($cpta);
				exit();
			}
			$this->_info();

			// Need to do some weird stuff because of legacy dependencies
			$_view = JRequest::getCmd('view', null);
			$_view = ( ! $_view && $this->jid) ? 'form' : $_view;
			$_view  = ($this->dummy) ? 'form' : $_view;
			$_view = ($this->cid && ! $this->jid) ? 'category' : $_view;
			$_view = ($this->app) ? 'app' : $_view;
			$view = (! $_view ) ? 'default' : $_view;
			
			$this->automaticProcess = true;
		}else{
			$this->jid = (int) $jid;
			$this->cid = (int) $cid;
		}		
		
		

		// Directly exit if a bot spamming is detected
		if($view == 'form' && isset($_REQUEST['email']) && $_REQUEST['email'] ) {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			exit();
		}
				
		if(JFile::exists($this->viewsPath.'/' .$view ."/view.php")){
			include_once ($this->viewsPath.'/' .$view ."/view.php");
			$className = "ProformsView" . ucfirst(strtolower($view));
			if(class_exists($className) && get_parent_class($className)== "ProformsView" ){
				$this->viewObject = new $className($this);
			}else{
				$this->viewObject = null;
			}
			
		}else{
			$this->viewObject = null;
		}
		
		//Execute the view
		if($this->isAutomatic() && is_object($this->viewObject)){
			$this->viewObject->start()->render();
			$this->buffer =  (string) $this->viewObject;
		}
		
	}
	
	/**
	 * 
	 * @return Proforms
	 */
	public function & renderView(){
		$this->viewObject->start()->render();
		$this->buffer =  (string) $this->viewObject;
		return $this;
	}
	
	
	
	protected function captcha($id = 0){
		if($id< 3 || $id>5) exit();
		$path = JPATH_ROOT . '/components/com_proforms/sec/im' . $id . '.php';
		if(file_exists( $path )){
			include_once $path;
		}else exit();
	}
	
	protected function _joomlaVersion(){
		if(defined('_M4J_IS_J16')) return;
		// Joomla version detection
		$jVersion = new JVersion;
		$j = $jVersion->getShortVersion();
		$jsub = floatval(substr($j,0,3));
		if($jsub == 1.5 ){
			define("_M4J_IS_J16" ,0);
			define("_M4J_IS_J30" ,0);
			define("_M4J_IS_J32" ,0);
		}else if($jsub > 1.5 && $jsub <3.0){
			define("_M4J_IS_J16" ,1);
			define("_M4J_IS_J30" ,0);
			define("_M4J_IS_J32" ,0);
		}else if($jsub > 2.6 && $jsub <3.2){
			define("_M4J_IS_J16" ,1);
			define("_M4J_IS_J30" ,1);
			define("_M4J_IS_J32" ,0);
		}else{
			define("_M4J_IS_J16" ,1);
			define("_M4J_IS_J30" ,1);
			define("_M4J_IS_J32" ,1);
		}
	}
	
	
	public function isAutomatic(){
		return (bool) $this->automaticProcess;
	}
	
	protected function includeLegacy(){
		static $legacyIsIncluded;
		if(! empty($legacyIsIncluded)) return;
		
		if(! defined('M4J_ABS')){
			define("M4J_ABS", JPATH_ROOT);
		}
		
		
		require_once JPATH_ROOT . '/administrator/components/com_proforms/includes/evolution.php';
		
		
		if(! defined('M4J_APPS_BASE')){
			require_once JPATH_ROOT . '/components/com_proforms/frontend.defines.proforms.php';
		}		
		
		
		$legacyIsIncluded = true;
	}
	
	protected function includeConfiguration(){
		static $configIsIncluded;
		if(! empty($configIsIncluded)) return;
		
		$configPath = JPATH_ROOT . '/administrator/components/com_proforms/config.proforms.php';
		if(file_exists($configPath)) require_once($configPath);
		$configIsIncluded = true;
	}
	
	
	protected  function includeLanguageFiles(){
		static $langIsIncluded;
		if(! empty($langIsIncluded)) return;
				
		$languagePath = JPATH_ROOT . '/components/com_proforms/language';
		$lang = ProformsHelper::getLang();
		if( file_exists( $languagePath.'/frontend.' . $lang.'.php' ) ) include_once( $languagePath.'/frontend.' . $lang.'.php' );
		else include_once(  $languagePath.'/frontend.en.php' );
		$langIsIncluded  = true;
	}
	
	/**
	 * @return	ProformsView
	 */
	public function & getView(){
		return $this->viewObject;
	}
	
	public function __toString(){
		return $this->buffer;
	}
	
	protected function _info(){
		if( JRequest::getString("option",null) === "com_proforms" && JRequest::getInt("info",0)){
			ob_get_clean();
			die( M4J_UNIQUE_ID );
		}
	}
	
	
	/**
	 * @return	boolean
	 */
	public static function isSend(){
		if(empty(self::$send)){
			$token = JRequest::getCmd('proformstoken', null);
			$send = (bool) JRequest::getInt('send',0);
			if($token && $send){
				self::$send = $send;
				self::$token = $token;
			}
		}
		return self::$send;
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public static function getToken(){
		return self::$token;
	}
	
	/**
	 * 
	 * @param string $view
	 * @param int	$jid
	 * @param int	$cid
	 * @return Proforms
	 */
	public static function getInstance($view = 'automatic', $jid = 0 , $cid = 0){
		return new Proforms($view, $jid, $cid);
	}
	

	public function getCid()
	{
	    return $this->cid;
	}

	public function getJid()
	{
	    return $this->jid;
	}

	public function getApp()
	{
	    return $this->app;
	}

	public function getOpt()
	{
	    return $this->opt;
	}

	public function getDummy()
	{
	    return $this->dummy;
	}

	public function getItemid()
	{
	    return $this->Itemid;
	}
	

}//EOF class