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


if(! defined('PFM_AFTERSENDING_DEFAULTTEXT')){
	define('PFM_AFTERSENDING_DEFAULTTEXT', 0);
	define('PFM_AFTERSENDING_REDIRECT', 1);
	define('PFM_AFTERSENDING_CUSTOMTEXT', 2);
	define('PFM_AFTERSENDING_PAYPAL', 3);
	define('PFM_AFTERSENDING_OPT', 4);
	define('PFM_AFTERSENDING_APP', 5);
	
	
	define('PFM_CONFIRMATION_EMAIL_NEVER', 0);
	define('PFM_CONFIRMATION_EMAIL_EVER', 1);
	define('PFM_CONFIRMATION_EMAIL_ASK', 2);
}

class ProformsViewForm extends ProformsView{
	
	protected static $index = 1;
	
	protected $uniqueIndex = 0;
	
	protected $jid = 0;
	protected $send = 0;
	protected $token = 0;
	
	protected $hiddenFields = array();
	/**
	 * 
	 * @var MFormElement[]
	 */
	protected $formElements = array();
	protected $elementsOrder = array();
	protected $aliasToElements = array();
	
	protected $isTooltip = 1;
	
	protected $requiredAdvice = 1;
	
	protected $isInIframe = 0;
	
	protected $noResponsive = 0;
	
	protected $isRaw = 0;
	
	protected $stid = false;
	
	protected $mailcopy = 0;
	
	protected $userMailElement = null;
	
	protected $endScripts = array();
	
	protected $errorIds = array();
	
	protected $isPaypal = 0;
	
	protected $emailAddress = null;
	protected $confirmationEmailAddress = null;
	
	protected $uploads = array();
	protected $uploadHeap = array();
	
	/**
	 * 
	 * @var MPayPal
	 */
	protected $paypal = null;
	
	/**
	 * @var	AppStopClass
	 */
	protected $stop = null;
	
	/**
	 * 
	 * @var bool
	 */
	protected $sendingSuccess = false;
	
	/**
	 * 
	 * @var AppPluginManager
	 */
	protected $pluginManager = null;
	
	protected $isDummy = false;
	/**
	 * 
	 * @var ProformsModelForm
	 */
	protected $model = null;
	
	/**
	 * 
	 * @var string
	 */
	protected $tmpPath = null;
	
	/**
	 *
	 * @var string
	 */
	protected $tmpDir = null;
	
	/**
	 * 
	 * @var Opt
	 */
	protected $opt = null;
	
	/**
	 * 
	 * @var MCaptcha
	 */
	protected $captcha = null;
	
	/**
	 * 
	 * @var string
	 */
	protected $error = '';
	
	/**
	 * Double opt in hash
	 * @var string
	 */
	protected $optInIdent = null;
	

	protected $dataListBuffer = '';
	protected $submissionListBuffer = '';
	
	
	protected function _init(){
		$this->_loadLegacy();
		$this->isDummy = (bool) $this->parent->getDummy();
		
		
		if(  ! $this->isDummy ){
			
			$this->jid = (int) $this->parent->getJid();
			$this->uniqueIndex = ProformsViewForm::getIndex();
			$this->model->setJid($this->jid);
			
			if(! $this->model->isAvailable() ){
				$this->template = 'noform';
				return;
			}
			
			if( ! $this->model->getAllowed() ){
				$this->template = 'noaccess';
				return;
			}
			
			$this->_includeFormLib();
			$this->_includeAppPluginLib();
			$this->_initAppPluginManager();
			
			
			$uidx = JRequest::getInt('uniqueindex', null);
			$send = (bool) JRequest::getInt('send',0);
			
			$this->send = ( $send && $uidx === $this->uniqueIndex  );
			
			$this->mailcopy = JRequest::getInt('mailcopy', 0);
			
			// Perform App Plugin Manager on Templates
			$this->model->performAppPluginOnTemplates($this->pluginManager, $this->send);			
			
			$this->_createHiddens();
			
		}else{
			$this->template = 'dummy';
			$this->_includeAppPluginLib();
			$this->pluginManager = new AppPluginManager();
			$this->stop = & $this->pluginManager->getStop();
		}

		$this->_styleSheet();
		$this->_scripts();
		
		
		
// 		MDebug::pre($this->pluginManager);
		
	}
	
