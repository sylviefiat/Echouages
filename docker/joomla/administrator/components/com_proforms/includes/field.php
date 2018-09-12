<?php
/**
 * @name MOOJ Proforms
 * @version 1.5
 * @package proforms
 * @copyright Copyright (C) 2008-2013 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.mad4media.de Mad4Media Software Development - Softwareentwicklung
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once M4J_INCLUDE_FUNCTIONS;
require_once M4J_INCLUDE_JSTEXT;

ProformsAdminHelper::setComponentView();

$document = JFactory::getDocument();
$document->addScript(M4J_JS. "proforms-footer.js");
$document->addScript(M4J_JS. "info.js");
$document->addScript(M4J_JS. "topinfo.js");;
$document->addScript(M4J_JS. "parsing.js");

/* @var $model ProformsAdminModelField */
$model = ProformsAdminModel::getInstance("field");


if($task == "convert") {
		$model->convert();
		m4jRedirect($model->returnURL );
}
if( $model->request() ){
	die('<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr"><head>'.
		'<script type="text/javascript">window.parent.FormItems.reloadMarked();</script>' .
		'</head><body></body></html>');
}

$model->debug();


ob_start();
include M4J_TEMPLATES . "field.php";
$buffer = ob_get_clean();
echo $buffer;
if(_M4J_DEBUG && 0) {
	MDebug::out(1);
	echo '<div class="pfmClearfix" style="display:block; width: 100%; height: 52px"></div>';
}