<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


$file = JPATH_ADMIN_JFORMS .DS. "classes" .DS. "images.php";
if(file_exists($file) AND !class_exists('JformsImages')){
	require_once($file);
}

/**
* Images Class for Jforms.
*
* @package	Jforms
* @subpackage	Class
*/
class JformsCkClassImage extends JformsImages
{

}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsClassImage')){ class JformsClassImage extends JformsCkClassImage{} }

