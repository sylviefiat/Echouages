<?PHP
	/**
	* @version $Id: proforms 10041 2008-03-18 21:48:13Z fahrettinkutyol $
	* @package joomla
	* @copyright Copyright (C) 2008 Mad4Media. All rights reserved.
	* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	* Joomla! is free software. This version may have been modified pursuant
	* to the GNU General Public License, and as distributed it includes or
	* is derivative of works licensed under the GNU General Public License or
	* other free or open source software licenses.
	* See COPYRIGHT.php for copyright notices and details.
	* @copyright (C) mad4media , www.mad4media.de
	*/

	/**  ENGLISH VERSION. NEEDS TO BE TRANSLATED */

	defined( '_JEXEC' ) or die( 'Direkt &aring;tkomst &auml;r f&ouml;rbjudet.' );

	define('M4J_LANG_FORM_CATEGORIES','Formul&auml;r kategori');
	define('M4J_LANG_ERROR_NO_CATEGORY','Formul&auml;ret existerar inte eller &auml;r inte publicerat');
	define('M4J_LANG_ERROR_NO_FORM','Formul&auml;ret existerar inte');
	define('M4J_LANG_YES','Ja');		
	define('M4J_LANG_NO','Nej');	
	
	define('M4J_LANG_NO_CATEGORY','Utan kategori');
	define('M4J_LANG_NO_CATEGORY_LONG','H&auml;r finns alla formul&auml;r som inte &auml;r kopplade till n&aring;gon kategori.');
	define('M4J_LANG_SUBMIT','Skicka');
	define('M4J_LANG_MISSING','Fyll i dessa formul&auml;r!:');
	define('M4J_LANG_ERROR_IN_FORM','Information som saknas:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Det finns ingen m&aring;ladress angiven. Mailet kunde inte skickas!');
	define('M4J_LANG_ERROR_CAPTCHA','Fel s&auml;kerhetskod!');
	define('M4J_LANG_MAIL_SUBJECT','Formul&auml;rets meddelande: ');
	define('M4J_LANG_CAPTCHA_ADVICE','H&aring;ll muspekaren &ouml;ver v&auml;nstra bilden och skriv in koden i det h&ouml;gra f&auml;ltet.');
	define('M4J_LANG_REQUIRED_DESC','M&aring;ste fyllas i.');
	define('M4J_LANG_SENT_SUCCESS','Mailet &auml;r nu skickat!');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- F&ouml;r stor fil: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Otill&aring;ten fil&auml;ndelse !<br/> &nbsp;&nbsp; Till&aring;tna fil&auml;ndelser: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Ett fel uppstod. <br/> Mailet blev inte skickat!');

	//New To Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Du måste ange en giltig e-postadress:');
	define ( 'M4J_LANG_RESET', 'Återställ');
	define ( 'M4J_LANG_REQUIRED',' krävs och kan inte vara tomt. ');
	define ( 'M4J_LANG_ERROR_PROMPT', 'Vår ursäkter. Några av inmatade data är inte giltig och kan inte behandlas. motsvarande fält är markerade.');
	define ( 'M4J_LANG_ALPHABETICAL', 'måste vara alfabetisk. ');
	define ( 'M4J_LANG_NUMERIC', 'måste vara numeriska. ');
	define ( 'M4J_LANG_INTEGER', 'måste vara ett heltal. ');
	define ( 'M4J_LANG_URL', 'måste vara en URL. ');
	define ( 'M4J_LANG_EMAIL', 'måste vara en giltig e-postadress.');
	define ( 'M4J_LANG_ALPHANUMERIC','måste vara alfanumeriska. ');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Välj');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Skicka mig en bekräftelse.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Om du aktiverar den här kryssrutan, kommer du att få en e-postbekräftelse av de inlämnade uppgifterna. ');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Du har bekräftat ditt bidrag.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Du har upphävt bekräftelse på ditt bidrag.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Tecken kvar');
	
	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	