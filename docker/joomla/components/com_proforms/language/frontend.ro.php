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

	/** Romanian Version. NEEDS TO BE TRANSLATED */

	defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

	define('M4J_LANG_FORM_CATEGORIES','Categorie formulare');
	define('M4J_LANG_ERROR_NO_CATEGORY','Categoria formulare inexistenta sau este nepublicata');
	define('M4J_LANG_ERROR_NO_FORM','Formularul cerut nu exista sau este nepublicat');
	define('M4J_LANG_YES','Da');		
	define('M4J_LANG_NO','Nu');	
	
	define('M4J_LANG_NO_CATEGORY','Fara Categorie');
	define('M4J_LANG_NO_CATEGORY_LONG','Aici poti gasi toate formularele care nu au fost asociate cu o categorie');
	define('M4J_LANG_SUBMIT','trimite');
	define('M4J_LANG_MISSING','Campuri lipsa');
	define('M4J_LANG_ERROR_IN_FORM','lipsa informatie ceruta');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Nu este adresa destinatar pentru acest formular. mesajul nu a putut fi trimis');
	define('M4J_LANG_ERROR_CAPTCHA','cod de securitate gresit sau a expirat validitatea sa!');
	define('M4J_LANG_MAIL_SUBJECT','Mesaj formular:');
	define('M4J_LANG_CAPTCHA_ADVICE','Stai cu mouse-ul deasupra imaginii din stanga si introdu codul de securitate in campul din dreapta');
	define('M4J_LANG_REQUIRED_DESC','informatie ceruta');
	define('M4J_LANG_SENT_SUCCESS','Informatie trimisa cu succes');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Fisier prea mare! Maxim: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Extensii de fisier gresite !<br/> &nbsp;&nbsp; Extensii permise: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','O eroare s-a produs la trimitere <br/> Mailul nu a fost trimis!');
	
			
	//New To Proforms
	define('M4J_LANG_ERROR_USERMAIL','You need to enter a valid email address: ');
	define('M4J_LANG_RESET','reset');
	define('M4J_LANG_REQUIRED','Is required and may not be blank.');
	define('M4J_LANG_ERROR_PROMPT','Our apologies. Some of the entered data are not valid and can not be processed. The corresponding fields are marked.');
	define('M4J_LANG_ALPHABETICAL','Must be alphabetical.');
	define('M4J_LANG_NUMERIC','Must be numeric.');
	define('M4J_LANG_INTEGER','Must be an integer.');
	define('M4J_LANG_URL','Must be an URL.');
	define('M4J_LANG_EMAIL','Must be a valid email address.');
	define('M4J_LANG_ALPHANUMERIC','Must be alphanumerical.');
	define('M4J_LANG_PLEASE_SELECT','Please select');
	define('M4J_LANG_ASK2CONFIRM','Please send me a confirmation.');
	define('M4J_LANG_ASK2CONFIRM_DESC','If you activate this checkbox; you will obtain a confirmation email of the submitted data.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Ati confirmat depunerea ta.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Ati revocat confirmarea depunerii dumneavoastra.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Caractere ramase');
	
	
	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	