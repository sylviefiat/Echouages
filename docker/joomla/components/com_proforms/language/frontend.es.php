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



	/**  SPANISH VERSION. TRANSLATION BY MARC VANWAY */



	defined( '_JEXEC' ) or die( 'El acceso directo a ese sitio no está permitido.' );



	define('M4J_LANG_FORM_CATEGORIES','Formulario Categorías');

	define('M4J_LANG_ERROR_NO_CATEGORY','El formulario de categoría pedido no existe. o no está publicado.');

	define('M4J_LANG_ERROR_NO_FORM','El formulario pedido no existe o no está publicado.');

	define('M4J_LANG_YES','Si');		

	define('M4J_LANG_NO','No');	

	

	define('M4J_LANG_NO_CATEGORY','Sin Categoría');

	define('M4J_LANG_NO_CATEGORY_LONG','Aquí puedes encontrar todos los formularios que no están asignados a ninguna categoría.');

	define('M4J_LANG_SUBMIT','Enviar');

	define('M4J_LANG_MISSING','Campo no rellenado: ');

	define('M4J_LANG_ERROR_IN_FORM','Falta información requerida:');

	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','No hay dirección de destino para este formulario. El mensaje no pudo ser enviado.');

	define('M4J_LANG_ERROR_CAPTCHA','Código de seguridad erróneo o expiró su validez!');

	define('M4J_LANG_MAIL_SUBJECT','Mensaje del formulario: ');

	define('M4J_LANG_CAPTCHA_ADVICE','Pase el mouse por encima de la imagen de la izquierda e inserte el código de seguridad en la área de texto de la derecha.');

	define('M4J_LANG_REQUIRED_DESC','Información requerida');

	define('M4J_LANG_SENT_SUCCESS','Información enviada satisfactoriamente.');

	

	//New To Version 1.1.8

	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- El archivo es demasiado grande ! Máximo: ');

	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Extensión de archivo errónea !<br/> &nbsp;&nbsp; Extensiones admitidas: ');

	

	//New To Version 1.1.9

	define('M4J_LANG_SENT_ERROR','Mientras se enviaba, ha ocurrido un error. <br/> Mail no enviado!');

	//New To Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Debes introducir una dirección válida de correo electrónico:');
	define ( 'M4J_LANG_RESET', 'Reset');
	define ( 'M4J_LANG_REQUIRED', 'es necesaria y no puede estar en blanco.');
	define ( 'M4J_LANG_ERROR_PROMPT' ,'Nuestras disculpas. Algunos de los datos introducidos no son válidos y no pueden ser procesados. Los campos correspondientes están marcados.');
	define ( 'M4J_LANG_ALPHABETICAL', 'debe ser una letra.');
	define ( 'M4J_LANG_NUMERIC', 'debe ser numérico.');
	define ( 'M4J_LANG_INTEGER', 'Debe ser un entero.');
	define ( 'M4J_LANG_URL', 'Debe ser una URL.');
	define ( 'M4J_LANG_EMAIL', 'Debe ser una dirección válida de correo electrónico.');
	define ( 'M4J_LANG_ALPHANUMERIC', 'Debe ser alfanumérico.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Seleccione');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Por favor, envíe una confirmación.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Si se activa esta casilla, usted obtendrá un mensaje de confirmación de los datos presentados.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');

	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Se han confirmado su presentación.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Usted ha revocado la confirmación de su presentación.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Caracteres restantes');

	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'.
			'<li> The form has been sent faster than in a potential for human processing time.<br/>'.
			'For administrators: This setting and the limit is set in Proform\'s configuration </li>');
	
	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'.
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '.
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	