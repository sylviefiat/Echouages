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

JSText::add(array(
	"errornotemplate"=>M4J_LANG_ERROR_NO_TEMPLATE
));

global $m4jConfig_live_site, $helpers;
?>
<script type="text/javascript">
	var XHRALIAS = "<?php echo M4J_LOAD_XHR;?>falias&fids=";
	
	dojo.addOnLoad(function(){
		var tab = <?php echo $tab; ?>;
		var tabElement = dojo.query( ".tabNo"+tab)[0];
		if(tabElement){
			switch(tab){
			case 0: 
				cTab.set('m4jMainConfig','configTab',tabElement,0);
				break;
			case 1: 
				cTab.set('m4jEmailTab','configTab',tabElement,1);
				break;
			case 2: 
				cTab.set('m4jIntroText','configTab',tabElement,2);
				break;
			case 3: 
				cTab.set('m4jMainText','configTab',tabElement,3);
				break;
			case 4: 
				cTab.set('m4jAfterSending','configTab',tabElement,4);
				break;
			case 5: 
				cTab.set('m4jPaypalTab','configTab',tabElement,5);
				break;
			case 6: 
				cTab.set('m4jCodeTab','configTab',tabElement,6);
				break;
			case 7: 
				cTab.set('m4jOptTab','configTab',tabElement,7);
				break;
			case 8: 
				cTab.set('m4jCustomizeTab','configTab',tabElement,8);
				break;
			}
		}
	});
	
</script>



<form id="m4jForm" name="m4jForm" method="post"
	action="<?PHP echo M4J_JOBS_NEW.M4J_REMEMBER_CID_QUERY.M4J_HIDE_BAR ?>" onsubmit="return evalRequired();">

<input type="hidden" name="tab" value="<?php echo $tab; ?>" id="tabField" ></input>

<div class="m4j_tabs_back"
	style="background-position: 0px 20px; height: 78px;">
<div
	style="display: block; height: 55px; padding-left: 10px; padding-right: 10px;">
<?php HTML_HELPERS_m4j::caption($heading,$feedback,$breadcrumbs);?></div>

<div class="ieJobTabWidth m4jTabLabelWrap">
<div class="tabNo0 m4jActiveTab" id="configTab"
	onclick="cTab.set('m4jMainConfig','configTab',this,0); return false;"><?PHP echo M4J_LANG_MAIN_CONFIG;?><span></span></div>
<div class="tabNo1 ieJobTabWidth40 m4jEmailTab" <?php echo ($process == 1) ? 'style="display:none;"':'';?>
	onclick="cTab.set('m4jEmailTab','configTab',this,1); return false;"><?PHP echo M4J_LANG_EMAIL;?><span></span></div>
<div class="tabNo2 ieJobTabWidth"
	onclick="cTab.set('m4jIntroText','configTab',this,2); return false;"><?PHP echo M4J_LANG_INTROTEXT;?><span></span></div>
<div class="tabNo3 ieJobTabWidth"
	onclick="cTab.set('m4jMainText','configTab',this,3); return false;"><?PHP echo M4J_LANG_MAINTEXT;?><span></span></div>
<div class="tabNo4 ieJobTabWidth"
	onclick="cTab.set('m4jAfterSending','configTab',this,4); return false;"><?PHP echo M4J_LANG_AFTER_SENDING;?><span></span></div>
<div class="tabNo5 ieJobTabWidth40"
	onclick="cTab.set('m4jPaypalTab','configTab',this,5); return false;" style="color:red;" info="<?php echo M4J_LANG_ONLYPRO_DESC; ?>"><?PHP echo M4J_LANG_PAYPAL;?><span></span></div>	
<div class="tabNo6 ieJobTabWidth40"
	onclick="cTab.set('m4jCodeTab','configTab',this,6); return false;" style="color:red;" info="<?php echo M4J_LANG_ONLYPRO_DESC; ?>"><?PHP echo M4J_LANG_CODE;?><span></span></div>
<div class="tabNo7 ieJobTabWidth40"
	onclick="cTab.set('m4jOptTab','configTab',this,7); return false;" style="color:red;" info="<?php echo M4J_LANG_ONLYPRO_DESC; ?>"><?PHP echo M4J_LANG_OPTIN;?><span></span></div>
<div class="tabNo8 ieJobTabWidth40"
	onclick="cTab.set('m4jCustomizeTab','configTab',this,8); return false;"><?PHP echo M4J_LANG_CUSTOMIZE;?><span></span></div>

	
</div>
<div class="m4jCLR"></div>
</div>
<div class="m4jCLR"></div>

<div class="m4jTabWrap " id="configTabWrap">
<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ MAIN TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>
<div class="m4jTabContent" id="m4jMainConfig">

