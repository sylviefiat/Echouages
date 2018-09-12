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

class MLayoutResponsive {
	
	protected $fid = null;
	
	protected $isHelp = false;
	protected $data = null;
	
	protected $position = array();

	protected $slot = array();
	
	
	public function __construct($fid = null, $useHelp = false, $data = null){
		$this->fid = $fid;
		$this->isHelp = $useHelp;
		$this->data = & $data;
		foreach ($data->rows as &$row){
			foreach($row as & $section){
				$this->position[ (int) $section->slot] = array();
				$this->slot[ (int) $section->slot] = & $section;
			}
		}
	}

	/**
	 * 
	 * @param bool $isHelp
	 */
	public function setHelp($isHelp = false){
		$this->isHelp = (bool) $isHelp;
	}
	
	
	/**
	 * 
	 * @param int $slot
	 * @param string $question
	 * @param string $formString
	 * @param int $required
	 * @param string $help
	 * @param int $align
	 * @param int $usermail
	 * @param int $eid
	 * @param int $isHidden
	 * @param MFormElement $element
	 */
	public function addRow($slot=1, $question = null, $formString =null,$required=0,$help=null,$align=0,$usermail = null,$eid = null ,$isHidden = 0, & $element = null){
		$slot = 1;
		
		if($isHidden){
			if(isset($this->position[(int) $slot])) {
				$this->position[(int) $slot][] =  '<div id="m4je-'.$eid.'" class="pfmHidden">' . $formString . '</div>' . "\n";
			}
			return;
		}
		
		$mark= ($required==1 || $usermail == 1) ?  ' <span class="m4j_required pfmRequired">*</span>'  : '';
		$_mark = '';
		$direction = ' pfmMargin'; $left = ''; $right = ''; 
		$addClassLeft =  $align == 1 ? ' pfmCLR' : ''; 
		$addClassRight = '';
		$tooltip = '';
		$tooltipPadding = '';
		if($help && $this->isHelp) {
			$tooltipPadding = ' padding-right: 21px;' ;
			$help = preg_replace("(\r\n|\n|\r)", "<br/>", $help);
			$ttStyle= $align == 1 ? 'style="margin-left: 5px;" ' : '';
			$tooltip = '<div class="pfmInfoImage" style="background-image: url(./components/com_proforms/images/help'.M4J_HELP_ICON.'.png);">'.$help.'</div>'."\n";		
		}
		
		if($align != 3){
			
			if($align == 2){
				$question = '';
				$_mark = $mark ?   ' <span class="m4j_required pfmRequired pfmRequiredNoQuestion" >*</span>'  : '' ;
				$mark  = ! $this->slot[$slot]->direction ? '' : $mark;
			}
			

			
			if(! $this->slot[$slot]->direction){
				$direction = ' pfmFullRange pfmCLR';
				$questionWidth = (int) $this->slot[$slot]->questionsWidth;
				$fieldWidth = 100 - $questionWidth -1.43478;
				

				$left =  'style="width: 100%;"';
				$right =  'style="width: 100%;"';
				$dataLeft = ''; $dataRight = '';
				if($align != 1){
					$left =  ' style="width: '.$questionWidth . '%; '. $tooltipPadding .'" ' ;
					$right = ' style="width: '.$fieldWidth . '%;" ' ;
					$dataLeft = ' data-width="'.$questionWidth.'%"';
					$dataRight = ' data-width="'.$fieldWidth.'%"';
				}
				
				$buffer = '<div class="pfmElement '.$direction.'" id="m4je-'.$eid.'">' ."\n";
				$buffer .= '<label class="pfmQuestion proformsQuestions m4jq-'.$eid.$addClassLeft.'"'.$left.$dataLeft.'>' .$question. $mark .  $_mark .  $tooltip.'</label>' . "\n"  ;
				$buffer .= '<div class="pfmField'.$addClassRight.'"'.$right.$dataRight .'>'.$formString  . '</div>' . "\n";
				// 		$buffer .= '<div class="pfmDiv pfmCLR"></div>' ."\n";
				$buffer .="</div>\n";
			}else{
				$addClassLeft .=  $align != 1 ? ' pfmPadding' : '';
			
				$left = $align != 1 ? ' style="'. $tooltipPadding .'" ' : 'style="width: 100%;"';
				switch ($align){
					default: 
					case 0:

						$buffer = '<div class="pfmElement pfmMargin" id="m4je-'.$eid.'">' ."\n";
						$buffer .= '<label class="pfmQuestion proformsQuestions m4jq-'.$eid.$addClassLeft.'"'.$left.'>' .$question . $mark . $tooltip ;				
						$buffer .= '</label>' . "\n" ;
						$buffer .= '<div class="pfmField">'.$formString .  '</div>' . "\n";
						$buffer .="</div>\n";
						break;
						
					case 1:
						$buffer = '<div class="pfmElement pfmMargin" id="m4je-'.$eid.'">' ."\n";
						$buffer .= '<label class="pfmQuestion proformsQuestions m4jq-'.$eid.$addClassLeft.'"'.$left.'>' .$question . $mark . $tooltip  .  '</label>' . "\n";				
						$buffer .= '<div class="m4jCLR"></div>'. "\n" ;
						$buffer .= '<div class="pfmField">'.$formString .  '</div>' . "\n";
						$buffer .="</div>\n";
						break;
						
					case 2: 

						$mark = ! $tooltip && $mark ? ' <span class="m4j_required pfmRequired pfmRequiredNoQuestion" style="padding-left: 5px;">*</span>'  : $mark;
						$buffer = '<div class="pfmElement pfmMargin pfmSingle" id="m4je-'.$eid.'">' ."\n";
						$buffer .= $tooltip.    "\n";
						$buffer .= '<div class="pfmField">'.$formString .  '</div>' . "\n"  .  $mark . "\n";
						$buffer .="</div>\n";
						break;
					
				}
				
			}
			
		}else{
			
			$mark = ! $tooltip && $mark ? ' <span class="m4j_required pfmRequired pfmRequiredSingleNoQuestion" style="padding-left: 5px;">*</span>'  : $mark;
			
			$_class = !$this->slot[$slot]->direction ? ' pfmFullRange' : '';
			$buffer = '<div class="pfmElement pfmMargin pfmSingle'.$_class.'" id="m4je-'.$eid.'" >' ."\n";

			$buffer .= $tooltip.    "\n";
			$buffer .= '<div class="pfmField">' . $formString .  '</div>' .  $mark . "\n";
			$buffer .="</div>\n";
		}
	
		
		
	
		if(isset($this->position[(int) $slot])) {
			$this->position[(int) $slot][] = $buffer;
		}
	}
	/**
	 *
	 * @param int $slot
	 * @param string $html
	 * @param int $eid
	 * @param MFormElement $element
	 */
	public function addHTMLRow($slot=1, $html= null,$eid = null, & $element = null){
		$buffer = '<div class="pfmHTML" id="m4je-'.$eid.'">'. $html . '</div>' . "\n";
		if(isset($this->position[(int) $slot])) {
			$this->position[(int) $slot][] = $buffer;
		}
	}
	
