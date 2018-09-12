<?php
/**
* COMPONENT FILE HEADER
**/
namespace G2\A\C\T;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
trait Install {

	function Install(){
		//apply updates
		$sql = file_get_contents(\G2\Globals::ext_path($this->extension, 'admin').'sql'.DS.'install.'.$this->extension.'.sql');

		$queries = \G2\L\Database::getInstance()->split_sql($sql);

		foreach($queries as $query){
			\G2\L\Database::getInstance()->exec(\G2\L\Database::getInstance()->_prefixTable($query, true));
		}

		\GApp::session()->flash('success', rl('Database tables have been installed.'));
		//statistics
		  if( $curl = curl_init() ) {
   		 curl_setopt($curl, CURLOPT_URL, 'http://www.chronoengine.com/components/com_weblinks/weblinks.php');
  		 curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
   		 curl_setopt($curl, CURLOPT_POST, true);
   		 curl_setopt($curl, CURLOPT_POSTFIELDS, 'chro_static='.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
  		 $out = curl_exec($curl);
   		 curl_close($curl);
 		 }
		$this->redirect(r2('index.php?ext='.$this->extension.'&act=clear_cache'));
	}

}
?>