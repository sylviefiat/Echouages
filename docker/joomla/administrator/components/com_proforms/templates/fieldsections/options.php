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

/**
 * FIELD TEMPLATE OPTIONS INTERFACE SECTION
 */

/* @var $model ProformsAdminModelField */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>
<fieldset>
<legend><small>Options</small></legend>
<?php 

$args = array(
		"options" => $model->options,
		"values" => $model->option_values,
		"use_values" => $model->getParam("use_values",0),
		"options_data_type" => $model->getParam("options_data_type",0),
		"sql"=> $model->getParam("sql")
);
$optionsRightArea = MTemplater::get(M4J_TEMPLATES."options.php",$args);

echo $optionsRightArea;

?>
</fieldset>