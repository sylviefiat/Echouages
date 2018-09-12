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

if( ! class_exists('MLayoutResponsive')){
	require_once  dirname(__FILE__) . '/layoutresponsive.php';
}

class MLayoutPositionObject {

	public function __construct(){

	}

	public function add($key= null, $value= null){
		if(!$key) return null;
		$this->$key = $value;
	}
}

class MLayout  {

	var $error = false;
	var $name = null;
	var $template = null;
	var $parameters = array();
	var $http = null;
	var $pos = array();
	var $slot = array();
	var $isHelp = false;
	var $fid = null;

	public function __construct($name = null){
		if(! is_dir(M4J_LAYOUT.$name)){
			$this->error = true;
			return null;
		}
		$this->setName($name);
		if( ! $this->setTemplate()) return null;
		if( ! $this->loadParameters()) return null;
		$this->http = M4J_HTTP_LAYOUT.$this->name."/";
		for($t=0 ; $t<100; $t++){
			$this->slot[$t]= "";
		}
	}

	public function getName (){
		return $this->name;
	}

	public function setName ($name = null){
		$this->name = $name;
	}

	public function setTemplate($tplName = "/template.php"){
		$this->template = M4J_LAYOUT.$this->name.$tplName;
		if(! is_file($this->template)){
			$this->error = true;
			return false;
		}else return true;
	}

	public function loadParameters(){
		$path = M4J_LAYOUT.$this->name."/parameters.php";
		if(! is_file($path)){
			$this->error = true;
			return false;
		}

		$lines = file($path);
		foreach ($lines as $line){
			$splitByEqual = explode("=",$line);
			if(sizeof($splitByEqual)==2){
				$value = trim($splitByEqual[1]);
				if(is_int($value)) $value = (int) $value;
				else if (is_float($value)) $value = (float) $value;
				$this->parameters[trim($splitByEqual[0])] = $value;
			}// EOF if
		}//EOF foreach
		return true;
	}

	public function getParameter($paramName){
		return $this->parameters[$paramName];
	}

	public function getHTTP(){
		return $this->http;
	}

	public function getIcon($evalActive = null){
		if(is_file(M4J_LAYOUT.$this->name."/layout-icon.png")){
			$id = "";
			$hidden="";
			if($evalActive == $this->name){
				$id = ' id="activeLayout" ';
				$hidden = '<input type="hidden" name="current_layout" value="'.$this->name.'" id="currentLayout"></input>'."\n";
			}
				
				
			$infoParameter = $this->getParameter("desc");
			$info = "";
			if($infoParameter){
				$info = ' info="'.constant($infoParameter).'" ';
			}
			return '<div class="m4jLayoutIcon" mleft="-10"'.$info.$id.' layoutname="'.$this->name.'"'.
				'><img src="'.M4J_HTTP_LAYOUT.$this->name.'/layout-icon.png" border="0"></img></div>'."\n".$hidden;
		}else return "No Icon";
	}//EOF getIcon

	public function setHelp($is = false){
		$this->isHelp = $is ? true : false;
	}

	public function addData($array=array()){
		$this->pos = $array;
	}

	public function getData(){
		return $this->pos;
	}

	public function getValue($pos=1,$name=null){
		if(!$name &&  ! isset($this->pos[$pos])) return null;
		if(!$name && isset($this->pos[$pos])) return $this->pos[$pos];
		if(isset($this->pos[$pos]->$name)){
			return $this->pos[$pos]->$name;
		}else return null;
	}

	public function clearSlot($slotNumber = null){
		if(! $slotNumber){
			$this->slot = array();
		}else{
			$this->slot[$slotNumber] = null;
		}
	}
	
	public function reset(){
		for($t=0 ; $t<100; $t++){
			$this->slot[$t]= "";
		}
	}

	public function feedSlot($number=1,$value=null){
		$this->slot[$number] .= $value;
	}

