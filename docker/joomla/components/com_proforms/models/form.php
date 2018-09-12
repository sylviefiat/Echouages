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


class ProformsModelForm extends ProformsModel{
	
	protected $jid = 0;

	protected $available = false;
	
	protected $fid2Data = array();
	
	protected $eids = array();
	
	protected $fids = array();
	
	protected $allowed = true;
	
	
	protected $showRequiredAdvice = false;
	
	
	protected $userMailId = 0;
	
	protected $insertId = false;
	
	protected $tempHash = null;
	
	/**
	 * 
	 * @var stdClass
	 */
	protected $category = null;
	
	protected $categoryEmailAddress = null;
	
	/**
	 * 
	 * @var array
	 */
	protected $apps = array();
	
	
	public function setJid($jid = null){
		$this->jid = (int) $jid;
		
		$this->db->setQuery("SELECT * FROM `#__m4j_jobs` WHERE `jid` = '$this->jid' AND `active` = 1 LIMIT 1 ");
		$data = $this->db->loadObject();
		$this->available = (! empty($data) ) ? true : false;
		if(! $this->available) return ;
		
		if( ! ProformsHelper::access($data->access)){
			$this->allowed = false;
			return;
		}
		
		//Check category
		$cid = isset($data->cid) ? (int) $data->cid : 0;		
		if($cid > 0){
			$this->db->setQuery("SELECT * FROM `#__m4j_category` WHERE `cid` = '$cid'  LIMIT 1 ");
			$cat = $this->db->loadObject();
			$this->category = $cat;
			if(isset($cat->email)){
				$this->categoryEmailAddress = trim($cat->email);
			}
		}
		
		$data->customize = $this->getCustomizeParams($data);
		
		$this->setParams($data);
		$this->params->formTemplates = array();
		
		if(isset($this->params->fid)){
			$fids = explode(";", $this->params->fid);
			$this->eids = array();
			foreach($fids as & $fid){
				$fid = intval($fid);
				// process form templates
				if($fid && $fid > 0 ){
					$this->_retreiveFormTemplate($fid);
				}
				
			}
			$this->params->fids = $fids;			
			
		} 
		//Retreive App Plugins for this form (job)
		$this->_getAppPlugins();
		
	}
	
	protected function _retreiveFormTemplate($fid = 0){
		
		$this->fids[] = $fid;
		
		$this->db->setQuery("SELECT * FROM `#__m4j_forms` WHERE `fid` = '$fid' LIMIT 1 ");
		$form = $this->db->loadObject();
		if($form){
			$this->db->setQuery("SELECT * FROM `#__m4j_formelements` WHERE `fid`='".$fid."' AND `active` = '1' ORDER BY `slot`,`sort_order` ASC");
			$form->elements = $this->db->loadObjectList();
			if($form->elements){
				foreach ($form->elements as $row){
					if(isset($row->eid)){
						if(isset($row->required) && $row->required){
							$this->showRequiredAdvice = true;
						}
						
						if(isset($row->usermail) && $row->usermail){
							$this->userMailId = (int) $row->eid;
							$this->showRequiredAdvice = true;
						}
						array_push($this->eids, (int) $row->eid);						
					}
				}
			}
		}
		$this->params->formTemplates[(int) $fid] = $form;
	}
	
	protected function _getAppPlugins(){
		$this->db->setQuery("SELECT `a`.`app`, `aj`.`params` FROM `#__m4j_apps` AS `a`
			  LEFT JOIN `#__m4j_apps2jobs`AS `aj` ON `a`.`app` = `aj`.`app`
			  WHERE `a`.`active` = '1' AND `aj`.`active` = '1' AND `a`.`has_plugin` = '1'  AND `aj`.`jid` = '$this->jid'");
		$this->apps = $this->db->loadObjectList();
	}
	
	/**
	 * 
	 * @param AppPluginManager $pluginManager
	 */
	public function performAppPluginOnTemplates( & $pluginManager = null, $send = 0){
		$send = (int) $send;
		if(isset($this->params->formTemplates)){
			foreach ($this->params->formTemplates as  $fid => & $template){
				if(isset($template->elements)){
					$pluginManager->onTemplate($template->elements, $fid, $send);
				}//EOF has elements
			}//EOF foreach
		}//EOF isset formTemplates
	}	
	
	public function isAvailable(){
		return (bool) $this->available;
	}
	
	public function getEids(){
		return $this->eids;
	}
	
	public function getCustomizeParams($raw = null){
		$customize = new stdClass();
		$customize->submit_align = 0;
		$customize->submit_text = null;
		$customize->reset_text = null;
		$customize->use_reset = 1;
		$customize->metatitle = 1;
	
		if(isset($raw->customize)){
			$unserCustomize = unserialize(bDec($raw->customize));
			if(is_object($unserCustomize)){
				foreach($unserCustomize as $key => & $value){
					$customize->$key = $value;
				}//EOF foreach
			}//EOF is object
		}// EOF isset
		return $customize;
	}
	
	/**
	 * 
	 * @param ProformsViewForm $view
	 * @return boolean
	 */
	public function createStore( & $view = null){
		if(!$view) return false;
		
		$user = JFactory::getUser();
		$ip = ProformsHelper::getRemoteIp();
			
// 		$rootDir = ($this->rootDir !== M4J_TMP) ? $this->rootDir : "";
		$rootDir = $view->getTmpPath();
		$this->tempHash = $view->getTmpDir();
		
		
		$query = "INSERT INTO `#__m4j_storage`"
		. "\n ( `jid`, `fid`, `tmp_dir` , `user_id` , `user_ip`, `root_dir` )"
		. "\n VALUES"
		. "\n ( '".(int) $this->params->jid
		. "', '" . $this->dbEscape($this->getFids(true))
		. "', '" . $this->dbEscape($this->tempHash) 
		. "', '" . $this->dbEscape($user->id)
		. "', '" . $this->dbEscape($ip)
		. "', '" . $this->dbEscape($rootDir)
		. "' )";
		
		$this->db->setQuery($query);
		$this->db->query();
		$this->insertId = $this->db->insertid();
		
// 		if($GLOBALS["optin"]){
// 			if($GLOBALS["optin"]->is_optin){
// 				$this->optInIdent = md5($this->jid.$this->fids.$this->tmpDir.$user->id.$ip).":".$insert_id;
// 			}
		
// 		}
		
		return $this->insertId; 
		
	}
	
	
	public function getAllowed(){
		return $this->allowed;
	}
	

	public function getShowRequiredAdvice()
	{
	    return $this->showRequiredAdvice;
	}

	public function getUserMailId()
	{
	    return $this->userMailId;
	}

	public function getApps()
	{
	    return $this->apps;
	}

	public function getInsertId()
	{
	    return $this->insertId;
	}

	public function getTempHash()
	{
	    return $this->tempHash;
	}
	
	public function getCategory(){
		return $this->category;
	}
	
	public function getCategoryEmail(){
		return $this->categoryEmailAddress;
	}
	
	public function getFids($toString = false){
		return $toString ? implode(";", $this->fids) : $this->fids;
	}
	
	public function getJid(){
		return (int) $this->params->jid;
	}
}