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
/* @var $this ProformsViewForm */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


if( !isset($customize->metatitle) || $customize->metatitle){
	$this->setMetaTitle( MReady::_($title) );
}

// if(! defined('M4J_FORM_TITLE') || ( defined('M4J_FORM_TITLE') && M4J_FORM_TITLE ) ){
// 	echo $this->htmlHeading($title);
// } 
?>
<div id="proforms_proforms<?PHP if( ! $this->parent->isAutomatic() ) echo $this->uniqueIndex; ?>" class ="<?php echo M4J_CLASS_FORM_WRAP; ?>">
	<?php echo ProformsHelper::spambottrap1() . "\n"; ?>
	<?php 
		$afterSendingBuffer = ($aftersending == 2) ?  $this->aliasReplacement( $custom_text ) : '<h3>'.M4J_LANG_SENT_SUCCESS.'</h3><br/>';
		$this->pluginManager->onAfterSending($afterSendingBuffer);
		if( ! $this->stop->aftersending ){
			echo $afterSendingBuffer;
		}
		
		$this->pluginManager->onAfterSendingEnd();
	?>
	
</div>
<div class="m4jCLR"></div>
<?php 
	
	//App Plugin at end
	echo $this->pluginManager->atEnd();
	
	$this->_renderEndScripts(true); 
?>