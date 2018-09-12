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
 * FIELD INTERFACE FOR MULTISELECT
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>
<div class="pfmRow">
<label class="labelSize"><?PHP echo M4J_LANG_ROWS;  ?> <?php echo HTML_HELPERS_m4j::info_button(getLeftOfBreak(stripBold(M4J_LANG_ROWS_LIST) ) );?></label>
	<input type="text" name="element_rows" value="<?php echo $model->getParam("element_rows",3);?>" style="width:60px;"></input>
</div>