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

class MFormDate extends MFormElement{

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

	/**
	 * 
	 */
	protected function _renderDefault() {
		
		$this->_getWidth(true);
		$eval = ($this->required != 0)? 'alt="1000" ' : 'alt="" ';
		$placeholder = isset($this->params->placeholder) ? ' placeholder="'. htmlentities($this->params->placeholder, ENT_COMPAT, "UTF-8") .'" ' : '';
		$out =  '<input '.$eval.$placeholder.'class="m4jInputField '.$this->_getClass().'" id="m4j-'.$this->eid.'" type="date" size="30" name="m4j-'.$this->eid.'" value="'.htmlentities( $this->value ).'" '.$this->_getStyle() . '></input>'."\n".
			    '<img class="pfmDateIcon" style="cursor: pointer;" align="top" src="components/com_proforms/images/date-icon.png" onclick="return showCalendar(\'m4j-'.$this->eid.'\');" border="0" alt=""></img>'."\n";

		return $out;
	}
	
	/**
	 * 
	 */
	protected function _renderResponsive() {
		$placeholder = isset($this->params->placeholder) ? ' placeholder="'. htmlentities($this->params->placeholder, ENT_COMPAT, "UTF-8") .'" ' : '';
		$this->_getWidth(true);
		$eval = ($this->required != 0)? 'alt="1000" ' : 'alt="" ';
		$out =  '<div class="pfmTBL pfmDate" '.	$this->_getStyle() . '>' . "\n" . ' <div class="pfmTR">'  . "\n" .
				'<div class="pfmTD">'  . "\n" .
				'<input '.$eval.$placeholder.' class="m4jInputField pfmDateInput'.$this->_getClass().'" id="m4j-'.$this->eid.'" type="date" size="30" name="m4j-'.$this->eid.'" value="'.htmlentities( $this->value ).'" ></input>'."\n".
				'</div>'  . "\n" .
				'<div class="pfmTD pfmDateTD">' . "\n" .
			    '<img class="pfmDateIcon" src="components/com_proforms/images/date-icon.png" onclick="return showCalendar(\'m4j-'.$this->eid.'\');" alt=""></img>'."\n" .
			    '</div>' . "\n" .		
			    '</div>' . "\n" . '</div>' . "\n" 
				;

		return $out;

	}

	
	
}//EOF class