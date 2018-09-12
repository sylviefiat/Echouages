<?PHP
	/**
	* @translated ELPROKO-EPD Informatyka dla wszystkich www.e-wolomin.pl
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

	/**  POLISH VERSION. WERSJA POLSKA */

	defined( '_JEXEC' ) or die( 'Bezpo&#x015B;redni dost&#x0119;p do tego katalogu jest zabroniony.' );

	define('M4J_LANG_FORM_CATEGORIES','Kategorie Formularzy');
	define('M4J_LANG_ERROR_NO_CATEGORY','&#x017B;&#x0105;dana kategoria formularza nie istnieje lub nie zosta&#x0142;a opublikowana');
	define('M4J_LANG_ERROR_NO_FORM','&#017B;&#x0105;dany formularz nie istnieje lub nie zosta&#x0142; opublikowany');
	define('M4J_LANG_YES','Tak');		
	define('M4J_LANG_NO','Nie');	
	
	define('M4J_LANG_NO_CATEGORY','Bez kategorii');
	define('M4J_LANG_NO_CATEGORY_LONG','Tutaj znajdziesz wszystkie formularze, kt�re nie zosta&#x0142;y przypisane do &#x017c;adnej kategorii.');
	define('M4J_LANG_SUBMIT','Wy&#x015B;lij');
	define('M4J_LANG_MISSING','Brakuj&#x0105;ce pole: ');
	define('M4J_LANG_ERROR_IN_FORM','Brak wymaganej informacji:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Brak adresu docelowego w tym formularzu. Wiadomo&#x015B;&#x0107; nie zosta&#x0142;a wys&#x0142;ana.');
	define('M4J_LANG_ERROR_CAPTCHA','Nieprawid&#x0142;owy kod z obrazka lub obrazek si&#x0119; przedawni&#x0142;!');
	define('M4J_LANG_MAIL_SUBJECT','Temat wiadomo&#x015B;ci: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Umie&#x015B;&#x0107; kursor myszy nad obrazkiem i przepisz kod do pola z jego prawej strony.');
	define('M4J_LANG_REQUIRED_DESC','Pole wymagane');
	define('M4J_LANG_SENT_SUCCESS','Formularz zosta&#x0142; wys&#x0142;any poprawnie.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Za&#x0142;&#x0105;cznik jest zbyt du&#x017c;y! Dopuszczalna wielko&#x015B;&#x0107;: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Niedopuszczalny typ pliku!<br/> &nbsp;&nbsp; Typy dozwolone to: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Wyst&#x0105;pi&#x0142; b&#x0142;&#x0105;d podczas wysy&#x0142;ania formularza.<br/> Dane nie zosta&#x0142;y dostarczone.');

	//New to Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Musisz podać prawidłowy adres e-mail:');
	define ( 'M4J_LANG_RESET', 'reset');
	define ( 'M4J_LANG_REQUIRED', 'jest wymagane i nie mogą być puste. ');
	define ( 'M4J_LANG_ERROR_PROMPT', 'Przepraszamy. Niektóre z wprowadzonych danych nie są ważne i nie mogą być przetwarzane. odpowiednie pola są zaznaczone.');
	define ( 'M4J_LANG_ALPHABETICAL',' powinno być alfabetyczne.');
	define ( 'M4J_LANG_NUMERIC',' muszą być numeryczne.');
	define ( 'M4J_LANG_INTEGER',' musi być liczbą całkowitą. ');
	define ( 'M4J_LANG_URL',' musi być adres URL.');
	define ( 'M4J_LANG_EMAIL',' musi być prawidłowy adres email. ');
	define ( 'M4J_LANG_ALPHANUMERIC',' musi być alfanumerycznych.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Wybierz');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Proszę o przesłanie potwierdzenia.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Jeśli chcesz włączyć pole, otrzymasz email z potwierdzeniem przedstawionych danych. ');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','potwierdzeniu złożenia wniosku.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Masz odwołane potwierdzenia zgłoszenia.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Pozostało znaków');
	
	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	