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
 * FIELD INTERFACE FOR DROP DOWN
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>
<div class="pfmRow">
<label class="labelSize"><?PHP echo M4J_LANG_PLEASE_SELECT_OPTION;  ?> <?php echo getInfoButton(M4J_LANG_PLEASE_SELECT_OPTION_DESC);?></label>
	<input type="text" name="please_select_option" value="<?php echo $model->getParam("pleaseSelectOption"); ?>"></input>
</div>