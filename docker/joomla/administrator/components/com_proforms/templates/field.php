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
 * FIELD TEMPLATE
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

define( "M4J_FIELDSECTIONS" , M4J_TEMPLATES . 'fieldsections/'  );
?>

<!-- START OF FORM -->
<form id="m4jForm" name="m4jForm" method="post"	action="" onsubmit="return true;">
<input type="hidden" name="slot" value="<?php echo $model->slot; ?>" ></input>
<input type="hidden" name="responsive_slot" value="<?php echo $model->responsive_slot; ?>" ></input>
<input type="hidden" name="eid" value="<?php echo $model->eid ? $model->eid : null; ?>" ></input>
<input type="hidden" name="fid" value="<?php echo $model->fid; ?>"></input>
<input type="hidden" name="send" value="1" ></input>

<div class="fieldsWrap"  style="min-height: 500px;">

<div class="pfmClearfix" style="display:block; width: 100%; height: 80px"></div>

<?php if( $model->getIni("interface.head", false) ) include M4J_FIELDSECTIONS . 'main.php'; ?>

<?php if( $model->getIni("interface.split", false) || $model->getIni("interface.options", false) ):?>
<div class="split">
<?php endif;?>

<?php if($model->getIni("interface.width", false)):?>
<fieldset>
<legend><small><?PHP echo M4J_LANG_OPTICAL_ALIGNMENT; ?></small></legend>
	<div class="pfmRow">
	<label class="labelSize"><?PHP echo M4J_LANG_WIDTH ?> <?PHP echo HTML_HELPERS_m4j::info_button(M4J_LANG_ITEM_WIDTH_LONG); ?></label>
		<input name="width" type="text" style="width: 60px;" value="<?PHP echo $model->getParam("width");?>"></input>
	</div>
</fieldset>
<?php endif;?>

<?php include $model->includePath;?>

<?php if( $model->getIni("interface.split", false)|| $model->getIni("interface.options", false) ):?>
</div>
<div class="split pfmClearfix">
<?php 
	if( $model->getIni("interface.options", false) ){
		include M4J_FIELDSECTIONS. "options.php";
	}else{		
		if( $model->includePath2 ) include $model->includePath2;	
	}
?>	
</div>
<?php endif;?>

</div>

</form>
<!-- END OF FORM -->

<?php if($model->isEdit() && $model->ini->convert && !$model->isUsermail):?>
<div id="converter">
	<div style="padding: 10px; text-align: left;">
	<div style="font-size: 16px; font-weight: bold; margin-bottom: 4px; clear: both;"><?php echo M4J_LANG_CONVERTTO; ?>:</div>
	
	 	<?php foreach($model->ini->convert as $form):?>
		<a href="<?php echo M4J_FIELD . '&task=convert&eid='. $model->eid .'&slot=' . $model->slot . '&fid=' . $model->fid . '&to='. $form ;?>" class="convertFormElement"><img src="components/com_proforms/images/element-icons/<?php echo $form; ?>.png"><?php echo PText::field($form);?></a>
	 	<?php endforeach;?>
	</div>
</div>
<?php endif;?>

<div class="pfmClearfix" style="display:block; width: 100%; height: 52px"></div>

<div class="fieldHeader">

<h3 style="padding:0; margin:0; margin-bottom: 10px;"><?php 
	if($model->isEdit()){
		echo M4J_LANG_EDIT_ELEMENT . '<span class="m4j_green">' .$model->layoutData->name . '</span>'; 
	}else{
		echo M4J_LANG_NEW_ELEMENT_LONG . '<span class="m4j_green">' .$model->layoutData->name . '</span>';		
	}
?></h3>
<div style="vertical-align: middle;">
	<img class="elementIcon" 
    		 src="components/com_proforms/images/element-icons/<?php echo $model->data->form; ?>.png" />
    <span><?php echo $m4j_lang_elements[$model->data->form]; ?></span>
    <?php if($model->isEdit() && $model->getIni("convert", false) && !$model->isUsermail):?>
    <span>&gt;</span>
    <a class="convert" onclick="mWindow.fromNode('converter',{width: 480, height: 240}); return false;"><?php echo M4J_LANG_CONVERTTO; ?> ...</a>
    <?php endif;?>
</div>


</div>


<div class="fieldActionArea">
	<div class="cancelButton" onclick="window.parent.mWindow.close();"><?php echo M4J_LANG_CANCEL; ?></div>
	<div class="saveButton" onclick="dojo.byId('m4jForm').submit();"><?php echo M4J_LANG_SAVE; ?></div>
</div>
<?php renderEndScripts();?>
