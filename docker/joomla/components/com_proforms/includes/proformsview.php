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


class ProformsView {
	/**
	 *
	 * @var Proforms
	 */
	protected $parent = null;

	/**
	 *
	 * @var string
	 */
	protected $viewPath = null;

	/**
	 *
	 * @var string
	 */
	protected $templatePath = null;

	/**
	 *
	 * @var string
	 */
	protected $template = 'default';

	/**
	 *
	 * @var string
	 */
	protected $buffer = '';
	/**
	 *
	 * @var stdClass
	 */
	protected $params = null;

	/**
	 * 
	 * @var string
	 */
	protected $routing = null;
	
	/**
	 * 
	 * @var ProformsModel
	 */
	protected $model = null;
	
	/**
	 * 
	 * @var string
	 */
	protected $viewName = null;
	
	/**
	 *
	 * @param Proforms $parent
	 */
	public function __construct( & $parent = null){
		$this->parent = $parent;
		$this->params = new stdClass();
		$reflection = new ReflectionClass(get_called_class());
		$this->viewPath = realpath( dirname( $reflection->getFileName()) );
		$this->viewName = basename($this->viewPath);
		$this->templatePath = $this->viewPath . '/tmpl';
		
		$model = ProformsModel::getInstance($this->viewName, $this);
		$this->setModel( $model );
		
		$this->_init();
		
	}

	
	protected function _init(){
		return;
	}

	protected function _process(){
		return;
	}


	public function & start(){
		$this->_process();
		if($this->routing){
			$destination = trim( JRequest::getCmd($this->routing, false) );			
			if( method_exists($this, $destination) ) {
				$this->$destination();
			}
		}
		return $this;
	}


	public function render(){
		$this->_renderTemplate();
		
		if( defined('_MDEBUG') && _MDEBUG ){
			$this->buffer .= MDebug::out();
		}
	}
	
	/**
	 * 
	 * @param ProformModel $model
	 */
	public function setModel( & $model = null){
		if($model && is_object($model) && get_parent_class($model) === 'ProformsModel'){
			$this->model = $model;
		}
	}
	
	
	protected function _renderTemplate(){
		$tmplPath = $this->templatePath . '/' . $this->template . '.php';
		if(! JFile::exists($tmplPath)){
			$tmplPath = $this->templatePath . '/default.php';
			if(! JFile::exists($tmplPath)) return;
		}
		if( is_object($this->params) ){
			foreach ($this->params as $key => $value){
				$$key = $value;
			}
		}
		ob_start();
		include $tmplPath;
		$this->buffer = ob_get_clean();
		
		if($this->viewName !== "app"){
			if(! class_exists("MFormElement")) {
				require_once M4J_INCLUDE_FUNCTIONS;
				require_once M4J_INCLUDE_VALIDATE;
				require_once M4J_INCLUDE_OPT;
			}
			$std = new stdClass();
			$element = new MFormElement($std);
			$this->buffer .= $element->save();
		}		

		// Some action hooks for after rendering 
		$this->_onAfterRender();
	}

	protected function _onAfterRender(){
		return;
	}
	

	protected function _renderRaw($content = null){
		$this->buffer = $content;
		// Some action hooks for after rendering 
		$this->_onAfterRenderRaw();
	}
	
	protected function _onAfterRenderRaw(){
		return;
	}
	
	public function setBuffer($content = null){
		$this->buffer = $content;
	}
	
	public function setMetaTitle($title = null){
		$title = htmlspecialchars($title);
		$document= JFactory::getDocument();
		$document->setTitle($title);
	}
	
	protected function htmlLink($url = "",$innerHTML = "",$class = null, $id = null, $optionalAttributes = null){
		$itemID = (int) $this->parent->getItemid(); 
		if($itemID)	$url = $url.'&Itemid='.$itemID;
		$url = JRoute::_(str_replace('&amp;', '&',$url));
		$add = ($class) ?  'class="'.$class.'"' : '';
		$add .= ($id) ? ' id="'.$id.'"' : '';
		$add .= ($optionalAttributes) ? ' '.$optionalAttributes : '';
		
		return '<a href="'.$url.'" '.$add.'>'.$innerHTML.'</a>' . "\n";
	}
	
	protected function htmlHeading($heading = null, $link = null){
		if(! _M4J_IS_J16){
			return '<div class="componentheading">'.( $link ? $this->htmlLink($link, $heading) : $heading  ) .'</div>' . "\n";
		}
		
		return '<div class="componentheading page-header"><h2>'.( $link ? $this->htmlLink($link, $heading) : $heading  ) . '</h2></div>' . "\n";
		
		// J3.2 => <div class ="page-header"><h2>$heading </h2></div>
		
	}
	
	protected function systemError($message = null){
		return '<div class ="proforms_system_error text-error alert-error"><span>' .$message . '</span></div>' . "\n";
	}
	
	public function __toString(){
		return $this->buffer;
	}

	protected function _cleanBacktrace($debug = true, $maxSteps = 0){
		$ignoreFirst = true;
		$count = 1;
		$arr = debug_backtrace();
		
		$ret = '<h3>CLEAN BACKTRACE:</h3>';
		$allow = array('file', 'line', 'class' , 'function' );
		foreach($arr as $station){
			if($ignoreFirst){
				$ignoreFirst = false;
				continue;
			}
			$buffer = "\n";
			foreach($station as $key => $info){
				$buffer .= (in_array($key, $allow)) ? strtoupper($key) . ": " . $info . "\n" : '';
			}
			$ret .= '<b>STEP: '. $count . "</b><br/><pre>" . $buffer . "</pre>\n";
			if($maxSteps === $count++) break;
		}
		if($debug) MDebug::_($ret);
		else return $ret;
	}

	public function getTemplatePath(){
		return $this->templatePath;
	}

	public function setTemplatePath($templatePath){
		$this->templatePath = $templatePath;
	}

	public function getTemplate(){
		return $this->template;
	}

	public function setTemplate($template){
		$this->template = $template;
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

	
	
}//EOF class