	// Alias for addHTMLRow
	public function addDisplayOnlyRow($slot = 1, $html = null, $eid = null){
		$this->addHTMLRow($slot, $html,$eid);
	}
	
	public function render($print=false){
		$buffer = '<div class="pfmTemplate" style="width: '.$this->data->layoutWidth . $this->data->layoutWidthUnit.';">' . "\n";

		foreach ($this->data->rows as $row){
			$buffer .= '<div class="pfmRow">' . "\n";
			foreach ($row as $section){
				$fieldset  = (bool) $section->useFieldset ;
				$legend = '';
				if( $fieldset && $section->legend){
					$legend = '<legend>' . $section->legend . '</legend>';
				}
				
				$span = 'pfmSpan' . $section->span;
				$height = $section->height ? ' style="height: ' . $section->height . 'px;" ' : '';
				$buffer .= '<div class="pfmSlot '.$span.'"'.$height.' data-span="'.$section->span.'">' . "\n";
				$buffer .= $fieldset ? '<fieldset>' .$legend  : '';
				
				$buffer .= isset($this->position[ (int) $section->slot]) ? implode("\n", $this->position[ (int) $section->slot]) : '&nbsp;';

				$buffer .= $fieldset ? '</fieldset>' : '';
				$buffer .= '</div>' . "\n";
			}
			
			$buffer .= '</div>' . "\n";
		}
		
		
		
		$buffer .= '</div>' . "\n";

		if(! $print) return $buffer;
		echo $buffer;
		return true;
	}
	
}
