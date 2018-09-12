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
 * FIELD TEMPLATE MAIN SECTION
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>




<div class="pfmRow">
	<label class="toLeft"><?php echo M4J_LANG_TITLE; ?></label> 
	<input class="toLeft"  style="width: 50%;" type="text" name="question" value="<?php echo htmlentities($model->data->question, ENT_COMPAT, "UTF-8");?>" placeholder="<?php echo htmlentities(M4J_LANG_EXTRA_HTML);?>"></input>
	<div style="display:inline-block; margin-left: 15px;" class="toLeft twen2Right">
		<span class="toLeft five2Right"><?PHP echo M4J_LANG_ACTIVE; ?></span> 
		<span class="toLeft"><?PHP echo MForm::specialCheckbox("active",(int) $model->data->active); ?>		</span>  
	</div>
	<div class="pfmClearfix"></div>
</div>

<div class="pfmRow">
<?php echo GetMEditorArea('html',$model->data->html,'html','100%','400','75','30'); ?>
</div>