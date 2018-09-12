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

class MFormText extends MFormElement{

	/**
	 *
	 * @var boolean
	 */
	protected $sqlLoadSingle = true;
	
	/**
	 * @param stdClass $dataObject
	 */
	protected function _init(& $dataObject) {
		// TODO: Auto-generated method stub
	}

	// Validation 
	protected function _validation(){
		if(!$this->value && ! $this->required && ! $this->usermail) return;
		
		$errorMessage = '';
		if($this->usermail){
			$errorMessage = $this->validate->email($this->value) ? '' : M4J_LANG_ERROR_USERMAIL.": ".$this->question;
		}else{	
			ProformsHelper::validateType($this->value, (int) $this->getParameter('eval', 0), 	$errorMessage  );
			if($errorMessage){
				$errorMessage .= (trim($this->question)) ? $this->question : '`' . $this->alias . '`';
			}
		}
		
		if($errorMessage){
			$this->_addError($errorMessage);
		}
	
	}
	
	
	/**
	 * 
	 */
	protected function _renderDefault() {
		
		$this->_getWidth(true);
		$out = '';
		$eval = isset($this->params->eval) ? intval($this->params->eval) : 0;
		$eval = ($this->usermail !=0) ? 1004 : $eval;
		$eval = ($this->required)? ($eval+1000) : $eval;
		$eval = ($eval) ? 'alt="'.$eval.'" ' : 'alt="" ';
		$placeholder = isset($this->params->placeholder) ? ' placeholder="'. htmlentities($this->params->placeholder) .'" ' : '';
		$maxlength = $this->params->maxchars ? (int) $this->params->maxchars : "";
		$out .= '<input class="m4jInputField '.$this->_getClass().'" id="m4j-'.$this->eid.'" name="m4j-'.$this->eid.'" '. $placeholder .
				'type="text" size="18" maxlength="'.$maxlength.'" value= "'.htmlentities($this->value, ENT_COMPAT ).'" '.$eval.$this->_getStyle() .'></input>'."\n";

		return $out;
	}
	
	/**
	 * 
	 */
	protected function _renderResponsive() {
		return $this->_renderDefault();

	}

	
	
}//EOF class