<table width="100%" border="0" cellspacing="0" cellpadding="6">
	<tr>
		<td valign="top" align="left"><?PHP echo M4J_LANG_TITLE_FORM; ?><br />
		<input name="title" type="text" id="title"
			value="<?PHP echo $title; ?>" size="50" maxlength="60"
			style="width: 100%" /></td>

		<td valign="top" align="left" width="300px"><?PHP echo JText::_("alias"); ?><br />
		<input name="alias" type="text" id="alias" style="width: 100%"
			value="<?PHP echo $alias; ?>" maxlength="80" /> <br />
		</td>
		
	</tr>
	<tr>
		<td align="left" valign="top">		
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:-8px;">
			<tr>
				<td align="left" valign="top"><?PHP echo M4J_LANG_ACTIVE; ?><br />
				<?php echo MForm::specialCheckbox("active",(int) $active); ?> <br />
				</td>
				
				<td align="left" valign="top"><?PHP echo M4J_LANG_PROCESS; ?><br />
				<select id="m4jProcess" style="margin-top: 2px; width: 140px;" size="1" name="process">
					<option selected="selected" value="0"><?php echo M4J_LANG_EMAIL; ?></option>
					<option style="color:red;" value="99" info="<?php echo M4J_LANG_ONLYPRO_DESC; ?>"><?php echo M4J_LANG_DATABASE; ?></option>
					<option style="color:red;" value="999" info="<?php echo M4J_LANG_ONLYPRO_DESC; ?>"><?php echo M4J_LANG_EMAIL."+".M4J_LANG_DATABASE; ?></option>
				</select>
				</td>
				
				<td align="left" valign="top">
					<?PHP echo JText::_("Access"); ?><br />
					<?php echo MForm::access($access);   ?>
				</td>
							
				<td align="left" valign="top">
				<div style="color:red;" info="<?php echo M4J_LANG_ONLYPRO_DESC; ?>">
					<?PHP echo M4J_LANG_CONFIRMATION_MAIL; ?>
					<br />
					<?php
					
					$confirmationArray = array(
					array("val" => "0","text" => M4J_LANG_NEVER),
					array("val" => "1","text" => M4J_LANG_EVER),
					array("val" => "2","text" => M4J_LANG_ASK)
					);
					echo MForm::select(
						"confirmation",
						$confirmationArray,
						$confirmation,
						MFORM_DROP_DOWN,
						null,
						'style="margin-top:2px; width: 100px;"'
						);
					?> 
				</div>
				</td>
				
				
			</tr>
		</table>
		</td>
		<td align="left" valign="top">	
			<div style="margin-top:-8px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td align="left" valign="middle"> 
				<img alt="" src="<?php echo M4J_IMAGES?>cat.png" align="top" border="0" style="float:left; margin-top:2px; margin-right:5px;"></img>
				</td>
				
				<td align="left" valign="top" style="width: 250px;">
					<div style="display:block; width:250px;">
					<?PHP echo M4J_LANG_CATEGORY; ?><br />
						
					<?PHP echo HTML_HELPERS_m4j::category_menu($categories,$cid,null,1)?> 
						
					</div>
				</td>
			
			</tr>
			</table>
			
				
			</div>
		</td>
		
		</tr>
		</table>
		
