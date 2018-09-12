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

require_once PROFORMS_FORMLIB . DS . 'init.php';

class ProformsAdminModelFormitems extends ProformsAdminModel{
	
	/**
	 * 
	 * @var int
	 */
	protected $fid = 0;

	/**
	 *
	 * @var bool
	 */
	public $isResponsive = false;
	
	/**
	 * 
	 * @var stdClass
	 */
	public $form = null;
	
	public $slots = array();
	public $slotsById = array();
	
	public $elements2Slots = array();
	
	protected $elementIsReadOnly = array();
	protected $eid2DisplayOnly = array();
	
	protected function _init(){
		$this->fid = JRequest::getInt("id", null);
		$this->getForm($this->fid);
	}
	
	
	public function getForm($fid = 0){
		if(!$fid) return;
		
		$this->slots = array();
		$this->slotsById = array();
		$this->elements2Slots = array();
		
		$this->fid = (int) $fid;
		$this->form = $this->singleSelect('#__m4j_forms', null, $this->where("fid", $this->fid));
		

		if($this->form && isset($this->form->responsive)){
			$this->isResponsive = $this->form->responsive ? true: false;
		}
		
		if($this->form && isset($this->form->responsive_data) && $this->isResponsive ){
			$this->_processResponsiveData();
		}elseif($this->form && ! $this->isResponsive ){
			$this->_processDefaultData();
		}

// 		MDebug::pre($this->slots);
// 		MDebug::pre($this->slotsById);
// 		MDebug::pre($this->form);
	}
	
	public function reload(){
		$this->getForm($this->fid);
	}
	
	
	protected function _processResponsiveData(){
		$this->form->responsive_data = unserialize(bDec( $this->form->responsive_data));
		foreach($this->form->responsive_data->rows as & $row){
			foreach($row as & $section){
				
			    /**
			     * $selection->order is the normal slot 
			     */
				$section->elements = $this->_getItemsForSlot($section->order);
				
				
				$this->slots[] = & $section;
				$this->slotsById[$section->slot] =  & $section;
			}
		}
	}
	
	protected function _processDefaultData(){
		$params = $this->_loadLayoutParameters();
		
		$this->form->layout_data = $this->_makeData($this->form->layout_data);
		
		for($t=0; $t< $params->positions; $t++){
				if( $data = isset( $this->form->layout_data[$t+1] )  ? $this->form->layout_data[$t+1] : null ) {
					$data->slot = $t+1;
					$data->image = M4J_HTTP_LAYOUT . $this->form->layout . "/slot" . $data->slot . ".png";
					$data->elements = $this->_getItemsForSlot($data->slot);
					$this->slots[] = $data;
					$this->slotsById[ ($t+1) ] =  $data;
				}
				
		}	
	}
	
	
	protected function _getItemsForSlot($slot = 1){
		
		$slotColumnName = $this->isResponsive ? 'responsive_slot' : 'slot';
		$slotColumnName = 'slot';
		
		$itemData = $this->select('#__m4j_formelements',null, $this->where(array($slotColumnName => $slot, "fid" => $this->fid)), "ORDER BY `sort_order`" );
		foreach($itemData as & $data){
			$formNumber = (int) $data->form;
			
			if(! isset($this->elementIsReadOnly[$formNumber])){
				$folder = MFormFactory::getFolder($formNumber);
				if(is_readable($folder . 'admin' . DS . 'info.ini')){
					$ini = parse_ini_file($folder . 'admin' . DS . 'info.ini', TRUE);
					$this->elementIsReadOnly[$formNumber] = (isset($ini["displayonly"]) && $ini["displayonly"]) ? 1 : 0;
				}else{
					$this->elementIsReadOnly[$formNumber] = 0;
				}
			}//EOF no element is readonly entry
			
			$data->displayonly = $this->elementIsReadOnly[$formNumber];
			$this->eid2DisplayOnly[(int) $data->eid] = $data->displayonly;
		}
		return $itemData;
	}
	
	
	public function isDisplayOnly($eid){
		return (bool) ( (isset( $this->eid2DisplayOnly[ (int) $eid])) ? $this->eid2DisplayOnly[ (int) $eid] : 0 );
	}
	
