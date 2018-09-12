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
 * FIELD TEMPLATE MAIN SECTION
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>

<fieldset><legend><small><?php echo M4J_LANG_MAIN_CONFIG; ?></small></legend>
<div class="pfmRow">
	<label class="toLeft"><?php echo M4J_LANG_YOUR_QUESTION; ?></label> 
	<input class="toLeft"  style="width: 50%;" type="text" name="question" value="<?php echo htmlentities($model->data->question);?>" placeholder="<?php echo htmlentities(M4J_LANG_YOUR_QUESTION);?>"></input>
	<label class="toLeft"><?php echo JText::_("alias"); ?></label> 
	<input class="toLeft pfmClearfix"  style="width: 30%;" type="text" name="alias" value="<?php echo htmlentities($model->data->alias);?>" placeholder="<?php echo htmlentities(JText::_("alias"));?>"></input>
</div>
<div class="pfmRow" style="padding: 6px; padding-bottom: 0; margin-bottom: 0;">
	<label class="pfmClearfix" style="height: auto; margin-left: -6px;"><?php echo M4J_LANG_HELP_TEXT_SHORT . HTML_HELPERS_m4j::info_button(M4J_LANG_HELP_TEXT); ?></label>
	<textarea placeholder="<?php echo htmlentities(M4J_LANG_HELP_TEXT_SHORT);?>" style="width: 100%; height: 50px;" class="borderBox" name="help"><?php echo htmlentities($model->data->help);?></textarea>
</div>

<div class="pfmRow" style="padding: 6px; padding-bottom: 0;">
				<div style="display:inline-block" class="toLeft twen2Right">
					<span class="toLeft five2Right"><?PHP echo M4J_LANG_ACTIVE; ?></span> 
					<span class="toLeft"><?PHP echo MForm::specialCheckbox("active",(int) $model->data->active); ?>		</span>  
				</div>
				<div style="display:inline-block; position: relative;" class="toLeft twen2Right">
					<span class="toLeft five2Right"><?PHP echo M4J_LANG_REQUIRED_LONG; ?></span> 
					<span class="toLeft"><?PHP echo MForm::specialCheckbox("required",(int) $model->data->required,"m4jToggleRequired",0); ?></span>
					<div class="hide<?php  echo ( ! $model->usermailField ||  $model->usermailField != $model->eid   ) ? 'hide': ''; ?>" info="<?php echo htmlentities(M4J_LANG_USERMAIL_TOOLTIP);?>"></div>
				</div>
				
				<div style="display:inline-block" class="toLeft twen2Right">
					<span class="toLeft five2Right"><?PHP echo M4J_LANG_DISPLAY; ?></span> 
					<div class="toLeft">
						<?php echo MForm::multiSwitch("align", $model->data->align, array(
							array("value" => 0 , "image" => "display-horizontal.png" , "topinfo" => M4J_LANG_QUESTIONSRIGHT ), 
							array("value" => 1 , "image" => "display-vertical.png", "topinfo" => M4J_LANG_QUESTIONSABOVE), 
							array("value" => 2 , "image" => "display-noquestionright.png", "topinfo" => M4J_LANG_NOQUESTIONRIGHT ), 
							array("value" => 3 , "image" => "display-noquestion.png", "topinfo" => M4J_LANG_NOQUESTION ), 
								
						) );?>
					</div>
				<!--	<span class="toLeft"><?PHP echo MForm::specialCheckbox("align",(int) $model->data->align,"m4jToggleAlignment",1); ?></span>   -->
				</div>
			
				<div class="pfmClearfix"></div>
			
</div>
</fieldset>