<fieldset class="jobsFieldSet">
	<legend>
		<img alt="" src="<?php echo M4J_IMAGES?>addtemplate.png" align="top" border="0" style="float:left; margin-top:0px; margin-right:5px;">
		<?PHP echo M4J_LANG_TEMPLATE; ?>
	</legend>	
	
	<table width="100%" border="0" cellspacing="10px" cellpadding="10px" style="margin-top: -10px;">
		<tr>
		<!-- +++++++++++++++++SELECTED TEMPLATES +++++++++++++++++++++++++++++ -->
			<td width="50%" valign="top" align="left">
				<div class="m4jSelectionHeading">
					<span><?php echo M4J_LANG_INCLUDED_TEMPLATES; ?>
						<a onclick="javascript: resetTemplate(); return false;" 
						   class="m4jReset" 
						   style="float:right;margin-right:-10px; border-left: 1px solid #898989;" >
						<?php echo M4J_LANG_RESET;?>
						</a>
					</span>
				</div>	
				<div class="m4jSelectionWrap" id="m4jSelectedTemplates">
				<?php 
					$fid2Template = array();
					foreach ($templates as $template){
						$fid2Template[$template->fid] = $template;
					}
					
					$hasUserMail = false;
					foreach ($fid as $formID){
						$tpl = $fid2Template[$formID];
						if($tpl->usermail == 1){
							$hasUserMail = true;
						}
				?>
					<a class="m4jSelect m4jSelected" 
					   id="m4jTemplate_<?php echo (int) $formID; ?>" 
					   fid="<?php echo (int) $formID; ?>"
					   usermail = "<?php echo $tpl->usermail; ?>""
					   onclick="javascript: addTemplate(this); return false;"
					   <?php if($template->description != ""){
							echo ' info="'.$tpl->description.'"';
					   }?>>
						<div class="m4jSelectExtend">
						<?php 
						$tpl->name = HTML_HELPERS_m4j::fitString($tpl->name,45);
						echo $tpl->name;
						if($tpl->usermail==1){
						?>
						<img src="<?php echo M4J_IMAGES?>is_usermail.png" 
							 align="top" 
							 class="usermailImage" 
							 border="0"
							 info = "<?php echo M4J_LANG_USERMAIL; ?>"></img>
						<?php }?>
						</div>
						<span class="m4jValueContainer" id="valueContainer_<?php echo (int) $tpl->fid; ?>"><input type="hidden" value="<?php echo (int) $tpl->fid; ?>" name="fid"></input></span>
					</a>					  				   
					<?php 
					}//eof foreach fid ?>						  				   
				</div>
				<div class="m4jCLR"></div>
				<script type="text/javascript">
				var m4jHasUserMail = <?php echo ($hasUserMail) ? 'true' : 'false'; ?>;
			
				function m4jUserMailPrompt(){
					alert("<?php echo M4J_LANG_ADVICE_USERMAIL_ERROR; ?>");
				}
				</script>
			</td>
			<!-- +++++++++++++++++ AVAILABLE TEMPLATES +++++++++++++++++++++++++++++ -->
			<td width="50%" valign="top" align="left">
				
				<!-- REMOVE THIS -->
				<!-- <input type="hidden" name="fid" value ="<?php echo $template->fid; ?>"></input> -->
				<!-- EOF REMOVE THIS -->
				
			<div class="m4jSelectionHeading"><span><?php echo M4J_LANG_ADD_TEMPLATE; ?></span></div>	
			<div class="m4jSelectionWrap" id="m4jTemplateSelection">	
					<?php 
					
					foreach ($templates as $template){
						if(! in_array($template->fid, $fid)){
					
					?>
					<a class="m4jSelect<?php echo ($hasUserMail && $template->usermail == 1) ? ' m4jSelectDisabled' : '' ?>" 
					   id="m4jTemplate_<?php echo (int) $template->fid; ?>" 
					   fid="<?php echo (int) $template->fid; ?>"
					   usermail = "<?php echo $template->usermail; ?>""
					   onclick="javascript: addTemplate(this); return false;"
					   <?php if($template->description != ""){
							echo ' info="'.$template->description.'"';
					   }?>>
						<div class="m4jSelectExtend">
						<?php 
						$template->name = HTML_HELPERS_m4j::fitString($template->name,45);
						echo $template->name;
						if($template->usermail==1){
						?>
						<img src="<?php echo M4J_IMAGES?>is_usermail.png" 
							 align="top" 
							 class="usermailImage" 
							 border="0"
							 info = "<?php echo M4J_LANG_USERMAIL; ?>"></img>
						<?php }?>
						</div>
						<span class="m4jValueContainer" id="valueContainer_<?php echo (int) $template->fid; ?>"></span>
					</a>					  				   
					<?php 
						}// EOF fid != $template->fid
					}//eof foreach templates?>				  				   
			</div>
			<div class="m4jCLR"></div>	
				
				
			</td>
		</tr>
	</table>
</fieldset>	

<div style="clear:both"></div>
<center><span style="display:inline-block; margin-top: 10px; color:red;"><?php echo M4J_LANG_ONLYONETEMPLATE; ?></span></center>
</div><?php //EOF MAIN TAB?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ EMAIL TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>

