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

include_once (M4J_INCLUDE_EDIT_AREA);
  $GLOBALS['editArea'] = new EditArea();

addScriptAtEnd(M4J_JS."options.js");
addScriptDeclarationAtEnd("
	OptManager.useValues = $use_values;
");

// $options = (substr($options, -1)== ";") ? substr($options, 0, -1) : $options;

// $options = explode(";",$options);
// $values = explode(";",$values);
$count = 0;
?>
	<div style="float:left; display:block;  width: 339px; margin-bottom: 10px;">
		<?php 
		$dataType = array(
				array("val" => "0","text" => M4J_LANG_OPTIONS_DATA_TYPE_MANUAL),
				array("val" => "1","text" => M4J_LANG_OPTIONS_DATA_TYPE_SQL)
		);
		echo MForm::select("options_data_type",$dataType,$options_data_type,MFORM_DROP_DOWN,null,'id="dataType" onChange="javascript: OptManager.toggleDataType(this.value);" style="width:100%;"');
		
		?>
	</div>
	<div class="m4jCLR"></div>

	<div style="float:left; display:block;  width: 339px; margin-bottom: 10px;">
		<span style="float:left; display:block; height: 17px; line-height: 17px; font-size: 14px;"><?php echo M4J_LANG_USEVALUES; ?><img src="<?php echo M4J_IMAGES; ?>info.png" border="0" align="top" style="margin-left:4px;" info="<?php echo M4J_LANG_USEVALUES_DESC; ?>" />: </span>
		<span style="float:left; margin-left:10px;">
		<?php echo MForm::specialCheckbox("use_values",(int) $use_values , "m4jToggleActive", 0 , "OptManager.toggleUseValues();"); ?>
		</span>
	</div>
	<div class="m4jCLR"></div>
	
<script type="text/javascript"> var isOptionsDataType = <?php echo (int) $options_data_type; ?>; </script>
<!-- START SQL WRAP -->

<div id="sqlWrap" style="position: absolute; display:block; text-align: left; width: 100%; min-height: 650px;  margin-bottom: 10px;">
	<span style="font-size: 12px; color: red; font-weight: bold;"><?php echo M4J_LANG_OPTIONS_SQL_WARNING; ?></span>
	<span><?php echo M4J_LANG_OPTIONS_SQL_DESC; ?></span>
	<pre class="examples">SELECT  `username` AS `text`, 
	`email` AS `value` 
FROM `#__users` 
WHERE 1;</pre>
	
	<pre class="examples">SELECT  `username` AS `text`, 
	`username` AS `value` 
FROM `#__users` 
WHERE 1;</pre>
	
	<?php  echo MForm::codeEditor("sql",$sql, 'SQL','m4jSubmitCode','width:100%; height:300px;' ); ?>
	
</div>
<div class="m4jCLR"></div>
<!-- EOF SQL WRAP -->

<!-- START MANUAL WRAP -->
<div id="manualWrap" style="display:block;">
	
	<a  class="m4jSelect unselectable" style="float:left; width: 100px; margin-bottom: 10px; margin-left: -5px;" onclick="javascript: OptManager.add(); return false;" info="<?php echo M4J_LANG_ADD_OPTION_DESC; ?>">
		<div class="m4jSelectExtend" style="font-size:12px; padding-top: 3px; text-align:left;"><?php echo M4J_LANG_ADDOPTION; ?></div>
	</a>
	
	<a  class="m4jSelect unselectable" style="float:left; width: 100px;  margin-bottom: 10px;" onclick="javascript: OptManager.feed(); return false;" info="<?php echo M4J_LANG_FEED_OPTIONS_DESC; ?>">
		<div class="m4jSelectExtend" style="font-size:12px; padding-top: 3px; text-align:left;"><?php echo M4J_LANG_FEED_OPTIONS; ?></div>
	</a>
	
	<a  class="m4jSelect m4jSelectDisabled unselectable" style="float:left; width: 60px;margin-bottom: 10px;" onclick="javascript: OptManager.empty(); return false;">
		<div class="m4jSelectExtend" style="font-size:12px; padding-top: 3px; text-align:left;"><?php echo M4J_LANG_TRUNCATE; ?></div>
	</a>
	
	<div class="m4jCLR"></div>
	
	<div id="optionsRootNode" class="unselectable">

<?php foreach($options as $option){ ?>

	<div class="optionsWrap unselectable" >
		<div>
			<table cellspacing="0" cellpadding="0" border="0" style="width: 290px;">
				<tr>
					<td align="right" valign="middle" style="height:27px; padding-right:5px;"><?php echo M4J_LANG_TEXT; ?>: </td>
					<td align="left" valign="middle"><input class="optionInput" value="<?php echo $option; ?>" type="text" name="options[]" style="width:220px;" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0);"></input></td>
					<td align="right" valign="middle" style="width:20px;">
						<img class="selectable" src="<?php echo M4J_IMAGES?>remove.png" border="0" style="cursor:pointer;" onclick="javscript: OptManager.remove(this);" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0);"/>
					</td>
				</tr>
				<tr>
					<td align="right" valign="middle" class="optionValuesInputText" <?php echo $use_values ? "" : 'style="color: #888;"'; ?>><?php echo M4J_LANG_VALUE; ?>: </td>
					<td align="left" valign="middle"><input <?php echo $use_values ? "" : 'disabled'; ?> class="optionInput optionValuesInput"  style="width: 220px; " value="<?php
					if(isset($values[$count])){
						echo $values[$count++]; 
					}else{
						$count++;
					} 
						 
					 ?>" type="text" name="values[]" style="width:220px;" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0); "></input></td>
					<td align="right" valign="middle">
						<img class="selectable" src="<?php echo M4J_IMAGES?>copy.png" border="0"  style="cursor:pointer;"  onclick="javscript: OptManager.copy(this);" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0);" />
					</td>
				</tr>
			</table>	
		</div>
	</div>

