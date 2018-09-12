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

//VIEW TEMPLATE FOR CATEGORY ACCESS DENIED

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$document=JFactory::getDocument();
$document->addStyleSheet(M4J_CSS);
$document->addStyleSheet(M4J_CSS_SYSTEM);

$title = (! _M4J_IS_J16 ) ? JText::_('ACCESS DENIED') : JText::_('JGLOBAL_AUTH_ACCESS_DENIED');

$this->setMetaTitle( $title );
?>
<?php echo $this->systemError($title); ?>