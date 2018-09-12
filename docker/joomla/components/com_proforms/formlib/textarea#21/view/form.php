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

class MFormTextarea extends MFormElement{
	/**
	 * 
	 * @var int
	 */
	protected $checked = 0;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sqlLoadSingle = true;
	
	/**
	 * @param unknown_type $dataObject
	 */
	protected function _init(& $dataObject) {
		// TODO: Auto-generated method stub
	}
	
	// Validation
	protected function _validation(){
		if(!$this->value && ! $this->required) return;
	
		ProformsHelper::validateType($this->value, (int) $this->getParameter('eval', 0), 	$errorMessage  );
		if($errorMessage){
			$errorMessage .= (trim($this->question)) ? $this->question : '`' . $this->alias . '`';
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
		$eval = ($this->required)? ($eval+1000) : $eval;
		$eval = ($eval) ? 'lang="'.$eval.'" ' : 'lang="0" ';
		
			
		$ismaxcharstextarea = isset($this->params->ismaxcharstextarea) ? $this->params->ismaxcharstextarea : null;
		
		$ismaxcharstextarea = $this->params->maxchars ? $ismaxcharstextarea : null;
		
		$placeholder = isset($this->params->placeholder) ? ' placeholder="'. htmlentities($this->params->placeholder, ENT_COMPAT, "UTF-8") .'" ' : '';
		
		$maxLength = $ismaxcharstextarea ? 'maxlength="' . (int) $this->params->maxchars . '" ' : '';
		if($ismaxcharstextarea){
			$maxLength = 'maxlength="' . (int) $this->params->maxchars . '" ';
			$this->addClass('proformsMaxLength');
			
		}
		$filter = JFilterInput::getInstance();
		
		$out .= '<textarea class="m4jTextArea '. $this->_getClass(). '" '.$maxLength.' '. $placeholder .
				'id="m4j-'.$this->eid.'" name="m4j-'.$this->eid.'" cols="" rows="'.$this->params->element_rows.'" '.$eval.$this->_getStyle(). '>'.$filter->clean($this->value).'</textarea>'."\n";
		
		if($ismaxcharstextarea){
			$out .=  "\n". '<div class="pfmTAMaxChars m4jTextareaLimit">' . M4J_LANG_CHAR_LEFT . ': <b id="left_m4j-'.$this->eid.'"></b></div>' . "\n";
		}
		
		return $out;
	}
	
	/**
	 * 
	 */
	protected function _renderResponsive() {
		// TODO: Auto-generated method stub
		return $this->_renderDefault();
	}

	
	
}//EOF class