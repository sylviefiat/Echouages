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

class MFormHidden extends MFormElement{
		
	/**
	 *
	 * @var boolean
	 */
	protected $sqlLoadSingle = true;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $isHidden = TRUE;
	
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
		
		$out = '<input id="m4j-'.$this->eid.'" name="m4j-'.$this->eid.'" '.
				'type="hidden" value= "'. htmlentities($this->params->hidden_value , ENT_COMPAT, "UTF-8").'"></input>'."\n";

		return $out;
	}
	
	/**
	 * 
	 */
	protected function _renderResponsive() {
		return $this->_renderDefault();

	}

	
	
}//EOF class