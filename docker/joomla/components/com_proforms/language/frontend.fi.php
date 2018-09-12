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

	define('M4J_LANG_FORM_CATEGORIES','Lomake ryhmät');
	define('M4J_LANG_ERROR_NO_CATEGORY','Pyydettyä lomakeryhmää ei ole olemassa tai sitä ei ole julkaistu');
	define('M4J_LANG_ERROR_NO_FORM','Haettu lomake puuttuu tai sitä ei ole julkaistu');
	define('M4J_LANG_YES','Kyllä');		
	define('M4J_LANG_NO','Ei');	
	
	define('M4J_LANG_NO_CATEGORY','Ilman ryhmää');
	define('M4J_LANG_NO_CATEGORY_LONG','Täältä löydätte kaikki lomakkeet jotka eivät kuulu lomakeryhmään.');
	define('M4J_LANG_SUBMIT','lähetä');
	define('M4J_LANG_MISSING','Tiedot puuttuvat kentästä: ');
	define('M4J_LANG_ERROR_IN_FORM','Pakollisia tietoja puuttuu:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Lomakkeen lähetykselle ei ole sähköpostiosoitetta. Viesti ei voitu lähettää.');
	define('M4J_LANG_ERROR_CAPTCHA','Virheellinen turvallisuuskoodi tai turvallisuuskoodi on vanhentunut!');
	define('M4J_LANG_MAIL_SUBJECT','Lomakkeen viesti: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Pidä hiirtä kuvan vasemmalla puolella ja syötä turvallisuuskoodi kenttään oikealla.');
	define('M4J_LANG_REQUIRED_DESC','Pakollisia tietoja.');
	define('M4J_LANG_SENT_SUCCESS','Tiedot onnistuneesti lähetetty.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Tiedosto on liian suuri ! enimmäiskoko: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Tiedoston pääte on kielletty !<br/> &nbsp;&nbsp; Sallittuja päätteitä ovat: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Lomakkeen tietojen lähetyksessä tapahtui virhe, <br /> viestiä ei lähetetty!');

	//New to Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL ',' Sinun täytyy syöttää kelvollinen sähköpostiosoite:');
	define ( 'M4J_LANG_RESET', 'Reset');
	define ( 'M4J_LANG_REQUIRED',' tarvitaan ja se voi olla tyhjä. ');
	define ( 'M4J_LANG_ERROR_PROMPT',' Our anteeksi. Osa annetut tiedot eivät ole voimassa eikä sitä voida käsitellä. vastaavat kentät on merkitty. ');
	define ( 'M4J_LANG_ALPHABETICAL',' on aakkosjärjestyksessä. ');
	define ( 'M4J_LANG_NUMERIC',' on numeroita. ');
	define ( 'M4J_LANG_INTEGER',' on kokonaisluku. ');
	define ( 'M4J_LANG_URL',' on URL. ');
	define ( 'M4J_LANG_EMAIL',' on voimassa oleva sähköpostiosoite. ');
	define ( 'M4J_LANG_ALPHANUMERIC',' on aakkosnumeerinen. ');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Valitse');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Lähettäkää minulle vahvistus. ');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Jos otat tämän valintaruudun, voit saada sähköpostitse toimitettujen tietojen perusteella. ');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','olet vahvistanut jättämistä.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Sinulla on peruuttanut vahvistus lähetyksestä.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','merkkiä jäljellä');

	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'.
			'<li> The form has been sent faster than in a potential for human processing time.<br/>'.
			'For administrators: This setting and the limit is set in Proform\'s configuration </li>');
	
	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'.
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '.
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	