<div class="m4jTabContent" id="m4jEmailTab" style="left: -9999em;">

		<div class="m4jConfigInfo" style="margin-top:5px; margin-bottom:10px;"><?PHP echo M4J_LANG_JOBS_EMAIL_INFO; ?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="">
			<tr>
				<td align="left" valign="top" >
				
					<?PHP echo M4J_LANG_EMAIL; ?> <img src="components/com_proforms/images/info.png" info="<?php echo M4J_LANG_EMAIL_FORMAT_DESC; ?>" align="top" /><br />
					<input name="email" type="text" id="email" style="width: 99%"
						value="<?PHP echo $email; ?>"  maxlength="200"/>
					<br />
				</td>
			</tr>
			<tr>
				<td height="10px"> </td>
			</tr>
			<tr>
				<td align="left" valign="top" >
					<?PHP echo M4J_LANG_SUBJECT; ?> <img src="components/com_proforms/images/info.png" info="<?php echo M4J_LANG_EMAIL_SUBJECT_DESC; ?>" align="top" /><br />
					<input name="subject" type="text" id="subject" style="width: 99%"
						value="<?PHP echo $subject; ?>"  maxlength="200"/>
					<br />
				</td>
			</tr>
			<tr>
				<td height="10px"> </td>
			</tr>			
			<tr>
				<td align="left" valign="top">
				
					<table width="100%" cellpadding="0" cellspacing="0" border="0"><tbody><tr>
					
					<td align="left" valign="middle" width="215px">
					
						<?PHP echo M4J_LANG_DATA_LISTING; ?>  <img src="components/com_proforms/images/info.png" info="<?php echo M4J_LANG_DATA_LISTING_DESC; ?>" align="top" />
					</td>
					<td align="left" valign="top" width="80px">
						<?php echo MForm::specialCheckbox("data_listing",(int) $data_listing); ?>
					</td>
					<td><span></span>
				
					</td>
					</tr></tbody></table>
					<div id="m4jSelectAliasForHidden" class="m4jFieldAliasSelect m4jFASHidden">
						<div class="m4jConfigInfo" style="width: 90%; margin-top: 4px;"><?PHP echo M4J_LANG_ALIAS_ADVICE; ?></div>
						<div id="m4jSelectAliasForHiddenContent" style="display:block; margin: 10px;"></div> 
					</div>
					<img id="m4jSelectAliasForHiddenClose" src="<?php echo M4J_IMAGES;?>remove.png" class="m4jFASClose" onclick="javascript: isAliasWindow = ! isAliasWindow; setAliasWindow(0);"/>
					<img id="m4jSelectAliasForHiddenOpen" src="<?php echo M4J_IMAGES;?>add12.png" class="m4jFASClose" style="display:block;" onclick="javascript: showAliasWindow('hidden');"/>	
				</td>
		 	</tr>
		 	<tr>
				<td height="10px"> </td>
			</tr>
			<tr>
				<td align="left" valign="top" >
					<table width="100%" cellpadding="0" cellspacing="0" border="0"  style="font-size: 12px;"><tr>
					<td align="left" valign="top"><?PHP echo M4J_LANG_EMAIL_TEXT; ?></td>
					<td align="right" valign="top"><a class="m4jAddAliasButton" onclick="javascript: showAliasWindow('hidden'); return false;"><?php echo M4J_LANG_INSERT_FIELD_VALUE; ?></a> </td>
					</tr></table>
					
					<?php  MEditorArea('hidden',$hidden,'hidden','100%','300'); ?>
				</td>
			</tr>
		</table>
</div>
		<?php //EOF EMAIL TAB?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ INTROTEXT TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>

<div class="m4jTabContent" id="m4jIntroText" style="left: -9999em;">
<div class="m4jConfigInfo" style="margin-top:5px;"><?PHP echo M4J_LANG_JOBS_INTROTEXT_INFO;?></div>
<?PHP MEditorArea('introtext',$introtext,'introtext','100%','400'); ?>
</div>
<?php //EOF INTROTEXT TAB?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ MAIN TEXT TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>

<div class="m4jTabContent" id="m4jMainText" style="left: -9999em;">
<div class="m4jConfigInfo" style="margin-top:5px;"><?PHP echo M4J_LANG_JOBS_MAINTEXT_INFO;?></div>
<?php MEditorArea('maintext',$maintext,'maintext','100%','400'); ?>
</div>
<?php //EOF MAIN TEXT TAB?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ AFTER SENDING TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>
<div class="m4jTabContent" id="m4jAfterSending" style="left: -9999em;">
<div class="m4jConfigInfo" style="margin-top:5px;"><?PHP echo M4J_LANG_JOBS_AFTERSENDING_INFO;?></div>

<div class="m4jAfterSendingWrap" id="afterSendingWrap">

	<div class="m4jAfterSending">
		<a onclick="javascript: changeASArrowStyle(this,0);" style="margin-left:15px;" <?php echo ($aftersending == 0) ? 'id="asSelected"' : ''; ?>><?php echo M4J_LANG_STANDARD_TEXT; ?></a>
		<a onclick="javascript: changeASArrowStyle(this,1);" <?php echo ($aftersending == 1) ? 'id="asSelected"' : ''; ?>><?php echo M4J_LANG_REDIRECT; ?></a>
		<a onclick="javascript: changeASArrowStyle(this,2);" <?php echo ($aftersending == 2) ? 'id="asSelected"' : ''; ?>><?php echo M4J_LANG_CUSTOM_TEXT;?></a>
	</div>

	<div class="m4jAfterSendingRight" id="arrow<?php echo $aftersending; ?>" ></div>

	<div style="width:370px; float:left; margin-top: 10px; visibility: <?php echo ($aftersending == 1) ? 'visible' : 'hidden'; ?>;" id="m4jRedirectWrap">
		URL
		<br/>
		<input name="redirect" type="text" id="m4jRedirection"
				value="<?PHP echo $redirect; ?>"  maxlength="200"
				style="width: <?php echo ifnot30("300px", "270px");?>; float:left;" />	
				
		<div style="display:block; float:left; margin-left: 5px; margin-top: -3px;" info="<?php echo M4J_LANG_ARTICLE_LINK_INFO; ?>">
		<?php echo articleButton(M4J_LANG_ARTICLES, "getArticle('m4jRedirection');");?>
		</div>		
		
	</div>
	<input type="hidden" name="aftersending" value="<?php echo $aftersending; ?>" id="m4jAfterSendingField"></input>
	
