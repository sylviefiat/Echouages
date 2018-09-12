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

/**
 * DEFAULT TEMPLATE FOR FORM ITEMS
 */

/* @var $model ProformsAdminModelFormitems */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$document = JFactory::getDocument();
$document->addScript(M4J_JS . 'formitems.js');
$document->addScriptDeclaration('
		var formItemsURL = "'.$reloadURL.'"; 
		var newFormItemURL = "'. M4J_FIELD . "&fid=$id".'";
		
		');

// MDebug::pre($model->slots);

$app = JFactory::getApplication();
$_selection = $app->getUserState('ProformsFormItemsRememberSelection_' . $id, null);
if($_selection){
	$app->setUserState('ProformsFormItemsRememberSelection_' . $id, null);
	$document->addScriptDeclaration('
		var remberSelection = '.json_encode($_selection).';
		');
}else{
	$document->addScriptDeclaration('
		var remberSelection = null;
		');
}

$firstSlot =  $showSlot ? 0 : 1;

$trCounter = 1;
HTML_m4j::head(M4J_FORM_ITEMS);
?>
<div class="m4jSlotWrap" id="slotWrap" data-slot-count="<?php echo sizeof($model->slots);?>"> 
<div id="tabArrowLeft"></div>
<div id="tabArrowRight"></div>

<div class="innerWrap" id="m4jSlots">
<?php foreach($model->slots as & $slot):?>
<?php 
$_slot = isset($slot->order) ? $slot->order : $slot->slot;
$_responsive_slot =  isset($slot->order) ? $slot->slot : null;
?>
	<div class="m4jSlot<?php if($firstSlot || $_slot == $showSlot){ $firstSlot = 0; echo ' activeSlot'; }?>" 
		 data-slot="<?php echo $_slot; ?>"  
		 data-responsive-slot="<?php echo $_responsive_slot;  ?>"
		
		 data-top-info="<?php echo htmlentities(M4J_LANG_DROPHERETOMOVE); ?>"		 
		 data-top-info-stop="1"	
		 <?php if(isset($slot->slotTitle)) echo 'info="' . htmlentities($slot->slotTitle, ENT_COMPAT, "UTF-8") . '"' ;?>
		 >
		
			<?php  if(isset($slot->image)):?>
			<div class="m4jLayoutSlot"><img src="<?php echo $slot->image; ?>" /></div>
			<?php else:?>
			<span class="slotcount"></span>
			<?php endif;?>
		
	</div>
<?php endforeach;?>
</div></div>
<form id="m4jForm" name="m4jForm" method="post"	action="" onsubmit="return FormItems.checkForm();">
<input type="hidden" name="batch" value="0" id="proformsBatch"></input>
<input type="hidden" name="batch_verify" value="" id="proformsBatchVerify"></input>

<input type="hidden" name="task" value="0" id="proformsTask"></input>
<input type="hidden" name="slot" value="0" id="proformsSlot"></input>
<input type="hidden" name="responsive_slot" value="0" id="proformsResponsiveSlot"></input>
<input type="hidden" name="destination" value="0" id="proformsDestination"></input>
<input type="hidden" name="responsive_destination" value="0" id="proformsResponsiveDestination"></input>
<input type="hidden" name="eid" value="" id="proformsEid"></input>
<table class="proformsTable" id="m4jTableList" style="margin-top: -1px;">
<thead>
  <tr>
    <th style="width: 29px;"></th>
  	<th class="buttonWidth"><input type="checkbox" id="selectAll"></input></th>
    <th class="buttonWidth"><?php echo M4J_LANG_ACTIVE; ?></th>
    <th class="buttonWidth"><?php echo M4J_LANG_REQUIRED; ?></th>
    <th style="width: 29px;"></th>
    <th><?php echo M4J_LANG_QUESTION; ?></th>
    <th style="width: 90px;"><?php echo JText::_("alias");?></th>
    <th style="width: 25px;">eid</th>
    <th class="buttonWidth"></th>
    <th class="buttonWidth"></th>
    <th class="buttonWidth"></th>
  </tr>
 </thead>
 <tbody class="" id="proformsTbody">
<?php 
	foreach($model->slots as & $slot){
		$slotId = isset($slot->order) ? $slot->order : $slot->slot;
        $_responsive_slot =  isset($slot->order) ? $slot->slot : null;
		foreach($slot->elements as & $el){
		$requiredButton = $el->usermail ?  HTML_HELPERS_m4j::usermail_button() : HTML_HELPERS_m4j::required_button(M4J_FORM_ITEMS,$id,$el->required,1,$el->eid, "return FormItems.setRequired(this);");
?>
<tr id="elTr<?php echo $el->eid;?>" 
	data-slot="<?php echo $slotId; ?>" 
	data-responsive-slot="<?php echo $_responsive_slot;  ?>"
	data-eid="<?php echo $el->eid; ?>" >

    <td style="width: 29px;" class="first">
    	<img class="moveIcon" 
    		 src="components/com_proforms/images/move.png" 
    		 title="<?php echo M4J_LANG_POSITION; ?>"	 
			 data-eid="<?php echo $el->eid; ?>"  />
    </td>
	<td class="buttonWidth">
		<input class="elementSelector" 
			   id="elementSelector<?php echo $el->eid; ?>"
			   type="checkbox" 
			   value="<?php echo $el->eid; ?>" 
			   name="selection[]" 
			   data-slot="<?php echo $slotId; ?>" 
	           data-responsive-slot="<?php echo $_responsive_slot;  ?>"
			   data-eid="<?php echo $el->eid; ?>"
			   data-tr="elTr<?php echo  $el->eid; ?>"></input></td>
	<td class="buttonWidth"><?php  echo HTML_HELPERS_m4j::active_button(M4J_FORM_ITEMS,$id,$el->active,1,$el->eid, "return FormItems.setActive(this);"); ?></td>
	<td class="buttonWidth"><?php echo $requiredButton; ?></td>
	
    <td style="width: 29px;">
    	<img class="elementIcon" 
    		 src="components/com_proforms/images/element-icons/<?php echo $el->form; ?>.png" 
    		 data-top-info="<?php echo htmlentities(str_replace("(", " (", html_entity_decode($m4j_lang_elements[$el->form]) )); ?>" />
    </td>
	<td style="vertical-align: middle;" >
		<a class="editFormElement" 
		   href="<?php echo M4J_FIELD . '&slot=' . $slotId . '&responsive_slot=' . $_responsive_slot . '&eid='. $el->eid . '&fid='.$id . M4J_HIDE_BAR; ?>"
		   title="<?php echo htmlentities(M4J_LANG_EDIT); ?>"
		   data-eid="<?php echo $el->eid;?>"
		   data-empty="<?php  echo $el->question ? 0 : 1; ?>">
			<?php echo $el->question ? $el->question : '<i>' . M4J_LANG_EMPTY_QUESTION . '</i>'; ?>
		</a>
		</td>
	<td style="width: 90px; vertical-align: middle;" ><div class="aliasWrap" title="<?php echo htmlentities($el->alias, ENT_COMPAT, "UTF-8"); ?>" id="aliasWrap_<?php echo $el->eid; ?>"><?php echo $el->alias; ?></div></td>
	<td style="width: 25px; text-align: center;"><?php echo $el->eid; ?></td>
	<td class="buttonWidth"><?php echo HTML_HELPERS_m4j::copy_button(M4J_FORM_ITEMS .'&slot=' . $slotId,$id,M4J_LANG_DO_COPY,M4J_HIDE_BAR,$el->eid, "return true; FormItems.copy(this);"); ?></td>
	<td class="buttonWidth"><?php echo HTML_HELPERS_m4j::element_delete_button(M4J_FORM_ITEMS,$id,$el->eid,  "return FormItems.remove(this);"); ?></td>
	<td class="buttonWidth last">
		<a class="editFormElement" 
		   href="<?php echo M4J_FIELD . '&slot=' . $slotId . '&responsive_slot=' . $_responsive_slot . '&eid='. $el->eid . '&fid='.$id . M4J_HIDE_BAR; ?>"
		   title="<?php echo htmlentities(M4J_LANG_EDIT); ?>"
		   data-eid="<?php echo $el->eid; ?>">
			<img src="components/com_proforms/images/pen-small.png" alt="<?php echo htmlentities(M4J_LANG_EDIT); ?>" />
		</a>
	</td>
	
</tr>
<?php 
	$trCounter++;
}//EOF foreach elements 
?>
<tr id="endPosition<?php echo $slotId?>"  
    data-slot="<?php echo $slotId; ?>" 
	data-responsive-slot="<?php echo $_responsive_slot;  ?>"
    class="dummyRow" data-eid="" data-top-info="<?php echo M4J_LANG_ENDPOSITION; ?>">
	<td colspan="11"><div> <div class="unselectAll" onclick="FormItems.unselectAll();"><?php echo M4J_LANG_UNSELECTALL; ?></div> </div></td>
</tr> 
<?php }//EOF foreach slot;?>
</tbody>
</table>
</form>

<div id="batchProcessing">
	<div style="padding:10px">
		<div class="batchProcessingHeader"><?php echo M4J_LANG_BATCH_HEADER; ?></div>
		<div class="m4jConfigInfo batchDesc"><?php echo M4J_LANG_BATCH_DESC?></div>
	
		<table class="batchProcessTable">
			<tr>
			<th style="width: 50%"><?php echo M4J_LANG_SELECTED_ITEMS; ?></th>
			<th><?php echo M4J_LANG_PROCESS; ?></th>
			</tr>
			<tr>
			<td style="width: 50%; padding:0; margin:0;">
				<div id="batchSelectedItems">
				
				</div>
			</td>
			<td style="text-align: center;">
				<div class="batchProcessFire" onclick="FormItems.batchProcess('publish');"><?php echo M4J_LANG_ACTIVATE; ?></div>
				<div class="batchProcessFire" onclick="FormItems.batchProcess('unpublish');"><?php echo M4J_LANG_DEACTIVATE; ?></div>
				<div class="batchProcessFire" onclick="FormItems.batchProcess('required');"><?php echo M4J_LANG_SET_REQUIRED; ?></div>
				<div class="batchProcessFire" onclick="FormItems.batchProcess('not_required');"><?php echo M4J_LANG_SET_NOTREQUIRED; ?></div>
				<div class="batchProcessFire" onclick="FormItems.batchProcess('copy');"><?php echo M4J_LANG_COPY; ?></div>
				<div class="batchProcessFire" onclick="FormItems.batchProcess('delete');"><?php echo M4J_LANG_DELETE; ?></div>
			
			</td>
			</tr>
		</table>
	
	
	
	</div>
	
	
	
	
</div>

 <style type="text/css">table#m4jTableList tbody tr{display:none; }</style>

<div id="showElementsStyle"></div>
 
 
 <div id="loadingPane">
 <div class="background"></div>
 <div class="anim">
	 <table>
	 	<tr><td align="center" valign="middle"><img src="components/com_proforms/images/loading2.gif" alt="" /></td></tr>
	 </table>
</div>
 </div>
 
<?php 

if(M4J_SHOW_LEGEND) HTML_m4j::legend('formelements');
HTML_m4j::footer(true);
echo '<script type="text/javascript">';
echo 'var previewLink = "'.(M4J_PREVIEW. (int) $_REQUEST['id']).'";'."\n";
echo 'mText.askDelete = "'.M4J_LANG_ASK_DELETE.'";' . "\n";
echo 'mText.noitemsselected = "'.M4J_LANG_NOITEMSSELECTED.'";';

echo '</script>'."\n";