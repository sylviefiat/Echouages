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
    $name = JRequest::getString('name','layout01');
    $id = JRequest::getInt('lid',null);
    
    if($id==-1) $id = null;
    
    $layout = MLayoutList::get($name);
    if($id){
    	$currentLayout = MLayoutList::getLayoutById($id);
    	$layout->addData($currentLayout->getData());
    	
    }
    $root = M4J_HTTP_LAYOUT.$layout->getName()."/";
    
    $tableLeftCol = "150px";
    
    $slots = (int) $layout->getParameter("positions");
    echo '<input type="hidden" name="slots" id="slotCount" value="'.$slots.'"></input>'."\n";
        
    for($t=1; $t< ($slots+1); $t++){ 
?>
	<div style="display: block; width:100%; float:left;" >
		<div class="m4jLayoutSlot"  style="margin-right: 10px; ">
		<img src="<?php echo $root."slot".$t.".png"; ?>" border="0" align="top" ></img>
		</div>
		<span class="m4jSlotHeadinng">Position <?php echo $t;?></span>
	</div>
	
	<div style="display: block; width:100%; float:left; padding-left: 10px; margin-top:10px;">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" style="margin-bottom: 10px;"><tbody>
			<tr>
				<td valign="top" align="left" width="18px"><?php echo HTML_HELPERS_m4j::image("fieldset.png"); ?></td>
				<td valign="top" align="left" width="<?php echo $tableLeftCol; ?>"><?php echo M4J_LANG_USE_FIELDSET; ?></td>
				<td valign="top" align="left"><?php echo MForm::specialCheckbox("use_fieldset[".$t."]",(int) $layout->getValue($t,"use_fieldset"));?></td>
			</tr>
		
			<tr>
				<td valign="top" align="left" width="18px"><?php echo HTML_HELPERS_m4j::image("width.png"); ?></td>
				<td valign="top" align="left" width="<?php echo $tableLeftCol; ?>"><?php echo M4J_LANG_WIDTH; ?></td>
				<td valign="top" align="left"><input style="width:190px;" type="text" name="width[<?php echo $t;?>]" value="<?php echo $layout->getValue($t,"width"); ?>"></input> <b>px</b></td>
			</tr>
		
			<tr>
				<td valign="top" align="left" width="18px"><?php echo HTML_HELPERS_m4j::image("height.png"); ?></td>
				<td valign="top" align="left" width="<?php echo $tableLeftCol; ?>"><?php echo M4J_LANG_HEIGHT; ?></td>
				<td valign="top" align="left"><input style="width:190px;" type="text" name="height[<?php echo $t;?>]" value="<?php echo $layout->getValue($t,"height"); ?>"></input> <b>px</b></td>
			</tr>
		
		
			<tr>
				<td valign="top" align="left" width="18px"><?php echo HTML_HELPERS_m4j::image("legend.png"); ?></td>
				<td valign="top" align="left" width="<?php echo $tableLeftCol; ?>"><?php echo M4J_LANG_LEGEND_NAME; ?></td>
				<td valign="top" align="left"><input style="width:220px;" type="text" name="legend[<?php echo $t;?>]" value="<?php echo MReady::_($layout->getValue($t,"legend")); ?>"></input></td>
			</tr>
			
			<tr>
				<td valign="top" align="left" width="18px"><?php echo HTML_HELPERS_m4j::image("align-left.png"); ?></td>
				<td valign="top" align="left" width="<?php echo $tableLeftCol; ?>"><?php 
					$forPosition = sprintf(M4J_LANG_FOR_POSITION, $t);
				
					echo M4J_LANG_LEFT_COL; 
					echo HTML_HELPERS_m4j::info_button(M4J_LANG_Q_WIDTH.$forPosition);?></td>
				<td valign="top" align="left"><input style="width:190px;" type="text" name="left[<?php echo $t;?>]" value="<?php echo $layout->getValue($t,"left"); ?>"></input> <b>px</b></td>
			</tr>
			
			<tr>
				<td valign="top" align="left" width="18px"><?php echo HTML_HELPERS_m4j::image("align-right.png"); ?></td>
				<td valign="top" align="left" width="<?php echo $tableLeftCol; ?>"><?php 
					echo M4J_LANG_RIGHT_COL; 
					echo HTML_HELPERS_m4j::info_button(M4J_LANG_A_WIDTH.$forPosition);
				?></td>
				<td valign="top" align="left"><input style="width:190px;" type="text" name="right[<?php echo $t;?>]" value="<?php echo $layout->getValue($t,"right"); ?>"></input> <b>px</b></td>
			</tr>
			
		</tbody></table>
		
		
	</div>



<?php }//EOF slot loop?>