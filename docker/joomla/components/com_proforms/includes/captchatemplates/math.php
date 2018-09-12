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

/* @var $this MCaptcha */

?>
<div class="clr"></div>

<div id="captchaWrap" style="text-align: <?php echo $this->align;?>">
	
	<div id="proformsCaptcha">
		
		<div class="alignImage">
			<img src="index.php?option=com_proforms&amp;cpta=5&amp;uidx=<?php echo (int) $this->uniqueIndex; ?>&amp;rand=<?php echo uniqid(); ?>"  id="m4jCIM<?php echo (int) $this->uniqueIndex; ?>" alt="Proforms" />
		</div>
		
		<div class="alignInput">
			<input name="validate<?php echo (int) $this->uniqueIndex; ?>" type="text" id="validate<?php echo (int) $this->uniqueIndex; ?>" size="10" maxlength="6" ></input>
			<img src="<?php echo M4J_FRONTEND.'images/captchareload.png';?>" alt="Reload" onclick="javascript: m4jReloadCaptcha(<?php echo (int) $this->uniqueIndex; ?>); return false;" />
		</div>
		
	</div>	
</div>

<div id="captchaWrapButtons" style="margin-top: 2px; text-align: <?php echo $this->align;?>">
	<input type="submit" name="submit" value="<?php echo $this->submitText; ?>" class ="<?php echo M4J_CLASS_SUBMIT; ?>" ></input>
	<?php if ($this->useReset):?>
	<input id="m4jResetButton" class ="<?php echo M4J_CLASS_RESET; ?>" type="reset" name="reset" value="<?php echo $this->resetText; ?>"></input>
	<?php endif;?>
</div>
<div class="clr"></div>