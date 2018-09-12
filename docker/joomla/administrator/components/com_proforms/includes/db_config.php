<?php
/**
* @name MOOJ Proforms 
* @version 1.0
* @package proforms
* @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
* @author Dipl. Inf.(FH) Fahrettin Kutyol
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
* Please note that some Javascript files are not under GNU/GPL License.
* These files are under the mad4media license
* They may edited and used infinitely but may not repuplished or redistributed.  
* For more information read the header notice of the js files.
**/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class DBConfig {
	
	var $_optin = true;
	var $_date = true;
	var $_user = true;
	var $_ip = true;
	
	var $_exportOptin = true;
	var $_exportDate = true;
	var $_exportUser = true;
	var $_exportIP = true;
	
	
	var $eid = array();
	
	function __construct(){
		
	}
	function setOptin($is){
		$this->_optin = (bool) $is; 
	}
	
	function isOptin(){
		return $this->_optin;
	}
	
	function styleOptin(){
		return $this->isOptin() ? " display: table-cell; " : " display: none";
	}
	
	function setDate($is){
		$this->_date = (bool) $is; 
	}
	
	function isDate(){
		return $this->_date;
	}

	function styleDate(){
		return $this->isDate() ? " display: table-cell; " : " display: none";
	}
	
	function setUser($is){
		$this->_user = (bool) $is; 
	}
	
	function isUser(){
		return $this->_user;
	}

	function styleUser(){
		return $this->isUser() ? " display: table-cell; " : " display: none";
	}
	
	function setIP($is){
		$this->_ip = (bool) $is; 
	}
	
	function isIP(){
		return $this->_ip;
	}

	function styleIP(){
		return $this->isIP() ? " display: table-cell; " : " display: none";
	}
	
	
	function setExportOptin($is){
		$this->_exportOptin = (bool) $is; 
	}
	
	function isExportOptin(){
		return $this->_exportOptin;
	}
	
	function setExportDate($is){
		$this->_exportDate = (bool) $is; 
	}
	
	function isExportDate(){
		return $this->_exportDate;
	}
	
	function setExportUser($is){
		$this->_exportUser = (bool) $is; 
	}
	
	function isExportUser(){
		return $this->_exportUser;
	}
	
	function setExportIP($is){
		$this->_exportIP = (bool) $is; 
	}
	
	function isExportIP(){
		return $this->_exportIP;
	}

	function setArrayEID($eid){
		$this->eid = $eid;
	}
	
	function setEID($eid,$value){
		$this->eid[$eid] = $value;
	}
		
	function isEID($eid){
		return (isset($this->eid[$eid])) ? (bool) $this->eid[$eid] : true;
	}

	function styleEID($eid){
		return $this->isEID($eid) ? " display: table-cell; " : " display: none; ";
	}	
	
	public static function unserializeFromDB($jid= null){
		if(!$jid) return new DBConfig();
		$db = JFactory::getDBO();
		$query = "SELECT `db` FROM #__m4j_jobs where `jid` = '".$jid."' LIMIT 1";
		$db->setQuery($query);
		$data =  $db->loadObject();
		
		if($data){
			if(! $data->db) return new DBConfig();
			try {
				$object = unserialize($data->db);
			}catch (Exception $e){
				$object = new DBConfig();
			}
			return $object;
		}else{
			return new DBConfig();
		}
		
	}
	
	public static function serialize($object = null, $jid = null){
		if(!$jid) return false;
		$object = $object ? $object : new DBConfig();
		$data = serialize($object);
		MDB::update("#__m4j_jobs",array("db"=>$data),MDB::_("jid",(int) $jid));
		return true;		
	}
	
	function getSize($items){
		$size = 32 *2 ; // edit and delete area size
		$size += $this->isOptin() ? 32 : 0 ;
		$size += $this->isDate() ? 80 : 0 ;
		$size += $this->isUser() ? 80 : 0 ;
		$size += $this->isIP() ? 90 : 0 ;
		
		if($items){
			foreach($items as $item){
				$size += ($this->isEID($item->eid)) ? ( (int)M4J_STORAGE_TD ) : 0 ;
			}
		}
		return $size;		
	}
	
} 



?>