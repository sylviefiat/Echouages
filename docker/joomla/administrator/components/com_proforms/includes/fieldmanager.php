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


class ProformsFieldManagerGroups{
	public $identifier = '';
	public $min = 0;
	public $max = 10;
	public $elements = array();
	/**
	 * 
	 * @param string $ident
	 * @param number $min
	 * @param number $max
	 * @param array $ids
	 */
	public function __construct($ident = '', $min = 0 , $max = 10, & $ids = array() ){
		$this->identifier = $ident;
		$this->min = (int) $min;
		$this->max = (int) $max;
		
		foreach($ids as $id){
			$id = (int) $id;
			if($id >= $min && $id < $max) {
				array_push($this->elements, $id);
			}
		}
		sort($this->elements);
	}
	
}


class ProformsFieldManager {
	
	protected $formLibPath = null;
	protected $fieldGroups = array();
	protected $formIds = array();
	
	public function __construct(){
		$this->formLibPath = JPATH_ROOT . '/components/com_proforms/formlib';
		require_once $this->formLibPath . '/init.php';
		
		$this->formIds = MFormFactory::getIds();
		/**
		 * Creating the groups
		 */
		$this->createGroup('switch', 1, 10);
		$this->createGroup('date', 10, 20);
		$this->createGroup('textfield', 20, 30);
		$this->createGroup('options', 30, 40);
		$this->createGroup('attachment', 40, 50);
		$this->createGroup('displayonly', 50, 60);
	}
	
	
	protected function createGroup($ident = null, $min = 0, $max = 10 ){
		$ident = strtolower(trim($ident));
		if(!$ident) return false;
		$this->fieldGroups[$ident] = new ProformsFieldManagerGroups($ident, $min, $max, $this->formIds);
	}
	
	
	public function htmlGroups( $print = false){
		$buffer = '';
		foreach ($this->fieldGroups as & $group){
			/* @var $group ProformsFieldManagerGroups */
			$text = PText::group($group->identifier);			
			$buffer .= '<div class="proformsFieldGroup" info="'.$text->desc.'"><div class="pfmLegend">' . $text->title . '</div>' ;
			
			foreach($group->elements as $element){
				$buffer .= '<div class="proformsFormElement" data-form="'.$element.'">' . 
							'<img src="components/com_proforms/images/element-icons/'.$element.'.png" />' . 
						    PText::field($element) . 
							'</div>';
			}

			$buffer .= '</div>';
		}
		
		if($print) echo $buffer;
		else return $buffer;
		
	}
	
	
	public function getGroup($ident = null){
		$ident = strtolower(trim($ident));
		return isset($this->fieldGroups[$ident]) ? $this->fieldGroups[$ident] : new ProformsFieldManagerGroups($ident);
		
	}
	
	/**
	 * 
	 * @return ProformsFieldManager
	 */
	public static function getInstance(){
		static $instance;
		if( empty($instance)){
			$instance = new ProformsFieldManager();
		}
		return $instance;
	}
	
	
}