	protected function _timeTrapStart(){
		if(! definedAndBool('M4J_USE_TIMETRAP') ) return;
		
		$ident = 'PROFORMS_TIMETRAP_' . $this->jid . '_'.$this->uniqueIndex;
		$now  = microtime(true);
		$app = JFactory::getApplication();
		$app->setUserState($ident, $now);
		
	}
	
	protected function _timeTrapSent(){
		if(! definedAndBool('M4J_USE_TIMETRAP') ) return null;
	
		$ident = 'PROFORMS_TIMETRAP_' . $this->jid . '_'.$this->uniqueIndex;
		$now  = microtime(true);
		$app = JFactory::getApplication();
		$start = $app->getUserState($ident, 'PROFORMS_NO_COOKIE');
		if($start === 'PROFORMS_NO_COOKIE'){
			
			return ProformsHelper::errorTag(M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE);
		}
		$start = (float) $start;
// 		MDebug::pre('START: '.$start);

		$maxTime = definedAndValue('M4J_SUBMISSION_TIME', 10000);
		$maxTime = $maxTime / 1000;
// 		MDebug::pre('MAX TIME: '.$maxTime);

		$diff = $now - $start;
// 		MDebug::pre('DIFF: '.$diff);

		return ($diff < $maxTime) ?  ProformsHelper::errorTag(M4J_LANG_SPAMBOT_TRAPPED) : null;
		
	}

	
	
