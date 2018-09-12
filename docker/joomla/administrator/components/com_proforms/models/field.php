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

/**
 * FIELD MODEL
 */


defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once JPATH_ROOT . '/components/com_proforms/formlib/init.php';

class ProformsAdminModelField extends ProformsAdminModel{
	

	/**
	 *
	 * @var string
	 */
	protected $serString = 'PFMSER::';

	/**
	 *
	 * @var array
	 */
	protected $encodedParams = array('sql', 'pleaseSelectOption');
	
	public $fid = null;
	public $slot = null;
	public $responsive_slot = null;
	public $eid = null;
	public $formNumber = 10;
	
	protected $edit = false;
	
	public $data = null;
	
	public $options = array();
	public $option_values = array();
	
	public $layoutData = null;
	
	public $ini = null;
	public $includePath = null;
	public $includePath2 = null;
	
	public $returnURL = null;
	
	public $isResponsive = false;
	
	public $usermailField = null;
	public $isUsermail = false;
	
	public $send = false;
	
	public function isEdit(){
		return (bool) $this->edit;
	}
	
	protected function _init(){
		$this->includePath =  M4J_TEMPLATES .'fieldsections/interface.php';
		$this->fid = (int) JRequest::getInt("fid", 0);
		$this->eid = (int) JRequest::getInt("eid", 0);
		$this->slot = JRequest::getInt("slot", null);
		$this->responsive_slot = (int) JRequest::getInt("responsive_slot", 0);
		$this->formNumber = (int) JRequest::getInt("form", 10);

		$this->send = (bool) JRequest::getInt("send", 0);
		
		
		if($this->eid > 0){
			$this->_getElementData();
			$this->returnURL = M4J_FIELD . '&slot=' . $this->slot . '&eid='. $this->eid . '&fid='.$this->fid . M4J_HIDE_BAR;
		}else{
			$this->_emptyData();
		}
		
		if($this->fid){
			$this->layoutData = $this->singleSelect('#__m4j_forms', null, $this->where("fid", $this->fid));
			if($this->layoutData){
				if( isset($this->layoutData->responsive_data) && $this->layoutData->responsive_data){
					$this->layoutData->responsive_data = unserialize( bDec($this->layoutData->responsive_data));
				}
				
				if( isset($this->layoutData->layout_data) && $this->layoutData->layout_data){
					$this->layoutData->layout_data = $this->_makeOldLayoutData($this->layoutData->layout_data);
				}
				
				$this->isResponsive = $this->layoutData->responsive ? true : false;
				
				
				
				$usermailData = $this->singleSelect('#__m4j_formelements', "eid", $this->where( array( "fid" => $this->fid, "usermail" => 1) ) );
				$this->usermailField = $usermailData ? (int) $usermailData->eid : null;
				
				$this->isUsermail = ((int) $this->usermailField === (int) $this->eid);
				
				
			}//EOF is layout data
		}//EOF is fid
		
		$this->_getIni();
		
	}
	
	public function debug(){
		MDebug::pre("Usermail Field: " .$this->usermailField);
		MDebug::pre($this->ini);
		MDebug::pre($this->data);
		MDebug::pre($this->options);
		MDebug::pre($this->option_values);
		MDebug::pre($this->layoutData);
	}
	
	protected function _getElementData(){
		$this->data = $this->singleSelect('#__m4j_formelements', null, $this->where("eid", $this->eid));
		if($this->data){
			$this->edit = true;
			$this->formNumber = (int) $this->data->form;
			
			// Parameter parsing
			if(isset($this->data->parameters)){
				$parametersRaw = $this->data->parameters;
					
				$isSerialized = substr($parametersRaw, 0, strlen($this->serString)) === $this->serString;
				$parameters = null;
				if($isSerialized){
					try {
						$parameters = unserialize(bDec(str_replace($this->serString, '', $parametersRaw)));
					} catch (Exception $e) {
						MDebug::_('Error while unserializing parameters of eid: '. $this->eid . ' - formNumber: ' . $this->form . ' question: ' . $this->question  );
						MDebug::_("Exception: ");
						MDebug::pre($e);
					}
				}else{
					$parameters = $this->_makeParams($parametersRaw);
				}
					
				foreach($parameters as $key => & $value){
					if(!$isSerialized && in_array($key, $this->encodedParams)){
						$value = bDec($value);
					}
			
				}//EOF foreach
					
				$this->data->parameters = $parameters;
			
			}//EOF isset parameters
			
			//Option parsing
			if(isset($this->data->options)){
				$options = explode("\n", $this->data->options);
				if(sizeof($options) == 1){
					$this->data->parameters->use_values = 0;
					$this->options = explode(";", $options[0]);
				}elseif(sizeof($options) == 2){
					$this->options = explode(";", $options[0]);
					$vals = explode(";", $options[1]);
					$isEmpty = 1;
					foreach($vals as &$val){
						if($val){
							$isEmpty = 0;
							break;
						}
					}//EOF foreach
						
					if($isEmpty){
						$this->data->parameters->use_values = 0;
					}else{
						$this->option_values = $vals;
					}
				}//EOF elseif
			}// EOF if optionsd
		
		}
	}
	
