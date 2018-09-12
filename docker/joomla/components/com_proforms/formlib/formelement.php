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




class MFormElement {
	
	protected static $_COUNTER = 0;
	protected $_SERIAL = '';
	
	
	/**
	 * 
	 * @var string
	 */
	protected $buffer = '';
	
	
	protected $_yes = array(
			"ja","po","نعم","naam","ayo","beli","awo","bai","Так","ha",
			"hoon","da","ya","Да","hoke te","hman te","sí","v","是","对",
			"iè","si","ano","yes","jes","jah","é","io","kyllä","oui",
			"se","yan","vais","vaer","ki","diakh","ne","wi","'ae","ken",
			"हाँ ","igen","já","sì","hai","éé","eh","cha","baht","yego",
			"ney","yea","ere","bele","doy","tiao","euh","men leo","dai","sic",
			"jā","sci","èh","taip","jo","да","eny","iva","ae","za",
			"tiim","За","veire","vethe","òc","о","bale","âre","tak","sim",
			"ova","eja","giai","आम्।","éwa","ehe","ovu","yo","ndiyo","opo",
			"oo","e","wah","ஒம்","aye","avunu","ใช่","ค่ะ","ครับ","evet",
			"ben","o-o","y-y","han-ji","oyi","ydw","wè","waaw","yoh","beeni",
			"yebo");
	
	
	/**
	 * 
	 * @var array
	 */
	protected $_style = array();
	
	/**
	 * 
	 * @var int
	 */
	protected $eid = 0;
	
	/**
	 *
	 * @var int
	 */
	protected $fid = 0;
	
	/**
	 *
	 * @var int
	 */
	protected $form = 0;
	
	/**
	 *
	 * @var int
	 */
	protected $required = 0;
	
	/**
	 *
	 * @var int
	 */
	protected $active = 1;
	
	/**
	 *
	 * @var int
	 */
	protected $usermail = 0;
	
	/**
	 *
	 * @var int
	 */
	protected $align = 0;
	
	/**
	 *
	 * @var string
	 */
	protected $question = '';
	
	/**
	 *
	 * @var string
	 */
	protected $alias = '';
	
	/**
	 *
	 * @var string
	 */
	protected $help = '';
	
	/**
	 *
	 * @var int
	 */
	protected $slot = 1;
	

	/**
	 *
	 * @var int
	 */
	protected $responsive_slot = null;
	
	/**
	 *
	 * @var int
	 */
	protected $sort_order = 1;
	
	/**
	 *
	 * @var string
	 */
	protected $html = '';
	
	/**
	 * 
	 * @var string
	 */
	protected $sql = '';
	
	/**
	 * 
	 * @var boolean
	 */
	protected $sqlLoadSingle = false;
	
	/**
	 * 
	 * @var array|object
	 */
	protected $sqlResults = null;
	
	/**
	 *
	 * @var stdClass
	 */
	protected $params = null;
	
	/**
	 * 
	 * @var int
	 */
	protected $use_values = 0;
	
	/**
	 *
	 * @var array
	 */
	protected $options = array();
	
	/**
	 * 
	 * @var array
	 */
	protected $option_values = array();
	
	/**
	 * 
	 * @var string
	 */
	protected $serString = 'PFMSER::';

	/**
	 * 
	 * @var boolean
	 */
	protected $isSend = false;
	

	/**
	 * 
	 * @var boolean
	 */
	protected $isEdit = false;
	
	
	/**
	 * 
	 * @var any
	 */
	protected $value = NULL;
	
	/**
	 * 
	 * @var array
	 */
	protected $_class = array();
	
	/**
	 *
	 * @var array
	 */
	protected $params2HigherLevel = array('sql', 'use_values');
	
	/**
	 *
	 * @var array
	 */
	protected $encodedParams = array('sql', 'pleaseSelectOption');
	
	/**
	 * @var stdClass
	 */	
	protected $_originalDataObject = null;
		
	
	/**
	 * @var boolean
	 */	
	protected $isHidden = false;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $displayOnly = false;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $_upload = false;
	
	/**
	 * 
	 * @var bool
	 */
	protected $uniqueEmailField = false;
	
	/**
	 * 
	 * @var JDatabaseDriver
	 */
	protected $db = null;
	
	
	/**
	 * @var	M4J_validate
	 */
	protected $validate = null;
	