	protected function _processTemps(){
		$this->tmpPath = M4J_TMP;
		$this->tmpDir = md5(uniqid());
	}
	
	
	protected function _process(){
		if($this->isDummy){
			// Just retrurn if this is a dummy for backend preview
			return;
		}
		
		//Create the captcha object
		$uidx = $this->parent->isAutomatic() ? 0 : (int) $this->uniqueIndex ;		
		$this->captcha = new MCaptcha($this->model->getParameter('customize'), $this->model->getParameter('captcha'), $uidx);
		
		//Reset the error string
		$this->error = '';
		

		
		// Fetch params from model
		$params = $this->model->getParams();
		foreach($params as $key => $val){
			if($key != "formTemplates") $this->setParameter($key, $val);
		}
		
		$fids = $this->model->getParameter('fids', array());
		
		/***
		 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 * UGLY LEGACY STUFF BUT UNFORTUNALTELY REQUIRED
		 * This are two vars which represent the old value handling
		 * We need to emulate this for backward compatibility of Apps.
		 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 */
		
		//This is the horrible legacy values array
		$_LEGACY_VALUES = array();
		$_LEGACY_STORAGE = new ProformsLegacyStorage($this->jid, implode(";" ,$fids), $this );
		
		/***
		 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		*  EOF UGLY LEGACY STUFF 
		* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		*/		
		
		
			
		
		$this->errorIds = array();
		
		$modelUserMailId = $this->model->getUserMailId();
		
		$this->_processTemps();
			
		$formTemplates = $this->model->getParameter('formTemplates', array());
		foreach ($fids as $fid){
			$fid = (int) $fid;		
			
			if(isset($formTemplates[(int) $fid])){
				$template = & $formTemplates[(int) $fid];
				if(isset($template->elements)){
					
					//Fetch the layout type
					$type = ( isset($template->responsive) && $template->responsive ) ? 'responsive' : 'default';
					$formTemplateElements = & $template->elements;
					
					foreach( $formTemplateElements as $row){
						
						// Takeover of this view send detection
						$row->parentSend = $this->send;
						
						$_element = MFormFactory::create($row->form, $row, $type, $this->tmpPath, $this->tmpDir);
						
// 						if($_element->isUpload()){
// 							MDebug::pre($_element);
// 						}

						if($_element){
							
							//The unique id of an element
							$eid = (int) $_element->getEid();
							
							// Setting the elments order array
							array_push($this->elementsOrder, $eid);
							
							// Setting the form Element
							$this->formElements[$eid] =  $_element;
							
							// Set the reference to $element
							/* @var $element MFormElement */
							$element = & $this->formElements[$eid];
							

							
							if($element->hasAlias()){
								$this->aliasToElements[ (string) $element->getAlias()] = & $element;
							}
							
							if($element->hasUpload()){
								$this->uploadHeap[] = 'm4j-' . $eid;
								$this->uploads[$eid] = & $element;
							}
														
							
							// Setting the reference to the usermail element
							if( $eid === $modelUserMailId ){
								$element->setUniqueEmailField(true);
								$this->userMailElement = & $element;
							}
							
							if(! $element->getDisplayOnly()){
								// Apply UGLY LEGACY STUFF
								$_LEGACY_VALUES[$eid] = $element->getValue();
								$_LEGACY_STORAGE->add($fid,$eid,$element->getValue(), $element->getAlias());
								// EOF Apply UGLY LEGACY STUFF		
							}
							
							
						}//EOF if element
					}//EOF foreach
				
				}//EOF isset template elements
			}//EOF isset form template with fid
		
		}//EOF foreach fids
			
		if($this->send){
			
			// Test elements on errors
			foreach($this->formElements as & $element){
				// Test for errors ($this->error is a reference passing)
				$element->errorTest($this->error);
			}
			
			
			if(! $this->captcha->validate()){
				$this->error .= ProformsHelper::errorTag(M4J_LANG_ERROR_CAPTCHA);	
			}
			
			//App Plugin on validate
			$this->pluginManager->onValidate($_LEGACY_VALUES, $_LEGACY_STORAGE, $this->error);
			$_LEGACY_STORAGE->LEGACY_COMPARISION();
		}
				
// 		$this->error .= '<h4>THE FINAL TESTING OF APPS</h4>';
		
		
	
		//Sending time validation for the time trap only after sending
		if( $this->send &&   ! $this->error  ){
			if($trapMessage = $this->_timeTrapSent()){
				$this->error .= $trapMessage;			
			}
		}//EOF time validation
		
		
		// If there is no error we proceed with the form processing
		if( $this->send &&   ! $this->error ){
			$this->pluginManager->onSuccess();
			$_LEGACY_STORAGE->LEGACY_COMPARISION();
			return $this->_success();
		}else if($this->send &&  $this->error){
			$this->pluginManager->onError();
			$_LEGACY_STORAGE->LEGACY_COMPARISION();
			$this->_removeUploadFolder();
		}
		
		//Setting the sending time validation
		$this->_timeTrapStart();
		
		$this->_renderToLayout();
	}
	
	protected function _renderToLayout(){
		$fids = $this->model->getFids();
		
		$templates = array();
		foreach($fids as $fid){
			$templates[(int) $fid] = MLayoutList::getLayoutById($fid);
			// Just only the isTooltip prameter is not set we disallow tooltip viewing in the template
			if(! $this->isTooltip ){
				$templates[(int) $fid]->setHelp(0);
			}
		}

		$formContent = '';
		
		foreach ($this->formElements as & $element){
			/* @var $element MFormElement */
			$fid = $element->getFid();			
			$layout = & $templates[$fid];

			if($element->getDisplayOnly()){
				$layout->addHTMLRow(
						$element->getSlot(),
						(string) $element,
						$element->getEid(),
						$element);
			}else{
				$layout->addRow($element->getSlot(),
						$element->getQuestion(),
						(string) $element ,
						$element->getRequired(),
						$element->getHelp(),
						$element->getAlign(),
						$element->getUsermail(),
						$element->getEid(),
						$element->getIsHidden(),
						$element);
			}
		}//EOF foreach

		foreach ($fids as $fid){
			$formContent .=	$templates[(int) $fid]->render();
		}
		
		$this->setParameter('formContent', $formContent);
	}
	
	
	