</div>

<div style="position: relative; display: block; width: 100%;">


<div id="m4jCustomTextHeader" style="display:<?php echo ($aftersending == 2) ? 'block' : 'none'; ?>;">

	<div id="m4jSelectAliasForCustomText" class="m4jFieldAliasSelect m4jFASCustomText">
		<div class="m4jConfigInfo" style="width: 90%; margin-top: 4px;"><?PHP echo M4J_LANG_ALIAS_ADVICE; ?></div>
		<div id="m4jSelectAliasForCustomTextContent" style="display:block; margin: 10px;"></div> 
	</div>
	<img id="m4jSelectAliasForCustomTextClose" src="<?php echo M4J_IMAGES;?>remove.png" class="m4jFASClose" style="margin-top:-2px;" onclick="javascript: isAliasWindow2 = ! isAliasWindow2; setAliasWindow2(0);"/>
	<img id="m4jSelectAliasForCustomTextOpen" src="<?php echo M4J_IMAGES;?>add12.png" class="m4jFASClose" style="display:block; margin-top:-2px;" onclick="javascript: showAliasWindow2('custom_text'); "/>

	<table width="100%" cellpadding="0" cellspacing="0" border="0"  style="font-size: 12px;"><tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_CUSTOM_TEXT; ?></td>
		<td align="right" valign="top"><a class="m4jAddAliasButton" onclick="javascript: showAliasWindow2('custom_text'); return false;"><?php echo M4J_LANG_INSERT_FIELD_VALUE; ?></a> </td>
	</tr></table>
	
</div>

	<?php  MEditorArea('custom_text',$custom_text,'custom_text','100%','380'); ?>
	<div id="m4jCustomTextWrap" style="position: absolute; background-color: #fff; width: 100%; height: 100%; left: 0; top: 0;  display:<?php echo ($aftersending == 2) ? 'none' : 'block'; ?>; "></div>
</div>


<script type="text/javascript">
 var arrowQuery = dojo.query(".m4jAfterSendingRight",dojo.byId("afterSendingWrap"));
 var m4jChangeArrowStyle = arrowQuery[0];
 arrowQuery = undefined;
</script>



</div>
<?php //EOF AFTER SENDING TAB?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ PAYPAL TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>
<div class="m4jTabContent" id="m4jPaypalTab" style="left: -9999em;">
	<div class="m4jConfigInfo" style="margin-top:5px;margin-bottom:10px; height: 90px;">
		<?PHP echo M4J_LANG_JOBS_PAYPAL_INFO;?>
		<br/>
		<?PHP echo M4J_LANG_PAYPAL_ADDITIONAL_INFO;?>
		<div class="m4jCLR"></div>
	</div>
<div class="m4jCLR"></div>
<div>
	<span style="float:left; margin-right: 10px; padding-top: 2px;"><?php echo M4J_LANG_USE_PAYPAL; ?>:</span>
	<?php echo MForm::specialCheckbox("is_paypal",(int) 0); ?> 
</div>
<div>
	<span style="float:left; margin-left: 20px; margin-right: 10px; padding-top: 2px;"><?php echo M4J_LANG_USE_PAYPAL_SANDBOX; ?>:</span>
	<?php echo MForm::specialCheckbox("is_sandbox",(int) 0); ?> 
</div>
<div class="m4jCLR"></div>

<fieldset class="jobsFieldSet">
<legend><?php echo M4J_LANG_PAYPAL_PARAMETERS; ?></legend>
<?php echo ifnot30("<br/>"); ?>
<label><?php echo M4J_LANG_PAYPAL_ID; ?></label><?php echo ifnot30("<br/>"); ?>
<input type="text" name="business" value="" style="width: 100%;" ></input><?php echo ifnot30("<br/><br/>"); ?>
<label><?php echo M4J_LANG_PAYPAL_PRODUCT_NAME; ?></label><?php echo ifnot30("<br/>"); ?>
<input type="text" name="item_name" value="" style="width: 100%;" ></input><?php echo ifnot30("<br/><br/>"); ?>

