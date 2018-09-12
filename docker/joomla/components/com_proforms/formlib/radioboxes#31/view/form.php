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

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class MFormRadioboxes extends MFormElement {

	protected $options_data_type = 0;
	protected $isSelection = true;
	
	/**
	 * @param unknown_type $dataObject
	 */
	protected function _init(&$dataObject) {
		$this->_add2HigerLevel(array('options_data_type'));
	}

	protected function _prepareData(){
		if ($this->options_data_type && !empty($this->sql)) {

			$this->options = array();
			$this->option_values = array();
			$this->use_values = 1;
			foreach ($this->sqlResults as &$row) {
				$text = isset($row->text) ? str_replace('"', '“',
								strip_tags(trim($row->text))) : null;
				$value = isset($row->value) ? str_replace('"', '“',
								strip_tags(trim($row->value))) : null;
				$value = (!$value && $text) ? $text : $value;
				if ($text && $value) {
					array_push($this->options, $text);
					array_push($this->option_values, $value);
				}
			}//EOF foreach
		}//EOF is sql
		
	}//EOF _prepareData()
	
	protected function _addErrorBorder(){
		$this->addStyle('outline', ( defined('M4J_ERROR_COLOR') ? '1px solid #' . M4J_ERROR_COLOR : '1px solid red'  ));
	
	}
	
	/**
	 * 
	 */
	protected function _renderDefault() {
		$option = $this->options;
		$value = $this->use_values ? $this->option_values : $option;
		$count = sizeof($option);

		settype($this->value, 'string');
		$out = '';

		$this->_getWidth(true);

		$eval = ($this->required != 0) ? 'lang="1000" ' : 'lang="0" ';

		if($this->params->alignment){
			$out .= '<div '.$eval.'class="m4jRadioWrap '.$this->_getClass().'">'."\n".'<table ' . $this->_getStyle() . '>'."\n".'<tbody>'."\n";
			for($t=0;$t<$count;$t++){
				if(! trim($option[$t]) && ! trim($value[$t]) ) continue;
				$isSelected = $this->is_selected($value[$t]);
				$out .= '<tr>'."\n".'<td align="left" valign="top">'."\n".
				'<div class="m4jSelectItem m4jSelectItemVertical">'."\n".
				'<label>' . "\n" .
				'<input class="m4jRadio" type="radio" id="m4j-'.$this->eid.'-'.$t.'" name="m4j-'.$this->eid.'" value="'.htmlentities($value[$t], ENT_COMPAT, "UTF-8").'" '.$isSelected.'></input>'.$option[$t]."\n".
				'</label>' . "\n" .
				'</div>'."\n".
				'</td>'."\n".'</tr>'."\n";
			}
			$out .='</tbody>'."\n".'</table>'."\n".'</div>'."\n";
		}else{
			$out .= '<div '.$eval.'class="m4jRadioWrap '.$this->_getClass().'">'."\n".'<div ' . $this->_getStyle() . '>'."\n";
			for($t=0;$t<$count;$t++){
				if(! trim($option[$t]) && ! trim($value[$t]) ) continue;
				$isSelected = $this->is_selected($value[$t]);
				$out .= '<div class="m4jSelectItem">'."\n".
				'<label>' . "\n" .
				'<input class="m4jRadio" type="radio" id="m4j-'.$this->eid.'-'.$t.'" name="m4j-'.$this->eid.'" value="'.htmlentities($value[$t], ENT_COMPAT, "UTF-8").'" '.$isSelected.'></input>'.$option[$t]."\n".
				'</label>' . "\n" .
				'</div>'."\n";
			}
			$out .= '</div>'."\n".'</div>'."\n";
		}
							
		
		return $out;
	}

	/**
	 * 
	 */
	protected function _renderResponsive() {		
		$option = $this->options;
		$value = $this->use_values ? $this->option_values : $option;
		$count = sizeof($option);
		
		$this->_getWidth(true);

		$eval = ($this->required != 0) ? 'lang="1000" ' : 'lang="0" ';

		$alignment = ($this->params->alignment) ? ' pfmSelBreak' : '';
		
		$out = '<div ' . $eval . 'class="m4jRadioWrap"' . $this->_getStyle() . '>' . "\n";
		for ($t = 0; $t < $count; $t++) {
			if (!trim($option[$t]) && !trim($value[$t]))
				continue;
			$isSelected = $this->is_selected( $value[$t]);
			$out .= 
					 '<label class="m4jSelectItem pfmSelectItem'.$alignment.'">' . "\n" 
					. '<input class="m4jRadio" type="radio" id="m4j-'.$this->eid.'-'.$t.'" name="m4j-'.$this->eid.'" value="'.htmlentities($value[$t], ENT_COMPAT, "UTF-8").'" '
					.$isSelected.'></input>'.$option[$t]."\n"				
					. '</label>' . "\n" ;
		}
		$out .= '</div>' . "\n";
		
		
		return $out;

	}

	protected function is_selected($option) {
		return $this->value == $option ?  'checked="checked"' : '';
		
	}

}//EOF class