	/**
	 * The error string
	 * @var string
	 */
	protected $_error = '';
	
	/**
	 * The store id
	 * @var int
	 */
	protected $stid = null;
	
	/**
	 * The store item id
	 * @var int
	 */
	protected $stiid = null;
	
	/**
	 * 
	 * @var string
	 */
	public $formattedValueType = 'string';
	
	/**
	 * 
	 * @var string
	 */
	protected $tmpPath = M4J_TMP;
	
	/**
	 *
	 * @var string
	 */
	protected $tmpDir = null;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $uploadExists = false;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $isSelection = false;
	
	
	/**
	 * Called as pre function of the constructor
	 * Must be overriden.
	 * @param	array	$dataObject	referenced data object (from db) as array
	 */
	protected function _init( & $dataObject){
		return;
	}
	
	/**
	 * Creates the default value
	 */
	protected function _default(){
		if(isset($this->params->default)){
			$this->value = $this->params->default;
		}
	}
	
	/**
	 * Fired for special data preparation
	 */
	protected function _prepareData(){
		return ;
	}
	
	
	/**
	 * The function which retrieves the data
	 */
	protected function _request(){
		
		if(empty($this->eid) || ! $this->eid || $this->displayOnly) return;		
		$name = 'm4j-' . $this->eid;
		$this->_recursiveFilter( $_REQUEST[$name] );
		
		$len =  is_array( $_REQUEST[$name]) ?  sizeof($_REQUEST[$name]) : strlen(  $_REQUEST[$name] );
		if(! isset($_REQUEST[$name]) || $_REQUEST[$name] === null || ! $len ){
			$this->value = null;
			return;
		}
		
		$this->value = $_REQUEST[$name];
	}
	
	
	protected function _selectionValuePurge(){		
		$predefined =  $this->use_values ? $this->option_values : $this->options;
		
		if(is_array($this->value)){
			$matches = array();
			foreach ($this->value as $key => $value){
				if( ! is_array($value) && in_array($value, $predefined)){
					$matches[$key] = $value;
				}
			}
			$this->value = (empty($matches)) ? null : $matches;
			
			
		}else{
			$this->value = (in_array($this->value, $predefined)) ? $this->value : null;
		}
	
		if($this->isSend) $_REQUEST['m4j-'. $this->eid] = $this->value;
	}
	
	
	
	/**
	 * This is called when the parameters from the db object have been processed 
	 * but the before any sending actions like request, check required and validation. 
	 */
	protected function _onBeforeSendProcessing(){
		return;
	}
	
	
	/**
	 * Called after constructor
	 */
	protected function _postCreate(){
		return;
	}
	
	/**
	 * Called after sending is detected and the value have benn retreived
	 */
	protected function _validation(){
		return;
	}
	
	protected function _checkRequired(){
		if( ! $this->required && ! $this->usermail ) return;
		if( $this->value === null ){
			$this->_addError( M4J_LANG_MISSING.$this->question );			
		}
	}
	
	
	protected function _addError($error = null){
		if(! $error = trim($error)) return;
		$this->_addErrorBorder();
		$this->_error .= ProformsHelper::errorTag($error);
	}
	
	
	protected function _addErrorBorder(){
		$this->addStyle('border-color', ( defined('M4J_ERROR_COLOR') ? '#' . M4J_ERROR_COLOR : 'red'  ));
		
	}
	
	
	
	/**
	 * 
	 * @param stdClass|array $dataObject
	 */
	public function __construct( $dataObject = null, $tmpPath = NULL, $tmpDir = NULL){

		$this->tmpPath = $tmpPath ? $tmpPath : M4J_TMP;
		$this->tmpDir = $tmpDir;
		
		$this->db = JFactory::getDbo();
		$this->validate = M4J_validate::getInstance();
		
		if(is_object($dataObject)){
			$this->_originalDataObject = $dataObject;
			settype($dataObject, 'array');
		}
		
		// stats
		$counter = self::$_COUNTER++;
		$this->_SERIAL = 'COUNT:' . $counter .'_FID:' . ( isset($dataObject['fid'])  ? $dataObject['fid'] : 0 ). '_EID:' .  
		( isset($dataObject['eid'])  ? $dataObject['eid'] : 0 ) . '_FORM:'. ( isset($dataObject['form'])  ? $dataObject['form'] : 0 );
		
		
		// Calling the pre creation init function
		$this->_init($dataObject);
		
		// General parsing
		$ignore = array('parameters', 'options');
	
		foreach ($dataObject as $key => $value){
			if(in_array($key, $ignore)) continue;
			if(isset($this->$key)){
				
				$this->$key = $value;
			}
		}
		$this->responsive_slot = isset($dataObject["responsive_slot"]) ? $dataObject["responsive_slot"] : 0;
		
		
		// Parameter parsing
		if(isset($dataObject['parameters'])){
			$parametersRaw = $dataObject['parameters'];
			
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
				
				if(in_array($key, $this->params2HigherLevel) && isset($this->$key)){
					$this->$key  = $value;
				}
				
			}//EOF foreach
			
			$this->params = $parameters;
		
		}//EOF isset parameters

