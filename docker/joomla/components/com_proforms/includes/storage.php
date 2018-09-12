<?php
/**
* @name MOOJ Proforms 
* @version 1.0
* @package proforms
* @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
* @author Dipl. Inf.(FH) Fahrettin Kutyol
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Please note that some Javascript files are not under GNU/GPL License.
* These files are under the mad4media license
* They may edited and used infinitely but may not repuplished or redistributed.  
* For more information read the header notice of the js files.
**/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


class ProformsLegacyStorage{

	var $jid = null;
	var $fids = null;
	var $fid = array();
	var $rootDir = M4J_TMP;
	var $tmpDir = null;
	var $userEmail = null;
	var $optInIdent = null;
	var $insertID = null;
	var $lookUp = array();
	var $aliasLookUp = array();
	var $alias2id = array();
	var $stid = null;
	
	protected $compareOldFid = array();
	
	/**
	 * 
	 * @var ProformsViewForm
	 */
	protected $view = null;
	
	/**
	 * 
	 * @param int $jid
	 * @param string $fid
	 * @param ProformsViewForm $view
	 */
	public function __construct($jid=null, $fid = null, ProformsViewForm & $view = null){
		if(!$view) return;
		$this->view = & $view;
		
		$this->tmpDir = $this->view->getTmpDir();
		$this->jid = (int) $jid;
		$this->fids = $fid;
				
		$fids = explode(";",$fid);
		foreach($fids as $f){
			$f = (int) $f;
			if($f>0){
				$this->fid[$f] = array();
				$this->compareOldFid[$f] = array();
			}
		}
	}

	public function add($fid = null, $eid=null,$content=null,$alias = null){
		$fid = (int) $fid;
		$eid = (int) $eid;
		
		if(is_array($content)){
			$content = implode("\n",$content);
		}
		$this->compareOldFid[$fid][$eid] = $content;
		$this->fid[$fid][$eid] = $content;
		$this->lookUp[$eid] = & $this->fid[$fid][$eid];
		if($alias){
			$alias = md5($alias);
			$this->aliasLookUp[$alias] = & $this->fid[$fid][$eid];
			$this->alias2id[$alias] = $eid;
		}
	}
	
	public function & get($fid = null, $eid=null){
		return $this->fid[$fid][$eid];
	}
	/**
	 * 
	 * @param int $fid
	 * @param int $eid
	 * @param Array|string $content
	 * 
	 * @deprecated 
	 */
	public function set($fid = null, $eid=null, $content = null){
		if(is_array($content)){
			$content = implode("\n",$content);
		}
		$this->fid[$fid][$eid] = $content;
	}

	/**
	 * 
	 * @param int $eid
	 * @param Array|string $content
	 */
	public function setById($eid = null, $content=null){
		if(is_array($content)){
			$content = implode("\n",$content);
		}
		if(isset($this->lookUp[$eid])){
			$this->lookUp[$eid] = $content;
		}
	}
	
	/**
	 *
	 * @param string $alias
	 * @param Array|string $content
	 */
	public function setByAlias($alias = null, $content=null){
		$alias = md5($alias);
		if(is_array($content)){
			$content = implode("\n",$content);
		}
		if(isset($this->aliasLookUp[$alias])){
			$this->aliasLookUp[$alias] = $content;
		}
	}
	
	public function getIdByAlias($alias = null){
		$alias = md5($alias);
		return isset($this->alias2id[$alias]) ? (int) $this->alias2id[$alias] : false;
	}
			
	public function setRootDir($rootDir = M4J_TMP){
		$this->rootDir = $rootDir;		
	}
	
	public function setStid($stid = null){
		$this->stid = $stid;
	}
	
	
	
	public function LEGACY_COMPARISION(){
		$changes = array();
		foreach ($this->fid as $fid => & $el ){
			
			foreach ($el as $eid => $value ){
				if( ! isset($this->compareOldFid[$fid][$eid])  ) continue;
				$oldValue = $this->compareOldFid[$fid][$eid];
				if($oldValue !== $value){
					$changes[$eid] = $value;
				}//EOF if change
				
			}//EOF foreach elements

		}//EOF foreach form templates
		
		
		foreach ($changes as $eid => $value){
			$this->view->setElementValue($eid, $value);
		}
		
// 		MDebug::pre($this->fid);
// 		MDebug::pre($this->compareOldFid);
// 		MDebug::pre($changes);
	}
	
	
	
	public function update(){
		return;
	}//EOF update
	
	

	public function addUserEmail($email = null){
		
		$validate = M4J_validate::getInstance();
		if($validate->email($email)){
			$this->userEmail = $email;
			return true;
		} else{
			return false;
		}
	}

	public function addTempDir($tempDir=null){
		$this->tmpDir = $tempDir;
	}
	
	
	public function getDir(){
		return $this->rootDir . $this->tmpDir;
	}
	
	public function replaceByAlias($string = null, $escape = 0){
		return $this->view->aliasReplacement($string, (bool) $escape);
	}

}

