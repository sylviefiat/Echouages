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

	define('M4J_LANG_FORM_CATEGORIES','Categoria');
	define('M4J_LANG_ERROR_NO_CATEGORY','Categoria nao\existe ou nao esta publicada');
	define('M4J_LANG_ERROR_NO_FORM','Formulario nao\existe ou nao esta publicado');
	define('M4J_LANG_YES','Sim');		
	define('M4J_LANG_NO','Nao');	
	
	define('M4J_LANG_NO_CATEGORY','Sem Categoria');
	define('M4J_LANG_NO_CATEGORY_LONG','Aqui voce pode encontrar todas os formularios que nao estao atribuidos a uma categoria.');
	define('M4J_LANG_SUBMIT','Enviar');
	define('M4J_LANG_MISSING','Campo nao preenchido: ');
	define('M4J_LANG_ERROR_IN_FORM','Campo requirido nao preenchido:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Nao existe um endereco de destino neste formulario. Mensagem nao\pode ser enviada.');
	define('M4J_LANG_ERROR_CAPTCHA','O codigo de seguranca esta errado ou tempo de espera expirado!');
	define('M4J_LANG_MAIL_SUBJECT','Formulario de Mensagem: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Passe o mouse sobre a imagem a esquerda, e digite o codigo de seguranca no campo a direita.');
	define('M4J_LANG_REQUIRED_DESC','Informacao requerida.');
	define('M4J_LANG_SENT_SUCCESS','Informacoes enviadas com sucesso.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Arquivo grande demais! Maximo Excedido: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Extensao do arquivo deconhecido !<br/> &nbsp;&nbsp; Extensoes Permitidas: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Quando voc&ecirc; envia o e-mail &eacute; um erro. <br/>  e-mail n&atilde;o foi enviado!');
	
	//New To Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Você precisa digitar um endereço de email válido:');
	define ( 'M4J_LANG_RESET', 'reset');
	define ( 'M4J_LANG_REQUIRED', 'é necessária e não pode estar em branco. ');
	define ( 'M4J_LANG_ERROR_PROMPT', 'As nossas desculpas. Alguns dos dados inseridos não são válidos e não pode ser processado. Os campos correspondentes são marcadas.');
	define ( 'M4J_LANG_ALPHABETICAL', 'Deve ser alfabética.');
	define ( 'M4J_LANG_NUMERIC', 'deve ser numérico. ');
	define ( 'M4J_LANG_INTEGER', 'Deve ser um número inteiro.');
	define ( 'M4J_LANG_URL', 'Deve ser uma URL.');
	define ( 'M4J_LANG_EMAIL', 'Deve ser um endereço de e-mail válido. ');
	define ( 'M4J_LANG_ALPHANUMERIC', 'Deve ser alfanumérico.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Por favor selecione');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Por favor me envie uma confirmação.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Se você ativar esta opção, você irá obter um email de confirmação dos dados apresentados.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Você já confirmou a sua apresentação.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Você tem revogou a confirmação de sua inscrição');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Personagens esquerda');
	
	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	