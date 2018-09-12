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
 * FIELD INTERFACE FOR CHECKBOX GROUP
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>

<div class="pfmRow">
<label class="labelSize"><?PHP echo stripBold(getLeftOfBreak(M4J_LANG_ALIGNMENT_GROUPS)); ?></label>
<?php echo MForm::select("alignment", array(
	array("val" => 0 , "text" => M4J_LANG_HORIZONTAL),
	array("val" => 1 , "text" => M4J_LANG_VERTICAL)
    ),
    $model->getParam("alignment"), MFORM_DROP_DOWN,null, 'style="max-width: 300px; width: auto;"'
);?>
</div>