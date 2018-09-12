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

	/**  MACEDONIAN VERSION. */

	defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

	define('M4J_LANG_FORM_CATEGORIES','Категории на форми');
	define('M4J_LANG_ERROR_NO_CATEGORY','Бараната категорија не постои или е необјавена');
	define('M4J_LANG_ERROR_NO_FORM','Бараната форма не постои или е необјавена');
	define('M4J_LANG_YES','Да');		
	define('M4J_LANG_NO','Не');	
	
	define('M4J_LANG_NO_CATEGORY','Без категорија');
	define('M4J_LANG_NO_CATEGORY_LONG','Тука можете да ги најдете сите форми кои не се поврзани со категорија.');
	define('M4J_LANG_SUBMIT','прати');
	define('M4J_LANG_MISSING','Недостига полето: ');
	define('M4J_LANG_ERROR_IN_FORM','Недостигаат бараните информации:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Нема дестинациона адреса за формата. Пораката не може да биде пратена.');
	define('M4J_LANG_ERROR_CAPTCHA','Погрешен безбедносен код или му истекла важноста!');
	define('M4J_LANG_MAIL_SUBJECT','Порака од формата: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Минете над сликата лево и и внесете го безбедносниот код во полето десно.');
	define('M4J_LANG_REQUIRED_DESC','Барана информација.');
	define('M4J_LANG_SENT_SUCCESS','Информацијата е успешно испратена.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Документот е преголем! Максимално: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Несоодветна наставка на документот!<br/> &nbsp;&nbsp; дозволени наставки: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Се појави грешка при праќањето <br/> Пораката не е пратена!');

	//New To Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Мора да внесете валидна e-mail адреса:');
	define ( 'M4J_LANG_RESET', 'ресетирање');
	define ( 'M4J_LANG_REQUIRED', 'е потребна и не може да биде празно. ');
	define ( 'M4J_LANG_ERROR_PROMPT', 'ни извинување. Некои од внесе податоците не се валидни и не можат да се третираат. Соодветните полиња се означени.');
	define ( 'M4J_LANG_ALPHABETICAL', 'Мора да биде по азбучен ред. ');
	define ( 'M4J_LANG_NUMERIC', 'Мора да нумеричка.');
	define ( 'M4J_LANG_INTEGER', 'Мора да биде цел број. ');
	define ( 'M4J_LANG_URL', 'Мора да биде URL-то. ');
	define ( 'M4J_LANG_EMAIL', 'Мора да биде валидна email адреса.');
	define ( 'M4J_LANG_ALPHANUMERIC', 'Мора да alphanumerical.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Ве молиме одберете');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Ве молиме пратете ми потврда. ');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Ако ја активирате оваа наога; ќе добиете потврда на поднесените податоци. ');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Можете потврдија вашето дело.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Мора да го отповика потврдувањето на вашето дело.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','карактери');

	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'.
			'<li> The form has been sent faster than in a potential for human processing time.<br/>'.
			'For administrators: This setting and the limit is set in Proform\'s configuration </li>');
	
	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'.
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '.
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	