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

if(! defined('PROFORMSMODELOPT_ERROR_NOIDENT')){

	define( 'PROFORMSMODELOPT_ERROR_NOIDENT' , 0 );
	define( 'PROFORMSMODELOPT_ERROR_CORRUPTIDENT' , 1 );
	define( 'PROFORMSMODELOPT_ERROR_STIDMISSMATCH' , 2 );
	define( 'PROFORMSMODELOPT_ERROR_IDENTMISSMATCH' , 3 );
	define( 'PROFORMSMODELOPT_ERROR_NOOPTIN' , 4 );
	
}


class ProformsModelOpt extends ProformsModel{
	
	public $noOptAction = false;
	
	protected $action = 'in';
	
	protected $error = array();
	
	protected $storedElements = array();
	
	protected $job = null;
	
	protected $storage = null;
	
	protected $ident = null;
	
	protected $user = array( "name" => null, "realname"=> null, "ip"=> null);
	
	protected $optParameters = null;
	
	protected $stid = 0;
	
	/**
	 * 
	 * @var ProformsViewOpt
	 */
	protected $view = null;
	
	
	
	
	protected function _init(){
		$action = strtolower( trim( JRequest::getCmd("opt", null) )  );
		if($action !== 'in' && $action !== 'out' ){
			//NO PROPER OPT ACTION
			$this->noOptAction = true;
			return;
		}
		$this->action = $action;
		
		$ident = JRequest::getString('ident', null);
		$ident = (string) trim( preg_replace('/[^A-Z0-9:]/i', '', $ident) );
		if(! $ident){
			return $this->_setError(PROFORMSMODELOPT_ERROR_NOIDENT);
		}
		
		$this->ident = $ident;
		
		$ident = explode(":", $ident);
		if(sizeof($ident) !== 2 ){
			return $this->_setError(PROFORMSMODELOPT_ERROR_CORRUPTIDENT);
		}
		
		$hash = trim( $ident[0] );
		$stid = (int) $ident[1];
		
		$this->db->setQuery("SELECT * FROM `#__m4j_storage` WHERE `stid` = '$stid' LIMIT 1 ");
		$storageItem = $this->db->loadObject();
		if(empty($storageItem)){
			return $this->_setError(PROFORMSMODELOPT_ERROR_STIDMISSMATCH);
		}
		
		$storeParams = $this->_getBParameters($storageItem->parameters) ;
		$this->optParameters = $storeParams;
		
		$this->storage = $storageItem;
		$this->storage->parameters = $storeParams;
		
		$this->stid = $stid;
		
		$this->db->setQuery("SELECT * FROM `#__m4j_jobs` WHERE `jid` = '$storageItem->jid' LIMIT 1");
		$job = $this->db->loadObject();
		if(! $job || ! $job->is_optin){
			return $this->_setError(PROFORMSMODELOPT_ERROR_NOOPTIN);
		}
		$this->job = $job;
		
		$storageHash =  md5( $storageItem->jid.$storageItem->fid.$storageItem->tmp_dir.$storageItem->user_id.$storageItem->user_ip );
		
		if($storageHash !== $hash){
			return $this->_setError(PROFORMSMODELOPT_ERROR_IDENTMISSMATCH);
		}
		
		$this->db->setQuery("SELECT * FROM `#__users` WHERE `id` = '$storageItem->user_id' LIMIT 1");
		$user = $this->db->loadObject();
		
		$this->user["name"] = ($user) ? $user->username : null;
		$this->user["realname"] = ($user) ? $user->name : null;
		$this->user["ip"] = $storageItem->user_ip ; 
		
		$optin = $this->action === 'out' ?  0 : 1 ;
		
		$this->db->setQuery("UPDATE `#__m4j_storage` SET `optin` = '$optin' WHERE `stid` = '$stid' LIMIT 1;");
		$this->dbQuery();
		
		// Fetching  all element values with aliases
		$fids = explode(";", $this->job->fid);
		foreach ($fids as $fid){
			$fid = (int) $fid;
			if($fid > 0){
				$this->db->setQuery("SELECT *, 0 AS `parentSend`
									FROM `#__m4j_formelements` AS `fe`
									LEFT JOIN  `#__m4j_storage_items` AS `si` ON `fe`.`eid` = `si`.`eid`
									WHERE `fe`.`fid` = '$fid' AND `fe`.`active` = 1 AND `si`.`stid` = '$this->stid' 
									GROUP BY `fe`.`eid` 
									ORDER BY `slot`,`sort_order` ASC ");
				$fidRows = $this->db->loadObjectList();
				if(!empty($fidRows)){
					$this->storedElements[] = $fidRows;
				}
			}	
		}//EOF foreach fids
		
		
		
	}//EOF init
	
	protected function _setError($no = PROFORMSMODELOPT_ERROR_NOIDENT){
		
		$error = 'NO IDENT FOUND';
		
		switch ($no){
			case PROFORMSMODELOPT_ERROR_CORRUPTIDENT:
				$error = 'CORRUPT IDENTIFICATION TOKEN';
			break;
			

			case PROFORMSMODELOPT_ERROR_STIDMISSMATCH:
				$error = 'STORAGE ITEM NOT FOUND';
			break;
				

			case PROFORMSMODELOPT_ERROR_IDENTMISSMATCH:
				$error = 'IDENTIFICATION MISSMATCH';
			break;


			case PROFORMSMODELOPT_ERROR_NOOPTIN:
				$error = 'DOUBLE OPT IN IS NOT ENABLED FOR THIS FORM';
			break;
		}
		
		$this->error[] = $error;		
	}
	
	public function updateStorageParameters($parameters = null){
		$storageParameters = "";
		foreach($parameters as $key => $value){
			$storageParameters .= $key . "\t" . bEnc($value) . "\n";
		}
		
		if($storageParameters){
			$this->db->setQuery("UPDATE `#__m4j_storage` SET `parameters` = '" .$this->dbEscape($storageParameters)."' WHERE `stid` = '$this->stid' LIMIT 1");
		}else{
			$this->db->setQuery("UPDATE `#__m4j_storage` SET `parameters` = NULL WHERE `stid` = '$this->stid' LIMIT 1");
		}
		
		$this->dbQuery();
	}
	
	
	protected function _getBParameters($parameters = null){
		if(!$parameters) return null;
		$std = new stdClass();
		$chopped = explode("\n",trim($parameters));
		foreach($chopped as $atom){
			$split = explode("\t",$atom);
			if (sizeof($split)==2){
				$key = trim($split[0]);
				$std->$key = bDec( trim($split[1]) );
			}
		}
		return $std;
	}
	
	public function getError(){
		return $this->error;
	}
	

	public function getAction(){
		return $this->action;
	}
	
	public function getJob(){
		return $this->job;
	}
	
	public function getStoredElements(){
		return $this->storedElements;
	}
	
	public function getStorage(){
		return $this->storage;
	}
	
	public function getIdent(){
		return $this->ident;
	}
	
	public function getUser(){
		return $this->user;
	}
	
	public function hasOptParameters(){
		return $this->optParameters ? true : false;
	}
	
	public function getOptParameters(){
		return $this->optParameters;
	}
	
	
	
	
}