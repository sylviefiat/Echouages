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

class MFormCheckbox extends MFormElement{
	/**
	 * 
	 * @var int
	 */
	protected $checked = 0;

	public $formattedValueType = 'yesno';
	
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
		$this->_add2HigerLevel(array('checked'));
	}

	/**
	 * 
	 */
	protected function _renderDefault() {
		$eval = ($this->required != 0)? 'alt="1000" ' : 'alt="" ';
				
		$checked = $this->isSend ? (int) $this->value : $this->checked;
		
		$out = '<div class="m4jFormElementWrap">'."\n";
		// null = $eval
		$out .='  <input '.$eval.'class="m4jCheckBox '.$this->_getClass().'" name="m4j-'.$this->eid.'" type="checkbox" id="m4j-'.$this->eid.'" value="1" ';
		($checked==1) ? $out.='checked="checked"></input>'."\n".'</div>'."\n" : $out.= '></input>'."\n".'</div>'."\n";

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