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

	/**  Japanese VERSION. */

	defined( '_JEXEC' ) or die( 'このロケーションには直接アクセスできません。' );

	define('M4J_LANG_FORM_CATEGORIES','フォームカテゴリ');
	define('M4J_LANG_ERROR_NO_CATEGORY','要求されたフォームカテゴリが存在しないか、非公開になっています。');
	define('M4J_LANG_ERROR_NO_FORM','要求されたフォームカテゴリが存在しないか、非公開になっています。');
	define('M4J_LANG_YES','はい');
	define('M4J_LANG_NO','いいえ');	
	
	define('M4J_LANG_NO_CATEGORY','カテゴリなし');
	define('M4J_LANG_NO_CATEGORY_LONG','カテゴリに割当てられていないすべてのフォームを見つけることが出来ます。');
	define('M4J_LANG_SUBMIT','送信');
	define('M4J_LANG_MISSING','フィールドが見つかりません: ');
	define('M4J_LANG_ERROR_IN_FORM','必要な情報が見つかりません:');
	define('M4J_LANG_ERROR_NO_MAIL_ADRESS','送信先のe-mailアドレスがフォームに指定されていません。メッセージを送信することが出来ませんでした。');
	define('M4J_LANG_ERROR_CAPTCHA','セキュリティコードが間違っているか、セキュリティコードの有効期限が過ぎています。');
	define('M4J_LANG_MAIL_SUBJECT','フォームメッセージ: ');
	define('M4J_LANG_CAPTCHA_ADVICE','左のイメージ上にマウスをあわせ、右のフィールドにセキュリティコードを入力してください。');
	define('M4J_LANG_REQUIRED_DESC','必須情報');
	define('M4J_LANG_SENT_SUCCESS','送信に成功しました。');
	
	//New To Version 1.1.8
	define('M4J_LANG_TO_LARGE','<br/> &nbsp;- ファイルサイズが大きすぎます。サイズ上限: ');
	define('M4J_LANG_WRONG_ENDING','<br/> &nbsp;- ファイルの拡張子が一致しません。<br/> &nbsp;&nbsp; 許可されている拡張子: ');
	
	//New To Version 1.1.9
	define('M4J_LANG_SENT_ERROR','送信中にエラーが発生しました。<br/> メールは送信されませんでした。');
	
	//New To Proforms
	define('M4J_LANG_ERROR_USERMAIL','あなたは、有効な電子メールアドレスを入力する必要があります：');
	define('M4J_LANG_RESET','リセットを定義します。');
	define('M4J_LANG_REQUIRED','と空白にすることが必要です。');
	define('M4J_LANG_ERROR_PROMPT','私たちの謝罪を定義します。いくつかの入力されたデータと有効ではありませんが処理されないことができます。対応するフィールドにマークされます。');
	define('M4J_LANG_ALPHABETICAL','アルファベット順である必要があります。');
	define('M4J_LANG_NUMERIC','数値である必要があります。');
	define('M4J_LANG_INTEGER','整数値である必要があります。');
	define('M4J_LANG_URL','URLを指定する必要があります。');
	define('M4J_LANG_EMAIL','有効なメールアドレスである必要があります。');
	define('M4J_LANG_ALPHANUMERIC','英数字する必要があります。');
	define('M4J_LANG_PLEASE_SELECT','してください）を選択します');
	define('M4J_LANG_ASK2CONFIRM','ご確認のメールを送信します。');
	define('M4J_LANG_ASK2CONFIRM_DESC','の場合は、このチェックボックスをオン;場合は、提出されたデータの確認のメールを取得します。');
	// New to Proforms 1.0.5
	define('M4J_LANG_ERROR_NO_TEMPLATE','This form hasn\'t been assigned to a form template!');
	
	// New to Proforms 1.1
	define('M4J_LANG_OPTIN_CONFIRMATION_SUBJECT','あなたの提出を確認している。');
	define('M4J_LANG_OPTOUT_CONFIRMATION_SUBJECT','あなたは、あなたの提出の確認を無効にしました。');

	// New to Proforms 1.3
	define('M4J_LANG_CHAR_LEFT','文字は左');

	//New to Proforms 1.5
	define('M4J_LANG_SPAMBOT_TRAPPED', 'SPAMBOT TRAP!<br/><ul>'.
			'<li> The form has been sent faster than in a potential for human processing time.<br/>'.
			'For administrators: This setting and the limit is set in Proform\'s configuration </li>');
	
	define('M4J_LANG_SPAMBOT_TRAPPED_NO_COOKIE', 'SPAMBOT TRAP!<br/><ul>'.
			'<li>If you are humman and are still seeing this message, please enable the cookie usage in your browser, your firewall or your antivirus.</li>'.
			'<li>If this form is included on a foreign site via an iframe this violates the security policy against modern browser. '.
			'If you still want to use this form you need to set the security policies of your browser so that cookies are allowed in iframes of external pages</li>');
	
	