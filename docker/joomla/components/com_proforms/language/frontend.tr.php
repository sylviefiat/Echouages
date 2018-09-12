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

	defined( '_JEXEC' ) or die( 'Bu konuma direkt erisime izin verilemez.' );

	define('M4J_LANG_FORM_CATEGORIES','Form Kategorileri');
	define('M4J_LANG_ERROR_NO_CATEGORY','Istenen kategori olusturulmamis yada yayinlanmamis');
	define('M4J_LANG_ERROR_NO_FORM','Istenen form olusturulmamis yada yayinlanmamis');
	define('M4J_LANG_YES','Evet');		
	define('M4J_LANG_NO','Hayir');	
	
	define('M4J_LANG_NO_CATEGORY','Kategori Disi');
	define('M4J_LANG_NO_CATEGORY_LONG','Buradan kategori için isaretlenmemis tüm formlari bulabilirsiniz.');
	define('M4J_LANG_SUBMIT','Yolla');
	define('M4J_LANG_MISSING','Eksik/kayip  dosya: ');
	define('M4J_LANG_ERROR_IN_FORM','Gerekli bilgiler eksik yada kayip:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Bu form için gidilecek gerekli adres yok. Mesaj yollanamadi.');
	define('M4J_LANG_ERROR_CAPTCHA','Yanlis güvenlik kodu veya kod girimi sirasinda geçerli süre asildi!');
	define('M4J_LANG_MAIL_SUBJECT','Form mesaji: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Lütfen güvenlik kodunu sag taraftaki text alanina dogru bir sekilde giriniz.');
	define('M4J_LANG_REQUIRED_DESC','Gerekli bilgi (bos birakilamaz).');
	define('M4J_LANG_SENT_SUCCESS','Bilgiler Basarili bir biçimde gönderildi.');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- Dosya izinverilien limitlerden daha büyük: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Dosya uzantisi yanlis yada eksik !<br/> &nbsp;&nbsp; Izin verilen dosya uzantilari: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Mesaj gönderilirken bir hata olustu <br/> Mail gönderilemedi!');
		
	//New To Proforms
	define('M4J_LANG_ERROR_USERMAIL','Geçerli bir e-posta adresinizi girmeniz gerekir: ');
	define('M4J_LANG_RESET','reset');
	define('M4J_LANG_REQUIRED','Gerekli bilgi ve bos olmayabilir');
	define('M4J_LANG_ERROR_PROMPT','Özür. Bazı girilen verilerin geçerli değildir. İlgili alanları vurgulanır.');
	define('M4J_LANG_ALPHABETICAL','Alfabetik olmali.');
	define('M4J_LANG_NUMERIC','Sayisal olmali.');
	define('M4J_LANG_INTEGER','Bir tamsayi olmali.');
	define('M4J_LANG_URL','Bir URL olmasi gerekir.');
	define('M4J_LANG_EMAIL','Geçerli bir e-posta adresi olmali.');
	define('M4J_LANG_ALPHANUMERIC','Alfanümerik olmali.');
	define('M4J_LANG_PLEASE_SELECT','Lütfen seçin');
	define('M4J_LANG_ASK2CONFIRM','Lütfen bana bir onay gönderinz.');
	define('M4J_LANG_ASK2CONFIRM_DESC','Eğer gönderilen verilerin bir onay e-postasi olarak almak istiyorsaniz bu onay kutusunu etkinleştirin.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Size teslim teyit etmiştir');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Size teslim onayı iptal yok.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Karakterler sol');
	
	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	