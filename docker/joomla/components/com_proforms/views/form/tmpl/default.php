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


if( ( !isset($customize->metatitle) || $customize->metatitle ) && $this->parent->isAutomatic() ){
	$this->setMetaTitle( MReady::_($title) );
}

if( ( ! defined('M4J_FORM_TITLE') || ( defined('M4J_FORM_TITLE') && M4J_FORM_TITLE ) ) 
	&& ! $this->stop->title
	&& ! $this->isRaw
	){
	$headingTitle = $title;
	$this->pluginManager->onTitle( $headingTitle );
	echo $this->htmlHeading($headingTitle);
} 


$tmplUniqueIndex = ( ! $this->parent->isAutomatic() ) ? $this->uniqueIndex : null;


$form_width = isset($customize->form_width) && ! empty( $customize->form_width ) ?  ' style="width: '.$customize->form_width.';"' : ' style="width: 100%;"';
$form_alignment = '';
if( isset($customize->form_alignment)  ){
	switch ((int) $customize->form_alignment ){
		default: 
		case 0;
			$form_alignment = ' style="text-align: left;"';
		break;
		
		case 1:
			$form_alignment = ' style="text-align: center;"';
		break;
		
		case 2: 

			$form_alignment = ' style="text-align: right;"';
		break;
	}
}

?>
<?php if(! defined('PROFORMS_IE8_CSS_CONDITIONAL')){ define('PROFORMS_IE8_CSS_CONDITIONAL',1)?>
<!--[if IE 8]>
<link rel="stylesheet" href="components/com_proforms/css/ie8.css" />
<![endif]-->
<?php };?>


<div id="proforms_proforms<?PHP echo $tmplUniqueIndex; ?>" class ="<?php echo M4J_CLASS_FORM_WRAP; ?> proformsFormWrap"<?php echo $form_alignment; ?>>
	<div class="proformsInnerFormWrap"<?php echo $form_width; ?>>
		<?php echo ProformsHelper::spambottrap1() . "\n"; ?>
		<div class ="<?php echo M4J_CLASS_HEADER_TEXT; ?> item-page"><?php 
		
			// Prompting error messages if there are some
			if( trim( $this->error ) ){
				echo $this->error;
			}
	
			// Plugin Manager on Before Content
			echo $this->pluginManager->onBeforeContent();
	
			// if maintext display is allowed show maintext
			if(! $this->stop->maintext){
	
				// Apply possible plugin modifications of the maintext
				$this->pluginManager->onContent($maintext);
				
				// Prompting the main text. If you remove the constant of the class declaration above you will not be able
				// for setting the CSS Class for the wrapper via your admin area of Proforms.
				echo $maintext;
				
			}//EOF if not stop maintext
			
			//Plugin Manager on After Content
			echo $this->pluginManager->onAfterContent();
			
		?></div>
	
<?php if( (bool) $this->model->getShowRequiredAdvice() && (bool) $this->requiredAdvice ): ?>
		<div class="m4jRequiredWrapSystem m4j_required_wrap"><span class="m4j_required">*</span><?php echo M4J_LANG_REQUIRED_DESC; ?></div>
		<div class="m4jCLR"></div>
<?php endif;?>		

		<form id="m4jForm" name="m4jForm" method="post" enctype="multipart/form-data" class="ProformsForm" action="">
		<?php 
			//Feed the hidden fields. This is about Proforms' system related hidden fields. Don't remove this action for proper processing.
			echo "\n\t" . implode("\t", $this->hiddenFields );
			//EOF feet the hidden fields
			
			//Plugins Form Head
			echo $this->pluginManager->formHead();
			
			/**
			 * 	This is another spambot trap. Don't remove this if you want no spam!  
			 *  This trap is hidden by the CSS class div.m4j-email
			 *  The class is located in components/com_proforms/css/system.css.
			 *  If you are able to see this trap in the form it means that your Joomla template does not include the HTML head properly.
			 *  A Joomla Template must include the Joomla generated head as follows in the head-tag : <jdoc:include type="head" />
			 *  This must be also included that way in the Joomla template's component view!
			 */
		?>
		<div class="m4j-email"><label for="email">This is a security field. If you want this form being sent leave the following email field blank:</label><input type="text" name="email" value=""></input></div>
		<?php 
			/**
			 * EOF SPAM TRAP
			 */
		
			/**
			 * Now flushing all form elements in their layouts
			 */
			echo $formContent; 	
		?>
		<div class="m4jCLR"></div>
		<?php if(isset($confirmation) && (int) $confirmation == 2 &&  $this->model->getUserMailId() && ! $this->stop->confirmation ):?>
		<div class="m4jAsk2Confirm" id="m4jAsk2Confirm">
			<div style="display: none;" id="m4jAsk2ConfirmDesc"><?php echo M4J_LANG_ASK2CONFIRM_DESC; ?></div>
			<input type="checkbox" name="mailcopy" value="1" <?php echo $this->mailcopy ? 'checked="checked" ' : '';?>></input>
			<label><?php echo M4J_LANG_ASK2CONFIRM; ?></label>
		</div>
		<div class="m4jCLR"></div>
		<?php 
			endif; 
			//EOF confirmation question
			
			// Plugins Form Footer
			echo $this->pluginManager->formFooter();		
			
			// Apply the captcha 
			echo $this->captcha;
			?>
		</form>
		<div class="m4jCLR"></div>
	</div>
	<div class="m4jCLR"></div>
</div>
<div class="m4jCLR"></div>
<?php 
	// Apply on submit callback js for end script rendering
	$this->pluginManager->onSubmitCallBacks();

	//App Plugin at end
	echo $this->pluginManager->atEnd();
	
	
	$this->_renderEndScripts(true); 
?>