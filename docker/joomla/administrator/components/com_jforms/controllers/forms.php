<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Forms
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');



/**
* Jforms Forms Controller
*
* @package	Jforms
* @subpackage	Forms
*/
class JformsCkControllerForms extends JformsClassControllerList
{
	/**
	* The context for storing internal data, e.g. record.
	*
	* @var string
	*/
	protected $context = 'forms';

	/**
	* The URL view item variable.
	*
	* @var string
	*/
	protected $view_item = 'form';

	/**
	* The URL view list variable.
	*
	* @var string
	*/
	protected $view_list = 'forms';

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

	/**
	* Method to publish an element.
	*
	* @access	public
	* @return	void
	*/
	public function publish()
	{
		CkJSession::checkToken() or CkJSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$this->_result = $result = parent::publish();
		$model = $this->getModel();

		//Define the redirections
		switch($this->getLayout() .'.'. $this->getTask())
		{
			case 'form.trash':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'form.archive':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'form.publish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'form.unpublish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.trash':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.archive':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.publish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.unpublish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.publish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.unpublish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'default.trash':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'modal.trash':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'modal.archive':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'modal.publish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			case 'modal.unpublish':
				$this->applyRedirection($result, array(
					'stay',
					'com_jforms.forms.default'
				), array(
					'cid[]' => null
				));
				break;

			default:
				$this->applyRedirection($result, array(
					'stay',
					'stay'
				));
				break;
		}
	}


}

// Load the fork
JformsHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsControllerForms')){ class JformsControllerForms extends JformsCkControllerForms{} }

