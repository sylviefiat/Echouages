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
	

class AppPlugin{
	
	protected $_VERSION = 1.5;
	
	public $app = null;
	
	protected  $_BASE, $_INCLUDES, $_LANGUAGE,  $_HTTP, $_JS, $_CSS, $_IMAGES	  ;
	
	public $params = null;

	/**
	 * 
	 * @var JDocument
	 */
	protected $document;
	
	/**
	 * 
	 * @var JDatabaseDriver
	 */
	protected $db;
	
	/**
	 * 
	 * @var int
	 */
	public $jid;
	
	/**
	 * 
	 * @var AppStopClass
	 */
	public $stop ;
	
	public $redirectURL = null ;
	public $jsOnSubmit = "";
	public $isError = 0;
	public $forceTmpDelete = 0;
	protected  $jobs, $values,$error;
	
	/**
	 * 
	 * @var ProformsLegacyStorage
	 */
	protected $storage;
	
	/**
	 * 
	 * @var MPayPal
	 */
	protected  $paypal = null;
	
	/**
	 * 
	 * @var ProformsViewForm
	 */
	protected $view = null;
	
	public function __construct($app = null, $jid = null, $params = null){
		if($app) $this->app = $app;
		if($jid) $this->jid = $jid;
		$this->params = $params ? $params : new stdClass();
		
		$this->document = JFactory::getDocument();
		$this->db = JFactory::getDBO();
			
		$this->_BASE = M4J_APPS_BASE .  $this->app .DS ;
		$this->_INCLUDES = $this->_BASE . "includes" .DS;
		$this->_LANGUAGE = $this->_BASE . "language" .DS;
		
		$this->_HTTP = M4J_HTTP_APPS . $this->app ."/" ;
		$this->_JS = $this->_HTTP . "js/";
		$this->_CSS = $this->_HTTP . "css/";
		$this->_IMAGES = $this->_HTTP . "images/";
		
		$this->stop = AppPluginManager::createStopForPlugin();
		
		$this->init();
	}	

	public function init(){
		return true;
	}

	
	public function onAfterFormElementCreation(){
		return;
	}
	
	
	public function setVSE( & $values, & $storage, & $error){
		$this->values = & $values;
		$this->storage = & $storage;
		$this->error = & $error;
		
		$this->onValidate();
	}
	
	public function onValidate(){
		return false;
	}
	
	public function applyError($state = 0){
		$this->isError = $state;
	}
	
	
	public function onError(){
		return false;
	}
	
	public function onSuccess(){
		return false;
	}
	
	/**
	 * Called after creating the paypal object
	 *  @param MPayPal $paypal Reference of the Paypal Object
	 */
	public function onPaypal(& $paypal){
		return false;
	}
	
	/**
	 * 
	 * @param JMail $mail
	 * @param JMail $confirmMail
	 * @param array $upload_heap
	 * @return boolean
	 */
	public function onBeforeEmail(& $mail, & $confirmMail, & $upload_heap){
		return false;		
	}
	
	public function onAfterSending(& $afterSendingBuffer){
		return false;
	}
	
	public function onAfterSendingEnd(){
		return false;
	}
		
	/**
	 * Called on building the title of a form
	 * Doesn't require to return anything.
	 * @param	string	$title	The refernce of the title variable
	 */
	public function onTitle( & $title){
		return false;
	}
	
	/**
	 * Called after the title of a form and before rendering the content (main text)
	 * Outputs can be echoed or returned 
	 */
	public function onBeforeContent(){
		return false;
	}
	
	/**
	 * Called on rendering the content (main text) of a form
	 * Doesn't require to return anything.
	 * This method can be used to modify the main text of a form
	 * @param	string	$content	The refernce of the content variable
	 */
	public function onContent(& $content){
		return false;
	}
	
	/**
	 * 
	 * Called for each form template
	 * elements are the raw database data for all including form elements
	 * fid is the form template id
	 * 
	 * @param stdClass $elements
	 * @param int $fid
	 */
	public function onTemplate(& $elements =null, $fid = null, $isSend = 0){
		return false;
	}
	
	
	/**
	 * Called after rendering the content (main text) of a form
	 * Outputs can be echoed or returned 
	 */
	public function onAfterContent(){
		return false;
	}
	
	/**
	 * Called inside the form tag at first
	 * Outputs can be echoed or returned 
	 */
	public function formHead(){
		return false;
	}
	
	/**
	 * Called inside the form tag at the end.
	 * But it is called before the confirmation question and before the captcha and submit button
	 * Outputs can be echoed or returned 
	 */
	public function formFooter(){
		return false;
	}
	
	/**
	 * Called at the end of the form site
	 * Outputs can be echoed or returned 
	 */
	public function atEnd(){
		return false;
	}
	
	/**
	 * Adds an error message to the error buffer at the form validation
	 * As long as the error buffer is empty a form will be submitted
	 * If there is at least one character in the buffer no form will be submitted.
	 *
	 * @param	string	$errorText	The error message which shall be added.
	 */
	public function addError($errorText = null){
		$this->error .= ProformsHelper::errorTag($errorText); 
	}
	
	
	public function disable($stopKey = null){
		$stopKey = strtolower(trim($stopKey));
		if(isset($this->stop->$stopKey)){
			$this->stop->$stopKey = 1;
		}
	}
	
	public function enable($stopKey = null){
		$stopKey = strtolower(trim($stopKey));
		if(isset($this->stop->$stopKey)){
			$this->stop->$stopKey = 0;
		}
	}
	
	public function addOnSubmitJS($jsFunctionName= null){
		if($jsFunctionName) $this->jsOnSubmit .= 'addValidationFunction("'.trim($jsFunctionName).'"); '."\n";
	}
	
	public function getOnSubmitJS(){
		return $this->jsOnSubmit;
	}
	
	public function setRedirect($url = null){
		$this->redirectURL = JRoute::_($url);
	}
	
	public function redirect(){
		if($this->redirectURL){
			ProformsHelper::redirect($this->redirectURL);
		}
	}
	
	public function setJobs( & $jobs){
		$this->jobs = & $jobs;
	}
	
	public function footerScript($src = null){
		if($src) {
			$this->view->addEndScript($src);
		}
	}
	
	public function footerScriptDeclaration($code = null){
		if($code) {
			$this->view->addEndScriptDeclaration($code);
		}
	}
	
	public function setForceTmpDelete($status = 1){
		$this->forceTmpDelete = $status;
	}
	
	public function setValue($eid = null, $value = null){
		return $this->view->setElementValue($eid,$value);				
	}
	
	public function setValueByAlias($alias = null, $value = null){
		$formElement = $this->view->getFormElementByAlias($alias);
		return  $formElement ? $formElement->setValue($value) : false;
	}
	
	public function getValue($eid = null){
		$formElement = $this->view->getFormElement($eid);
		return $formElement ? $formElement->getValue() : false;
	}
	
	public function getValueByAlias($alias = null){
		$formElement = $this->view->getFormElementByAlias($alias);
		return $formElement ? $formElement->getValue() : false;
	}
	
	public function & getAllFormElements(){
		return $this->view->getAllFormElements();
	}
	
	
	public function setUploadRootDir($rootDir = M4J_TMP){
		$this->view->setTmpPath($rootDir);
	}
	
	public function setUploadDir($tmpDir = null){
		$this->view->setTmpDir($tmpDir);
	}
	
	/**
	 * 
	 * @param ProformsViewForm $view
	 */
	public function setView( ProformsViewForm & $view = null ){
		$this->view = & $view;
	}
}
