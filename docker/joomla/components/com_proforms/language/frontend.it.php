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

	define('M4J_LANG_FORM_CATEGORIES','Categorie di Form');
	define('M4J_LANG_ERROR_NO_CATEGORY','La categoria di form richiesta non esiste o non &egrave; stata pubblicata');
	define('M4J_LANG_ERROR_NO_FORM','La form richiesta non esiste o non &egrave; stata pubblicata');
	define('M4J_LANG_YES','Si');		
	define('M4J_LANG_NO','No');	
	
	define('M4J_LANG_NO_CATEGORY','Senza categoria');
	define('M4J_LANG_NO_CATEGORY_LONG','Qui puoi trovare tutte le form che non sono state assegnate a una categoria.');
	define('M4J_LANG_SUBMIT','Invia');
	define('M4J_LANG_MISSING','Campo mancante: ');
	define('M4J_LANG_ERROR_IN_FORM','Mancano alcuni dati obbligatori:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Non &egrave; stato specificato alcun indirizzo destinatario. Il messaggio non pu&ograve; essere inviato.');
	define('M4J_LANG_ERROR_CAPTCHA','Codice di sicurezza errato o la sua validit&agrave; &egrave; scaduta!');
	define('M4J_LANG_MAIL_SUBJECT','Messaggio: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Passa sopra l\'immagine a sinistra e inserisci il codice di sicurezza nel campo testuale a destra.');
	define('M4J_LANG_REQUIRED_DESC','Dati obbligatori.');
	define('M4J_LANG_SENT_SUCCESS','L\'informazione &egrave; stata inviata con successo.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Il file &egrave; troppo grande. Massimo: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- L\'estensione del file non corrisponde !<br/> &nbsp;&nbsp; Estensioni consentite: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Durante l\'invio &egrave; avvenuto un errore <br/> Il messaggio non &egrave; stato inviato!');
	
	//New to proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Hai bisogno di un indirizzo email valido:');
	define ( 'M4J_LANG_RESET', 'Reset');
	define ( 'M4J_LANG_REQUIRED', 'è necessario e non può essere vuoto.');
	define ( 'M4J_LANG_ERROR_PROMPT', 'Le nostre scuse. Alcuni dei dati inseriti non sono validi e non possono essere trattati. campi corrispondenti sono contrassegnati.');
	define ( 'M4J_LANG_ALPHABETICAL', 'deve essere alfabetico.');
	define ( 'M4J_LANG_NUMERIC', 'devono essere numerici.');
	define ( 'M4J_LANG_INTEGER', 'deve essere un intero.');
	define ( 'M4J_LANG_URL', 'Deve essere un URL.');
	define ( 'M4J_LANG_EMAIL', 'Deve essere un indirizzo email valido.');
	define ( 'M4J_LANG_ALPHANUMERIC', 'Deve essere alfanumerici.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Seleziona');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Vi prego di inviarmi una conferma.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Se si attiva questa casella di controllo, si otterrà una email di conferma dei dati trasmessi.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');

	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Hai confermato la tua presentazione.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Hai revocato la conferma della vostra presentazione.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Caratteri rimasti');
	
	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	