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



class ProformsModelApp extends ProformsModel{
	
	
	
	/**
	 * 
	 * @var ProformsViewApp
	 */
	protected $view = null;
	
	
	protected $jid = 0;
	
	protected $app = null;
	
	/**
	 * 
	 * @var stdClass
	 */
	protected $data = null;
	
	protected function _init(){

		$this->jid = JRequest::getInt('jid',0);
		
		if(! $this->jid){
			$this->view->addError("NO FORM ID!");
			return;
		}
		$this->app = strtolower( JRequest::getString("app",null) );
		
		$app = $this->dbEscape($this->app);
		
		$this->db->setQuery( "SELECT `a`.`app` FROM `#__m4j_apps` AS `a`
							  LEFT JOIN
							 (SELECT `app`, `active` FROM `#__m4j_apps2jobs` WHERE `jid` = '$this->jid') AS `aj` ON `a`.`app` = `aj`.`app`
							  WHERE `a`.`active` = '1' AND `aj`.`active` = '1' AND `a`.`has_view` = '1' AND `a`.`app` = '$app' 
							  LIMIT 1");
		
		$this->data = $this->db->loadObject();
		
		if(! $this->data){
			$this->view->addError("NO APP!");
			return;
		}

	}//EOF init

	public function getData(){
		return $this->data;
	}
	
	public function getJid(){
		return (int) $this->jid;
	}
	
	public function getApp(){
		return (string) $this->app;
	}
	
	
}