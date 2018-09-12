<?php
/*
* @version		0.4.4
* @package		jForms
* @subpackage	fitofo ("fields to form" ugly name! :-) )
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		GNU GPL v3 or later
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.log.log');

class fitofo {

	var $_logErrors = array();
	var $_logInfo = array();
	var $_fields;
	var $_fieldsById;
	var $_fieldsets;
	var $_allFields;
	var $_ml_fields;
	var $_htmlSanitize;
	var $_boolean;
	var $_fieldsAttributes;
	var $_fsetTag;
	var $_fiTag;
	var $_jForm;
	var $_mainForm;
	
	
   function __construct($fields = null, $jForm = null, $fieldsById = null){
		$config = JComponentHelper::getParams('com_jforms');
		$this->writeFullXmlInLog = $config->get('writeFullXmlInLog',false);
		$emptyLogOnScriptExecution = $config->get('emptyXmlLogOnScriptExecution',true);
		
		static $cleaned;
		if($emptyLogOnScriptExecution){
			$logFile = JFactory::getConfig()->get('log_path') . DS . 'com_jforms.fitofo.errors.php';
			if(empty($cleaned) AND is_file($logFile)){
				unlink($logFile);
			}
			$cleaned = true;
		}
		
		JLog::addLogger(
			 array(
					// Sets file name
					'text_file' => 'com_jforms.fitofo.errors.php'
			 ),
			 // Sets messages of all log levels to be sent to the file
			 JLog::ALL,
			 // The log category/categories which should be recorded in this file
			 // In this case, it's just the one category from our extension, still
			 // we need to put it inside an array
			 array('com_jforms.fitofo')
		);	
	
		if(empty($jForm)) return false;
		
		if(!empty($fields)){
			$this->_fields = $fields;
		}
		
		if(!empty($jForm->id)){
			$this->_jForm = $jForm;
			$this->_fieldsets = ByGiroHelper::groupArrayByValue($jForm->fieldsets, 'id', false);
			$this->_fieldsById = $this->_jForm->fields;
		} else {
			$this->_fieldsets = $jForm;
			$this->_fieldsById = $fieldsById;		
		}
				
		$this->_fsetTag = 'fieldset';
		$this->_fiTag = 'field';
		
		// multilanguage attributes
		$this->_ml_fields = array('label','description','placeholder','afterInput','spacer_content');
		
		// HTML sanitize fields
		$this->_htmlSanitize = array('label','description','default','spacer_content','placeholder','afterInput','selectors','specialDates','message');
		
		// boolean attributes
		$this->_boolean = array(
			'required','printable','readonly','disabled','distanceWidget','multiple',
			'autoclose','showDaysNotInMonth','showDisabledTimes','indirect','preview',
			'hide_none','hide_default','stripext','ui_allowCustomValues','ui_hideSelect',
			'active','animated','translate','selectable','repeatYear','repeatMonth','repeatWeek');
		
		$this->_fieldsAttributes = array(
			"ckaddresspicker" => array(
				'mode_ckaddresspicker',
				'distanceWidget',
				'distanceWidgetRadius',
				'mapType',
				'zoom',
				'latitude',
				'longitude',
				'markerType',
				'cssSelectorTarget',
				'targets_prefix'
			),
			"ckcalendar" => array(
				'format'
			),
			"ckcaptcha" => array(
				'pubkey',
				'privkey',
				'theme'
			),
			"ckcheckbox" => array(
				'value'
			),
			"ckcheckboxes" => array(
				'cols',
				'layout',
				'options'
			),
			"ckcolor" => array(
				'layout_ckcolor'
			),
			"ckcontentlanguage" => array(
				'multiple'
			),
			"ckdatetimepicker" => array(
				'autoclose',
				'calendars',
				'firstDay',
				'format',
				'uiFormat',
				'minuteStep',
				'minView',
				'maxView',
				'mode_ckdatetimepicker',
				'separator',
				'showDaysNotInMonth',
				'showDisabledTimes',
				'startView'
			),
			"ckeditor" => array(
				'width',
				'height',
			),
			"ckfile" => array(
				'allowedExtensions',
				'rename',
				'root',
				'indirect',
				'preview',
				'target',
				'thumbnails',
				'width',
				'height',
				'actions',
				'maxSize',
			),
			"ckfilelist" => array(
				'directory',
				'exclude',
				'fileFilter',
				'hide_none',
				'hide_default',
				'stripext'
			),
			"ckhidden" => array(),
			"cklist" => array(
				'multiple',
				'nullLabel',
				'size',
				'ui',
				'ui_direction',
				'ui_allowCustomValues',
				'ui_hideSelect',
				'options'
			),
			"ckmedia" => array(
				'directory',
				'preview',
				'width',
				'height',
				'target'
			),
			"ckmeter" => array(
				'active',
				'animated',
				'color',
				'value'
			),
			"cknumber" => array(
				'min',
				'max',
				'step'
			),
			"ckradio" => array(
				'options'
			),
			"ckrange" => array(
				'min',
				'max',
				'step'			
			),
			"ckrepeatable" => array(
				'limit',
				'repeatable_fields'
			),
			"ckspacer" => array(
				'spacer_content'
			),
			"ckstate" => array(
				'display'
			),
			"cksql" => array(
				'query',
				'key_field',
				'value_field',
				'translate'
			),
			"cktags" => array(
				'min',
				'max',
				'separator_cktags'
			),
			"cktext" => array(),
			"cktextarea" => array(
				'width',
				'height',
			),
			"cktimepicker" => array(
				'start',
				'end',
				'minuteStep',
				'time',
				'format',
				'uiFormat',
			),
			"ckuser" => array(),
			"ckusergrouplist" => array(
				'multiple',
				'size'
			)
		);
	}
	
	function setMainForm($form){
		if($form instanceof JForm) $this->_mainForm = $form;
	}
	
	function getForm($repeatable = false, $justXML = false){		
		if($repeatable){
			$this->_fsetTag = 'fset';
			$this->_fiTag = 'fi';
		}
				
		$result = '';
	
		$fields = is_array($this->_fields) ? $this->_fields : array();
		// convert fields to XML string
		foreach($fields as $fset_k => $fields){
			$form = false;
			$xml = array();
			if(empty($this->_fieldsets[$fset_k])) continue;
			
			$fset = clone $this->_fieldsets[$fset_k];
			
			if(empty($fset) OR !$fset->enabled) continue;
			$fset = $this->multilanguage($fset,$this->_ml_fields);
			
			// fieldsets attributes
			$xml[] = PHP_EOL ."\t<". $this->_fsetTag ;
			
			$festName = $fset->name;
			if(!empty($fset->form) AND $fset->form instanceof JForm AND $fieldsets = $fset->form->getFieldsets() AND !empty($fieldsets)){
				$form = $fset->form;
				$firstFset = array_values($fieldsets);
				$firstFset = $firstFset[0];
				foreach($firstFset as $k => $v){
					$xml[] = "\t\t". $k .'="'. $v .'"';
				}
				$xml[] = '>'. PHP_EOL;
			} else {
				$xml[] = "\t\t".'name="'. htmlspecialchars($fset->name, ENT_COMPAT, 'UTF-8') .'"'. PHP_EOL;
				@$xml[] = "\t\t".'description="'. htmlspecialchars($fset->description, ENT_COMPAT, 'UTF-8') .'"'. PHP_EOL;
				@$xml[] = "\t\t".'class="'. htmlspecialchars($fset->class, ENT_COMPAT, 'UTF-8') .'">'. PHP_EOL;
			}
			
			foreach($fields as $fi){
				if(empty($fi->type)) continue;
				$xml[] = $this->fiToXml($fi);
			}

			$xml[] = PHP_EOL ."\t</". $this->_fsetTag .">". PHP_EOL;
			
			if($justXML){
				$result .= PHP_EOL . implode(PHP_EOL,$xml) . PHP_EOL;
			} else {
				$xml = '<?xml version="1.0" encoding="utf-8"?>'. PHP_EOL ."<form>". PHP_EOL . implode(PHP_EOL,$xml) . PHP_EOL .'</form>';

				// test the XML
				$msg = JText::sprintf('JFORMS_ERROR_INCORRECT_XML_FORM', $this->_jForm->name .' - ID: '. $this->_jForm->id, $fset->name .' - ID:'. $fset->id);
				
				if(!ByGiroHelper::testXML($xml, $msg, $this->writeFullXmlInLog)) continue;
				
				$this->writeXML($xml);
				
				// create / merge a form 
				if(!$form){	
					$form = JForm::getInstance('com_jforms.'. $this->_jForm->id .'fset'. $fset_k, $xml,array('control'=>'jform'));	
					$form->addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields');
					$form->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules');

				} else {
					$form->load(simplexml_load_string($xml), true);			
				}

				if($this->_mainForm instanceof JForm){
					$this->_mainForm->load(simplexml_load_string($xml), true);
				} else {
					$this->_mainForm = JForm::getInstance('com_jforms.main', $xml,array('control'=>'jform'));	
					$this->_mainForm->addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields');
					$this->_mainForm->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules');			
				}
				if($form instanceof JForm AND !empty($this->_jForm->id)){
					$this->_fieldsets[$fset_k]->form = $form;
					$this->_fieldsets[$fset_k]->form_file_content = $xml;
				}
			}
		}

		if(!$justXML AND !empty($this->_jForm->id)){
			// restore the regular order
			$this->_jForm->fieldsets = ByGiroHelper::sort_on_field($this->_fieldsets, 'ordering', 'ASC');
			$result = $this->_jForm;
		}
		return $result;
	}
	
	public function fiToXml($f){
		$xml = array(
			'class' => array(),
			'containerClass' => array()
		);
		$validate_js = array();
		$fieldContent = array();
		
		$xml['alias'] = $xml['name'] = $f->name;
		
		// multilanguage attributes
		$f = $this->multilanguage($f,$this->_ml_fields);
				
		// common attributes
		$common = array('type','label','description','placeholder','afterinput','default','class','containerClass','style','required','filter','validate','validate_js','condRules','priceRules','printable','readonly','disabled','selectors','canView','canEdit','newRequired','editRequired');
		foreach($common as $co){
			$val = !empty($f->$co) ? $f->$co : null;
			
			if(empty($val)) continue;
			if(in_array($co,$this->_boolean)) $val = 'true';
			
			$skip = false;
			switch($co){
				case 'selectors':
					if(is_string($val)){
						try {
							$val = json_decode($val);
						} catch(Exception $e){
							$val = array();
						}
					}
					if(!is_array($val)) continue;
					$newVal = array();
					unset($v);
					foreach($val as $v){
						$newVal[] = $v->attribute .'='. $v->value;
					}
					
					$val = implode('|',$newVal);
					break;
					
				case 'required':
					$validate_js[] = 'required';
					break;
					
				case 'canView':
				case 'canEdit':
				case 'newRequired':
				case 'editRequired':
					$val = implode(',',$val);
					break;
				
				case 'priceRules':					
				case 'condRules':
					$createFunction = 'createRule';
					if($co == 'priceRules'){
						$createFunction = 'createPriceRule';
					}
				
					$skip = true;
					// create conRules
					if(is_string($val)){
						try {
							$val = json_decode($val);
						} catch(Exception $e){
							$val = array();
						}
					}
					if(!is_array($val)) continue;
				
					$rules = array();
					unset($v);
					foreach($val as $v){
						if($rule = $this->$createFunction($v)){
							$rules[] = $rule;
						}
					}

					if(!empty($rules)){
						$xml['class'][] = implode(' ',$rules);					
					}
					break;
					
				case 'validate_js':
					$skip = true;					
					
					if($val == 'regexp'){
						if(!empty($f->regexp)){
							$xml['regexp'] = $f->regexp;
							$xml['regexpModifiers'] = $f->regexpModifiers;
							if(!empty($f->regexpInvert)){
								$xml['regexpInvert'] = 'true';
							}
						} else {
							break;
						}
					}
					
					$validate_js[] = 'custom['. $val .']';
					break;
					
				default:
					break;
			}
			
			// sanitize
			if(in_array($co,$this->_htmlSanitize)){
				$val = htmlspecialchars($val, ENT_COMPAT, 'UTF-8');
			}
			
			if(!$skip){
				if(isset($xml[$co]) AND is_array($xml[$co])){
					if(is_array($val)){
						$xml[$co] = array_merge($xml[$co],$val);
					} else {
						$xml[$co][] = $val;
					}
				} else {
					$xml[$co] = $val;
				}
			}
		}
		
		$fAttributes = $this->_fieldsAttributes[$f->type];
		
		switch($f->type){
			case 'ckdatetimepicker':
			case 'cktimepicker':
				if(!empty($f->format) AND empty($f->uiFormat)){
					$f->uiFormat = $f->format;
				} else if(empty($f->format) AND !empty($f->uiFormat)){
					$f->format = $f->uiFormat;
				}

				if($f->type == 'ckdatetimepicker'){
					if(!empty($f->startDate_type)){
						$xml['startDate'] = $f->startDate_dynamic;
						if($f->startDate_type == 'fixed'){
							$xml['startDate'] = $this->getDate($f->format,$f->startDate_fixed);
						}
					}
					
					if(!empty($f->endDate_type)){
						$xml['endDate'] = $f->endDate_dynamic;
						if($f->endDate_type == 'fixed'){
							$xml['endDate'] = $this->getDate($f->format,$f->endDate_fixed);
						}
					}
					
					// selectableDays // specialDates
					$sub = 'specialDates';
					if(empty($f->$sub)) continue;

					$val = $f->$sub;
					try {
						$val = json_decode($val);
					} catch(Exception $e){
						$val = array();
					}
					
					$subFi = array();
					unset($v);
					foreach($val as $k => $v){
						$sel = array();
						
						$this->multilanguage($v,$this->_ml_fields);
						
						if(empty($v->date)) continue;
						$sel['date'] = date($f->format,strtotime($v->date));
						
						$sel['repeatYear'] = !empty($v->repeatYear) ? 'true' : 'false';
						$sel['repeatMonth'] = !empty($v->repeatMonth) ? 'true' : 'false';
						$sel['repeatWeek'] = !empty($v->repeatWeek) ? 'true' : 'false';							
						
						$cleanVal = array();
						unset($t);
						if(!empty($v->selectableTimes)){
							foreach($v->selectableTimes as $t){
								$cleanVal[] = $t->start .' - '. $t->end;
							}
						}
						$sel['selectableTimes'] = $cleanVal;
					
					
						$cleanVal = array();
						unset($t);
						if(!empty($v->selectors)){
							foreach($v->selectors as $t){
								$cleanVal[$t->attribute] = htmlspecialchars($t->value, ENT_COMPAT, 'UTF-8');
							}
						}
						$sel['selectors'] = $cleanVal;
						
						$sel['message'] = htmlspecialchars($v->message, ENT_COMPAT, 'UTF-8');
						$sel['selectable'] = !empty($v->selectable) ? 'true' : 'false';
						
						$subFi[] = (object)$sel;
					}
					$xml[$sub] = json_encode($subFi,JSON_NUMERIC_CHECK);
					$xml[$sub] = htmlspecialchars($xml[$sub], ENT_COMPAT, 'UTF-8');
				}
				break;
			
			case 'ckfile':
				$f->root = '[DIR_SUBMISSIONS_ATTACHED_FILES]';
				$xml['thumbnails'] = array();
				$f->allowedExtensions = !empty($f->allowedExtensions) ? strtolower($f->allowedExtensions) : '';
				if(!empty($f->preview)){
					if($f->width AND $f->height){
						$xml['thumbnails'][] = intval($f->width) .'x'. intval($f->height);
					}
				}
				$xml['attrs'] = 'fit,crop,center,color:ffffff';
				break;
				
			case'ckmedia':
				$f->preview_width = $f->width;
				unset($f->width);
				
				$f->preview_height = $f->height;
				unset($f->height);
				break;
			
			case 'ckspacer':
				$xml['label'] = htmlspecialchars($f->spacer_content, ENT_COMPAT, 'UTF-8');		
				unset($f->spacer_content);
				break;
				
			case 'cktimepicker':
				$f->step = $f->minuteStep;
				unset($f->minuteStep);
				break;
		}
		
		unset($co);
		foreach($fAttributes as $co){
			$skip = false;
			@$val = $f->$co;
			
			if(empty($val)) continue;
			if(in_array($co,$this->_boolean)) $val = 'true';
			
			// sanitize
			if(in_array($co,$this->_htmlSanitize)){
				$val = htmlspecialchars($val, ENT_COMPAT, 'UTF-8');
			}
			
			switch($f->type){				
				case 'cktimepicker':
					switch($co){
						case 'start':
						case 'end':
						case 'time':
							$format = $f->format ?: 'H:i';
							
							$currTZ = date_default_timezone_get();
							date_default_timezone_set('UTC');
							$val = date($format,$val);
							date_default_timezone_set($currTZ);
							break;
					}
					break;
			}

			// special fields
			switch($co){
				case 'options':
					$skip = true;
					try {
						$val = json_decode($val);
					} catch(Exception $e){
						$val = array();
					}
					
					$groupsVal = ByGiroHelper::groupArrayByValue($val, 'groupBy');
					
					$groupsby = array();
					unset($v);
					foreach($groupsVal as $vals){
						foreach($vals as $v){					
							if(!empty($v->groupBy)){
								$v->groupByKey = ByGiroHelper::safeAlias($v->groupBy);
								$groupsby[$v->groupByKey] = $v->groupBy;
							}
							
							if($opt = $this->buildOption($v)){
								$fieldContent[] = $opt;
							}
						}
					}
					
					if(!empty($groupsby)){
						$xml['groupBy'] = 'groupByKey:groupBy';
					}
					break;
					
				case 'repeatable_fields':
					$skip = true;
					
					// get subfields
					$subfields = array();					
					unset($v);
					
					foreach($val as $v){
						@$field = $this->_fieldsById[$v];
						if(empty($field)) continue;
						
						$subfields[$f->id][$field->ordering] = $field;
					}
					ksort($subfields[$f->id]);

					// build a form with the subfields
					if(!empty($subfields)){
						$subSelf = new self($subfields,array($f->id => $f),$this->_fieldsById);
						$fieldContent[] = $subSelf->getForm(true, true);
					}
					break;
					
				case 'thumbnails':
					$skip = true;
					try {
						$val = json_decode($val);
					} catch(Exception $e){
						$val = array();
					}

					$thumbs = array();
					unset($v);
					foreach($val as $v){
						if($v->width AND $v->height){
							$thumbs[] = intval($v->width) .'x'. intval($v->height);
						}
					}
					
					if(!empty($thumbs)){
						$xml['thumbnails'] = array_merge($xml['thumbnails'], $thumbs);
					}
					
					break;
			}

			
			// remove name field
			if(strpos($co,'_'. $f->type) !== false){
				$co = str_replace('_'. $f->type,'',$co);
			}
			
			if(!$skip){
				$xml[$co] = $val;
			}
		}
		
		if(!empty($validate_js)){
			$xml['class'][] = 'validate['. implode(',',$validate_js) .']';
		}
		
		if(!empty($xml['thumbnails'])){
			$xml['thumbnails'] = implode(',',$xml['thumbnails']);
		}
		
		$xmlAttr = array();
		unset($x);
		foreach($xml as $k => $x){
			if(empty($x)) continue;
			
			if(is_array($x)) $x = implode(' ',$x);		
			$xmlAttr[] = $k .'="'. $x .'"';
		}
		
		$xml = "\t\t<field ".PHP_EOL ."\t\t\t". implode(PHP_EOL ."\t\t\t",$xmlAttr) .">". PHP_EOL ."\t\t\t\t". implode(PHP_EOL ."\t\t\t\t",$fieldContent) . PHP_EOL ."\t\t</field>" . PHP_EOL ;
		return $xml;
	}
	
	public static function multilanguage($item,$fields){
		static $lang_tag;
		
		$isObj = is_object($item);
		
		if($isObj){
			$item = (array)$item;
		}

		if(empty($lang_tag)){
			$lang = JFactory::getLanguage();
			$lang_tag = strtolower(str_replace('-','', $lang->getTag()));
			if($lang_tag != ''){
				$lang_tag = '_'. $lang_tag;
			}
		}
	
		foreach($fields as $fi){
			$langKey = $fi . $lang_tag;
			
			if(!empty($item[$langKey])){
				$item[$fi] = $item[$langKey];
			}
		}
		
		if($isObj){
			$item = (object)$item;
		}
		
		return $item;
	}
	
	function buildOption($opt){
		// multilanguage attributes
		$opt = $this->multilanguage($opt,$this->_ml_fields);
		
		$xml = array();		
		foreach($opt as $k => $v){
			if(empty($v)) continue;
			// sanitize
			if(in_array($k,$this->_htmlSanitize)){
				$v = htmlspecialchars($v, ENT_COMPAT, 'UTF-8');
			}
			
			if($k != 'label'){
				$xml[$k] = $v;
			} else {
				$opt->label = $v;
			}
		}

		$xmlAttr = array();
		unset($x);
		foreach($xml as $k => $x){
			if(is_array($x)) $x = implode(' ',$x);
			$xmlAttr[] = $k .'="'. $x .'"';
		}
		
		@$label = $opt->label ?: $opt->value;
		if(empty($label)) return false;
		
		$xml = '<option '. implode(PHP_EOL,$xmlAttr) .'>'. $label .'</option>';
		return $xml;
	}
	
	public function createRule($rule){
		$class= "";
		
		$task = (!empty($rule->task)) ? $rule->task : 'show';
		if(!empty($rule->task_values)){
			$task .= '('. $rule->task_values .')';
		}
		
		$triggers = $rule->trigger;
		if(!is_array($triggers)){
			$triggers = array($rule->trigger);
		}
		
		$newTriggers = array();
		foreach($triggers as $k => $tr){
			if(ctype_digit($tr)){
				@$field = $this->_jForm->fields[$tr];
				if(empty($field)) continue;
				
				$triggers[$k] = '#jform_'. $field->name;
			} else {
				$newTriggers = array_merge($newTriggers,explode(',',$tr));
			}
		}
		
		$triggers = array_merge($triggers, $newTriggers);
		@$class= 'condRule['. $task .','. implode('|',$triggers) .','. $rule->values .','. (int)$rule->behaviour .']';
		
		return $class;
	}
	
	public function createPriceRule($rule){
		$class= "";
		$type = (!empty($rule->type) && $rule->type == 'percentage')  ? '%' : '';
		$price = empty($rule->price) ? 'this' : floatval($rule->price);
		
		@$class = 'condRule[priceRule('. $rule->variation .'|'. $price .'|'. $type .'),this,'. $rule->values .','. (int)$rule->behaviour .']';

		return $class;
	}
	
	function getDate($format,$val){
		$format = $format ?: 'Y-m-d';
		$currTZ = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$val = date($format,$val);
		date_default_timezone_set($currTZ);
		return $val;
	}
	
	function writeXML($xml){
		return;
		
		$xml = '<?php die(\'Forbidden.\'); ?>'. $xml;
	
		$basePath = JFactory::getConfig()->get('tmp_path') . DS ;
		$original_filename = $filename = 'jforms_xml';
		$counter = 0;
		while(is_file( $basePath . $filename .'.php')){
			$counter++;
			$filename = $original_filename .'_'. $counter;
		}
		file_put_contents($basePath . $filename .'.php',$xml);		
	}
}