	protected function _success(){
		
		
		$process = 0;
		$this->_email();
		
		//Removing the uploads on the server
		$this->_removeUploadFolder();
		
		
		
		$redirectionURL = $this->getParameter('redirect', null);
		$aftersendingCase = (int) $this->getParameter('aftersending', PFM_AFTERSENDING_DEFAULTTEXT);

		
		switch ($aftersendingCase) {
			default:
			case PFM_AFTERSENDING_DEFAULTTEXT:
			case PFM_AFTERSENDING_CUSTOMTEXT:
				$this->template = 'aftersending';
			break;
				
			case PFM_AFTERSENDING_REDIRECT: 
				if(! $this->stop->redirection){
					ProformsHelper::redirect($redirectionURL, null, (bool) $this->isInIframe );	
				}
				
			break;
		}	
		
	}//EOF success
	

	
	protected function _email(){
		
		$this->_processEmailAddresses();
		
		$isHTMLMail = defined('M4J_HTML_MAIL') ? (bool) M4J_HTML_MAIL : true;
		$iso = defined('M4J_MAIL_ISO') ? trim( M4J_MAIL_ISO ): 'utf-8';
		
		$from = ProformsHelper::healEmail( $this->userMailElement ? $this->userMailElement->getValue() : ( defined('M4J_FROM_EMAIL') ? M4J_FROM_EMAIL : 'noreply@proforms.com') ) ;
		$fromname = $this->userMailElement ? $this->userMailElement->getValue() : ( defined('M4J_FROM_NAME') ? M4J_FROM_NAME : 'Proforms')  ;

		$subject = trim( $this->getParameter('subject') ); 
		$subject = $this->aliasReplacement( $subject ? $subject : $this->getParameter('title') );
		
		$this->_createSubmissionList();
		
		$mailBodyContent = 	$this->aliasReplacement( $this->getParameter('hidden') );
		
		$dataListing = $this->getParameter('data_listing', false);
		$textForConfirmationOnly = false;
		$_body =  $this->_mailBodyHead( $mailBodyContent, (bool) $textForConfirmationOnly ) ;		
		$body = $_body . (  $dataListing ? $this->submissionListBuffer : '' );
		$body .= (! $dataListing ) ? ProformsHelper::serverData(false,true) : '';
		
		$mail = JFactory::getMailer();		
		$mail->From 	= $from;
		$mail->FromName = $fromname;
		$mail->Subject 	= wordwrap( $subject , 78, "\n", true);
		$mail->Body 	= wordwrap( $body , 78, "\n", true) ;
		$mail->WordWrap = 78;
		$mail->CharSet = $iso;
		$mail->IsHTML($isHTMLMail);
		ProformsHelper::addMultipleMailAddress($mail, $this->emailAddress);
		if(! empty($this->uploads)){
			foreach ($this->uploads as &  $upload){
				/* @var $upload MFormElement */
				$path = $this->tmpPath. '/' . $this->tmpDir . '/' . $upload->getValue();
				if(JFile::exists($path)){
					$mail->AddAttachment($path);
				}
			}
		}
		$dummy = JFactory::getMailer();
		$this->pluginManager->onBeforeEmail($mail, $dummy, $this->uploadHeap);

		
			
		if(!$this->stop->email ){
			
			$mail->Send();
		
		}
		
	}
	
	protected function _removeUploadFolder(){
		if($this->stop->deletetemp && ! $this->pluginManager->forceTmpDelete ) return;
		
		if(	JFolder::exists($this->tmpPath . '/'. $this->tmpDir ) ){
			JFolder::delete($this->tmpPath . '/'. $this->tmpDir );
		}
	}
	
