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


class ProformsModel{

	protected static $models = array();
	
	
	/**
	 * 
	 * @var stdClass
	 */
	protected $params = null;
	
	/**
	 * 
	 * @var JDatabaseDriver
	 */
	protected $db = null;

	/**
	 * 
	 * @var ProformsView
	 */
	protected $view = null;
	
	public function __construct(){
		$this->params = new stdClass();
		$this->db = JFactory::getDbo();
		$this->_init();
	}

	
	protected function dbEscape($toEscape = null){
		return method_exists($this->db, 'escape') ? $this->db->escape($toEscape) : $this->db->getEscaped($toEscape);
	}
	
	protected function dbQuery(){
		if(method_exists($this->db, 'execute')){
			$this->db->execute();
		}else{
			$this->db->query();
		}
	}
	
	protected function _init(){
		return;
	}

	public function & getParamsReference(){
		return $this->params;
	}
	
	public function getParams(){
		return $this->params;
	}

	public function setParams($params){
		$this->params = $params;
	}
	
	public function getParameter($name = null, $default = null){
		return isset($this->params->$name) ? $this->params->$name : $default;
	}
	/**
	 * 
	 * @param string|array|object $name
	 * @param any $value
	 */
	public function setParameter($name = null, $value = null){
		if($name && (is_array($name || is_object($name)))  ){
			foreach($name as $key => $val){
				$this->params->$key = $val;
			}
		}elseif($name){
			$this->params->$name = $value;
		}
	}
	
	/**
	 * 
	 * @param string $modelName
	 * @param ProformsView $view
	 * @return multitype:|boolean|ProformsModel
	 */
	public static function getInstance($modelName = null, & $view = null){
		if( isset( self::$models[$modelName] ) ) return self::$models[$modelName];
		$modelPath = JPATH_ROOT. '/components/com_proforms/models/' . $modelName . '.php';
		if(JFile::exists($modelPath)){
			require_once $modelPath;
			$className = "ProformsModel" . ucfirst(strtolower($modelName));
			if(class_exists($className) && get_parent_class($className)== "ProformsModel" ){
				self::$models[$modelName] = new $className();
				if($view && is_object($view) && get_parent_class($view) === 'ProformsView'){
					self::$models[$modelName]->setView( $view );
				}
				return self::$models[$modelName];
			}
		}
		return false;
	}
	/**
	 * 
	 * @param ProformsView $view
	 */
	public function setView( & $view = null){
		if($view && is_object($view) && get_parent_class($view) === 'ProformsView'){
			$this->view = $view;
		}
	}
	
}//EOF class