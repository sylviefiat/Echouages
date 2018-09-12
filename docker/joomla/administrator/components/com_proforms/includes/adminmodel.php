<?php
/**
 * @name MOOJ Proforms
 * @version 1.5
 * @package proforms
 * @copyright Copyright (C) 2008-2013 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


class ProformsAdminModel{

	protected static $models = array();
	
	protected static $imported = array();
	
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
	 * @param string $table
	 * @param string|array $fields
	 * @param string $where
	 * @param string $additional
	 * @return NULL|array
	 */
	public function select($table = null, $fields = null, $where = null, $additional = null){
		if(!$table) return null;
		$_fields = '*';
		if(is_array($fields) && !empty($fields)){
			$pairs = array();
			foreach ($fields as $field => $alias){
				$pair = (is_string($field)) ? "`" . trim($field) . "` AS `" . trim($alias) . "`" : "`" . trim($alias) . "`"; 
			}
			$_fields = implode(", ", $pairs);
		}elseif($fields !== null || is_string($fields)){
			$_fields = "`" . trim( (string) $fields ) . "`"; 
		}
		
		$where = trim($where) ; 
		$where = $where ? "WHERE " . $where : '';
		$this->db->setQuery("SELECT " . $_fields . " FROM `" . trim($table) . "` " . $where . " " . trim( (string) $additional ) );
		return $this->db->loadObjectList();		
	}
	
	/**
	 * 
	 * @param string $table
	 * @param string|array $fields
	 * @param string $where
	 * @param string $additional
	 * @return stdClass|NULL
	 */
	public function singleSelect($table = null, $fields = null, $where = null, $additional = null){
		$list = $this->select($table, $fields, $where, $additional . " LIMIT 1");
		return ($list) ? $list[0] : null;
	}
	
	
	
	/**
	 * 
	 * @param string $table
	 * @param array|object $fields
	 * @param string $additional
	 * @return int
	 */
	public function insert($table = null, $fields = null, $additional = null){
		$keys = array(); $values = array();
		foreach($fields as $key=>$value){
			array_push( $keys, "`".trim($key)."`" );
			array_push( $values, "'".$value."'" );
		}		
		$keys = '( ' . implode(", ", $keys) . ')';
		$values = '( ' . implode(", ", $values) . ')';
		$query = "INSERT INTO `".trim($table)."` " . $keys . " VALUES " . $values .  " " . trim($additional);
		$this->db->setQuery($query);
		$this->db->query();
		return $this->db->insertid();
	}
	
	/**
	 * 
	 * @param string $table
	 * @param array|object $fields
	 * @param string $where
	 * @param string $additional
	 * @return bool
	 */
	public function update($table = null, $fields = null, $where = null, $additional = null){
		$pairs = array();
		foreach($fields as $key=>$value){
			array_push( $pairs,  "\n `".trim($key)."` = '".$value."'" );
		}		
		$query = "UPDATE `".trim($table)."` SET" . implode(", ", $pairs) . ( ($where = trim( (string) $where)) ? " \nWHERE ".$where." " : '' ) . $additional;		
		
		$this->db->setQuery($query);
		return $this->db->query();
	}
	
	/**
	 * 
	 * @param string $table
	 * @param string $where
	 * @param string $whereValue
	 * @return bool
	 */
	public function delete($table = null, $where = null, $whereValue = null){
		$table = trim($table); $where = trim($where); $whereValue = trim( (string) $whereValue );
		if(!$table || !$where) return null;			
		$query = "DELETE FROM `$table` ". "WHERE " . ( ($whereValue) ? "`".trim($where)."` = '".$whereValue."' " : $where );
		$this->db->setQuery($query);
		return $this->db->query();
	}
	
	
	public function copy( $table=null , $where = null){
		$aiQuery = "SHOW COLUMNS FROM `".trim($table)."`";
		$this->db->setQuery($aiQuery);
		$rows = $this->db->loadObjectList();
		$insert = "(";
		$select = "|";
		$Field = null;
		foreach ($rows as $row){
				
			if(!$Field){
				if(isset($row->field)) $Field = "field";
				else if (isset($row->Field))$Field = "Field";
				else echo "Can not find Field Names. Please check your server / PHP settings and contact Mad4Media![function: MDB::copy()]<br/>";
			}
			$Extra = "Extra";
			if(isset($row->extra)) $Extra = "extra";
				
			$add = true;
			if(isset($row->$Extra)){
				if($row->$Extra == "auto_increment"){
					$add = false;
				}
			}
			if($add){
				$insert .= ",`".$row->$Field."` ";
				$select .= ",`".$row->$Field."` " ;
			}
		}
		if($where==null) $where = "1";
		$insert = "INSERT INTO `" . trim($table) . "` " . str_replace("(,","( ",$insert) . ") ";
		$select = "( SELECT ". str_replace("|,","",$select) . "FROM `" . trim($table) . "` WHERE ". trim($where) . " )";
	
		$copyQuery = $insert . $select ;
	
		$this->db->setQuery($copyQuery);
		$this->db->query();
		return $this->db->insertid();
	}
	/**
	 *
	 * @param string $copyTable Table Name
	 * @param string $copyWhere	Where statement for the rows which shall be copied
	 * @param string $orderWhere	WHERE statement for getting the maximum order
	 * @param string $sortOrderName	Sort order name
	 * @return int
	 */
	public function copyPlus($copyTable=null, $copyWhere = null, $orderWhere=null , $sortOrderName = "sort_order"){
		$id = $this->copy($copyTable,$copyWhere);
		$maxPlus = $this->getMax( $copyTable, $orderWhere, 1, $sortOrderName);
		$ai = "`".$this->getAI($copyTable)."`='".$id."'";
		$this->setSortOrder($copyTable,$maxPlus,$ai,$sortOrderName);
		return $id;
	}
	
	/**
	 * 
	 * @param string $key
	 * @return string
	 */
	public function quotePoints($key = null){
		$_key = explode(".", $key);
		foreach($_key as & $_k){
			$_k = "`" . $_k . "`";
		}
		return implode(".", $_key);
	}
	
	/**
	 * 
	 * @param string $key
	 * @param any $value
	 * @return string
	 */
	public function where($key = null, $value = null){
		$where = null;
		if(is_array($key) || is_object($key)){
			$pairs = array();
			foreach($key as $k => $val){
				$val = trim( (string) $val );
				$val = (strtoupper($val) === "NULL") ? 'NULL' : "'" . $this->dbEscape($val) . "'";
				$k = $this->quotePoints($k);
				array_push($pairs, $k . ' = ' . $val );
			}
			$where = implode(" AND ", $pairs);
		}else{
			$val = trim( (string) $value );
			$val = (strtoupper($val) === "NULL") ? 'NULL' : "'" . $this->dbEscape($val) . "'";
			$where = $this->quotePoints($key) . " = " . $val ; 
		}
		return $where;
	}
	
	/**
	 * 
	 * @param string $table
	 * @param string $value
	 * @param string $where
	 * @param string $name
	 * @return boolean|mixed
	 */
	public function setSortOrder($table = null,$value= 'NULL',$where=null,$name="sort_order"){
		if(! $table)return false;
		$value = ($value !== "NULL") ? (int) $value : $value;
		
		$query = "UPDATE `".$table."`"
				. "\n SET"
				. "\n `".trim($name)."` = '".$value."' ";
		$query.= ($where) ? " WHERE ".$where : "";
		$this->db->setQuery($query);
		return $this->db->query();
	}
	
	/**
	 * 
	 * @param string $table
	 * @param string $where
	 * @param number $add
	 * @param string $name
	 * @return number
	 */
	public function getMax($table = null, $where = null , $add = 0, $name = "sort_order"){
		$maxinc = ' MAX(`'.$name.'`) AS `maxplus`  ';
		$query = "SELECT".$maxinc." FROM `".$table."`";
		$query.= ($where) ? " WHERE ".$where : "";
	
		$this->db->setQuery($query);
		$info = $this->db->loadObject();
		return $info->maxplus + (int) $add;
	}
	
	/**
	 * 
	 * @param string $table
	 * @return string|bool
	 */
	public function getAI($table = null){
		if(!$table ) return false;
		$this->db->setQuery("SHOW COLUMNS FROM `".trim($table)."`");
		$infos = $this->db->loadObjectList();
		foreach($infos as $info){
			$Extra = "Extra";
			if(isset($row->extra)) $Extra = "extra";
			$isExtra = false;
			if(isset($info->$Extra)){
				if($info->$Extra == "auto_increment"){
					$isExtra = true;
				}
			}
			if($isExtra){
				if(isset($info->field)) return $info->field;
				else if (isset($info->Field))return $info->Field;
				else echo "Can not find Field Names. Please check your server / PHP settings and contact Mad4Media![function: MDB::getAI()]<br/>";
			}
		}
		return false;
	}
	

	/**
	 * 
	 * @param string $table
	 * @param string $where
	 * @param string $additional
	 * @return number
	 */
	public  function count($table = null, $where = null , $additional = null){
		$query = "SELECT COUNT(*) AS `total` FROM `".$table."`";
		$where = $where ? "WHERE ".trim($where) : " ";
		$query .= $where . trim($additional);
		$this->db->setQuery($query);
		$count = $this->db->loadObject();
		return (int) $count->total;
	}
	
	
	public function batchSql($sql = null){
		if(!$sql) return false;
		$error = 0;
		$queries = $this->db->splitSql($sql);
		foreach($queries as & $query){
			if(trim($query)){
				$this->db->setQuery(trim($query));
				$error += ($this->db->query() === false) ? 1:0;
			}
		}
		return $error ? false : true;
	}
	
	
	/**
	 * 
	 * @param string $modelName
	 * @return multitype:|boolean|ProformsModel
	 */
	public static function getInstance($modelName = null){
		if( isset( self::$models[$modelName] ) ) return self::$models[$modelName];
		$modelPath = JPATH_ROOT. '/administrator/components/com_proforms/models/' . $modelName . '.php';
		if(JFile::exists($modelPath)){
			require_once $modelPath;
			$className = "ProformsAdminModel" . ucfirst(strtolower($modelName));
			if(class_exists($className) && get_parent_class($className)== "ProformsAdminModel" ){
				self::$imported[$modelName] = true;
				self::$models[$modelName] = new $className();
				return self::$models[$modelName];
			}
		}
		return false;
	}

	public static function import($modelName = null){
		if( isset( self::$imported[$modelName] ) ) return true;
		$modelPath = JPATH_ROOT. '/administrator/components/com_proforms/models/' . $modelName . '.php';
		if(JFile::exists($modelPath)){
			require_once $modelPath;
			$className = "ProformsAdminModel" . ucfirst(strtolower($modelName));
			if(class_exists($className) && get_parent_class($className)== "ProformsAdminModel" ){
				self::$imported[$modelName] = true;
				return true;
			}
		}
		return false;
	}
	
	
	
}//EOF class