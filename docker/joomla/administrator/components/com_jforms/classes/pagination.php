<?php
/**
* @package		jForms
* @version		0.4.4
* @copyright	2013 - Girolamo Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		GNU GPL v3 or later
*/

defined('JPATH_PLATFORM') or die;

// j1.6 compatibility

jimport('joomla.html.pagination');


/**
 * Pagination Class. Provides a common interface for content pagination for the Joomla! CMS.
 *
 * @package     Joomla.Libraries
 * @subpackage  Pagination
 * @since       1.5
 */
class CKJPagination extends JPagination
{
	/**
	 * Constructor.
	 *
	 * @param   integer  $total       The total number of items.
	 * @param   integer  $limitstart  The offset of the item to start at.
	 * @param   integer  $limit       The number of items to display per page.
	 * @param   string   $prefix      The prefix used for request variables.
	 *
	 * @since   1.5
	 */
	public function __construct($total, $limitstart, $limit, $prefix = '', &$state = null)
	{
		parent::__construct($total, $limitstart, $limit, $prefix);
	}

	/**
	 * Creates a dropdown box for selecting how many records to show per page.
	 *
	 * @return  string  The HTML for the limit # input box.
	 *
	 * @since   11.1
	 */
	public function getLimitBox()
	{
		$app = JFactory::getApplication();
		$limit = $this->limit;
		$prefix = $this->prefix;
		
		// Initialise variables.
		$options = array();

		// Make the option list.
		$limits = array(5,10,15,20,25,30,50,100);
		foreach($limits as $st){
			$options[] = JHtml::_('select.option', "$st");
		}
		
		if($limit > 0 AND !in_array($limit, $limits)){			
			$options[] = JHtml::_('select.option', $limit);
		} else {
			$options[] = JHtml::_('select.option', '0', JText::_('JALL'));
		}
		$selected = $limit;

		// Build the select list.
		if ($app->isAdmin())
		{
			$html = JHtml::_(
				'select.genericlist',
				$options,
				$prefix . 'limit',
				'class="inputbox" size="1" onchange="Joomla.submitform();"',
				'value',
				'text',
				$selected
			);
		}
		else
		{
			$html = JHtml::_(
				'select.genericlist',
				$options,
				$prefix . 'limit',
				'class="inputbox" size="1" onchange="this.form.submit()"',
				'value',
				'text',
				$selected
			);
		}
		
		return $html;
	}
}
