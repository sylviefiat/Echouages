<?php
/**
 * @name MOOJ Proforms
 * @version 1.3
 * @package proforms
 * @copyright Copyright (C) 2008-2012 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class MCaptcha{
	protected $align = "center";
	protected $type = "RECAPTCHA";
	protected $isCaptcha = 0;
	protected $submitText, $resetText;
	protected $useReset = 1;
	
	protected $buffer = '';
	protected $isHuman = false;
	
	protected $uniqueIndex = NULL;
	/**
	 * 
	 * @var JApplicationCms
	 */
	protected $app = null;
	
	public static $singleActions = false;
	
	public function __construct($customize = null, $captcha = true, $uniqueIndex = null){
		
		$this->uniqueIndex = (int) $uniqueIndex;
		$this->app = JFactory::getApplication();
		
		if(! self::$singleActions){
			// Get document
			$document=JFactory::getDocument();
			// include the captcha CSS
			$document->addStyleSheet(M4J_CSS_CAPTCHA);
		}
		self::$singleActions = true;
		
		$this->_smartCaptcha();
		
		if($customize && isset($customize->submit_align)){
			switch ($customize->submit_align){
				default:
				case 0: 
					$this->align = "center";
				break; 
				
				case 1: 
					$this->align = "left";
				break;
				
				case 2:
					$this->align = "right";
				break;
			}
		}
		
		if( $captcha == 1 && ! $this->isHuman ){
			$this->isCaptcha = 1;
			$this->type = M4J_CAPTCHA;
			if($this->type == "CSS") $this->type = "SIMPLE";
			if(! file_exists(M4J_INCLUDE_CAPTCHA_TEMPLATES . strtolower($this->type) . ".php")) {
				$this->isCaptcha = 0;
				$this->type = "NOCAPTCHA";
			}
		}else{
			$this->isCaptcha = 0;
			$this->type = "NOCAPTCHA";
		}
		
		$this->submitText = (isset($customize->submit_text) && $customize->submit_text) ? trim($customize->submit_text) : M4J_LANG_SUBMIT;
		$this->resetText = (isset($customize->reset_text) && $customize->reset_text) ? trim($customize->reset_text) : M4J_LANG_RESET;
		$this->submitText = str_replace('"', '“', $this->submitText );
		$this->resetText = str_replace('"', '“', $this->resetText );
		if(isset($customize->use_reset)) $this->useReset = (int) $customize->use_reset;
	}//EOF construct
	
	public function render( $echo = false){ 
		ob_start();
		echo '<div id="m4jSubmitWrap">' . "\n";
		include M4J_INCLUDE_CAPTCHA_TEMPLATES . strtolower($this->type) . ".php"; 
		echo '</div>' . "\n";
		$this->buffer = ob_get_clean();
		
		if($echo) echo $this->buffer;
		else return $this->buffer;
	}
	
	protected function _smartCaptcha(){
		// UNCOMMENT THE LINE BELOW IF YOU DON'T WISH USING SMART CAPTCHA
// 		$this->isHuman = 0; return;
		$this->isHuman = $this->app->getUserState( "proforms_is_human", 0 );
	}
	
	
	public function __toString(){
		return $this->render( false );
	}
	
	public function validate(){
		if( $this->isHuman || ! $this->isCaptcha ) return true;
		
		if($this->type == 'RECAPTCHA'){
			$resp = pfm_recaptcha_check_answer(PFM_RE_CAPTCHA_PRIVATE,
										   $_SERVER["REMOTE_ADDR"],
										   $_POST["recaptcha_challenge_field"],
										   $_POST["recaptcha_response_field"]);
			if ( ! $resp->is_valid){
				return false;
			}else{
				$this->app->setUserState('proforms_is_human', 1);
				return true;
			}
		}//EOF reCaptcha validation
		
		$generatedCaptchaValue = $this->app->getUserState('m4j_captcha' . $this->uniqueIndex , '_NOSTATE!' );
		if($generatedCaptchaValue === '_NOSTATE!') return false;
		$sentCaptchaValue = JRequest::getString('validate' . $this->uniqueIndex , null);
		if($generatedCaptchaValue === $sentCaptchaValue ){
			$this->app->setUserState('proforms_is_human', 1);
			return true;
		}else return false;
		
	}
	
	
}