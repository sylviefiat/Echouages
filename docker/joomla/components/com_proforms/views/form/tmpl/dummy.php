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
 * DEFAULT TEMPLATE FOR FORM
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>

<div id="proforms_proforms<?PHP if( ! $this->parent->isAutomatic() ) echo $this->uniqueIndex; ?>" class ="<?php echo M4J_CLASS_FORM_WRAP; ?>">
<script type="text/javascript" > 
	var PROFORMS_NO_RESPONSIVE = 0;
	var m4jShowTooltip = 1; 
	var pfmFields = window.parent.pfmFields;
	document.write(window.parent.document.getElementById('preview').innerHTML);
</script>	
</div>
<div class="m4jCLR"></div>