		//Option parsing		
		if(isset($dataObject['options'])){
			$options = explode("\n", $dataObject['options']);
			if(sizeof($options) == 1){
				$this->use_values = 0;
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
					$this->use_values = 0;
				}else{
					$this->option_values = $vals;
				}
			}//EOF elseif
		}// EOF if optionsd
		
		
		// Specials
		$this->sql = trim( preg_replace("(\r\n|\n|\r)", "", $this->sql) );
		$this->help = preg_replace("(\r\n|\n|\r)", "<br/>", $this->help);
		$this->help = str_replace('"','“', $this->help  );

		
		// SQL query
		$this->_sqlQuery();
		
		// Hook for individual data preparation
		$this->_prepareData();
		
		// Check if this is a send action
		$this->isSend = (isset($dataObject->parentSend)) ? (bool) $dataObject->parentSend : (  (isset($_REQUEST['send']) && (int) $_REQUEST['send'] ) ? true : false ) ;
			
		// Init the default value
		$this->_default();
		
		// Calling hooks before sending processing
		$this->_onBeforeSendProcessing();
		
		// Just if the form has been submited request the value 
		if($this->isSend){
			$this->_request();
		}
		
		// If this is a selection item we need to purge corrupt data in the value. We need to do this always even if no submit
		if($this->isSelection) $this->_selectionValuePurge();
		
		// Just if the form has been submitted do the main validation actions
		if($this->isSend){
			$this->_checkRequired();
			$this->_validation();
		}
		
		// Custom post create hook
		$this->_postCreate();
	}
	
	
	public function errorTest( & $error = '' ){
		$error .= $this->_error;
	}
	
	protected function _sqlQuery(){
		if(!empty($this->sql)){
			$this->db->setQuery($this->sql);
			$this->sqlResults = $this->sqlLoadSingle ? $this->db->loadObject() : $this->db->loadObjectList();				
		}
	}
	
	
	
	protected function _add2HigerLevel($paramNames = null){
		settype($paramNames, 'array');
		$this->params2HigherLevel = array_unique(array_merge($this->params2HigherLevel,$paramNames));
	}
	
	protected function _addEncodedParams($paramNames = null){
		settype($paramNames, 'array');
		$this->encodedParams = array_unique(array_merge($this->encodedParams,$paramNames));
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
	
	/**
	 * This is a recursive filter for the request
	 * @param any $value	The value
	 * @param	string	$type	The type of filtering | default is string
	 * @param	JFilterInput	$filter	An instance of the input filter object
	 */
	protected function _recursiveFilter(& $value = null, $type = 'string', JFilterInput & $filter = null){
			
		if(!$filter){
			// The most strict filter
			$filter = JFilterInput::getInstance();
		}
		
		if(! is_array($value)){
// 			$value =  str_replace(array('"','\x22', '&quote;', '&#34;', '%22'), '“', $filter->clean($value, $type) );
			$value =  $filter->clean($value, $type);
			return;
		}
		
		foreach ($value as & $val){
			$this->_recursiveFilter($val, $type, $filter);
		}
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
	 * This is the rendering function which renders the HTML of a form field to the buffer
	 * @param string $type
	 */
	public function render($type = 'default'){
		
		switch ($type){
			default:
			case 'default':
				$this->buffer = $this->_renderDefault();
				break;
				
			case 'responsive':
				$this->buffer = $this->_renderResponsive();
				break;
		}
// 		MDebug::_("FORM: $this->form | QUESTION: $this->question");
// 		MDebug::_("<pre>" . htmlspecialchars($this->buffer) . '</pre>');
// 		$dbt = 'DEBUG BACKTRACE:' ."\n";
// 		$dbta = debug_backtrace();
// 		MDebug::pre($dbta[0]);
	}
	
	/**
	 * Render the field to the buffer in classic / default view
	 */
	protected function _renderDefault(){
		return '<input type="text" alt="" value="'.$this->value.'" name="m4j-'.$this->eid.'" class="m4jInputField">';
	}
	
	/**
	 * Render the field to the buffer in responsive view
	 */
	protected function _renderResponsive(){
		return '<input type="text" alt="" value="'.$this->value.'" name="m4j-'.$this->eid.'" class="pfmInputField">';
	}
	
	public function setValue($value = null){
		$this->value = $value;
		return true;
	}
	
	public function getValue(){
		return $this->value;
	}
	
	
	/**
	 * toString outputs the buffer
	 */
	public function __toString(){
		return (string) $this->buffer;
	}
	
	

	/**
	 * 
	 * @return array
	 */
	public function getClass(){
	    return $this->_class;
	}

	/**
	 * Private function for getting the class as string for attribute
	 * @return string
	 */
	protected function _getClass(){
		return implode(' ', $this->_class);
	}
	
	/**
	 * 
	 * @param string|array	$_class	
	 */
	public function setClass($_class = null){
		settype($_class, 'array');
	    $this->_class = $_class;
	}
	
	
	/**
	 *
	 * @param string|array	$_class
	 * @param	boolean	$unique   Be sure that there only unique classes
	 */
	public function addClass($_class = null, $unique = false){
		settype($_class, 'array');
		$this->_class = $unique ? array_unique(array_merge($this->_class,$_class)) : array_merge($this->_class,$_class);
	}
	
	
	public function hasError(){
		return ( trim ($this->_error) ) ? true : false;
	}
	
	/**
	 * 
	 * @return 
	 */
	public function getActive()
	{
	    return $this->active;
	}

	/**
	 * 
	 * @param $active
	 */
	public function setActive($active)
	{
	    $this->active = $active;
	}

	/**
	 * 
	 * @return 
	 */
	public function getQuestion()
	{
	    return $this->question;
	}

	/**
	 * 
	 * @param $question
	 */
	public function setQuestion($question)
	{
	    $this->question = $question;
	}

	/**
	 * 
	 * @return 
	 */
	public function getHelp()
	{
	    return $this->help;
	}

	/**
	 * 
	 * @param $help
	 */
	public function setHelp($help)
	{
	    $this->help = $help;
	}

	/**
	 * 
	 * @return 
	 */
	public function getSlot()
	{
	    return $this->responsive_slot ? $this->responsive_slot : $this->slot;
	}

	/**
	 * 
	 * @return 
	 */
	public function getStiid()
	{
	    return $this->stiid;
	}

	/**
	 * 
	 * @param $stiid
	 */
	public function setStiid($stiid)
	{
	    $this->stiid = $stiid;
	}

	/**
	 * 
	 * @return 
	 */
	public function getIsEdit()
	{
	    return $this->isEdit;
	}

	/**
	 * 
	 * @param $isEdit
	 */
	public function setIsEdit($isEdit)
	{
	    $this->isEdit = $isEdit;
	}
	/**
	 * @param	boolean	$toStyle	Set a style with this width and return.
	 * @return string
	 */
	protected function _getWidth($toStyle = false){
		if (!isset($this->params->width) || $this->params->width == null || intval($this->params->width) == 0){
			return false;
		}else if(isset($this->params->width) && intval($this->params->width < 0 )){
			return false;
		}
		$add = (substr($this->params->width, -1, 1) == '%') ?  '%' : 'px';
		$width = intval($this->params->width);
		$width = ( !$width ) ? '100%' : $width . $add;
		if($toStyle){
			$this->addStyle('width', $width);
		}
		return $width;
	}
	
	public function replacePlaceholder( & $string = null, $escape = false){
		if( ! ($alias = trim($this->alias) ) ) return;
		
		$value = $this->getFormattedValue();
		
		if($escape){
			$value = _M4J_IS_J30 ? $this->db->escape($value) : $this->db->getEscaped($value);
		}else{
			$value = $value;
		}
		if(function_exists("preg_quote")){
			$alias = preg_quote($alias, '/');
		}
		$string = preg_replace('/{(\s*)('.$alias.')(\s*)}/i' , $value , $string);
	}
	
	
	/**
	 * Adds a style to the form
	 * addStyle('display:none;');
	 * or 
	 * addStyle('display', 'none');
	 * @param string $attr
	 * @param string $value
	 */
	public function addStyle($attr = false, $value = false ){
		if($attr === false && $value === false) return;
		$arr = array();
		if($attr && $value === false){
			$chunks = explode(';', $attr);
			foreach($chunks as  $chunk){
				$test = explode($chunk, ":");
				if(sizeof($test) === 2){
					$arr[ strtolower( trim($test[0]) )] =  rtrim( trim($test[1]) , ";") ;
				}
			}
		}else{
			$arr[ strtolower( trim($attr) ) ] = rtrim( trim($value), ';' );
		}
		$this->_style = array_merge($this->_style, $arr);
	}
	
	public function removeStyle($attr = null){
		$attr = strtolower( trim($attr) );
		if($attr && isset($this->_style[$attr])){
			unset($this->_style[$attr]);
		}
	}
	
	public function getStyle(){
		$term = '';
		foreach($this->_style as $attr => $value){
			$term .= $attr . ':' . $value . ';' ;
		}
		return $term ;
	}
	
	protected function _getStyle(){
		$style = $this->getStyle();
		return $style ? 'style="' .$style . '"' : '';
	}
	
	
	protected function dbEscape($toEscape = null){
		return method_exists($this->db, 'escape') ? $this->db->escape($toEscape) : $this->db->getEscaped($toEscape);
	}

	public function save($stid = null){
		if( ! $stid = (int) $stid) return; 
		
		if(! $this->stid){
			$this->stid = $stid;
			$value = (is_array($this->value)) ? implode("\n", $this->value) : (string) $this->value;
			
			$query = "INSERT INTO `#__m4j_storage_items`"
			. "\n ( `stid`, `eid`, `content` )"
			. "\n VALUES"
			. "\n ( '".$stid."', '".(int) $this->eid ."', '".$this->dbEscape($value)."' )";
			$this->db->setQuery($query);
			$this->db->query();
			$this->stiid = $this->db->insertid();
		}
	}
	
	public function toYesNo($value = 0){
		return $value ? M4J_LANG_YES : M4J_LANG_NO;
	}
	
	public function fromYesNo($value = null){
		$value = strtolower( trim( $value) );
		return in_array($value, $this->_yes) ? 1 : 0;
	}
	
	
	public function getFormattedValue(){
		
		$type = strtolower(trim($this->formattedValueType));
		
		switch (strtolower($type)){
			default:
			case "string":
				return (string) $this->value;
			break;

			case "int":
				return (int) $this->value;
			break;

			case "bool":
				return (bool) $this->value;
			break;

			case "yesno":
				return  $this->toYesNo($this->value);
			break;
			
			case "colapsed":
				return implode("\n", $this->value);
			break;
			
			case "colapsed_html":
				return implode("<br/>", $this->value);
			break;
					
		}
	}
	
	public function getRequired()
	{
	    return $this->required;
	}

	public function getAlign()
	{
	    return $this->align;
	}

	public function getEid()
	{
	    return $this->eid;
	}

	public function getUsermail()
	{
	    return $this->usermail;
	}

	public function getIsHidden()
	{
	    return $this->isHidden;
	}

	public function getDisplayOnly()
	{
	    return $this->displayOnly;
	}

	public function hasAlias(){
		return (bool) trim($this->alias);
	}
	
	public function getAlias()
	{
	    return $this->alias;
	}
	
	public function setUniqueEmailField($is = false){
		$this->uniqueEmailField = (bool) $is;
	}
	
	public function isUniqueEmailField(){
		return (bool) $this->uniqueEmailField;
	}
	

	public function getStid()
	{
	    return $this->stid;
	}

	public function setStid($stid)
	{
	    $this->stid = $stid;
	}
	
	public function getFid(){
		return (int) $this->fid;
	}
	
	public function isUpload(){
		return $this->_upload;
	}
	
	public function hasUpload(){
		return  $this->uploadExists;
	}
	
}//EOF class