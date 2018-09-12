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

/**
 * FIELD INTERFACE FOR PASSWORD
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>

<div class="pfmRow">
	<label class="labelSize"><?PHP echo M4J_LANG_PLACEHOLDER; ?><?PHP echo HTML_HELPERS_m4j::info_button(M4J_LANG_PLACEHOLDER_DESC); ?></label>
	<input style="width: 650px;"name="placeholder" type="text"  value="<?PHP echo $model->getParam("placeholder", null);?>" placeholder="<?php echo htmlentities(M4J_LANG_PLACEHOLDER_ADVICE); ?>"></input>
</div>

<fieldset>
<legend><small><?php echo M4J_LANG_FIELD_VALIDATION; ?></small></legend>

<div class="m4jCLR"></div>

	<?php 
	   $maxchars = $model->getParam("maxchars", null);
	   $maxchars = $maxchars ? $maxchars : null;
	?>
<div class="pfmRow">
	<label class="labelSize"><?PHP echo M4J_LANG_MAXCHARS_LONG; ?></label>
	<input style="width: 60px;"name="maxchars" type="text" id="maxchars" value="<?PHP echo htmlentities($maxchars, ENT_COMPAT, "UTF-8");?>"></input>
</div>
	
	
<div class="pfmRow"  style="vertical-align: middle; display: inline-block;">
	<label class="labelSize"><?PHP echo M4J_LANG_FIELD_VALIDATION; ?></label>
		<?php 
				$evaluation = array(
					array("text"=>M4J_LANG_NONE,"val"=>""),
					array("text"=>M4J_LANG_ALPHABETICAL,"val"=>"1"),
					array("text"=>M4J_LANG_ALPHANUMERIC,"val"=>"6"),
					array("text"=>M4J_LANG_NUMERIC,"val"=>"2"),
					array("text"=>M4J_LANG_INTEGER,"val"=>"3"),
					array("text"=>M4J_LANG_EMAIL,"val"=>"4"),
					array("text"=>"URL","val"=>"5")					
				);
				echo MForm::select("eval",$evaluation,$model->getParam("eval", null), MFORM_DROP_DOWN,null, 'style="width:auto;"');
				?>
</div>


</fieldset>