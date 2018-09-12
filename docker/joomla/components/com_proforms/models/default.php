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

class ProformsModelDefault extends ProformsModel{
	
	protected $list = array();
	
	protected function _init(){
		$this->db->setQuery( "SELECT COUNT(*) as count FROM #__m4j_jobs WHERE  cid = '-1' " );
		$count = $this->db->loadObject();
		
		if($count->count > 0 && defined('M4J_SHOW_NO_CATEGORY') && M4J_SHOW_NO_CATEGORY){
			$this->_addToList( M4J_LANG_NO_CATEGORY, M4J_LANG_NO_CATEGORY_LONG, M4J_CID.'-1' );
		}
		
		$this->db->setQuery("SELECT `name`, `cid`, `access` ,`introtext` FROM #__m4j_category WHERE  `active` = '1' ORDER BY `sort_order`");
		$rows = $this->db->loadObjectList();
		foreach ($rows as $row){
			if(ProformsHelper::access($row->access)){
				$this->_addToList( MReady::_( $row->name ), $row->introtext, M4J_CID. (int) $row->cid );
			}
		}
		
	}
	
	protected function _addToList($heading = null,$introtext = null,$link = null){
		$item = new stdClass();
		$item->heading = $heading;
		$item->introtext = $introtext;
		$item->link = $link;
		array_push($this->list, $item);
	}
	
	/**
	 * @return	array
	 */
	public function getList(){
		return $this->list;
	}
	
}