	public function wrap($slot=1){
		$info = $this->pos[$slot];
		$width="";
		if($info->left && $info->right){
			$width = $info->left + $info->right;
			$width += ($this->isHelp) ? 16 : 0;
		}
		$out = '';
		if($info->use_fieldset){
			
			$fieldStyle ="";
			if((int) $info->width != 0 || (int) $info->height != 0){
				$fieldStyle = ' style="';
				$fieldStyle .= ((int) $info->width != 0) ? 'width:'.$info->width.'px;': '' ;
				$fieldStyle .= ((int) $info->height != 0) ? 'height:'.$info->height.'px;': '' ;
				$fieldStyle .= '"';
			}
			
			$out.= '<fieldset'.$fieldStyle.'>'."\n";
			if($info->legend){
				$out.= '<legend>'.MReady::_($info->legend).'</legend>'."\n";
			}
		}
		$width = ($width) ? ' width="'.$width.'px" ' : '';
		$class = (defined("M4J_CLASS_FORM_TABLE")) ? ' class="'.M4J_CLASS_FORM_TABLE.'" ' : ' ';
		$out.= '<table'.$class.$width.' border="0"><tbody>'."\n";
		$out .= $this->slot[$slot];
		$out .= '</tbody></table>'."\n";

		if($info->use_fieldset){
			$out.= '</fieldset>'."\n";
		}
		return $out;
	}//EOF wrap

	public function addRow($slot=1, $left= null,$right=null,$required=0,$help=null,$align=0,$usermail = null,$eid = null ,$isHidden = 0){
		$heap = "";
		if(! array_key_exists($slot,$this->pos)) return null;
		
		$right = '<span class="rspInfo" id="rspInfo'.$eid.'"></span>' . $right;
		$info = $this->pos[$slot];
		$tooltip = '';	
		if($help && $this->isHelp) {
			MReady::change($help);
			$help = preg_replace("(\r\n|\n|\r)", "<br/>", $help);
			$ttStyle= $align == 1 ? 'style="margin-left: 5px;" ' : '';
			$tooltip = '<img '.$ttStyle.'class="m4jInfoImage" src="'.M4J_FRONTEND.'images/help'.M4J_HELP_ICON.'.png" border="0" alt="'.htmlentities($help, ENT_COMPAT, "UTF-8").'" data-eid="'.$eid.'"></img>'."\n";

		}
		$mark= ($required==1 || $usermail == 1) ?  ' <span class="m4j_required">*</span>'  : '';
		$elementID = $eid ? ' id="m4je-'.$eid.'" ' : "";	
		$questionClassEid = $eid ? ' m4jq-'.$eid : "";
		$hide = $isHidden ? ' style = "display:none;" ' : '';
		$right = str_replace("M4J_LANG_PLEASE_SELECT",M4J_LANG_PLEASE_SELECT,$right);
		
		switch ($align){
			
			//Question left - Field right
			default:
			case 0:
				$heap .= "<tr$elementID $hide>\n\t";
				$heap .= '<td width="'.$info->left.'px" align="left" valign="top" class="proformsQuestions'.$questionClassEid.'" >'.$left.$mark.'</td>'."\n\t";
				if($this->isHelp == 1){
					$heap .=  '<td width="16px" align="left" valign="top" class="proformsInfoImage">'.$tooltip.'</td>'."\n\t";
				}
				$heap .= '<td width="'.$info->right.'px;" align="left" valign="top" >'.$right;
				$heap .= '</td>'."\n".'</tr>'."\n";
				
				break;
				
			//Question top - Field below
			case 1:
				$colspan = ($this->isHelp == 1)?3:2;
				$heap .= "<tr$elementID $hide>\n\t";
				$heap .= '<td colspan="'.$colspan.'" align="left" valign="top">'."\n\t";
				$heap .= '<div style="width: 100%;text-align:left;"  class="proformsQuestions'.$questionClassEid.'" ><label>'.$left.$mark.$tooltip."</label></div>\n\t";
				$heap .= '<div style="width: 100%;text-align:left;">'.$right."</div>\n\t";
				$heap .= '</td>'."\n".'</tr>'."\n";
				
				break;
			
			//No - Question left - Field right
			case 2: 
				$heap .= "<tr$elementID $hide>\n\t";
				$heap .= '<td width="'.$info->left.'px" align="left" valign="top" class="proformsQuestions'.$questionClassEid.'" ></td>'."\n\t";
				if($this->isHelp == 1){
					$heap .=  '<td width="16px" align="left" valign="top" class="proformsInfoImage">'.$tooltip.'</td>'."\n\t";
				}
				$heap .= '<td width="'.$info->right.'px;" align="left" valign="top" ><label>'.$right . $mark;
				$heap .= '</label></td>'."\n".'</tr>'."\n";
				
				break;
				
			//No - Question left - Field over the whole range
			case 3:
				$colspan = ($this->isHelp == 1)?3:2;
				$heap .= "<tr$elementID $hide>\n\t";
				$heap .= '<td colspan="'.$colspan.'" align="left" valign="top">'."\n\t";
				$heap .= '<label style="width: 100%; text-align:left;">'.$right.$tooltip . $mark."</label>\n\t";
				$heap .= '</td>'."\n".'</tr>'."\n";
				
				
				break;
				
		}
		
		$this->feedSlot($slot,$heap);
	}//EOF addRow

