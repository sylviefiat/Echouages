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

global $m4jConfig_live_site, $helpers;

$document = JFactory::getDocument();
$document->addStyleSheet(M4J_CSS_RESPONSIVE_WHIZZARD);


$yes_query ="";
$no_query = "";
if ($use_help==1) $yes_query = 'selected="selected"'; else $no_query = 'selected="selected"';
?>
<form id="m4jForm" name="m4jForm" method="post"
	action="<?PHP M4J_FORM_RESPONSIVE.M4J_HIDE_BAR.M4J_REMEMBER_CID_QUERY ?>">
	<?PHP M4J_LANG_TEMPLATE_NAME ?>
	<div
		style="display: block; width: 100%; clear: both; margin-bottom: 5px; margin-top: -5px;">

		<div
			style="display: block; float: left; width: 33%; margin-right: 2%;">
			<?php echo M4J_LANG_TEMPLATE_NAME; ?><br /> <input name="name"
				type="text" id="name" maxlength="80" style="width: 100%"
				value="<?PHP echo $name; ?>" />
		</div>

		<div style="display: block; float: left; width: 63%;">
			<?PHP echo M4J_LANG_TEMPLATE_DESCRIPTION; ?><br /> <input
				name="description" value="<?PHP echo $desc; ?>" id="description"
				style="width: 100%;" type="text"></input>
		</div>
		<div class="m4jCLR"></div>
	</div>
	<div class="m4jCLR"></div>
	<div style="margin-top: 10px;">
		<img src="<?php echo M4J_IMAGES; ?>tooltip-icon.png" border="0"
			align="top" style="float: left; margin-right: 4px;"></img> <span
			style="float: left; margin-right: 10px; padding-top: 2px;">
			<?php HTML_HELPERS_m4j::info_button(M4J_LANG_USE_HELP, true);?>
			<?PHP // echo M4J_LANG_USE_HELP; ?>
			</span>
			<?php echo MForm::specialCheckbox("use_help",(int) $use_help);?>
	</div>
	<div class="m4jCLR"></div>
	<h1><?php echo M4J_LANG_LAYOUT;?></h1>

	<ul id="spanSelect">
		<li><span info="<?php echo M4J_LANG_ONLYPRO_DESC; ?>"><b>+</b> <?php echo M4J_LANG_ADD_ROW; ?></span>
			<ul>
			<?php 
			for ($t=1; $t< 13; $t++){
				echo '<li info="'. M4J_LANG_ONLYPRO . '">'.sprintf(M4J_LANG_XCOLUMNS_ROW, $t).'</li>';
			}?>
			</ul>
		</li>
	</ul> 
	
		<label for="layout_width" style="display:inline-block; margin-left: 10px;"><?php echo M4J_LANG_LAYOUTWIDTH; ?>: </label>
		<input type="text" value="100%" name="layout_width" id="layoutWidth" style="width: 80px;"
			onchange="RLayout.setLayoutWidth();"
			onkeypress="return RLayout.validateNumbers(event);"></input>
		<select id="layoutWidthUnit" style="width: 70px; margin:0;" onchange="RLayout.setLayoutWidth();">
			<option value="%">%</option>
			<option value="px">px</option>
		</select>
		
<br/><br/>
	<div class="whizzardWrap">

		<div class="responsiveWhizzard" id="whizzard">

			<script type="text/javascript">
				//Rendering the pattern
				var buffer = '<div class="pattern">';
				var rows = 24;
				for(var t=0; t< rows-1; t++){
					buffer += '<div class="dark"></div>';
				}	
				buffer += '<div class="dark" style="margin-right: 0;"></div><div class="light"></div></div>';	
				document.write(buffer);
			</script>

			<div id="layoutContainer" class="layoutContainer">


				<div id="lastClear" class="m4jCLR"></div>
			</div>


		</div>

	</div>
	
	
	
	<input type="hidden" id="json" name="json" value="<?php echo $json ? htmlentities($json, ENT_COMPAT, "UTF-8") : ''; ?>"></input>
	
	<input name="apply" type="hidden" id="apply" value="0" /> 
	<input name="task" type="hidden" id="task" /> 
	<input name="editID" type="hidden" id="editID" value=<?PHP echo $id; ?> />
</form>

<div id="hidePane"></div>

<div id="editAnimPane"></div>