	protected function _makeData($string=null){
		
		if(!$string) return null;
		$splitPosition = explode("|",$string);
		
		$posArray = array();
		$count = 1;
		foreach($splitPosition as $position){
			if(!$position) continue;
			$position = rtrim($position,";");
			$attributes = explode(";",$position);
			$obj = new stdClass();
			foreach($attributes as $attribute){
				$element = explode("=",$attribute);
				if(sizeof($element)==2){
					$key = $element[0];
					$obj->$key = $element[1];
				}//EOF is element
			}// EOF foreach attributes
			$posArray[$count++] = $obj;
		}//EOF foreach positions
		return $posArray;
	}
	
	protected function _loadLayoutParameters(){
		$path = M4J_LAYOUT.$this->form->layout."/parameters.php";
	
		if(! JFile::exists($path)){
			return false;
		}
		$content = file_get_contents($path);
		$splitContent = explode('?>', $content, 2);
		
		if(sizeof($splitContent) !== 2) return false;
		$_data = explode( "\n" , $splitContent[1] );
		$data = new stdClass();
		foreach($_data as $d){
				$split = explode('=', $d, 2);
				if(sizeof($split) === 2){
					$split[0] = trim($split[0]);
					$split[1] = trim($split[1]);
					if(is_numeric($split[1])) $split[1] = (int) $split[1];
					$key = $split[0];
					$data->$key = $split[1];
				}
			
		}
		return $data;
	}
	
	protected function _setElementsColumn($columnName = null, $value = 0, $eid = null){
		if(! $eid || ! $columnName) return false;
		return $this->update('#__m4j_formelements',array($columnName => $value), $this->where("eid", $eid));
	}
	
	public function moveToSlot($destinationSlot = null, $selection = array(), $responsiveDestination = null){
		if( !$destinationSlot || ! $selection) return false;
		$start = (int) $this->getMax("#__m4j_formelements", $this->where(array("fid" => $this->fid, "slot" => $destinationSlot )), 1);
// 		MDebug::pre("Start: " .$start);
		foreach ($selection as $eid){
			$this->update("#__m4j_formelements", 
			              array( 
			                     "sort_order" => $start++, 
			                     "slot" => (int) $destinationSlot,
			                     "responsive_slot" => (int) $responsiveDestination
			                    ),  
			              $this->where(
    			                    array(
    			                        "eid" =>$eid, 
    			                        "fid" => $this->fid 
    			                         )
			                         ) 
			             );
		}
		return true;
	}
	
	
	public function movePosition( $slot = null,  $selection = array(), $destination = null){	
		
// 		MDebug::pre($slot);
// 		MDebug::pre($selection);
// 		MDebug::pre($destination);
		
		if( !$selection || ! $slot) return false;
	

		$fid = $this->fid;
		
		// Add at end
		if(!$destination){
// 			MDebug::_("NO DESTIONATION");
			$start = (int) $this->getMax("#__m4j_formelements", $this->where(array("fid" => $fid, "slot" => $slot )), 1);
// 			MDebug::_("START " + $start);
			foreach($selection as  $eid){
				$this->update("#__m4j_formelements", array("sort_order" => $start++ ),  $this->where(array("eid" =>$eid, "fid" => $fid,  "slot" => $slot )) );
			}
			$this->refactorSlotSortOrder($slot);
			return true;
		}
		
		
		// Add at position		
		$before = $selection; $after = array();
		$pos = array_search($destination, $selection);
		if($pos !== NULL && $pos !== FALSE){
			$before = array_slice($selection, 0, $pos);
			$after = array_slice($selection, $pos+1);
		}
// 		MDebug::_("before");
// 		MDebug::pre($before);
// 		MDebug::_("after");
// 		MDebug::pre($after);
		
		$destPos = (int) $this->singleSelect("#__m4j_formelements", "sort_order", $this->where("eid", (int) $destination))->sort_order;
		
		

		foreach ($selection as $eid){
			if($eid != $destination){
				$this->db->setQuery("UPDATE `#__m4j_formelements` SET `sort_order` = `sort_order` * -1 WHERE `eid` = '$eid' LIMIT 1;");
				$this->db->query();
			}
		}
		

		$beforeSize = sizeof($before);
		$afterSize = sizeof($after);
		$newDestPos = $destPos + $beforeSize;

		
		
		if(!empty($before)){
// 			MDebug::_("Process before");
			$oneBeforeDestPost = $destPos -1 ;
			$this->db->setQuery("UPDATE `#__m4j_formelements` SET `sort_order` = `sort_order` + $beforeSize WHERE `fid` = '$fid' AND `slot` = '$slot' AND `sort_order` > $oneBeforeDestPost ;");
			
			$this->db->query();
		}

		if(!empty($after)){
// 			MDebug::_("Process after");
			$afterTotalSize = $beforeSize + $afterSize;
			$this->db->setQuery("UPDATE `#__m4j_formelements` SET `sort_order` = `sort_order` + $afterTotalSize WHERE `fid` = '$fid' AND `slot` = '$slot' AND `sort_order` > $newDestPos ;");
			$this->db->query();
		}
		
		
		$beforeStart = $destPos;
		$afterStart = $destPos + $beforeSize +1;
		foreach ($selection as $eid){
			
			if(in_array($eid, $before)){
				$this->db->setQuery("UPDATE `#__m4j_formelements` SET `sort_order` = '$beforeStart' WHERE `eid` = '$eid' LIMIT 1;");
				$this->db->query();
				$beforeStart++;
			}
			
			if(in_array($eid,$after)){
				$this->db->setQuery("UPDATE `#__m4j_formelements` SET `sort_order` = '$afterStart' WHERE `eid` = '$eid' LIMIT 1;");
				$this->db->query();
				$afterStart++;
			}
			
		}
		
		
		$this->refactorSlotSortOrder($slot);
		
	}
	
	
	public function refactorSlotSortOrder($slot = null, $responsiveSlot = null){
		$slotName  = $responsiveSlot ? 'responsive_slot' : 'slot';
		$slotValue = $responsiveSlot ? (int) $responsiveSlot : (int) $slot;
		$fid = $this->fid;
		$count = 1;
		$ordered = $this->select("#__m4j_formelements","eid", $this->where(array("fid"=>$this->fid, $slotName => $slotValue)), "ORDER BY `sort_order` ASC");
		foreach ($ordered as $element){
			$this->update("#__m4j_formelements", array("sort_order"=> $count++), $this->where("eid", $element->eid), "LIMIT 1");
		}
		
	}
	