	public function addHTMLRow($slot=1, $html,$eid = null){
		if(! array_key_exists($slot,$this->pos)) return null;
		$info = $this->pos[$slot];
		$colspan = ($this->isHelp == 1) ? 3:2;
		$elementID = $eid ? ' id="m4je-'.$eid.'" ' : "";	
		$heap =  "<tr$elementID>\n\t".'<td colspan="'.$colspan.'" valign="top" align="left" class="m4jHTMLRow">'.$html.'</td>'."\n".'</tr>'."\n";
		$this->feedSlot($slot,$heap);
	}//EOF addHTMLRow

	
	// Alias for addHTMLRow
	public function addDisplayOnlyRow($slot = 1, $html = null, $eid = null){
		$this->addHTMLRow($slot, $html,$eid);
	}
	
	
	public function setFid($fid =null){
		$this->fid = "pfmTemplate_" . $fid;
	}
	
	public function render($print=false){
		$positions = (int) $this->getParameter("positions");
		$slots = array();
		for( $t=1; $t< ($positions+1) ; $t++){
			$name = "slot".$t;
			$slots[$name] = $this->wrap($t);
		}
		$slots["fid"] = $this->fid;
		$rendered = MTemplater::get($this->template,$slots);		
		if($print){
			echo $rendered;
		}else{
			return $rendered;
		}
		return true;		
	}
	

}//EOF class MLayout

class MLayoutList {

	public static $list = array();
	
	
	public static function init(){
		// get the folder class
		jimport('joomla.filesystem.folder'); 
		$array = JFolder::folders(M4J_LAYOUT);
		foreach($array as $layout){
			self::$list[$layout] = new MLayout($layout);
		}
		ksort(self::$list);
	}//EOF init

	public static function getIcons($eval = "layout01"){
		$icons = "";
		$count = 0;
		foreach (self::$list as $layoutObject){
			$icons .= $layoutObject->getIcon($eval);
			if($count++ == 2){
				$count = 0;
				$icons .= '<div class="m4jCLR"></div>'."\n";
			}
		}
		return $icons;
	}

	/**
	 * 
	 * @param string $name
	 * @return	MLayout
	 */
	public static function get($name="layout01"){		
		if(array_key_exists($name,self::$list)){
			return clone self::$list[$name];
		}else return null;
	}

	public static function getResponsive($info){
		if(isset($info->responsive_data) && ! empty( $info->responsive_data )  ){
			
			$fid = (int) $info->fid;
			$useHelp = (bool) $info->use_help;
			$data = unserialize( bDec($info->responsive_data) ) ;
			return new MLayoutResponsive($fid, $useHelp, $data);
			
		}else return null;
	}
	
	
	/**
	 * 
	 * @param int $id
	 * @return NULL|MLayout <NULL, MLayout>
	 */
	public static function getLayoutById($id=null){
		$id = (int) $id;
		if(!$id) return null;
		$database = JFactory::getDbo();

		
		$query = "SELECT `fid`, `layout`, `layout_data`, `use_help`, `responsive`, `responsive_data` FROM `#__m4j_forms` WHERE `fid` = '". $id . "' LIMIT 1";
		$database->setQuery( $query );
		$info = $database->loadObject();
		if(! $info) return null;

		
		if(! ( isset($info->responsive) && $info->responsive ) ){			
			$layout = MLayoutList::get($info->layout);
			$layout->addData(MLayoutList::makeData($info->layout_data));
			$layout->setFid($id);
			$layout->setHelp($info->use_help);
		}else{
			
			$layout = MLayoutList::getResponsive($info);
		}
		
		
		return $layout;
	}

	public static function makeData($string=null){
		if(!$string) return null;
		$splitPosition = explode("|",$string);
		$posArray = array();
		$count = 1;
		foreach($splitPosition as $position){
			if(!$position) continue;
			$attributes = explode(";",$position);
			$obj = new MLayoutPositionObject();
			foreach($attributes as $attribute){
				$element = explode("=",$attribute);
				if(sizeof($element)==2){
					$obj->add($element[0],$element[1]);
				}//EOF is element
			}// EOF foreach attributes
			$posArray[$count++] = $obj;
		}//EOF foreach positions
		return $posArray;
	}


}//EOF class MLayoutList

// initialize the Layout List
MLayoutList::init();

?>