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

class MFormList extends MFormElement {

	protected $options_data_type = 0;
	
	protected $isSelection = true;
	
	/**
	 * @param unknown_type $dataObject
	 */
	protected function _init(&$dataObject) {
		$this->_add2HigerLevel(array('options_data_type'));
	}

	protected function _prepareData() {
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

		$out .= '<select '.$eval.'class="m4jSelection '.$this->_getClass().'" id="m4j-'.$this->eid.'" name="m4j-'.$this->eid.'" size="'.$this->params->element_rows.'" ' . $this->_getStyle() . '>'."\n";
		for($t=0;$t<$count;$t++){
			if(! trim($option[$t]) && ! trim($value[$t]) ) continue;
			$isSelected = $this->is_selected($value[$t]);
			$out .='<option value="'.$value[$t].'" '.$isSelected.'>'.$option[$t].'</option>'."\n";
		}
		$out .='</select>'."\n";
		
		return $out;
	}

	/**
	 * 
	 */
	protected function _renderResponsive() {
		return $this->_renderDefault();

	}

	protected function is_selected($option) {
		return $this->value == $option ?  'selected="selected"' : '';
		
	}

}//EOF class
