<?php
/**
* @name MOOJ Proforms 
* @version 1.0
* @package proforms
* @copyright Copyright (C) 2008-2010 Mad4Media. All rights reserved.
* @author Dipl. Inf.(FH) Fahrettin Kutyol
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
* Please note that some Javascript files are not under GNU/GPL License.
* These files are under the mad4media license
* They may edited and used infinitely but may not repuplished or redistributed.  
* For more information read the header notice of the js files.
**/
	
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>

<table cellpadding="0" cellspacing="0" border="0" class="m4jSystemTable m4jLayout04" id="<?PHP echo $fid; ?>">
	<tbody>
		<tr>
			<td align="left" valign="top">
				<?php echo $slot1; ?>
			</td>
			<td align="left" valign="top">
				<?php echo $slot2; ?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" colspan="2">
				<?php echo $slot3; ?>
			</td>
		</tr>
	</tbody>
</table>