	/**
	 * The old way for creating the parameters
	 * @param string $string
	 * @return	stdClass
	 */
	protected function _makeParams($string){
		$std = new stdClass();
		$chopped = explode(';',$string);
		foreach($chopped as $atom){
			$pos = strpos($atom, "=");
			if($pos !== false){
				$key = trim( substr($atom, 0, $pos) );
				$value = substr($atom, ($pos+1), (strlen($atom) - $pos) );
				$std->$key = trim($value);
			}
		}
		return $std;
	}
	
	protected function _makeOldLayoutData($string=null){
		if(!$string) return null;
		$splitPosition = explode("|",$string);
		$posArray = array();
		$count = 1;
		foreach($splitPosition as $position){
			if(!$position) continue;
			$attributes = explode(";",$position);
			$obj = new stdClass();
			foreach($attributes as $attribute){
				$element = explode("=",$attribute);
				if(sizeof($element)==2){
					$obj->$element[0] = $element[1];
				}//EOF is element
			}// EOF foreach attributes
			$posArray[$count++] = $obj;
		}//EOF foreach positions
		return $posArray;
	}
	
	
	protected function _emptyData(){
		$data = array(
			"eid" => $this->eid,
		    "fid" => $this->fid,
		    "required" => 0,
		    "active" => 1,
		    "usermail" => 0,
		    "align" => 0,
		    "question" => '',
		    "form" => $this->formNumber,
		    "parameters" => array
		        (    
		            "endings" => null,
                    "maxsize" => null,
                    "measure" => 1024,
		            "maxchars" => null,
		            "eval" => null,
		            "hidden_value" => null,
		            "ismaxcharstextarea" => 0,
		            "checked" => 0,
		            "element_rows" => 3,
		            "width" => '100%',
		            "alignment" => 0,
		            "use_values" => 0,
		            "pleaseSelectOption" => null,
		            "options_data_type" => 0,
		            "sql" => null
		        ),
		
		    "options" => null,
		    "help" => '',
		    "html" => null,
		    "slot" => $this->slot,
		    "sort_order" => null,
		    "alias" => '',
		    "responsive_slot" => null 
		);
		
		settype($data["parameters"], 'object');
		settype($data, "object");
		$this->data = $data;		
	}
	
	protected function _getIni(){
		if($this->formNumber){
			$path = MFormFactory::getFolder($this->formNumber);
			
			$includePath = $path . 'admin/interface.php';
			$this->includePath = file_exists($includePath) ? $includePath : M4J_TEMPLATES .'fieldsections/interface.php';
			
			$includePath2 = $path . 'admin/interface2.php';
			$this->includePath2 = file_exists($includePath2) ? $includePath2 : null;
			
			if(file_exists($path . 'admin/info.ini')){
				$this->ini = parse_ini_file($path . 'admin/info.ini', TRUE);
				$this->ini = json_decode(json_encode($this->ini), FALSE);
				if(isset($this->ini->convert) && $this->ini->convert){
					$this->ini->convert = explode(",", $this->ini->convert);
				}else{
					$this->ini->convert = false;
				}
			}else{
				$this->_defaultIni();
			}
		}else $this->_defaultIni();
	}
	
	public function getIni($key = null, $default = null){
		$key = explode(".", $key);
		$focus = & $this->ini;
		foreach($key as $k){
			if( isset($focus->$k) ){
				$focus = &$focus->$k;  
			}else return $default;

		}
		return $focus;
	}
	
	public function getParam($key = null, $default = null){
	    return isset($this->data->parameters->$key) ? $this->data->parameters->$key : $default;
	}
	
	public function setParam($key = null, $value = null){
	    if(!$key ) return;
	    $this->data->parameters->$key = $value;
	}
	
	public function getData($key = null, $default = null){
		return isset($this->data->$key) ? $this->data->$key : $default;
	}
	