	protected function _processEmailAddresses(){
		// Standard-Admin-Email routing process
		$email = M4J_EMAIL_ROOT;
		$catMail = $this->model->getCategoryEmail();
		$email = $catMail ? $catMail : $email;
		$_email =  trim( $this->getParameter('email') );
		$this->emailAddress = $_email ? $_email : $email;
		
		
		// Confirmation Email Address
		if($this->mailcopy){
			$this->confirmationEmailAddress = $this->userMailElement->getValue();
		}
	
	}
	
	
	protected function _mailBodyHead($content = null, $clean = false){

		$isHTMLMail = defined('M4J_HTML_MAIL') ? (bool) M4J_HTML_MAIL : true;
		$iso = defined('M4J_MAIL_ISO') ? trim( M4J_MAIL_ISO ): 'utf-8';
		
		if($clean){
			return ($isHTMLMail) ? 
					'<meta http-equiv="Content-Type" content="text/html; charset='.$iso.'" />'. "\n" .
					'<div  style="font-family: Segoe UI, Lucida Grande, Arial, sans-serif; ">' . "\n" 
			:		
					'';
		}
		
		
		return  ($isHTMLMail) ? 
				'<meta http-equiv="Content-Type" content="text/html; charset='.$iso.'" />'. "\n" .
				'<div style="font-family: Segoe UI, Lucida Grande, Arial, sans-serif; ">'. "\n" . $content."\n" . '<br/>' . "\n" :
				
				strip_tags(str_replace(array("<br>","<br />", "<br/>", "</p>"),"\n",$content) )."\n\n";
		
	}
	

	
	protected function _createSubmissionList($addServerData = true, $ignoreEmpty = false, $recreateDataList = false){
		
		$isHTMLMail = defined('M4J_HTML_MAIL') ? (bool) M4J_HTML_MAIL : true;
		
		$this->submissionListBuffer =  ($isHTMLMail) ? 
			'<table border="0" align="left" cellpadding="2" cellspacing="3"  style="font-family: Segoe UI, Lucida Grande, Arial, sans-serif; " >'."\n".'<tbody>'."\n" : 
			"\n";
		
		if( $recreateDataList || ! $this->dataListBuffer ){

			foreach ($this->elementsOrder as $eid){
				/*var $element MFormElement*/
				$element = & $this->formElements[$eid];
				if( $element->getDisplayOnly() ) continue;
				$value = trim( $element->getFormattedValue() );
				if(!$value && $ignoreEmpty) continue;
					
				$question = $element->getQuestion();
				$question = trim($question) ? $question : '`'. $element->getAlias().'`';
					
				$this->dataListBuffer .= ($isHTMLMail) ?
				'<tr>'. "\n" .
				'<td align="left" valign="top" width="33%">'."\n" .
				$question. "\n" .'</td>'. "\n" .
				'<td align="center" valign="top" width="16px" > : </td>'. "\n" .
				'<td align="left" valign="top">'. "\n" .
				str_replace("\n", "<br/>", $value). "\n" .
				'</td>'. "\n" .
				'</tr>'. "\n"
						:
						$question.": \n".$value."\n\n";
			}//EOF loop
			
		}//EOF if list processing
		
		$this->submissionListBuffer .= $this->dataListBuffer;
		
		$this->submissionListBuffer .= $addServerData ? ProformsHelper::serverData() : '';
		
		$this->submissionListBuffer .=  ($isHTMLMail) ? 
			'</tbody>'."\n" .'</table>'."\n" .'<br/>'."\n" .'</div>'."\n" :
			"----------------------------------------------------------------------------\n";
	
	}
	
	
	protected function _styleSheet(){
		// Get document
		$document=JFactory::getDocument();
		// include the system CSS classes
		$document->addStyleSheet(M4J_CSS_SYSTEM);
		
		if(! $this->stop->css){
			$document->addStyleSheet(M4J_CSS);	
		}
		$document->addStyleSheet(M4J_CSS_RESPONSIVE_SYSTEM);
		if( ! $this->noResponsive){
			$document->addStyleSheet(M4J_CSS_RESPONSIVE_SYSTEM_MEDIA);	
		}
		$document->addStyleSheet(M4J_CSS_RESPONSIVE);
		
		
		$document->addStyleSheet(M4J_FRONTEND_BALOONTIP_CSS);
	}
	
