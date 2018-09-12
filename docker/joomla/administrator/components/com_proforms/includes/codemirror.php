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

class MCodeMirror {
	
	protected static $instance;
	protected static $count = 0;
	protected static $ids = array();
	protected static $included = array();
	
	public function get($params = array(), $value = null, $type = "htmlmixed", $width = "100%", $height = "400px"){
		
		MCodeMirror::init();
		
		$this->_typeConverter($type);
		
		$params['class'] = isset($params['class']) ? 'pfmCodeMirror ' .$params['class'] : 'pfmCodeMirror';
		MCodeMirror::setId($params['id']);
		
		$attributes = '';
		foreach ($params as $attr => $attrvalue){
			$attributes .= ' ' . $attr . '="'. $attrvalue . '"';
		}
		return  $this->_wrap('<textarea '. $attributes.' data-syntax="'.$type.'" data-width="'.$width.'" data-height="'.$height.'">'.
				str_replace(array('</textarea', '</ textarea'), '&lt;/textarea', $value ).'</textarea>'."\n" 
				, $params['id']
				);
	
	}

	
	protected function _wrap($textarea = null, $id = null){
		return '<div class="proformsCodeMirror" id="'.$id.'_WRAP">' .
				$textarea .
				'</div>';
	}
	
	protected function _typeConverter(& $type =  "htmlmixed"){
		$_type = strtolower(trim($type));
		switch ($_type) {

			default:
			case 'html':
			case 'htmlmixed':
			case 'text/html':
				$type =  "text/html";
				MCodeMirror::import(array(
					'addon.hint.show-hint',
					'addon.hint.html-hint',
					'addon.hint.xml-hint',
					'addon.hint.javascript-hint',
					'addon.hint.css-hint',
					

					'addon.edit.matchbrackets',
					'addon.comment.continuecomment',
					'addon.comment.comment',
					
					'mode.xml.xml',
					'mode.javascript.javascript',
					'mode.vbscript.vbscript',
					'mode.htmlmixed.htmlmixed'
				));
			break;
			
			case 'php':
			case 'text/x-php':
				$type =  "text/x-php";
				MCodeMirror::import(array(
					'addon.hint.show-hint',
					'addon.hint.html-hint',
					'addon.hint.xml-hint',
					'addon.hint.javascript-hint',
					'addon.hint.css-hint',
	
					'addon.edit.matchbrackets',
					'addon.comment.continuecomment',
					'addon.comment.comment',
					
					'mode.clike.clike',
					'mode.xml.xml',
					'mode.javascript.javascript',
					'mode.htmlmixed.htmlmixed',
					'mode.php.php'
				));
			break;

			
			case 'js':
			case 'text/javascript':
				$type =  "text/javascript";
				MCodeMirror::import(array(
					'addon.hint.show-hint',
					'addon.hint.javascript-hint',
						
	
					'addon.edit.matchbrackets',
					'addon.comment.continuecomment',
					'addon.comment.comment',
					
					'mode.javascript.javascript'
				));
			break;

			
			case 'json':
			case 'application/json':
				$type =  "application/json";
				MCodeMirror::import(array(
					'addon.hint.show-hint',
					'addon.hint.javascript-hint',

					'addon.edit.matchbrackets',
					'addon.comment.continuecomment',
					'addon.comment.comment',
					
					'mode.javascript.javascript'
				));
			break;			

			
			case 'css':
			case 'text/css':
				$type =  "text/css";
				MCodeMirror::import(array(
					'addon.hint.show-hint',
					'addon.hint.css-hint',
						
					'mode.css.css'
				));
			break;

			
			case 'xml':
			case 'application/xml':
				$type =  "application/xml";
				MCodeMirror::import(array(
					'addon.hint.show-hint',
					'addon.hint.html-hint',
					'addon.hint.xml-hint',
						
					'mode.xml.xml'
				));
			break;				


			case 'sql':
			case 'mysql':
			case 'text/mysql':
			case 'text/sql' :
				$type =  "text/x-sql";
				MCodeMirror::import(array(
					'addon.hint.show-hint',
					'addon.hint.sql-hint',
						
					'mode.sql.sql'
				));
			break;
		
		}
	}
	
	
	
	
	
	public static function setId(& $id = null){
		if(! $id ){
			$id = 'pfmCodeMirror_' . self::$count; 
		}
		array_push(self::$ids, $id);
		self::$count++;
	}
	
	
	public static function getCount( $increment = false){
		return $increment ? self::$count++ : self::$count;
	}
	
	/**
	 * @return	MCodeMirror
	 */
	public static function getInstance(){
		if( empty( self::$instance) ){
			self::$instance = new MCodeMirror();
		}
		return self::$instance;
	}
	
		
	
	public static function import($paths = null){
		settype($paths, 'array');
		$document = JFactory::getDocument();
		foreach($paths as $path){
			if(! in_array($path, self::$included)){
				array_push(self::$included, $path);
				$path = str_replace('.', '/', str_replace('.js', '', $path) ) . '.js';
				$document->addScript(M4J_JS . 'codemirror/' . $path);
			}
		}
	}
	
	public static function init(){
		static  $isInit;
		if(! empty($isInit)) return;
		$isInit = true;
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(M4J_JS . 'codemirror/codemirror.css');
		$document->addStyleSheet(M4J_JS . 'codemirror/theme/eclipse.css');
		$document->addStyleSheet(M4J_JS . 'codemirror/addon/hint/show-hint.css"');
		$document->addScript( M4J_JS . 'codemirror/codemirror.js');
		$document->addScript( M4J_JS . 'codemirror/codemirror_parser.js');
		
	}
	
}//EOF class