<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Submissions
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');



/**
* Jforms Submissions Controller
*
* @package	Jforms
* @subpackage	Submissions
*/
class JformsCkControllerSubmissions extends JformsClassControllerList
{
	/**
	* The context for storing internal data, e.g. record.
	*
	* @var string
	*/
	protected $context = 'submissions';

	/**
	* The URL view item variable.
	*
	* @var string
	*/
	protected $view_item = 'submission';

	/**
	* The URL view list variable.
	*
	* @var string
	*/
	protected $view_list = 'submissions';

	/**
	* Constructor
	*
	* @access	public
	* @param	array	$config	An optional associative array of configuration settings.
	* @return	void
	*/
	public function __construct($config = array())
	{
		parent::__construct($config);
		$app = JFactory::getApplication();

	}

	/**
	* Return the current layout.
	*
	* @access	protected
	* @param	bool	$default	If true, return the default layout.
	*
	* @return	string	Requested layout or default layout
	*/
	protected function getLayout($default = null)
	{
		if ($default)
			return 'default';

		$jinput = JFactory::getApplication()->input;
		return $jinput->get('layout', 'default', 'CMD');
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsControllerSubmissions')){ class JformsControllerSubmissions extends JformsCkControllerSubmissions{} }

