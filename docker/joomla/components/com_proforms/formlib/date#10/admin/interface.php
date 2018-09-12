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
 * FIELD INTERFACE FOR DATE
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>

<div class="pfmRow">
	<label class="labelSize"><?PHP echo M4J_LANG_PLACEHOLDER; ?><?PHP echo HTML_HELPERS_m4j::info_button(M4J_LANG_PLACEHOLDER_DESC); ?></label>
	<input style="width: 650px;"name="placeholder" type="text"  value="<?PHP echo $model->getParam("placeholder", null);?>" placeholder="<?php echo htmlentities(M4J_LANG_PLACEHOLDER_ADVICE); ?>"></input>
</div>
