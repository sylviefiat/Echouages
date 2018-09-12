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

define ("MDB_AND","AND");
define ("MDB_OR","OR");
define ("MDB_NOT","NOT");

class MDB{
	
	public static function copy( $table=null , $where = null){
		$db = JFactory::getDBO();
		$aiQuery = "SHOW COLUMNS FROM `".trim($table)."`";
		$db->setQuery($aiQuery);
		$rows = $db->loadObjectList();
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
				
		$db->setQuery($copyQuery);
		$db->query();
		return $db->insertid();
	}
	
	public static function setSortOrder($table,$value= 'NULL',$where=null,$name="sort_order"){
		$db = JFactory::getDBO();
		$query = "UPDATE `".$table."`"
				. "\n SET"
				. "\n `".$name."` = '".$value."' ";
		$query.= ($where) ? " WHERE ".$where : "";	
		$db->setQuery($query);
		return $db->query();		
	}
	
	public static function getMax($table, $where = null , $add = 0, $name = "sort_order"){
		$maxinc = ' MAX(`'.$name.'`) AS `maxplus`  ';
		$db = JFactory::getDBO();
		$query = "SELECT".$maxinc." FROM `".$table."`";
		$query.= ($where) ? " WHERE ".$where : "";
		
		$db->setQuery($query);
		$info = $db->loadObject();
		return $info->maxplus + (int) $add;
	}
	
	public static function getAI($table){
		$db = JFactory::getDBO();
		$aiQuery = "SHOW COLUMNS FROM `".trim($table)."`";
		$db->setQuery($aiQuery);
		$infos = $db->loadObjectList();
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
		return null;
	}
	
	
	public static function copyPlus($copyTable=null, $copyWhere, $orderWhere=null , $sortOrderName = "sort_order"){
		$id = MDB::copy($copyTable,$copyWhere);
		$maxPlus = MDB::getMax( $copyTable, $orderWhere,1, $sortOrderName);
		$ai = "`".MDB::getAI($copyTable)."`='".$id."'"; 
		MDB::setSortOrder($copyTable,$maxPlus,$ai,$sortOrderName);
		return $id;
	}
	
	public static function _($key=null , $value = null){
		if(is_array($key)){
			if(!$value || $value == MDB_NOT) $value = MDB_AND;
			$out = "";
			foreach($key as $k => $v){
				$out .= "`".$k."`='".$v."' ".$value." ";
			}
			$out.="STRIP";
			return str_replace(array("AND STRIP","OR STRIP"," STRIP"),"",$out);
		}else{
			return "`".$key."`='".$value."'";
		}
	}
	
	public static function update($table = null, $fields = null, $where = null, $additional = null){
		$db = JFactory::getDBO();
		$query = "UPDATE `".trim($table)."` SET";
		foreach($fields as $key=>$value){
			$query .= "\n `".trim($key)."` = '".$value."', ";
		}
		$query .= "#§?";
		$query = str_replace(array(", #§?","#§?")," ",$query);
		if($where){
			$query .= "\nWHERE ".$where." ";
		}
		$query .= $additional;
				
		$db->setQuery($query);
		return $db->query();
	}
	
	public static function insert($table = null, $fields = null, $additional = null){
		$db = JFactory::getDBO();
		$query = "INSERT INTO `".trim($table)."` ";
		$f = "("; $v = "(";
		foreach($fields as $key=>$value){
			$f .= "`".trim($key)."`, ";
			$v .= "'".$value."', ";
		}
		$query .= $f.") VALUES ".$v.")";
		$query = str_replace(", )",")",$query);
		$query .= " ".trim($additional);
		$db->setQuery($query);
		$db->query();
		return $db->insertid();
	}

	public static function get($table = null, $field = null, $where = null, $additional = null){
		$db = JFactory::getDBO();
		$query = "SELECT ";
		if(is_array($field)){
			$query .= "|";
			foreach($field as $f){
				$query .= ", `".trim($f)."`";
			}
			$query = str_replace(array("|,","|"),"",$query);
		}else{
			if($field && $field !="*"){
				$query .= "`".trim($field)."`";
			}else{
				$query .= "*";
			}
		}
		$query.= " FROM `".$table."`";
		$where = ($where) ? " WHERE ".trim($where) : " ";
		$query .= $where ." ".trim($additional);
		$db->setQuery($query);
		return $db->loadObjectList();		
	}
	

