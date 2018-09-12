<?php
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

// App Plugins
$GLOBALS['appplugins'] = array();	

class AppStopClass extends stdClass{
		public $email  = 0;
		public $confirmation  = 0;
		public $redirection  = 0;
		public $datastorage  = 0;
		public $title  = 0;
		public $maintext  = 0;
		public $css  = 0;
		public $customscript  = 0;
		public $paypal  = 0;
		public $aftersending  = 0;
		public $deletetemp  = 0;
		public $aftersendingscript  = 0;		
}

class AppPluginManager{
	
	var $appPlugins = array();
	var $jid;
	/**
	 * 
	 * @var AppStopClass
	 */
	public $stop ;
	var $forceTmpDelete = 0;
	
	/**
	 * 
	 * @var ProformsViewForm
	 */
	protected $view = null;
	
	
	public function __construct(){
		$this->stop = $this->createStop();
	}
	
	public function setJID($jid){
		$this->jid = $jid;
	}
	
	/**
	 * @return	AppStopClass
	 */
	public function createStop(){
		return  new AppStopClass();
	}
	
	public static function createStopForPlugin(){
		return new AppStopClass();
	}
	
	
	/**
	 * @return	AppStopClass
	 */
	public function & getStop(){
		return  $this->stop;
	}
	
	public function isStop($key = null, $default = 0){
		if($key && isset($this->stop->$key)){
			return $this->stop->$key;
		}else return $default;
	}
	
	public function analyseStop(){
		foreach($this->stop as $key=>$value){
			foreach($this->appPlugins as $plugin){
				$this->stop->$key = (int) ($this->stop->$key || $plugin->stop->$key) ;
			}
		}
	}

	public function analyseForceTmpDelete(){
		foreach($this->appPlugins as $plugin){
				/* @var $plugin AppPlugin */
				$this->forceTmpDelete = (int) ( $this->forceTmpDelete || $plugin->forceTmpDelete ) ;
			}
	}
	
	public function isForceTmpDelete(){
		return $this->forceTmpDelete;
	}
	
	public function add($app = null,$paramsRaw = null){
		if(!$app) return false;
		$params = $this->_decodeParameters(unserialize($paramsRaw)) ;
		$params = ($params !== false) ? $params : new stdClass();
		if(JFile::exists(M4J_APPS_BASE .$app . DS . "plugin.php" )){
			include_once (M4J_APPS_BASE .$app . DS . "plugin.php" );
			$className = "AppPlugin" . ucfirst(strtolower($app));
			if(class_exists($className) && get_parent_class($className)== "AppPlugin" ){
				array_push($this->appPlugins, new $className($app, $this->jid , $params ) );				
			}//EoF is the right class
		}//EOF file exists
	}
	
	protected function _decodeParameters($parameters){
		if(empty( $parameters) ) return null;
	
		foreach ($parameters as & $parameter){
			$parameter = bDec($parameter);
		}
		return $parameters;
	}
	
	
	public function onAfterFormElementCreation(){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onAfterFormElementCreation();
		}
	}
	
	public function onValidate( & $values, & $storage, & $error){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->setVSE($values,$storage,$error);
		}
	}
	
	public function onError(){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->applyError(1);
			$plugin->onError();
		}
	}
	
	public function onSuccess(){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onSuccess();
		}
	}
	
	public function onPaypal(& $paypal){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->paypal = & $paypal;
			$plugin->onPaypal($paypal);
		}
	}
	
	public function onBeforeEmail(& $mail, & $confirmMail, & $upload_heap){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onBeforeEmail($mail, $confirmMail, $upload_heap);
		}
	}
	
	public function onAfterSending(& $afterSendingBuffer){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onAfterSending($afterSendingBuffer);
		}
	}
	
	public function onAfterSendingEnd(){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onAfterSendingEnd();
		}
	}
	
	public function onTitle( & $title){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onTitle($title);
		}
	}
	
	public function onBeforeContent(){
		ob_start();
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$out = $plugin->onBeforeContent();
			if($out) echo $out;
		}
		return ob_get_clean();
	}
	
	public function onContent(& $content){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onContent($content);
		}
	}
	
	public function onTemplate(& $elements =null, $fid = null, $isSend = 0){
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->onTemplate($elements, $fid, $isSend);
		}
	}
	
	public function onAfterContent(){
		ob_start();
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$out = $plugin->onAfterContent();
			if($out) echo $out;
		}
		return ob_get_clean();
	}
	
	public function formHead(){
		ob_start();
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$out = $plugin->formHead();
			if($out) echo $out;
		}
		return ob_get_clean();
	}
	
	public function formFooter(){
		ob_start();
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$out = $plugin->formFooter();
			if($out) echo $out;
		}
		return ob_get_clean();
	}
	
	public function atEnd(){
		ob_start();
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$out = $plugin->atEnd();
			if($out) echo $out;
		}
		return ob_get_clean();
	}

	public function onSubmitCallBacks(){
		$onSubmitJS = "";
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$onSubmitJS .= $plugin->getOnSubmitJS();
		}
		
		if($onSubmitJS){
			$this->view->addEndScriptDeclaration($onSubmitJS);
		}
		
	}
	
	/**
	 * 
	 * @param ProformsViewForm $view
	 */
	public function applyFormView( & $view = null){
		$this->view = & $view;
		
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->setView($view);
		}
	}
	
	public function applyJobsReference(& $jobs){
	
		foreach($this->appPlugins as $plugin){
			/* @var $plugin AppPlugin */
			$plugin->setJobs($jobs);
		}
	}
	
	public function debug(){
		MDebug::pre($this->appPlugins);
	}
	
	public static function & getInstance(){
		static $instance;

		if (!is_object($instance)){
			$instance = new AppPluginManager();
		}
		return $instance;
	}//EOF getInstance
	
}


?>