	public function copyElements($items = array(), $slot = null){
		if(!$slot || ! $items) return;
		if(! is_array($items)){
			settype($items, "array");
		}
		
		foreach ($items as $eid){
			$id = $this->copyPlus('#__m4j_formelements',$this->where("eid", $eid), $this->where(array("fid" => $this->fid, "slot" => $slot)));
			$add = '_' .$this->dbEscape(M4J_LANG_COPY);
			$this->db->setQuery("UPDATE `#__m4j_formelements` SET `question` = CONCAT(`question`, '$add'), `alias` = CONCAT(`alias`, '$add') WHERE `eid` = '$id' LIMIT 1");
			$this->db->query();	
		}
	}
	
	
	public function setActive($state = 0, $eid = null){
		$state = (int) $state;
		$result = $this->_setElementsColumn("active", $state, $eid);
		if($result === false){
			return false;
		}
		$newResult = $this->singleSelect('#__m4j_formelements', 'active',  $this->where("eid", $eid) );
		return $newResult ? $newResult->active : false;
		
	}
	
	public function setRequired($state = 0, $eid = null){
		$state = (int) $state;
		$result = $this->_setElementsColumn("required", $state, $eid);
		if($result === false){
			return false;
		}
		$newResult = $this->singleSelect('#__m4j_formelements', 'required',  $this->where("eid", $eid) );
		return $newResult ? $newResult->required : false;
	}
	
	public function remove($selection = array(), $eid = null, $slot = null){
		if(!$selection && ! $eid ) return;
		if($eid){
			$selection = array( (int) $eid);
		}
		
		foreach($selection as $_eid){
			$this->delete("#__m4j_formelements", "eid", $_eid);
		}
	
		if($slot){
			$this->refactorSlotSortOrder($slot);
		}
		
	}
	
	
}