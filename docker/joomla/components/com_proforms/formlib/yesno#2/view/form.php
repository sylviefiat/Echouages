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

class MFormYesno extends MFormElement{
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
	
	public $formattedValueType = 'yesno';
	
	/**
	 * @param unknown_type $dataObject
	 */
	protected function _init(& $dataObject) {
		// TODO: Auto-generated method stub
		$this->_add2HigerLevel(array('checked'));
	}

	/**
	 * 
	 */
	protected function _renderDefault() {
		
		$checked = $this->isSend ? htmlentities($this->value, ENT_COMPAT, "UTF-8") : $this->checked;
		
		$this->addStyle('width','70px');
		
		
		$out ='<select ' . $this->_getStyle() . ' class="m4jSelection '.$this->_getClass().'" name="m4j-'.$this->eid.'" id="m4j-'.$this->eid.'">'."\n".'<option value="1" ';
		if ($checked) $out.= 'selected="selected"';
		$out.= '>'.M4J_LANG_YES.'</option>'."\n".'<option value="0" ';
		if (!$checked) $out.= 'selected="selected"';
		$out .=  '>'.M4J_LANG_NO.'</option>'."\n".'</select>'."\n";
		
		return $out;
	}
	
	/**
	 * 
	 */
	protected function _renderResponsive() {
		return  $this->_renderDefault();

	}

	public function save($stid = null){
		if( ! $stid = (int) $stid) return;
	
		$value = $this->toYesNo($this->value);

		if(! $this->stid){
			$this->stid = $stid;
	
			$query = "INSERT INTO `#__m4j_storage_items`"
					. "\n ( `stid`, `eid`, `content` )"
							. "\n VALUES"
									. "\n ( '".$stid."', '".(int) $this->eid ."', '".$this->dbEscape($value)."' )";
			$this->db->setQuery($query);
			$this->db->query();
			$this->stiid = $this->db->insertid();
		}
	}
	
}//EOF class