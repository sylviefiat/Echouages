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
 *  FIELD INTERFACE LEFT SIDE FOR UPLOAD
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>

<fieldset>
<legend><small><?php echo M4J_LANG_ATTACHMENT; ?></small></legend>

<div class="pfmRow">
<label class="labelSize"><?PHP echo M4J_LANG_ALLOWED_ENDINGS; ?></label>
<textarea style="width: 200px;"	name="endings" id="endings" rows="8"><?PHP echo  $model->getParam("endings"); ?></textarea>
</div>

<div class="pfmRow">
<label class="labelSize"><?PHP echo M4J_LANG_MAXSIZE; ?></label>
    <input style="width: 80px;"	name="maxsize" type="text" id="maxsize"	value="<?PHP echo $model->getParam("maxsize"); ?>"></input>

	<?php 
					$measureArray = array(
										 array("val" => "1","text" => M4J_LANG_BYTE),
										 array("val" => "1024","text" => M4J_LANG_KILOBYTE),
										 array("val" => "1048576","text" => M4J_LANG_MEGABYTE)
										 );
					echo MForm::select("measure",$measureArray,$model->getParam("measure"),MFORM_DROP_DOWN,null,'style="width:120px; margin-bottom: 0;"');
					?>
					<div class="pfmClearfix"></div>
<label class="labelSize"></label><div style="display:inline-block; width: 200px; margin-left: 4px; margin-top: 5px;"><?php echo M4J_LANG_MAX_UPLOAD_ALLOWED.'' . ini_get('upload_max_filesize'); ?></div>
</div>
</fieldset>