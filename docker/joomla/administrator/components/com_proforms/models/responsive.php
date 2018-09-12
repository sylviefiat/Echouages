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


class ProformsAdminModelResponsive extends ProformsAdminModel{
	
	/**
	 * 
	 * @var int
	 */
	protected $fid = 0;

	protected $json = null;
	
	protected $oldJson = null;
	
	protected $lastResponsiveSlot = null;
	protected $lastSlot = null;
	
    public function setEditParams($fid = 0, $json = null){
        $this->fid = (int) $fid;
        $this->json = $json;
    }
	
    public function performSlotChanges(){
        $id = $this->fid;
        $this->db->setQuery( "SELECT `responsive_data` FROM #__m4j_forms WHERE fid = '$id' LIMIT 1" );
        $row = $this->db->loadObject();
       
        $this->oldJson = unserialize( bDec($row->responsive_data)  ); 
        $oldOrderToSlot = $this->oldJson->orderToSlot;
         
        $newOrderToSlot = $this->json->orderToSlot;
        $_newOrderToSlot = $newOrderToSlot;
        
        $this->lastResponsiveSlot = array_pop( $_newOrderToSlot); 
        $this->lastSlot = sizeof( $newOrderToSlot );
              
        $removedSlots = array_diff($oldOrderToSlot, $newOrderToSlot);
        $addedSlots = array_diff($newOrderToSlot, $oldOrderToSlot);
                
        foreach($newOrderToSlot as $slot => $responsive_slot){
            if(in_array($responsive_slot, $addedSlots)) continue;
            if( in_array($responsive_slot, $oldOrderToSlot)){
                $oldSlot = array_search($responsive_slot, $oldOrderToSlot);
                if($oldSlot == $slot) continue;
                $this->_refactorSlot($responsive_slot,  $slot);
            }
        } 
        
        
        foreach($removedSlots as $removedSlot){
            $this->_refactorRemovedSlot($removedSlot);
        }
    }
	
	protected function _refactorSlot($responsive_slot = null, $slot = null){
	    if(!$responsive_slot || !$slot) return;  
	    $this->update('#__m4j_formelements', array('slot' => $slot),$this->where(array("fid"=> $this->fid, "responsive_slot" => $responsive_slot)));

	}
    
	protected function _refactorRemovedSlot($removedSlot =null){
		
		
	    if(!$this->lastResponsiveSlot || !$this->lastSlot || !$removedSlot ) return;
	    if( ! $this->count('#__m4j_formelements', $this->where(array("fid"=> $this->fid, "responsive_slot" => $removedSlot )))) return;

	    $max = $this->getMax('#__m4j_formelements', $this->where(array("fid"=> $this->fid, "responsive_slot" => $this->lastResponsiveSlot )) );
	    $query = 			"UPDATE `#__m4j_formelements` 
	                        SET `slot` = '$this->lastSlot', `responsive_slot` = '$this->lastResponsiveSlot', `sort_order` = `sort_order` + $max 
	                        WHERE `responsive_slot` = '$removedSlot' ; ";
	    
	    $this->db->setQuery($query);
	    $this->db->query();
	}
	
	public function getElementCounts($json = null, $fid = null){
		if(!$json || ! $fid) return array();
		$elementCount = array();
		foreach($json->rows as &$row){
			foreach ($row as & $section){
				$count = $this->count('#__m4j_formelements', $this->where(array("fid" => (int) $fid, "responsive_slot" => $section->slot )));
				$elementCount[ (int) $section->slot] = (int) $count;
			}
		}
		return $elementCount;		
	}
    
}