	protected function _scripts(){
		$document = JFactory::getDocument();
		$document->addScriptDeclaration(' var errorColor = "#'.M4J_ERROR_COLOR.'"; var m4jShowTooltip = '. JRequest::getInt('tooltip',1) . ';  ');
		$document->addScript(M4J_FRONTEND_DOJO);
		$document->addScript(M4J_FRONTEND_BALOONTIP);
		$this->_initCalendar();
		$document->addScript(M4J_FRONTEND_UNDERLINE);
		$document->addScript(M4J_FRONTEND_MOOJ);
		$document->addScriptDeclaration('	
				var MText = {
					yes : "' .  M4J_LANG_YES . '",
					no : "' .  M4J_LANG_NO . '",
					required : "' .  M4J_LANG_REQUIRED . '",
					userMail : "' .  M4J_LANG_ERROR_USERMAIL . '",
					errorPrompt : "' .  M4J_LANG_ERROR_PROMPT . '",
					alphabetic : "' .  M4J_LANG_ALPHABETICAL . '",
					alphanumeric: "' .  M4J_LANG_ALPHANUMERIC . '",
					numeric : "' .  M4J_LANG_NUMERIC . '",
					integer : "' .  M4J_LANG_INTEGER . '",
					url : "' .  M4J_LANG_URL . '",
					email : "' .  M4J_LANG_EMAIL . '"
				};');
				
		$proformsJSFields="\t var pfmFields = [" .implode(",", $this->model->getEids() )."];\n";
		$document->addScriptDeclaration($proformsJSFields);
		
		if(M4J_USE_JS_VALIDATION){
			$document->addScriptDeclaration('var PROFORMS_USE_JS_VALIDATION = 1; ');
		}else{
			$document->addScriptDeclaration('var PROFORMS_USE_JS_VALIDATION = 0; ');
		}
		$this->addEndScript(M4J_FRONTEND_EVALUATION);
		
		$document->addScript(M4J_FRONTEND_JS.'scrollconfirm.js?BASIC=120');
		$document->addScript(M4J_FRONTEND_JS.'textareamaxchars.js?BASIC=120');
		$document->addScript(M4J_FRONTEND_JS.'proforms.js?BASIC=120');
		
		
	}
	
	protected function _createHiddens(){
		$this->_addHidden('send', $this->uniqueIndex );
		$tmpl = JRequest::getCmd("tmpl",null);
		if($tmpl == "component"){
			$this->_addHidden('tmpl', 'component' );
		}
		$this->_addHidden('sending_jid', (int) $this->jid );
		
		$this->isTooltip = 1;
		
		// check if no repsonsive request
		$this->noResponsive = JRequest::getInt('no_responsive',0);
		$this->_addHidden('no_responsive', $this->noResponsive  );
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('var PROFORMS_NO_RESPONSIVE = '.(int) $this->noResponsive.'; ');
			
		$this->_addHidden('uniqueindex', $this->uniqueIndex);
		
		$stid = JRequest::getInt("stid", null);
		if($stid && is_numeric($stid)){
			$this->stid = (int) $stid;
			$this->_addHidden('stid', $this->stid );
		}
		
	}	
	
	protected function _initCalendar()
	{
		$lang =JFactory::getLanguage();
		$lang_code = substr($lang->getTag(),0,2);
			
		$document=JFactory::getDocument();
		$document->addStyleSheet(M4J_FRONTEND_CALENDAR.'calendar-system.css',"text/css","all",array("title"=>"green"));
		$document->addScript(M4J_FRONTEND_CALENDAR.'m4j.js?BASIC=120');
		$document->addScript(M4J_FRONTEND_CALENDAR.'calendar.js?BASIC=120');
			
		if(M4J_FORCE_CALENDAR) $lang_code ="en";
			
		if(file_exists(M4J_JS_CALNEDAR.'lang/calendar-'.$lang_code.'.js')){
			$document->addScript(M4J_FRONTEND_CALENDAR.'lang/calendar-'.$lang_code.'.js?BASIC=120');
		}else{
			$document->addScript(M4J_FRONTEND_CALENDAR.'lang/calendar-en.js?BASIC=120');
		}
	
	}//EOF init_calendar
	
	protected function _checkToken(){
		$session = JFactory::getSession();
		$sessionToken = $session->get('token_' . (int) $this->jid , false, 'proforms');
	}
	
	protected function _addHidden($name = null, $value = null, $asTextarea = false){
		if($name){
			$this->hiddenFields[$name] =  $asTextarea ? 
				'<textarea style="display:none;" name="'.$name.'">' .$value . '</textarea>' . "\n" :
				'<input type="hidden" name="'.$name.'" value="'.$value.'"></input>' . "\n";
		}
	}
	
	protected function _createForm($content = null){
		
	}
	
	
	protected function _includeFormLib(){
		static $formLibIsIncluded;
		if(!empty($formLibIsIncluded)) return;
		require_once JPATH_ROOT . '/components/com_proforms/formlib/init.php';
		require_once JPATH_ROOT . '/components/com_proforms/includes/helpers.php';
		require_once JPATH_ROOT . '/components/com_proforms/includes/validate.php';
		$formLibIsIncluded = true;
	}
	
	protected function _includeAppPluginLib(){
		static $appPluginLibIncluded;
		if(! empty($appPluginLibIncluded)) return;
		// include app plugin main classes
		require_once M4J_INCLUDE_APPTEXT;
		require_once(M4J_INCLUDE_PLUGINMANAGER);
		require_once(M4J_INCLUDE_PLUGIN);
		$appPluginLibIncluded = true;
	}
	
	protected function _initAppPluginManager(){
		$this->pluginManager = new AppPluginManager();
		$this->pluginManager->setJID($this->jid);
		$plugins = $this->model->getApps();
	
		if($plugins){
			foreach($plugins as $pluginData){
				$this->pluginManager->add($pluginData->app, $pluginData->params);
			}//EOF plugin check
		}
		
		$this->pluginManager->applyFormView($this);
		$this->pluginManager->analyseStop();
		$this->pluginManager->analyseForceTmpDelete();

		$this->pluginManager->applyJobsReference( $this->model->getParamsReference() );
		$this->stop = & $this->pluginManager->getStop();
	}
	
	
	
	protected function _loadLegacy(){
		if( defined ('_PROFORMS_VIEW_FORM_LOADED_LEGACY') ) return;
		
		// import reCaptcha Lib if reCaptcha is selected
		if (defined('M4J_CAPTCHA') && M4J_CAPTCHA == "RECAPTCHA" && ! function_exists('recaptcha_get_html')){
			require_once(JPATH_ROOT . '/components/com_proforms/includes/recaptchalib.php');
		}

		// Dirty workaround for the old proforms.html.php class
		require_once(JPATH_ROOT . '/components/com_proforms/includes/legacyprint.php');
		
		// bootstrap some classes and functions
		require_once(M4J_INCLUDE_CALENDAR);
		require_once(M4J_INCLUDE_FUNCTIONS);
		require_once(M4J_INCLUDE_VALIDATE);
		require_once(M4J_INCLUDE_STORAGE);
		
		
		// a helper class for working with templates
		require_once(M4J_INCLUDE_TEMPLATER);
		// get the layout and the layout list class
		require_once(M4J_INCLUDE_LAYOUT);
		
		require_once (M4J_INCLUDE_CAPTCHA);
		
		
		define('_PROFORMS_VIEW_FORM_LOADED_LEGACY' ,1);
		
	}

	public function addEndScript($src = null){
		if(!$src) return;
		$script = "\n" .'<script type="text/javascript" src="'.$src.'"></script>';
		array_push($this->endScripts, $script);
	}

	public function addEndScriptDeclaration($code = null){
		if(!$code) return;		
		$script = "\n".'<script type="text/javascript">'."\n".$code."\n".'</script>';
		array_push($this->endScripts, $script);
	}
	
	
	protected function _renderEndScripts($echo = false){
		$buffer = trim( implode('', $this->endScripts) );
		if($echo) echo $buffer;
		else return $buffer;
	}
	
	public function aliasReplacement($string = null, $escape = false){
		
		$user = JFactory::getUser();
		$ip = strip_tags( array_key_exists('REMOTE_ADDR',$_SERVER) ? $_SERVER['REMOTE_ADDR'] : "") ;
		if(! preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip)){
			$ip = "UNKNOWN";
		}
		$username = $user->username ? $user->username : "Guest";
		$userrealname = $user->name ? $user->name : "Guest";
		$user_id = $user->id ;
		$user_email = $user->email ? $user->email : '';
		
		$isHTMLMail = defined('M4J_HTML_MAIL') ? (bool) M4J_HTML_MAIL : true;
		
			
		$string = preg_replace('/{(\s*)J_OPT_IN(\s*)}/' , "" , $string);
		$string = preg_replace('/{(\s*)J_OPT_OUT(\s*)}/' , "" , $string);
		$string = preg_replace('/{(\s*)J_USER_IP(\s*)}/' , $ip , $string);
		$string = preg_replace('/{(\s*)J_USER_NAME(\s*)}/' , $username , $string);
		$string = preg_replace('/{(\s*)J_USER_REALNAME(\s*)}/' , $userrealname , $string);
		$string = preg_replace('/{(\s*)J_USER_ID(\s*)}/' , $user_id , $string);
		$string = preg_replace('/{(\s*)J_USER_EMAIL(\s*)}/' , $user_email , $string);
		
		foreach($this->aliasToElements as & $element){
			/* @var $element MFormElement */
			$element->replacePlaceholder($string, $escape);
		}
		
		return $string;
	}
	
	
	
