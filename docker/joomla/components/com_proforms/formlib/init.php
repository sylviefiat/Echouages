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

if(! class_exists("MFormFactory")){

include_once  dirname(__FILE__) . '/formelement.php'	;
// include_once JPATH_ROOT . '/components/com_proforms/includes/selections.php';	

	class MFormFactory{
		protected static $_names2Classes = array();
		protected static $_id2Classes = array();
		protected static $_id2Folders = array();
		
		protected static $_loaded = array();
		
		protected static $_path;
		
		protected static $ids = array();
		
		public static function init(){
			if( empty(self::$_path) ){
				self::$_path = dirname(__FILE__);
			
			}
			$dirs = array();
			if( function_exists("scandir") ){
				$dirs = array_diff(scandir( dirname(__FILE__)), array('.','..'));
			}else{
				$dh  = opendir( dirname(__FILE__));
				while (false !== ($filename = readdir($dh))) {
					$dirs[] = $filename;
				}
				closedir($dh);
				$dirs= array_diff($dirs, array('.','..'));
			}
						
			foreach ($dirs as $dir){
				$formLibPath = self::$_path . DIRECTORY_SEPARATOR . $dir;
				if( is_dir( $formLibPath ) ){
					
					$splitName = explode("#", $dir);
					if( sizeof($splitName)=== 2 && is_numeric($splitName[1]) ){
						$id = (int) $splitName[1];
						array_push(self::$ids, $id);
						$name = trim($splitName[0]);
						self::$_id2Folders[$id] = $formLibPath . DIRECTORY_SEPARATOR;
						$className = "MForm" . ucfirst( strtolower(preg_replace('/[^A-Z0-9]/i','',$name) ) );
						self::$_id2Classes[$id] = $className;
						self::$_names2Classes[$name] = $className; 
					}//EOF if proper format					
				}//EOF dir exists
			}//EOF foreach
			sort(self::$ids);
		}
		
		public static function getIds(){
			return self::$ids;
		}
		
		public static function import($formId = 0){
			
			$formId = (int) $formId;			
			if(!$formId) return;
			if(!isset(self::$_loaded[$formId])&& isset(self::$_id2Folders[$formId]) && file_exists(self::$_id2Folders[$formId] . '/view/form.php')){
				require_once self::$_id2Folders[$formId] . '/view/form.php';
				if(isset(self::$_id2Classes[$formId]) && class_exists(self::$_id2Classes[$formId])){
					self::$_loaded[$formId] = true; 
				}else die("class not found");
			}//EOF is not loaded
		}
		
		/**
		 * 
		 * @param int $formId	The form type id
		 * @param stdClass $dataObject	The database std object
		 * @param string $type	'default' or 'responsive'
		 * @return MFormElement|NULL	A form object
		 */
		public static function create($formId = 0, $dataObject = NULL, $type = 'default', $tmpPath = NULL, $tmpDir = NULL){
			self::import($formId);
			if(isset(self::$_loaded[$formId])){
				$className = self::$_id2Classes[$formId];
				/* @var $object MFormElement */
				$object = new $className($dataObject, $tmpPath, $tmpDir);
				$object->render($type);
				return $object;
			}
			return null;
		}
		
		public static function getFolder($id = 0){
			if(isset(self::$_id2Folders[(int) $id])){
				return self::$_id2Folders[(int) $id];
			}else return false;
		}
		
		
	}//EOF class MFormFactory
	
	MFormFactory::init();
	
	
}//EOF class does not exist