	public function setData($key = null , $value = null){
		if(!$key ) return;
		$this->data->$key = $value;
	}
	
	
	protected function _defaultIni(){
		$ini = parse_ini_string("
						convert=
						displayonly=0
						[interface]
						head=1
						width=1", TRUE);

		$this->ini = json_decode(json_encode($ini), FALSE);
	}
	
	public function convert(){
		if($this->isEdit() && $this->eid){
			$to = (int) JRequest::getInt("to",null);
			if($to && in_array($to, $this->ini->convert)){
				return $this->update('#__m4j_formelements', array("form" => $to), $this->where("eid",$this->eid), "LIMIT 1");
			}
			return false;
			
		}else return false;
	}
	
	
	protected function int2Data($key = null, $default= null){
		if(!$key) return;
		$this->data->$key = $this->dbEscape( JRequest::getInt($key, $default) );
	}
	
	protected function string2Data($key = null, $default = null){
		if(!$key) return;
		$this->data->$key = $this->dbEscape( JRequest::getString($key, $default) );
	}
	
	protected function html2Data($key = null, $default = null){
		if(!$key) return;
		$this->data->$key = $this->dbEscape(  JRequest::getString($key,$default, 'default', JREQUEST_ALLOWHTML) );
	}
	
	
	protected function int2Param($key = null, $default= null){
		if(!$key) return;
		$this->data->parameters->$key = JRequest::getInt($key, $default);
	}
	
	protected function string2Param($key = null, $default = null){
		if(!$key) return;
		$this->data->parameters->$key = JRequest::getString($key, $default);
	}
	
	protected function html2Param($key = null, $default = null){
		if(!$key) return;
		$this->data->parameters->$key = JRequest::getString($key,$default, 'default', JREQUEST_ALLOWHTML);
	}
	

	/**
	 * This is a recursive filter for the request
	 * @param any $value	The value
	 * @param	string	$type	The type of filtering | default is string
	 * @param	JFilterInput	$filter	An instance of the input filter object
	 */
	protected function _recursiveOptionsFilter(& $value = null, $type = 'string', JFilterInput & $filter = null){
			
		if(!$filter){
			// The most strict filter
			$filter = JFilterInput::getInstance();
		}
	
		if(! is_array($value)){
			// 			$value =  str_replace(array('"','\x22', '&quote;', '&#34;', '%22'), '“', $filter->clean($value, $type) );
			$value =  $filter->clean($value, $type);
			$value = str_replace('"', '“', str_replace(";", ",", trim($value) ) );
			return;
		}
	
		foreach ($value as & $val){
			$this->_recursiveOptionsFilter($val, $type, $filter);
		}
	}
	
	public function request(){
		if( ! $this->send) return false;
		
		
		$this->data->fid = (int) $this->fid;
		$this->data->slot = (int) $this->slot;
		$this->data->responsive_slot = (int) $this->responsive_slot;
		
		if($this->getIni("interface.head")){
			$this->string2Data("question");
			$this->string2Data("alias");
			$this->string2Data("help");
			$this->int2Data("active",0);
			$this->int2Data("required",0);
			$this->int2Data("align");
		}
		if($this->getIni("interface.width")){
			$this->string2Param("width");
		}
		
		if($this->getIni("interface.options")){
			$this->int2Param("options_data_type",0);
			$this->int2Param("use_values",0);
			$this->string2Param("sql", null);
			
			if(isset($_REQUEST["options"]) ){
				$this->_recursiveOptionsFilter($_REQUEST["options"]);
				$options = $_REQUEST["options"];
			}
			
			if(isset($_REQUEST["values"])){
				$this->_recursiveOptionsFilter($_REQUEST["values"]);
				$values = $_REQUEST["values"];
			}
			
			if($this->data->parameters->use_values){
				if(sizeof($options) == sizeof($values)){
					$this->data->options = $this->dbEscape( implode(";", $options) ). "\n" .$this->dbEscape(  implode(";", $values) );
				}else{
					$this->data->options = $this->dbEscape( implode(";", $options) ). "\n" .$this->dbEscape(  str_repeat(";", sizeof($options)) );
				}
			}else{
					$this->data->options = $this->dbEscape( implode(";", $options) ). "\n" . $this->dbEscape( str_repeat(";", sizeof($options)) );
			}	
		}
		
		$dataRequests = $this->getIni("data");
		if($dataRequests){
			foreach($dataRequests as $key => $type){
				switch($type){
					case "int":
						$this->int2Data($key);
						break;	
					case "html":
						$this->html2Data($key);
						break;
					default:
					case "string":
						$this->string2Data($key);
						break;
				}
			}
		}
			
		$parameterRequests = $this->getIni("parameters");
		if($parameterRequests){
			foreach($parameterRequests as $key => $type){
				
				if($key == "please_select_option"){
					$this->data->parameters->pleaseSelectOption = JRequest::getString("please_select_option", null);
					continue;
				}
				
				switch($type){
					case "int":
						$this->int2Param($key);
						break;
					case "html":
						$this->html2Param($key);
						break;
					default:
					case "string":
						$this->string2Param($key);
						break;
				}
			}
		}
		
		
		$this->save();
		
		return true;
		
	}
	
	protected function save(){
		unset($this->data->eid);
		
		$this->data->parameters = $this->dbEscape( $this->serString . bEnc(serialize($this->data->parameters)) );
		
		if($this->isEdit()){
			$this->update('#__m4j_formelements', $this->data, $this->where("eid", $this->eid), "LIMIT 1");
		}else{
			$this->data->sort_order = $this->getMax('#__m4j_formelements', $this->where( array( "fid" => $this->fid, "slot" => $this->slot) ),1);
			$this->insert('#__m4j_formelements', $this->data);
		}
	}
	
	
	
}
