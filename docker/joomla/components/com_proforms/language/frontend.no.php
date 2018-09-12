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

	defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

	define('M4J_LANG_FORM_CATEGORIES','Skjema Kategorier');
	define('M4J_LANG_ERROR_NO_CATEGORY','Valgt skjema kategori finnes ikke eller er ikke publisert');
	define('M4J_LANG_ERROR_NO_FORM','Valgt skjema finnes ikke eller er ikke publisert');
	define('M4J_LANG_YES','Ja');		
	define('M4J_LANG_NO','Nei');	
	
	define('M4J_LANG_NO_CATEGORY','Uten Kategori');
	define('M4J_LANG_NO_CATEGORY_LONG','Her kan du finne alle skjemaer som ikke er i en kategori.');
	define('M4J_LANG_SUBMIT','Send');
	define('M4J_LANG_MISSING','Felt mangler: ');
	define('M4J_LANG_ERROR_IN_FORM','Nødvendig informasjon mangler:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Det finnes ikke en adresse for dette skjemaet. Meldingen kunne ikke bli sendt.');
	define('M4J_LANG_ERROR_CAPTCHA','Feil sikkerhetskode eller det har gått for lang tid!');
	define('M4J_LANG_MAIL_SUBJECT','Skjema melding: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Før musen over venstre bilde og skriv inn sikkerhetskode i tekstfeltet til høyre.');
	define('M4J_LANG_REQUIRED_DESC','Nødvendig information.');
	define('M4J_LANG_SENT_SUCCESS','Informasjonen ble sendt ok.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Filen er for stor! Maksimum: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Feil filtype!<br/> &nbsp;&nbsp; Tillatte filtyper: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Feil oppsto under sending <br/> Epost ble ikke sendt!');
	
	//New to Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Du må oppgi en gyldig e-postadresse:');
	define ( 'M4J_LANG_RESET', 'Nullstill');
	define ( 'M4J_LANG_REQUIRED', 'Er nødvendig og kan ikke være tomt. ');
	define ( 'M4J_LANG_ERROR_PROMPT', 'Vi beklager. Noen av inn data er ikke gyldig og kan ikke behandles. Tilsvarende felt er merket.');
	define ( 'M4J_LANG_ALPHABETICAL', 'Må være alfabetisk.');
	define ( 'M4J_LANG_NUMERIC', 'Må være numeriske.');
	define ( 'M4J_LANG_INTEGER', 'Må være et heltall. ');
	define ( 'M4J_LANG_URL', 'Må være en URL.');
	define ( 'M4J_LANG_EMAIL', 'Må være en gyldig e-postadresse.');
	define ( 'M4J_LANG_ALPHANUMERIC', 'Må være alfanumerisk.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Velg');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Vennligst send meg en bekreftelse.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Hvis du aktiverer dette alternativet, vil du få en e-postbekreftelse av de innsendte data.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Du har bekreftet innsendingen.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Du har tilbakekalt bekreftelse på innsendingen.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Tegn igjen');

	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'.
			'<li> The form has been sent faster than in a potential for human processing time.<br/>'.
			'For administrators: This setting and the limit is set in Proform\'s configuration </li>');
	
	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'.
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '.
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	