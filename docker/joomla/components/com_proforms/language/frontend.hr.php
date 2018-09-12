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

	defined( '_JEXEC' ) or die( 'Izravan pristup ovoj adresi nije dozvoljen.' );

	define('M4J_LANG_FORM_CATEGORIES','Kategorije formulara');
	define('M4J_LANG_ERROR_NO_CATEGORY','Tražena kategorija formulara ne postoji ili nije objavljena');
	define('M4J_LANG_ERROR_NO_FORM','Traženi formular ne postoji ili nije objavljen');
	define('M4J_LANG_YES','Da');		
	define('M4J_LANG_NO','Ne');	
	
	define('M4J_LANG_NO_CATEGORY','Bez kategorije');
	define('M4J_LANG_NO_CATEGORY_LONG','Na ovom mjestu možete naci sve formulare kojima nije pridodjeljena kategorija.');
	define('M4J_LANG_SUBMIT','pošalji');
	define('M4J_LANG_MISSING','Nedostaje polje: ');
	define('M4J_LANG_ERROR_IN_FORM','Nedostaje tražena informacija:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Za ovaj formular ne postoji odredišna adresa. Poruka ne može biti poslana.');
	define('M4J_LANG_ERROR_CAPTCHA','Pogrešan zaštitni kod ili je istekla valjanost koda!');
	define('M4J_LANG_MAIL_SUBJECT','Poruka: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Predite strelicom miša preko slike kako bi mogli vidjeti zaštitni kod te ga potom upišite u polje s desne strane.');
	define('M4J_LANG_REQUIRED_DESC','Obavezna polja.');
	define('M4J_LANG_SENT_SUCCESS','Informacija uspješno poslana.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Datoteka je prevelika ! Maksimum: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Ne slaže se ekstenzija datoteke !<br/> &nbsp;&nbsp; Dozvoljene ekstenzije: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Došlo je do pogreške prilikom slanja <br/> Informacije nisu poslane!');

	//New To Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Morate unijeti valjanu e-mail adresa:');
	define ( 'M4J_LANG_RESET', 'Reset');
	define ( 'M4J_LANG_REQUIRED', 'Da li je potrebna i svibanj ne biti prazan.');
	define ( 'M4J_LANG_ERROR_PROMPT', 'naše isprike. Neki od upisanih podataka nije valjan i ne može biti obrađen. odgovarajuća polja su označena.');
	define ( 'M4J_LANG_ALPHABETICAL', 'Treba abecednom.');
	define ( 'M4J_LANG_NUMERIC', 'Treba numeričke.');
	define ( 'M4J_LANG_INTEGER', 'mora biti cijeli broj. ');
	define ( 'M4J_LANG_URL', 'Treba URL.');
	define ( 'M4J_LANG_EMAIL', 'Mora biti valjana adresa e-pošte.');
	define ( 'M4J_LANG_ALPHANUMERIC', 'mora biti alfanumerički.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Odaberite');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Molim pošaljite mi potvrde.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Ako aktivirate ovu kućicu, dobiti ćete potvrdu e-mail dostavljenih podataka. ');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Vi ste potvrdili Vašu prijavu.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Ti si opozvao potvrdu Vaše prijave.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Likovi lijevo');

	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'.
			'<li> The form has been sent faster than in a potential for human processing time.<br/>'.
			'For administrators: This setting and the limit is set in Proform\'s configuration </li>');
	
	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'.
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '.
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	