<?php } ?>


	</div>

<div class="m4jCLR"></div>
</div>
<!-- EOF MANUAL WRAP -->

<div id="optionsFactoryNode" style="border: 1px solid red;">
	<div>
		<table cellspacing="0" cellpadding="0" border="0" style="width: 290px;">
			<tr>
				<td align="right" valign="middle" style="height:27px; padding-right:5px;"><?php echo M4J_LANG_TEXT; ?>: </td>
				<td align="left" valign="middle">
					<input class="optionInput" value="{textvalue}" type="text" name="options[]" style="width:220px;" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0);"></input>
				</td>
				<td align="right" valign="middle" style="width:20px;">
					<img src="<?php echo M4J_IMAGES?>remove.png" border="0" style="cursor:pointer;" onclick="javscript: OptManager.remove(this);" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0);"/>
				</td>
			</tr>
			<tr>
				<td align="right" valign="middle" class="optionValuesInputText" {textdisabled} ><?php echo M4J_LANG_VALUE; ?>: </td>
				<td align="left" valign="middle">
					<input {inputdisabled} class="optionInput optionValuesInput"  value="{valuevalue}" type="text" name="values[]" style="width:220px;" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0); "></input>
				</td>
				<td align="right" valign="middle">
					<img src="<?php echo M4J_IMAGES?>copy.png" border="0"  style="cursor:pointer;"  onclick="javscript: OptManager.copy(this);" onmouseover="javascript: OptManager.setBubble(1); " onmouseout="javascript: OptManager.setBubble(0);" />
				</td>
			</tr>
		</table>	
	</div>
</div>


<div id="optionsFeedTemplate" style="display:none;">
	<div style="margin:0; padding:10px">
		<span style="font-size: 18px; font-weight: bold;"><?php echo M4J_LANG_FEED_OPTIONS; ?></span><br/>
		<?php echo M4J_LANG_FEED_OPTIONS_DESC;?><br/><br/>
		<span style="font-size: 14px; font-weight: bold;"><?php echo M4J_LANG_FEED_PARSE_TYPE; ?></span><br/>
		<?php 
		$parsingTypes = array(
				array("val" => "0","text" => M4J_LANG_FEED_SINGLE),
				array("val" => "1","text" => M4J_LANG_FEED_SINGLE_SEMICOLON),
				array("val" => "2","text" => M4J_LANG_FEED_MULTI),
				array("val" => "3","text" => M4J_LANG_FEED_MULTI_COMMA)
		);
		echo MForm::select("feedparsingtype",$parsingTypes,null,MFORM_DROP_DOWN,null,'id="feedParsingType" onChange="javascript: OptManager.feedType = this.value;" style="width:100%;"');
		?>
		<span style="font-size: 14px; font-weight: bold;"><?php echo M4J_LANG_FEED_ADD_TYPE; ?></span><br/>
		<?php 
		$addingTypes = array(
				array("val" => "0","text" => M4J_LANG_ADD),
				array("val" => "1","text" => M4J_LANG_REPLACE)
		);
		echo MForm::select("feedaddingtype",$addingTypes,null,MFORM_DROP_DOWN,null,'id="feedAddingType" onChange="javascript: OptManager.feedAddingType = this.value;" style="width:100%;"');
		?>
		<br/><br/>
		<span style="font-size: 14px; font-weight: bold;"><?php echo M4J_LANG_LIST; ?></span><br/>
		<textarea id="FEEDREPLACE" style="display:inline-block; margin:0; width:760px; height: 300px;"></textarea>
		
		<a  class="m4jSelect unselectable" style="float:right; width: 100px; margin-top: 10px; margin-bottom: 10px;" onclick="javascript: OptManager.feedProcess(); return false;">
		<div class="m4jSelectExtend" style="font-size:12px; padding-top: 3px; text-align:left;"><?php echo M4J_LANG_GENERATE; ?></div>
	</a>
		
		
	</div>
</div>

<?php if(_M4J_IS_J30):?>
<script type="text/javascript">
<!--
	dojo.addOnLoad(function(){
		dojo.byId("feedAddingType").style.display = "inline-block";
		var bsAdd = dojo.byId("feedAddingType_chzn");
		if(bsAdd) bsAdd.parentNode.removeChild(bsAdd);
		
		dojo.byId("feedParsingType").style.display = "inline-block";	
		var bsParse = dojo.byId("feedParsingType_chzn");
		if(bsParse) bsParse.parentNode.removeChild(bsParse);
		
	});
//-->
</script>

<?php endif;?>

