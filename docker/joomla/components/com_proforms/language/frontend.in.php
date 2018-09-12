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

	/**  INDONESIA VERSION. */

	defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

	define('M4J_LANG_FORM_CATEGORIES','Kategori Form');
	define('M4J_LANG_ERROR_NO_CATEGORY','Kategori form yang diminta tidak ada atau tidak dipublikkan');
	define('M4J_LANG_ERROR_NO_FORM','Form yang dimintan tidak ada atau tidak dipublikkan');
	define('M4J_LANG_YES','Ya');
	define('M4J_LANG_NO','Tidak');

	define('M4J_LANG_NO_CATEGORY','Tanpa Kategori');
	define('M4J_LANG_NO_CATEGORY_LONG','Di sini anda dapat menemukan form2 yang tidak termasuk dalam sebuah kategori.');
	define('M4J_LANG_SUBMIT','kirim');
	define('M4J_LANG_MISSING','Lajur hilang: ');
	define('M4J_LANG_ERROR_IN_FORM','Informmasi wajib tidak ada:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','Form ini tidak memiliki alamat tujuan. Pesan tak dapat dikirim.');
	define('M4J_LANG_ERROR_CAPTCHA','Kode keamanan salah atau validitas kadaluwarsa!');
	define('M4J_LANG_MAIL_SUBJECT','Pesan Form: ');
	define('M4J_LANG_CAPTCHA_ADVICE','Lambaikan di atas gambar di kiri dan masukkan kode keamanan ke lajur teks di kanan.');
	define('M4J_LANG_REQUIRED_DESC','Informasi wajib.');
	define('M4J_LANG_SENT_SUCCESS','Informasi berhasil dikirim.');

	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- File is terlalu besar ! Maksimum: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- Ekstensi File tidak sesuai !<br/> &nbsp;&nbsp; Ekstensi2 yang diizinkan: ');

	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','Terjadi kesalahan saat mengirim <br/> Mail tidak terkirim!');

	//New To Proforms
	define ( 'M4J_LANG_ERROR_USERMAIL', 'Anda harus memasukkan alamat email yang valid:');
	define ( 'M4J_LANG_RESET', 'reset');
	define ( 'M4J_LANG_REQUIRED', 'Apakah yang diperlukan dan tidak boleh kosong.');
	define ( 'M4J_LANG_ERROR_PROMPT', 'Kami minta maaf. Beberapa dari data yang dimasukkan tidak valid dan tidak dapat diproses. ladang yang berkaitan ditandai.');
	define ( 'M4J_LANG_ALPHABETICAL', 'Harus alfabet.');
	define ( 'M4J_LANG_NUMERIC', 'Harus angka.');
	define ( 'M4J_LANG_INTEGER', 'Harus bilangan bulat.');
	define ( 'M4J_LANG_URL', 'Harus menjadi sebuah URL.');
	define ( 'M4J_LANG_EMAIL', 'Harus alamat email yang valid.');
	define ( 'M4J_LANG_ALPHANUMERIC', 'Harus alphanumerical.');
	define ( 'M4J_LANG_PLEASE_SELECT', 'Mohon pilih');
	define ( 'M4J_LANG_ASK2CONFIRM', 'Mohon konfirmasi.');
	define ( 'M4J_LANG_ASK2CONFIRM_DESC', 'Jika anda mengaktifkan checkbox ini, Anda akan mendapatkan email konfirmasi dari data yang dikirim.');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','Anda telah mengkonfirmasikan pengiriman Anda.');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','Anda telah membatalkan konfirmasi pengiriman Anda.');
	
	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','Karakter tersisa');
	
	
	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'. 
									   '<li> The form has been sent faster than in a potential for human processing time.<br/>'.
									   'For administrators: This setting and the limit is set in Proform\'s configuration </li>');

	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'. 
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '. 
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	