	public static function delete($table = null, $where = null, $whereValue = null){
		if(!$table || !$where) return null;
		
		$db = JFactory::getDBO();
		
		$whereQuery = "WHERE ";
		if($whereValue){
			$whereQuery .= "`".trim($where)."` = '".$whereValue."' ";
		}else{
			$whereQuery .= $where;
		}
		
		$query = "DELETE FROM `$table` ".$whereQuery;
		$db->setQuery($query);
		$db->query();
	}
	
	
	public static function count($table = null, $where = null , $additional = null){
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) AS `total` FROM `".$table."`";
		$where = $where ? "WHERE ".trim($where) : " ";
		$query .= $where . trim($additional);
		$db->setQuery($query);
		$count = $db->loadObject();
		return (int) $count->total;	
	}
	
	public static function refactorOrder($table = null, $where = null, $pk = null,  $start = 1 , $sortOrderName = "sort_order"){
		$ai = ($pk) ? $pk : MDB::getAI($table);
		$where = ($where) ? " WHERE ".trim($where)." " : "";
		$db = JFactory::getDBO();
		$query = "SELECT `".$ai."` FROM `".$table."` ".$where." ORDER BY `".$sortOrderName."`";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach($rows as $row){
			$query = "UPDATE `".$table."`"
				. "\n SET"
				. "\n `".$sortOrderName."` = '".$start++."' "
				. "WHERE `".$ai."` ='".$row->$ai."'";	
			$db->setQuery($query);
			$db->query();
		}		
	}
		
	public static function refactorFE($oldId,$newId){
		$db = JFactory::getDBO();
		$query = "SELECT `form`, `html` FROM #__m4j_formelements WHERE `eid` ='".$newId."' LIMIT 1";
		$db->setQuery($query);
		$value = $db->loadObject();
		if($value->form>=50 && $value->form<60) return true;

		
		$new = str_replace( "{".$oldId, "{".$newId,$value->html);
		$new = str_replace( "m4j-".$oldId, "m4j-".$newId,$new);
		
		$query = "UPDATE `#__m4j_formelements`"
				. "\n SET"
				. "\n `html` = '".dbEscape($new)."' "
				. "WHERE `eid` ='".$newId."'";	
		$db->setQuery($query);
		
		return $db->query();			
		
	}
	
	public static function slotChanged($eid,$newSlot){
		$db = JFactory::getDBO();
		$query = "SELECT `slot` FROM #__m4j_formelements WHERE `eid` ='".$eid."' LIMIT 1";
		$db->setQuery($query);
		$value = $db->loadObject();

		if($newSlot == $value->slot) return false;
		else return true;
		
	}
	
	public static function hasUserMail($fid){
		$db = JFactory::getDBO();
		$query = "SELECT `usermail` FROM #__m4j_formelements WHERE `fid` ='".$fid."' AND `usermail` = '1' AND `active` = '1' LIMIT 1";
		$db->setQuery($query);
		$value = $db->loadObject();
		if($value) return 1;
		else return 0;
	}
	
	public static function isUpload($eid){
		$db = JFactory::getDBO();
		$query = "SELECT `form` FROM #__m4j_formelements WHERE `eid` ='".(int) $eid."'  LIMIT 1";
		$db->setQuery($query);
		$value = $db->loadObject();
		return( $value->form == 40);
	}
	
	public static function copyApp($jid = null, $newJid =null){
		if(!$jid || !$newJid) return false;
		$db = JFactory::getDBO();
		$columnsQuery = "SHOW COLUMNS FROM `#__m4j_apps2jobs`";
		$db->setQuery($columnsQuery);
		$rows = $db->loadObjectList();
		
		$Field = null;
		$columns = array();
		foreach ($rows as $row){
			if(!$Field){
				if(isset($row->field)) $Field = "field";
				else if (isset($row->Field))$Field = "Field";
				else echo "Can not find Field Names. Please check your server / PHP settings and contact Mad4Media![function: MDB::copy()]<br/>";
			}
			array_push($columns, $row->$Field);
		}//EOF foreach rows
		
		$insertQuery = "INSERT INTO `#__m4j_apps2jobs` (`" . implode("`, `",$columns) ."`) VALUES \n";
		
		$select = "SELECT * FROM `#__m4j_apps2jobs` WHERE `jid` = '$jid'";
		$db->setQuery($select);
		$rows = $db->loadObjectList();
				
		$insertRows = array();
		foreach($rows as $row){
			$insert = array();
			foreach($columns as $column){
				
				if($column == "jid"){
					array_push($insert, $newJid);
				}else{
					array_push($insert, dbEscape($row->$column) );
				}
			}
			array_push($insertRows, "('". implode("', '", $insert) . "')" );
		}//EOF Foreach rows

		if(sizeof($insertRows)){
			$insertQuery .= implode(", \n", $insertRows) .";\n";
			$db->setQuery($insertQuery);
			return $db->query();
		}else return false;
	}//EOF copy App 
	
	
	public static function markCopied($table = null, $fields = null, $where = null, $noAddIfNull = 1){
		if(!$table || !$fields || !(is_string($fields) || is_array($fields)) ) return false;
		$queryFields = "*";
		if(is_string($fields)){
			$fields = array($fields);
		}
		$whereQuery = $where ? "WHERE " .$where : "";
		$fieldsQuery = "`" . implode('`, `',$fields) ."` ";
				
		$db = JFactory::getDBO();
		$query = "SELECT ".dbEscape($fieldsQuery) . "FROM `". dbEscape($table) . "` " . $whereQuery . " LIMIT 1";
		$db->setQuery($query);
		$row = $db->loadObject();
		$updateFields  = array();
		foreach ($fields as $key => & $field){
			if(isset($row->$field)) {
				if($row->$field || (! $row->$field && !$noAddIfNull) ){
					$updateFields[$field] = $row->$field . "_" .  M4J_LANG_COPY;
				}// EOF add copy mark
			}// EOF row is set
		}//EOF foreach
		
		if(sizeof($updateFields)){
			self::update($table,$updateFields,$where, " LIMIT 1");
		}
		
	}
	
	public static function batch($sql = null){
		if(!$sql) return false;
		$error = 0;
		$db = JFactory::getDbo();
		$queries = $db->splitSql($sql);
		foreach($queries as & $query){
			if(trim($query)){
				$db->setQuery(trim($query));
				$error += ($db->query() === false) ? 1:0;
			}
		}
		return $error ? false : true;		
	}
	
	
}//EOF class MDB

function MDB_($key = null,$value = null){return MDB::_($key,$value); }

 ?>