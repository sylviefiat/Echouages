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

class ProformsViewCategory extends ProformsView{
	/**
	 *
	 * @var array
	 */
	protected $list = array();
	/**
	 * 
	 * @var int
	 */
	protected $cid = 0;
	
	/**
	 * 
	 * @var ProformsModelCategory
	 */
	protected $model = null;
	
	protected function _init(){
		$this->cid = (int) $this->parent->getCid();
		$this->model->setCid($this->cid);
		if(! $this->model->getAllowed()){
			$this->template = 'noaccess';
			return;
		}
		
		if( $this->model->getCatNotFound()){
			$this->template = 'notfound';
			return;			
		}
		
		$this->params = $this->model->getParams();		
		foreach( $this->model->getList() as $item ){
			$this->_addListingItem( $item->heading , $item->introtext ,	$item->link );
		}
	}
	
	
	protected function _addListingItem($heading = null, $introtext = null, $link = null){
	
		$html  = '<div class ="'.M4J_CLASS_LIST_WRAP.'">'."\n";
		$html .= '<div class ="'.M4J_CLASS_LIST_HEADING.'">'."\n";
		//contentpagetitle
		$html .= ($link) ? $this->htmlLink($link,$heading) : $heading;
		$html .= '</div>'."\n";
		$html .= '<div class ="'.M4J_CLASS_LIST_INTRO.'">'."\n".$introtext."\n".'</div>'."\n";
		$html .= '</div>'."\n";
		array_push($this->list, $html);
	}
	
	
}