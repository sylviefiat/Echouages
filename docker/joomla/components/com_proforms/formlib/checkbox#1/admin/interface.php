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
 * FIELD INTERFACE FOR CHECKBOX
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>
<fieldset>
<legend><small><?php echo M4J_LANG_INIT_VALUE; ?></small></legend>
</fieldset>

<div class="pfmRow"  style="vertical-align: middle; display: inline-block;">
	<label class="labelSize"><?PHP echo M4J_LANG_VALUE; ?></label>
		<?php 
				$options = array(
					array("text"=>M4J_LANG_YES_ON,"val"=>1),
					array("text"=>M4J_LANG_NO_OFF,"val"=>0)			
				);
				echo MForm::select("checked",$options,$model->getParam("checked", 0), MFORM_DROP_DOWN,null, 'style="width:auto;"');
				?>
</div>

