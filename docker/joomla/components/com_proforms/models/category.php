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

class ProformsModelCategory extends ProformsModel{
	
	protected $cid = 0;
	
	protected $allowed = true;
	
	protected $catNotFound = false;
	
	protected $list = array();
	
	protected function _init(){
		return ;
	}
	
	public function setCid($cid = 0){
		$this->cid = (int) $cid;
		$this->setParameter('cid', (int) $cid);
		if($cid == -1){
			if(defined('M4J_SHOW_NO_CATEGORY') && ! M4J_SHOW_NO_CATEGORY){
				$this->allowed = false;
				return;
			}
			
			$this->setParameter('heading', M4J_LANG_NO_CATEGORY );
			$this->setParameter('headertext', M4J_LANG_NO_CATEGORY_LONG );
			$this->setParameter('title', M4J_LANG_NO_CATEGORY );
			
		}else{
		$this->db->setQuery( "SELECT `name`, `access` , `introtext` FROM `#__m4j_category` WHERE `cid` = '".$this->cid."'  AND `active` = '1' LIMIT 1" );
		$cat = $this->db->loadObject();
			if(! $cat ){
				$this->catNotFound = true;
				return;
			}
			
			if( ! ProformsHelper::access($cat->access) ){
				$this->allowed = false;
				return;
			}
			
			$this->setParameter('heading', $cat->name  );
			$this->setParameter('headertext', $cat->introtext );
			$this->setParameter('title', $cat->name );
			
		}
		//Fetch the forms of the category
		$this->db->setQuery(  "SELECT `jid`, `title`, `introtext`, `access` FROM #__m4j_jobs WHERE `cid`= '".$this->cid."' AND `active` = '1' ORDER BY `sort_order`" );
		$rows = $this->db->loadObjectList();
		foreach ($rows as $row){
			if(ProformsHelper::access($row->access)){
				$this->_addToList( MReady::_($row->title), $row->introtext, M4J_JID.$row->jid."&cid=".$this->cid );
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
	
	

	public function getCid(){
	    return $this->cid;
	}

	public function getAllowed(){
	    return $this->allowed;
	}

	public function getCatNotFound(){
	    return $this->catNotFound;
	}

	public function getList()
	{
	    return $this->list;
	}
}