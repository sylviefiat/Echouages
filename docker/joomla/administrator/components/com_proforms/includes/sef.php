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
 
define('MSEF_CAT',1);
define('MSEF_FORM',2);

class MSEF{
	
	var $formId = null;
	var $catId = null;
	var $title = null;
	var $alias = null;
	var $sefUrl = null;

	public function __construct($title=null,$alias=null,$formId=null,$catId=null){
		$this->title = $title;
		$this->alias = $alias;
		$this->formId =$formId;
		$this->catId = $catId;
		$this->createSEF();
	}
	
	public function createSEF(){
		if(! $this->formId && ! $this->catId ) return null;	
		$url = ($this->alias)? $this->alias: $this->title;
		if(!$this->formId || $this->catId <0){
			$this->sefUrl = $this->check($this->get($url));
		}else{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT `url` FROM `#__m4j_sef` WHERE `jid` = '0' AND `cid`= '".(int) $this->catId."' LIMIT 1");
			$cat = $db->loadObject();
			$this->sefUrl = $cat->url."/".$this->check($this->get($url));
		}	
	}
	
	public function insert(){
		$db = JFactory::getDBO();
		$query = "INSERT INTO `#__m4j_sef`"
						. "\n ( `jid`, `cid`, `url` )"
						. "\n VALUES"
						. "\n ( '".(int) $this->formId."', '". (int) $this->catId. "', '". dbEscape($this->sefUrl)."' )";
		$db->setQuery($query);
		$db->query();
	}
	
	public function update(){
		$db = JFactory::getDBO();
		if($this->formId){
			$db->setQuery("SELECT * FROM `#__m4j_sef` WHERE `jid`= '".(int) $this->formId."' LIMIT 1");
			$formData = $db->loadObject();
			$originalCid = (int) $formData->cid;
			if(! $formData ){
				die("no form data");
				$this->insert();
			}else{
				$cid = (int) $this->catId;
				$noCatChange = ($originalCid == $cid);
				
				if($noCatChange){
					
					$query = "UPDATE `#__m4j_sef` "
					. "\n SET"
					. "\n `url` = '".dbEscape($this->sefUrl)."' "
					. "\n WHERE `jid` = '". (int) $this->formId."' AND `cid`= '".$cid."'";
				}else{
					
					$query = "UPDATE `#__m4j_sef` "
					. "\n SET"
					. "\n `cid` = '".(int) $this->catId."', " 
					. "\n `url` = '".dbEscape($this->sefUrl)."' "
					. "\n WHERE `jid` = '". (int) $this->formId."' ";
				}
// 				echo'<pre>';
// 				echo $query;
// 				echo "\n";
// 				print_r($formData);
// 				echo "\n CID: $cid,  originalCid: $originalCid\n";
// 				print_r($this);
// 				die('/</pre>');
				
				$db->setQuery($query);
				$db->query();
			}
		}else if(!$this->formId && $this->catId){
			$db->setQuery("SELECT * FROM #__m4j_sef WHERE `jid` = '0' AND `cid`= '".(int)$this->catId."' LIMIT 1");
			if(! $db->loadObject()){
				$this->insert();
			}else{
				$query = "UPDATE #__m4j_sef "
						. "\n SET"
						. "\n `jid` = '0', "
						. "\n `url` = '". dbEscape($this->sefUrl)."' "
						. "\n WHERE `jid` = '0' AND `cid` = '". (int) $this->catId."' ";
				$db->setQuery($query);
				$db->query();
				$this->updateCatForms();
			}	
		}
	}
	
	protected function updateCatForms(){
		$db = JFactory::getDbo();
		$db->setQuery("SELECT `jid`, `title`, `alias` FROM `#__m4j_jobs` WHERE `cid`='".(int) $this->catId ."'");
		$rows = $db->loadObjectList();
		foreach($rows as $row){
			$sef = new MSEF($row->title, $row->alias, $row->jid, $this->catId);
			$sef->update();
		}
	}
	
	public function replace($string=null){
		$toReplace = " Å |S, Å’|O, Å½|Z, Å¡|s, Å“|oe, Å¾|z, Å¸|Y, Â¥|Y, Âµ|u, Ã€|A, Ã�|A, Ã‚|A, Ãƒ|A, Ã„|Ae, Ã…|A,".
					  " Ã†|A, Ã‡|C, Ãˆ|E, Ã‰|E, ÃŠ|E, Ã‹|E, ÃŒ|I, Ã�|I, ÃŽ|I, Ã�|I, Ã�|D, Ã‘|N, Ã’|O, Ã“|O, Ã”|O,".
					  " Ã•|O, Ã–|Oe, Ã˜|O, Ã™|U, Ãš|U, Ã›|U, Ãœ|Ue, Ã�|Y, ÃŸ|ss, Ã |a, Ã¡|a, Ã¢|a, Ã£|a, Ã¤|ae, Ã¥|a,".
					  " Ã¦|a, Ã§|c, Ã¨|e, Ã©|e, Ãª|e, Ã«|e, Ã¬|i, Ã­|i, Ã®|i, Ã¯|i, Ã°|o, Ã±|n, Ã²|o, Ã³|o, Ã´|o,".
					  " Ãµ|o, Ã¶|oe, Ã¸|o, Ã¹|u, Ãº|u, Ã»|u, Ã¼|ue, Ã½|y, Ã¿|y, Äƒ|a, ÅŸ|s, Å£|t, È›|t, Èš|T, È˜|S, È™|s, Åž|S".
					  " @|at, #|-, $|s, Â§|s, *|-, '|-, \"|-, +|-, &|-, %|-, ~|-, :|-, ;|-";

		$string = str_replace(array("?","!","^","Â°","|","<",">","(",")","[","]","{","}","\\","`","Â´",","),"",$string);
		
		$firstSplit = explode(",",$toReplace);
		
		foreach ($firstSplit as $combination){
			$secondSplit = explode ("|",$combination);
			if(sizeof($secondSplit)== 2){
				$replace = trim($secondSplit[0]);
				$with = trim($secondSplit[1]);				
				$string = str_replace($replace,$with,$string);
			}
		}
		
		$string = str_replace(array("-----","----","---","--"," "),"-",$string);
		$string = str_replace(array("-----","----","---","--"," "),"-",$string);
		$string = str_replace(array("-----","----","---","--"," "),"-",$string);
		
		return $string;		
	}
	
	public function get($string=null){
		if(!$string) return null;
		$string =  $this->replace($string);
		return strtolower($string);
	}
	
	public function check($url=null){
		if(! $url) return null;
		$db = JFactory::getDBO();
		$count = 2;
		$returnUrl = $url;
		$db->setQuery("SELECT `url` FROM #__m4j_sef WHERE `url`= '".dbEscape($url)."'");
		while($db->loadResult()){
			$returnUrl = $url."-".$count++;
			$db->setQuery("SELECT `url` FROM #__m4j_sef WHERE `url`= '".dbEscape($returnUrl)."'");
		}
		return $returnUrl;		
	}
	
	public function delete($id=null,$type= null){
		if(!$type || !$id) return null;
		$db = JFactory::getDBO();
		switch ($type){
			case MSEF_CAT:
				$query = "DELETE FROM #__m4j_sef WHERE `jid` = '0' AND `cid` = '".(int) $id."'";	
				break;

			case MSEF_FORM:
				$query = "DELETE FROM #__m4j_sef WHERE `jid` = '".(int) $id."'";
				break;
			
		}
		$db->setQuery($query);
		$db->query(); 	
	}
	
	
}
    
?>