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



/**
* Jforms  Controller
*
* @package	Jforms
* @subpackage	
*/
class JformsCkClassController extends CkJController
{
	/**
	* Call the parent display function. Trick for forking overrides.
	*
	* @access	protected
	* @return	void
	*
	* @since	Cook 2.0
	*/
	protected function _parentDisplay()
	{
		//Add the fork views path (LILO) instead of FIFO
		array_push($this->paths['view'], JPATH_COMPONENT . DS. 'fork' .DS. 'views');

		parent::display();
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsClassController')){ class JformsClassController extends JformsCkClassController{} }

