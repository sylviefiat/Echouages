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

	/** Czech translation by Milan Šedý - wortyr@woraif.cz, http://wortyr.woraif.cz */

	defined( '_JEXEC' ) or die( 'Přímý přístup na toto místo není povolen.' );

	define('M4J_LANG_FORM_CATEGORIES','Kategorie formulářů');
	define('M4J_LANG_ERROR_NO_CATEGORY','Požadovaná kategorie formulářů neexistuje nebo je neveřejná.');
	define('M4J_LANG_ERROR_NO_FORM','Požadovaný formulář neexistuje nebo je neveřejný.');
	define('M4J_LANG_YES','Ano');		
	define('M4J_LANG_NO','Ne');	
	
	define('M4J_LANG_NO_CATEGORY','Bez kategorie');
	define('M4J_LANG_NO_CATEGORY_LONG','Zde naleznete všechny formuláře, které nejsou zařazeny do žádné kategorie.');
	define('M4J_LANG_SUBMIT','odeslat');
	define('M4J_LANG_MISSING','Chybějící pole: ');
	define('M4J_LANG_ERROR_IN_FORM','Nebyly vyplněny povinné položky:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Pro tento formulář neni nastavena cílová adresa. Zpráva nemůže být odeslána.');
	define('M4J_LANG_ERROR_CAPTCHA','Byl špatně opsán bezpečnostní kód, nebo vypršela jeho platnost!');
	define('M4J_LANG_MAIL_SUBJECT','Zpráva formuláře: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Najeďte myší na obrázek vlevo a opište do textového pole kód, který se zobrazí.');
	define('M4J_LANG_REQUIRED_DESC','povinná položka');
	define('M4J_LANG_SENT_SUCCESS','Zpráva byla úspěšně odeslána.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Soubor je příliš velký! Maximum: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Nepovolená přípona souboru!<br/> &nbsp;&nbsp; Povolené přípony: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Během odesílání došlo k chybě <br/> Zpráva nebyla odeslána!');
	
	//New to Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Musíte zadat platnou e-mailovou adresu:');
	define ( 'M4J_LANG_RESET', 'reset');
	define ( 'M4J_LANG_REQUIRED', 'Je nezbytné, a nesmí být prázdná.');
	define ( 'M4J_LANG_ERROR_PROMPT', 'naši omluvu. Některé zadané údaje nejsou platné a nemohou být zpracovány. odpovídající pole jsou označena.');
	define ( 'M4J_LANG_ALPHABETICAL', 'musí být abecedy.');
	define ( 'M4J_LANG_NUMERIC', 'musí být číselná.');
	define ( 'M4J_LANG_INTEGER', 'musí být celé číslo.');
	define ( 'M4J_LANG_URL', 'musí být URL.');
	define ( 'M4J_LANG_EMAIL', 'musí být platnou e-mailovou adresu.');
	define ( 'M4J_LANG_ALPHANUMERIC', 'musí být alfanumerické.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Vyberte');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Prosím pošlete mi potvrzení.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Pokud aktivujete toto políčko, obdržíte potvrzovací e-mail z předložených údajů.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Ty potvrdily své podání');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Máte zrušeno potvrzení vašeho podání');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','zbývá znaků');

	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	