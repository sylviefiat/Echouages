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
$lang =JFactory::getLanguage();
$tag = explode("-", $lang->getTag() );
$availableLanguages =array("en","nl","fr","de","pt","ru","es","tr");
$langCode = (in_array($tag[0],$availableLanguages))? $tag[0]: "en";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

# was there a reCAPTCHA response?
if(isset($_POST["recaptcha_response_field"])) {
	$resp = pfm_recaptcha_check_answer(PFM_RE_CAPTCHA_PRIVATE,
								   $_SERVER["REMOTE_ADDR"],
								   $_POST["recaptcha_challenge_field"],
								   $_POST["recaptcha_response_field"]);

	if ( ! $resp->is_valid) {
		# set the error code so that we can display it
		$error = $resp->error;
	}
}
?>
<div class="clr"></div>

<div id="captchaWrap" style="text-align: <?php echo $this->align;?>">
	<script type="text/javascript">
		var RecaptchaOptions = {
			lang : '<?php echo $langCode;?>',
			theme : '<?php echo M4J_RECAPTCHA;?>'
		};
	</script>
	<div id="proformsReCaptcha">
		<?php echo pfm_recaptcha_get_html(PFM_RE_CAPTCHA_PUBLIC, $error); ?>
	</div>
</div>

<div id="captchaWrapButtons" style="margin-top: 2px; text-align: <?php echo $this->align;?>">
	<input type="submit" name="submit" value="<?php echo $this->submitText; ?>" class ="<?php echo M4J_CLASS_SUBMIT; ?>" ></input>
	<?php if ($this->useReset):?>
	<input id="m4jResetButton" class ="<?php echo M4J_CLASS_RESET; ?>" type="reset" name="reset" value="<?php echo $this->resetText; ?>"></input>
	<?php endif;?>
</div>
<div class="clr"></div>