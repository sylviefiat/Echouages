<?php
/**
 * @name MOOJ Proforms
 * @version 1.0
 * @package proforms
 * @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<div class="clr"></div>
<div id="captchaWrapButtons" style="margin-top: 2px; text-align: <?php echo $this->align;?>">
	<input type="submit" name="submit" value="<?php echo $this->submitText; ?>" class ="<?php echo M4J_CLASS_SUBMIT; ?>" ></input>
	<?php if ($this->useReset):?>
	<input id="m4jResetButton" class ="<?php echo M4J_CLASS_RESET; ?>" type="reset" name="reset" value="<?php echo $this->resetText; ?>"></input>
	<?php endif;?>
</div>
<div class="clr"></div>