	public static function getIndex(){
		return (int) self::$index++;
	}
	
	

	public function getRequiredAdvice()
	{
	    return $this->requiredAdvice;
	}

	public function setRequiredAdvice($requiredAdvice)
	{
	    $this->requiredAdvice = $requiredAdvice;
	}
	
	public function isSend(){
		return $this->send;
	}
	
	public function & getFormElementsReference(){
		return $this->formElements;
	}
	
	/**
	 * 
	 * @param int $eid
	 * @return MFormElement|boolean
	 */
	public function & getFormElement($eid = null){
		$eid = (int) $eid;
		if(!$eid || ! isset($this->formElements[$eid])) return false;
		return $this->formElements[$eid];
		
	}
	/**
	 * 
	 * @param string $alias
	 * @return MFormElement|boolean
	 */
	public function & getFormElementByAlias($alias = null){
		$alias = (string) trim( $alias );
		if(!$alias || ! isset($this->aliasToElements[$alias])) return false;
		return $this->aliasToElements[$alias];
	}
	/**
	 * 
	 * @param int $eid
	 * @param any $value
	 */
	public function setElementValue($eid = null, $value = null){
		$eid = (int) $eid;
		/* @var $element MFormElement */
		$element = isset($this->formElements[$eid]) ? $this->formElements[$eid] : null;
		if($element){
			$element->setValue($value);
			return true;
		}
		return false;		
	}
	
	public function & getAllFormElements(){
		return $this->formElements;
	}
	
	public function getTmpPath(){
		return $this->tmpPath;
	}
	
	public function getTmpDir(){
		return $this->tmpDir;
	}
	
	public function setTmpPath($path = M4J_TMP){
		$this->tmpPath = $path;
	}
	
	public function setTmpDir($dir = null){
		$this->tmpDir = $dir;
	}
}