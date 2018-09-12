<?php
/**
 * @name MOOJ Proforms
 * @version 1.5
 * @package proforms
 * @copyright Copyright (C) 2008-2013 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/


defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
$fm = ProformsFieldManager::getInstance();
?>
<div id="formElementDropdown">
<ul>
<li>
	<span class="ddheading">+ <?php echo M4J_LANG_NEW_ELEMENT ; ?></span>
	<ul>
		<li>
			<div class="elementsWrap">
			
			<?php $fm->htmlGroups(true);?>
			
			<div class="m4jCLR"></div>
			</div>
		</li>
	
	</ul>
	
	
</li>

</ul>
</div>