<table style="width: 100%;" cellpadding="2" cellspacing="2">
	<tbody>
		<tr>
			<td align="left" valign="top" style="width: 100px;">
				<label><?php echo M4J_LANG_PAYPAL_QTY; ?></label><?php echo ifnot30("<br/>"); ?>
				<input type="text" name="quantity" value="" style="width: 100px;" ></input><?php echo ifnot30("<br/><br/>"); ?>
			</td>
			
			<td align="left" valign="top" style="width: 180px;">
				<label><?php echo M4J_LANG_PAYPAL_NET_AMOUNT; ?></label><?php echo ifnot30("<br/>"); ?>
				<input type="text" name="amount" value="" style="width: 180px;" ></input><?php echo ifnot30("<br/><br/>"); ?>
			</td>
			
			<td align="left" valign="top">
				<label><?php echo M4J_LANG_PAYPAL_CURRENCY_CODE; ?></label><?php echo ifnot30("<br/>"); ?>
				<?php 
				$currencies = array(
					array("val"=>"", "text"=>M4J_LANG_PLEASE_SELECT),
					array("val"=>"AUD", "text"=>"AUD"),
					array("val"=>"BRL", "text"=>"BRL"),
					array("val"=>"CAD", "text"=>"CAD"),
					array("val"=>"CZK", "text"=>"CZK"),
					array("val"=>"DKK", "text"=>"DKK"),
					array("val"=>"EUR", "text"=>"EUR"),
					array("val"=>"HKD", "text"=>"HKD"),
					array("val"=>"HUF", "text"=>"HUF"),
					array("val"=>"ILS", "text"=>"ILS"),
					array("val"=>"JPY", "text"=>"JPY"),
					array("val"=>"MYR", "text"=>"MYR"),
					array("val"=>"MXN", "text"=>"MXN"),
					array("val"=>"NOK", "text"=>"NOK"),
					array("val"=>"NZD", "text"=>"NZD"),
					array("val"=>"PHP", "text"=>"PHP"),
					array("val"=>"PLN", "text"=>"PLN"),
					array("val"=>"GBP", "text"=>"GBP"),
					array("val"=>"SGD", "text"=>"SGD"),
					array("val"=>"SEK", "text"=>"SEK"),
					array("val"=>"CHF", "text"=>"CHF"),
					array("val"=>"TWD", "text"=>"TWD"),
					array("val"=>"THB", "text"=>"THB"),
					array("val"=>"TRY", "text"=>"TRY"),
					array("val"=>"USD ", "text"=>"USD")
				);
				echo MForm::select("currency_code",
									$currencies,
									"",
									MFORM_DROP_DOWN,
									null,
									'style="width: 120px" id="paypalCurrencies"	onchange="javascript: dojo.byId(\'tax_currency\').innerHTML = this.value; "
									
									');			
				?>			
			</td>
			
			<td style="width: 24px" align="center"><span style="font-weight:bold; font-size: 16px;">+</span></td>
	
			<td align="left" valign="top" >
				<label><?php echo M4J_LANG_TAX; ?></label><?php echo ifnot30("<br/>"); ?>
				<input type="text" name="tax" value="" style="width: 120px; float:left;" ></input>
				<span id="tax_currency" style="display:<?php echo  "block"?>; float:left; margin-left: 5px; <?php echo _M4J_IS_J16 ? "margin-top: 5px;" : ""; ?>"></span>			
				<span id="tax_percent" style="display:<?php echo  "none"?>; float:left; margin-left: 5px; <?php echo _M4J_IS_J16 ? "margin-top: 5px;" : ""; ?> ">%</span>
			</td>
			
			<td align="left" valign="top" style="width: 180px;">
				<label><?php echo M4J_LANG_TAXTYPE; ?></label><?php echo ifnot30("<br/>"); ?>	
				<?php
				$taxTypeArray = array(
				array("val" => "0","text" => M4J_LANG_OVERALL),
				array("val" => "1","text" => M4J_LANG_TAXFIXED),
				array("val" => "2","text" => M4J_LANG_PERCENT)
				);
				echo MForm::select("taxtype",$taxTypeArray,"",MFORM_DROP_DOWN,null,'onchange="javscript: toggleTaxType(this.value);"');
				?>
			</td>
			
		
			
			
		</tr>
	</tbody>
</table>

<label style="float:left;"><?php echo M4J_LANG_PAYPAL_LC; ?></label> <span style="float:left;"><?php echo getInfoButton(M4J_LANG_PAYPAL_LC_DESC);?></span><div class="m4jCLR"></div>
<?php 
$noCC = array( array("val"=> null, "text" => M4J_LANG_DONT_USE) );
echo MForm::select(
	"lc",
	array_merge($noCC,   m4jCountryDropDownArray(_M4J_COUNTRY_NAME_AND_ISO) ),
	"",
	MFORM_DROP_DOWN,
	null,
	'id="paypalLanguageCode" ');
			
?><?php echo ifnot30("<br/><br/>"); ?>


<label><?php echo M4J_LANG_PAYPAL_RETURN_URL; ?></label>
<table width="100%" cellspacing="0" cellpadding="0"><tbody><tr>
<td valign="top">
	<input id="m4jPaypalReturn" type="text" name="return" value="" style="width: 98%;" ></input>
</td>

<td style="width:80px;" valign="top">
	<div style="display:block; float:left; margin-left: 5px; margin-top: -3px;" info="<?php echo M4J_LANG_ARTICLE_LINK_INFO; ?>">
		<?php echo articleButton(M4J_LANG_ARTICLES, "getArticle('m4jPaypalReturn',1);");?>
	</div>
</td>
</tr></tbody></table>

<?php echo ifnot30("<br/><br/>"); ?>

<label><?php echo M4J_LANG_PAYPAL_CANCEL_RETURN_URL?></label>
<table width="100%" cellspacing="0" cellpadding="0"><tbody><tr>
<td valign="top">
<input id="m4jPaypalCancelReturn" type="text" name="cancel_return" value="" style="width: 98%;" ></input><br /><br />
</td>

<td style="width:80px;" valign="top" >
	<div style="display:block; float:left; margin-left: 5px; margin-top: -3px;" info="<?php echo M4J_LANG_ARTICLE_LINK_INFO; ?>">
		<?php echo articleButton(M4J_LANG_ARTICLES, "getArticle('m4jPaypalCancelReturn',1);");?>
	</div>
</td>
</tr>
</tbody></table>
<?php echo ifnot30("<br/><br/>"); ?>
</fieldset>

<fieldset class="jobsFieldSet">
<legend><?php echo M4J_LANG_PAYPAL_CONDITIONAL; ?></legend>
<div style="margin-bottom: 10px;" ><?php echo M4J_LANG_PAYPAL_CONDITIONAL_DESC; ?></div>
<div class="m4jCLR"></div>
<table width="100%" cellspacing="0" cellpadding="0"><tbody>

<tr>
<td valign="middle">
<?php echo M4J_LANG_PAYPAL_USE_CONDITIONAL; ?>
</td>
<td valign="middle" ><?php 
echo MForm::specialCheckbox("conditional",  0 )  ; 
?></td>
<td valign="middle" ><?php echo M4J_LANG_PAYPAL_CONDITIONAL_EID; ?></td>
<td valign="middle" ><input style="width: 100px;" name="conditional_eid" type="text" value=""></input></td>
<td valign="middle" ><?php echo M4J_LANG_PAYPAL_CONDITIONAL_VALUE; ?></td>
<td valign="middle" ><input name="conditional_value" type="text" value=""></input></td>
</tr>
</tbody></table>
</fieldset>


<center><span style="display:block; margin-top: 20px;font-size: 48px; color:red;"><?php echo M4J_LANG_ONLYPRO;?></span></center>
</div>
<?php //EOF PAYPAL TAB?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ CODE TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>

<div class="m4jTabContent" id="m4jCodeTab" style="left: -9999em;">
	<div class="m4jConfigInfo" style="margin-top:5px;margin-bottom:10px;"><?PHP echo M4J_LANG_JOBS_CODE_INFO;?></div>
	
	<div style="display:block; position: relative; ">
	<span style="display:block; z-index:200; position:absolute;">
	
		<span class="eaTab" 
			  id="tabHI" 
			  onclick="javascript: codeTab(this);"
			  onmouseover="javascript: this.style.textDecoration = 'underline';"
			  onmouseout="javascript: this.style.textDecoration = 'none';"
			  style="margin-right:-1px;">
		<?php echo M4J_LANG_FORM; ?>
		</span> 
		<span class="eaTab" 
			  onclick="javascript: codeTab(this,1);"
			  onmouseover="javascript: this.style.textDecoration = 'underline';"
			  onmouseout="javascript: this.style.textDecoration = 'none';">
		<?php echo M4J_LANG_AFTER_SENDING; ?>
		</span>
	</span>
	</div>
	<div class="m4jCLR"></div>
	<div style="position: relative; display:block; width:100%; float:left; height: 450px; margin-top: 21px; z-index:1;">
		
		<div id="secondEditArea" style="position: absolute; display:block; width:100%;  top:0px; background-color: #fff; left: -9999em">
			<center><span style="font-size: 48px; color:red;"><?php echo M4J_LANG_ONLYPRO;?></span></center>
		</div>
		<div style="position: absolute; display:block; width:100%; top:0px; background-color: #fff; left: auto;" id="firstEditArea">
			<center><span style="font-size: 48px; color:red;"><?php echo M4J_LANG_ONLYPRO;?></span></center>
		</div>
		<div class="m4jCLR"></div>
	</div>

		<div class="m4jCLR"></div>
</div>
<?php //EOF CODE TAB ?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ OPT-IN TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>

<div class="m4jTabContent" id="m4jOptTab" style="left: -9999em;">
<div class="m4jCLR"></div>
<div class="m4jConfigInfo" style="margin-top:5px;"><?PHP echo M4J_LANG_DOUBLE_OPTIN_DESC ;?></div>


	<center><span style="display:block;font-size: 48px; color:red; margin-top: 20px;"><?php echo M4J_LANG_ONLYPRO;?></span></center>

</div>
<?php //EOF DOUBLE OPT IN TAB?>

<?php 
/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
 * ++++ CUSTOMIZE TAB 
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */
?>

<div class="m4jTabContent" id="m4jCustomizeTab" style="left: -9999em;">
<div class="m4jConfigInfo" style="margin-top:5px;"><?PHP echo M4J_LANG_JOBS_INTROTEXT_CUSTOMIZE;?></div>
<fieldset>
<legend><?php echo M4J_LANG_SUBMISSIONFEATURES; ?></legend>
<table boder="0" cellspacing="2" cellpadding="4">
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_CAPTCHA; ?></td>
		<td align="left" valign="top"><?php echo getInfoButton(M4J_LANG_CAPTCHA_INFO);?></td>
		<td align="left" valign="top"><?php echo MForm::specialCheckbox("captcha",(int) $captcha); ?></td>
	</tr>
	
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_ALIGN_SUBMITAREA; ?></td>
		<td align="left" valign="top"></td>
		<td align="left" valign="top">
		<?php 
		$alignmentArray = array(
				array("val" => "0","text" => M4J_LANG_CENTER),
				array("val" => "1","text" => M4J_LANG_LEFT),
				array("val" => "2","text" => M4J_LANG_RIGHT)
		);
		echo MForm::select("submit_align",$alignmentArray,$customize->submit_align,MFORM_DROP_DOWN,null,'style="margin-top:2px; width: 140px;" ');
		?>
		</td>
	</tr>
	
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_SUBMIT_TEXT; ?></td>
		<td align="left" valign="top"><?php echo getInfoButton(M4J_LANG_SUBMIT_TEXT_INFO);?></td>
		<td align="left" valign="top"><input type="text" name="submit_text" value="<?php echo $customize->submit_text; ?>"></input></td>
	</tr>
	
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_USE_RESET; ?></td>
		<td align="left" valign="top"></td>
		<td align="left" valign="top"><?php echo MForm::specialCheckbox("use_reset",(int) $customize->use_reset); ?></td>
	</tr>
	
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_RESET_TEXT; ?></td>
		<td align="left" valign="top"><?php echo getInfoButton(M4J_LANG_RESET_TEXT_INFO);?></td>
		<td align="left" valign="top"><input type="text" name="reset_text" value="<?php echo $customize->reset_text; ?>"></input></td>
	</tr>
	
</table>
</fieldset>

<fieldset>
<table boder="0" cellspacing="2" cellpadding="4">
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_USE_META_TITLE; ?></td>
		<td align="left" valign="top"><?php echo getInfoButton(M4J_LANG_USE_META_TITLE_DESC);?></td>
		<td align="left" valign="top"><?php echo MForm::specialCheckbox("metatitle",(int) $customize->metatitle); ?></td>
	</tr>
</table>
</fieldset>

<fieldset>
<legend><?php echo M4J_LANG_ADJUSTMENT_FORM; ?></legend>
<table boder="0" cellspacing="2" cellpadding="4">
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_ADJUSTMENT_FORM_WIDTH; ?></td>
		<td align="left" valign="top"><?php echo getInfoButton(M4J_LANG_ADJUSTMENT_FORM_WIDTH_DESC);?></td>
		<td align="left" valign="top"><input type="text" name="form_width" value="<?php echo isset( $customize->form_width ) ? $customize->form_width  : '' ; ?>"></input></td>
	</tr>
	<tr>
		<td align="left" valign="top"><?PHP echo M4J_LANG_ADJUSTMENT_FORM_ALIGNMENT; ?></td>
		<td align="left" valign="top"><?php echo getInfoButton(M4J_LANG_ADJUSTMENT_FORM_ALIGNMENT_DESC);?></td>
		<td align="left" valign="top"><?php echo  MForm::multiSwitch("form_alignment",  ( isset( $customize->form_alignment ) ? (int) $customize->form_alignment  : 0 ) ,  array(				
				array("value" => 0,"text" => M4J_LANG_LEFT),
				array("value" => 1,"text" => M4J_LANG_CENTER),
				array("value" => 2,"text" => M4J_LANG_RIGHT)
		)  ) ; ?></td>
	</tr>
</table>
</fieldset>



</div>
<?php //EOF CUSTOMIZE TAB?>




<div class="m4jCLR"></div>
</div>
<div class="m4jCLR"></div>
<?php //EOF TAB WRAP ?> 

<input name="task" type="hidden" id="task" /> 
<input name="id" type="hidden" id="id" value="<?PHP echo $editID; ?>" /> 
<input name="former_cid" type="hidden" id="former_cid" value="<?PHP echo $cid; ?>" />
	
	
</form>


<?php // New Job JS ?>
<script type="text/javascript" src="<?php echo M4J_JS_NEW_JOB; ?>"></script>