<div id="editPane">
	<img style="position: absolute; display:block; top:-16px; right:-16px; cursor: pointer;" src="components/com_proforms/images/close.png" onclick="RLayout.closeEdit();" />
	
	<h1><?php echo M4J_LANG_SLOTID; ?>:<span id="slotId"></span><span style="float:right;"><?php echo sprintf(M4J_LANG_SLOT_CONTAINS_ELMENTS, '<span id="containingElements" style="">0</span>');?></span></h1>
	<div style="margin-bottom: 10px;"><img  src="components/com_proforms/images/info.png" align="left"  style="padding-right: 10px;"/> <span>The slot id is an internal and unique counter!</span> </div>
	<div class="content">
	 <div class="editRow">
		<label><?php echo M4J_LANG_SLOTTITLE?>: </label>	
		<input id="slotTitle" value="" onchange="RLayout.setSlotTitle(this);"></input>
	</div>
	
	<div class="editRow">
		<label><img border="0" src="components/com_proforms/images/fieldset.png" /><?php echo M4J_LANG_USE_FIELDSET;?></label>	
		<div class="special" id="wrapUseFieldset"><?php echo MForm::specialCheckbox("slotUseFieldset",0,"m4jToggleActive",0,"RLayout.useFieldset();"); ?></div>
	</div>
	
	<div class="editRow">
		<label><img border="0" src="components/com_proforms/images/legend.png" /><?php echo M4J_LANG_LEGEND_NAME; ?> </label>	
		<input id="slotLegendName" value="" onchange="RLayout.setLegend(this);"></input>
		<div class="hide" id="hideInputLegend"></div>
	</div>
	
	<div class="editRow">
		<label><?php echo M4J_LANG_FIELD_ARRANGEMENT; ?>: </label>	
		<div class="special" id="wrapArrangement"><?php echo MForm::specialCheckbox("slotFieldArrangement",0,"m4jToggleArrangement",1,"RLayout.arrangement();"); ?></div>
	</div>
	
	<div class="editRow">
		<label><img border="0" src="components/com_proforms/images/align-left.png" /><?php echo M4J_LANG_LAYOUT_WIDTH_QUESTIONS; ?> </label>	
		<input id="slotWidthQuestions" 
				maxlength="3" 
				style="width: 80px;" 
				value="33" 
				onchange="RLayout.setQuestionsWidth(this);"
				onkeypress="return RLayout.validateNumbers(event);" ></input><span><b>%</b></span>
		
		<div class="hide" id="hideInputDivision1" style="display: none;"></div>
	</div>
	
	<div class="editRow">
		<label><img border="0" src="components/com_proforms/images/align-right.png" /><?php echo M4J_LANG_LAYOUT_WIDTH_FIELDS; ?> </label>	
		<div id="slotDivisionFields" class="wrap" style="font-weight: bold;" >77 % </div>
		<div class="hide" id="hideInputDivision2" style="display: none;"></div>
	</div>
	
	<div class="editRow">
		<label><img border="0" src="components/com_proforms/images/height.png" /><?php echo M4J_LANG_HEIGHT; ?> </label>
		<input id="slotHeight" style="width: 80px;"  value="" onchange="RLayout.setHeight(this);" onkeypress="return RLayout.validateNumbers(event);"></input><b>px</b>		
	</div>
	
	
	<div class="editRow">
		<label><img border="0" src="components/com_proforms/images/height.png" /><?php echo M4J_LANG_MINHEIGHT; ?> </label>
		<input id="slotMinHeight" style="width: 80px;"  value="" onchange="RLayout.setMinHeight(this);" onkeypress="return RLayout.validateNumbers(event);"></input><b>px</b>	
	</div>
	
	<div class="setValues"  onclick="RLayout.closeEdit();" ><?php echo  M4J_LANG_CLOSE; ?></div>
	
		<div class="m4jCLR"></div>
	</div>
	
</div>



<script type="text/javascript">
	var errorNoName = "<?php echo M4J_LANG_TEMPLATE_NAME_REQUIRED; ?>";
	mText.questions = "<?php echo M4J_LANG_QUESTION; ?>";
	mText.fields = "<?php echo M4J_LANG_FIELD_ITEM; ?>";
	mText.askdelete = "<?php echo M4J_LANG_ASK_DELETE; ?>";
	mText.contains = "<?php echo M4J_LANG_SLOT_CONTAINS_ELMENTS; ?>";
	mText.height = "<?php echo M4J_LANG_HEIGHT; ?>";
	mText.minHeight = "<?php echo M4J_LANG_MINHEIGHT; ?>";


</script>

<script type="text/javascript"
	src="<?php echo M4J_JS . 'responsivelayout.js' ?>"></script>

	
	