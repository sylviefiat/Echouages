<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Contents
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');



$file = dirname(__FILE__) . DS . 'ByGiroHelper.php';
if(file_exists($file) AND !class_exists('ByGiroHelper')){
	require_once($file);
}

$file = dirname(__FILE__) . DS . 'multiLanguages.php';
if(file_exists($file) AND !class_exists('multiLanguages')){
	require_once($file);
}

$file = dirname(__FILE__) . DS . 'HtmlTxt.php';
if(file_exists($file) AND !class_exists('HtmlText')){
	require_once($file);
}

$file = dirname(__FILE__) . DS . 'Converter.php';
if(file_exists($file) AND !class_exists('Converter')){
	require_once($file);
}

$file = dirname(__FILE__) . DS . 'fitofo.php';
if(file_exists($file) AND !class_exists('fitofo')){
	require_once($file);
}



/**
* Jforms Helper functions.
*
* @package	Jforms
* @subpackage	Helper
*/
class JformsCkHelper
{
	/**
	* Cache for ACL actions
	*
	* @var object
	*/
	protected static $acl = array();

	/**
	* Directories aliases.
	*
	* @var array
	*/
	protected static $directories;

	/**
	* Determines when requirements have been loaded.
	*
	* @var boolean
	*/
	protected static $loaded = null;

	/**
	* Call a JS file. Manage fork files.
	*
	* @access	protected static
	* @param	JDocument	$doc	Document.
	* @param	string	$base	Component base from site root.
	* @param	string	$file	Component file.
	* @param	boolean	$replace	Replace the file or override. (Default : Replace)
	* @return	void
	*
	* @since	Cook 2.0
	*/
	protected static function addScript($doc, $base, $file, $replace = true)
	{
		$url = JURI::root(true) . '/' . $base . '/' . $file;
		$url = str_replace(DS, '/', $url);
		
		$urlFork = null;
		if (file_exists(JPATH_SITE .DS. $base .DS. 'fork' .DS. $file))
		{
			$urlFork = JURI::root(true) . '/' . $base . '/fork/' . $file;
			$urlFork = str_replace(DS, '/', $urlFork);
		}

		if ($replace && $urlFork)
			$url = $urlFork;

		$doc->addScript($url);

		if (!$replace && $urlFork)
			$doc->addScript($urlFork);
	}

	/**
	* Call a CSS file. Manage fork files.
	*
	* @access	protected static
	* @param	JDocument	$doc	Document.
	* @param	string	$base	Component base from site root.
	* @param	string	$file	Component file.
	* @param	boolean	$replace	Replace the file or override. (Default : Override)
	* @return	void
	*
	* @since	Cook 2.0
	*/
	protected static function addStyleSheet($doc, $base, $file, $replace = false)
	{
		$url = JURI::root(true) . '/' . $base . '/' . $file;
		$url = str_replace(DS, '/', $url);

		$urlFork = null;
		if (file_exists(JPATH_SITE .DS. $base .DS. 'fork' .DS. $file))
		{
			$urlFork = JURI::root(true) . '/' . $base . '/fork/' . $file;
			$urlFork = str_replace(DS, '/', $urlFork);
		}

		if ($replace && $urlFork)
			$url = $urlFork;

		$doc->addStyleSheet($url);

		if (!$replace && $urlFork)
			$doc->addStyleSheet($urlFork);
	}

	/**
	* Configure the Linkbar.
	*
	* @access	public static
	* @param	varchar	$view	The name of the active view.
	* @param	varchar	$layout	The name of the active layout.
	* @param	varchar	$alias	The name of the menu. Default : 'menu'
	* @return	void
	*
	* @since	1.6
	*/
	public static function addSubmenu($view, $layout, $alias = 'menu')
	{
		$items = self::getMenuItems();
		$app = JFactory::getApplication();
		$jinput = $app->input;

		// Will be handled in XML in future (or/and with the Joomla native menus)
		// -> give your opinion on j-cook.pro/forum

		
		$client = 'admin';
		if ($app->isSite())
			$client = 'site';
	
		$links = array();
		switch($client)
		{
			case 'admin':
				switch($alias)
				{
					case 'cpanel':
					case 'menu':
					default:
						$links = array(
							'admin.forms.default',
							'admin.submissions.default'
						);
								
						if ($alias != 'cpanel')
							array_unshift($links, 'admin.cpanel');
					
						break;
				}
				break;
		
			case 'site':
				switch($alias)
				{
					case 'cpanel':
					case 'menu':
					default:
						$links = array(
							'site.forms',
							'site.submissions'
						);
								
						if ($alias != 'cpanel')
							array_unshift($links, 'site.cpanel');
					
						break;
				}
				break;
		}


		$itemId = $jinput->get('Itemid',null,'INT');


		//Compile with selected items in the right order
		$menu = array();
		foreach($links as $link)
		{
			if (!isset($items[$link]))
				continue;	// Not found
		
			$item = $items[$link];
	
			// Menu link
			$extension = 'com_jforms';
			if (isset($item['extension']))
				$extension = $item['extension'];
	
			$url = 'index.php?option=' . $extension;
			if (isset($item['view']))
				$url .= '&view=' . $item['view'];
			if (isset($item['layout']))
				$url .= '&layout=' . $item['layout'];
	

			// add the current Itemid
			$url .= '&Itemid='. $itemId;


			// Is active
			$active = ($item['view'] == $view);
			if (isset($item['layout']))
				$active = $active && ($item['layout'] == $layout);
	
			// Reconstruct it the Joomla format
			$menu[] = array(JText::_($item['label']), $url, $active, $item['icon']);

		}

		$version = new JVersion();
		//Create the submenu in the old fashion way
		if (version_compare($version->RELEASE, '3.0', '<'))
		{
			$html = "";	
			// Prepare the submenu module
			foreach ($menu as $entry )
				JSubMenuHelper::addEntry($entry[0], $entry[1], $entry[2]);
		}

		return $menu;
	}

	/**
	* Prepare the enumeration static lists.
	*
	* @access	public static
	* @param	string	$ctrl	The model in wich to find the list.
	* @param	string	$fieldName	The field reference for this list.
	*
	* @return	array	Prepared arrays to fill lists.
	*/
	public static function enumList($ctrl = null, $fieldName = null)
	{

		static $lists;
		
		if(isset($lists)){
			if($ctrl AND $fieldName AND isset($lists[$ctrl][$fieldName])){
				return $lists[$ctrl][$fieldName];
			} else if($ctrl AND isset($lists[$ctrl])){
				return $lists[$ctrl];
			} else if(!$ctrl AND !$fieldName) {
				return $lists;
			} else {
				return array();
			}
		}
		
		$langs = ByGiroHelper::getInstalledLanguages();		
		$langValues = array();
		foreach($langs as $lg){
			$langValues[$lg->lang_code] = array(
				"value" => $lg->lang_code,
				"text" => $lg->title
			);
		}

		$lists = array();

		$lists["forms"]["layout_type"] = array();
		$lists["forms"]["layout_type"]["single_form"] = array("value" => "single_form", "text" => JText::_("JFORMS_ENUM_FORMS_LAYOUT_TYPE_SINGLE_FORM"));
		$lists["forms"]["layout_type"]["wizard"] = array("value" => "wizard", "text" => JText::_("JFORMS_ENUM_FORMS_LAYOUT_TYPE_WIZARD"));


		$lists["submissions"]["status"] = array();
		$lists["submissions"]["status"]["na"] = array("value" => "na", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_NA"));
		$lists["submissions"]["status"]["incomplete"] = array("value" => "incomplete", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_INCOMPLETE"));
		$lists["submissions"]["status"]["pending"] = array("value" => "pending", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_PENDING"));
		$lists["submissions"]["status"]["confirmed"] = array("value" => "confirmed", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_CONFIRMED"));
		$lists["submissions"]["status"]["canceled"] = array("value" => "canceled", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_CANCELED"));
		$lists["submissions"]["status"]["refunded"] = array("value" => "refunded", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_REFUNDED"));
		$lists["submissions"]["status"]["paid"] = array("value" => "paid", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_PAID"));
		$lists["submissions"]["status"]["shipped"] = array("value" => "shipped", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_SHIPPED"));
		$lists["submissions"]["status"]["completed"] = array("value" => "completed", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_STATUS_COMPLETED"));


		$lists["submissions"]["payment_status"] = array();
		$lists["submissions"]["payment_status"]["pending"] = array("value" => "pending", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_PAYMENT_STATUS_PENDING"));
		$lists["submissions"]["payment_status"]["canceled"] = array("value" => "canceled", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_PAYMENT_STATUS_CANCELED"));
		$lists["submissions"]["payment_status"]["completed"] = array("value" => "completed", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_PAYMENT_STATUS_COMPLETED"));
		$lists["submissions"]["payment_status"]["failed"] = array("value" => "failed", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_PAYMENT_STATUS_FAILED"));
		$lists["submissions"]["payment_status"]["refunded"] = array("value" => "refunded", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_PAYMENT_STATUS_REFUNDED"));
		$lists["submissions"]["payment_status"]["not_created"] = array("value" => "not_created", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_PAYMENT_STATUS_NOT_CREATED_YET"));
		$lists["submissions"]["payment_status"]["to_be_checked"] = array("value" => "to_be_checked", "text" => JText::_("JFORMS_ENUM_SUBMISSIONS_PAYMENT_STATUS_TO_BE_CHECKED"));



		$lists["submissions"]["currency"] = array();
		$lists["submissions"]["currency"]["AED"] = array("value" => "AED", "text" => JText::_("JFORMS_AED"), "symbol" => 'د.إ', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["AFN"] = array("value" => "AFN", "text" => JText::_("JFORMS_AFN"), "symbol" => '؋', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["ALL"] = array("value" => "ALL", "text" => JText::_("JFORMS_ALL"), "symbol" => 'Lek', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["ANG"] = array("value" => "ANG", "text" => JText::_("JFORMS_ANG"), "symbol" => 'ƒ', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["ARS"] = array("value" => "ARS", "text" => JText::_("JFORMS_ARS"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["AUD"] = array("value" => "AUD", "text" => JText::_("JFORMS_AUD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["AWG"] = array("value" => "AWG", "text" => JText::_("JFORMS_AWG"), "symbol" => 'ƒ', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["AZN"] = array("value" => "AZN", "text" => JText::_("JFORMS_AZN"), "symbol" => 'ман', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BAM"] = array("value" => "BAM", "text" => JText::_("JFORMS_BAM"), "symbol" => 'KM', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BBD"] = array("value" => "BBD", "text" => JText::_("JFORMS_BBD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BGN"] = array("value" => "BGN", "text" => JText::_("JFORMS_BGN"), "symbol" => 'лв', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BMD"] = array("value" => "BMD", "text" => JText::_("JFORMS_BMD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BND"] = array("value" => "BND", "text" => JText::_("JFORMS_BND"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BOB"] = array("value" => "BOB", "text" => JText::_("JFORMS_BOB"), "symbol" => '$b', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BRL"] = array("value" => "BRL", "text" => JText::_("JFORMS_BRL"), "symbol" => 'R$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BSD"] = array("value" => "BSD", "text" => JText::_("JFORMS_BSD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BWP"] = array("value" => "BWP", "text" => JText::_("JFORMS_BWP"), "symbol" => 'P', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BYR"] = array("value" => "BYR", "text" => JText::_("JFORMS_BYR"), "symbol" => 'p.', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["BZD"] = array("value" => "BZD", "text" => JText::_("JFORMS_BZD"), "symbol" => 'BZ$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["CAD"] = array("value" => "CAD", "text" => JText::_("JFORMS_CAD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["CHF"] = array("value" => "CHF", "text" => JText::_("JFORMS_CHF"), "symbol" => 'CHF', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["CLP"] = array("value" => "CLP", "text" => JText::_("JFORMS_CLP"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["CNY"] = array("value" => "CNY", "text" => JText::_("JFORMS_CNY"), "symbol" => '¥', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["COP"] = array("value" => "COP", "text" => JText::_("JFORMS_COP"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["CRC"] = array("value" => "CRC", "text" => JText::_("JFORMS_CRC"), "symbol" => '₡', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["CUP"] = array("value" => "CUP", "text" => JText::_("JFORMS_CUP"), "symbol" => '₱', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["CZK"] = array("value" => "CZK", "text" => JText::_("JFORMS_CZK"), "symbol" => 'Kč', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["DKK"] = array("value" => "DKK", "text" => JText::_("JFORMS_DKK"), "symbol" => 'kr', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["DOP"] = array("value" => "DOP", "text" => JText::_("JFORMS_DOP"), "symbol" => 'RD$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["EGP"] = array("value" => "EGP", "text" => JText::_("JFORMS_EGP"), "symbol" => '£', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["EUR"] = array("value" => "EUR", "text" => JText::_("JFORMS_EUR"), "symbol" => '€', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["FJD"] = array("value" => "FJD", "text" => JText::_("JFORMS_FJD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["FKP"] = array("value" => "FKP", "text" => JText::_("JFORMS_FKP"), "symbol" => '£', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["GBP"] = array("value" => "GBP", "text" => JText::_("JFORMS_GBP"), "symbol" => '£', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["GIP"] = array("value" => "GIP", "text" => JText::_("JFORMS_GIP"), "symbol" => '£', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["GTQ"] = array("value" => "GTQ", "text" => JText::_("JFORMS_GTQ"), "symbol" => 'Q', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["GYD"] = array("value" => "GYD", "text" => JText::_("JFORMS_GYD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["HKD"] = array("value" => "HKD", "text" => JText::_("JFORMS_HKD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["HNL"] = array("value" => "HNL", "text" => JText::_("JFORMS_HNL"), "symbol" => 'L', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["HRK"] = array("value" => "HRK", "text" => JText::_("JFORMS_HRK"), "symbol" => 'kn', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["HUF"] = array("value" => "HUF", "text" => JText::_("JFORMS_HUF"), "symbol" => 'Ft', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["IDR"] = array("value" => "IDR", "text" => JText::_("JFORMS_IDR"), "symbol" => 'Rp', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["ILS"] = array("value" => "ILS", "text" => JText::_("JFORMS_ILS"), "symbol" => '₪', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["INR"] = array("value" => "INR", "text" => JText::_("JFORMS_INR"), "symbol" => '₹', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["IRR"] = array("value" => "IRR", "text" => JText::_("JFORMS_IRR"), "symbol" => '﷼', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["ISK"] = array("value" => "ISK", "text" => JText::_("JFORMS_ISK"), "symbol" => 'kr', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["JMD"] = array("value" => "JMD", "text" => JText::_("JFORMS_JMD"), "symbol" => 'J$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["JPY"] = array("value" => "JPY", "text" => JText::_("JFORMS_JPY"), "symbol" => '¥', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["KGS"] = array("value" => "KGS", "text" => JText::_("JFORMS_KGS"), "symbol" => 'лв', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["KHR"] = array("value" => "KHR", "text" => JText::_("JFORMS_KHR"), "symbol" => '៛', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["KPW"] = array("value" => "KPW", "text" => JText::_("JFORMS_KPW"), "symbol" => '₩', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["KRW"] = array("value" => "KRW", "text" => JText::_("JFORMS_KRW"), "symbol" => '₩', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["KYD"] = array("value" => "KYD", "text" => JText::_("JFORMS_KYD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["KZT"] = array("value" => "KZT", "text" => JText::_("JFORMS_KZT"), "symbol" => 'лв', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["LAK"] = array("value" => "LAK", "text" => JText::_("JFORMS_LAK"), "symbol" => '₭', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["LBP"] = array("value" => "LBP", "text" => JText::_("JFORMS_LBP"), "symbol" => '£', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["LKR"] = array("value" => "LKR", "text" => JText::_("JFORMS_LKR"), "symbol" => '₨', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["LRD"] = array("value" => "LRD", "text" => JText::_("JFORMS_LRD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["LTL"] = array("value" => "LTL", "text" => JText::_("JFORMS_LTL"), "symbol" => 'Lt', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["LVL"] = array("value" => "LVL", "text" => JText::_("JFORMS_LVL"), "symbol" => 'Ls', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["MKD"] = array("value" => "MKD", "text" => JText::_("JFORMS_MKD"), "symbol" => 'ден', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["MNT"] = array("value" => "MNT", "text" => JText::_("JFORMS_MNT"), "symbol" => '₮', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["MUR"] = array("value" => "MUR", "text" => JText::_("JFORMS_MUR"), "symbol" => '₨', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["MXN"] = array("value" => "MXN", "text" => JText::_("JFORMS_MXN"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["MYR"] = array("value" => "MYR", "text" => JText::_("JFORMS_MYR"), "symbol" => 'RM', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["MZN"] = array("value" => "MZN", "text" => JText::_("JFORMS_MZN"), "symbol" => 'MT', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["NAD"] = array("value" => "NAD", "text" => JText::_("JFORMS_NAD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["NGN"] = array("value" => "NGN", "text" => JText::_("JFORMS_NGN"), "symbol" => '₦', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["NIO"] = array("value" => "NIO", "text" => JText::_("JFORMS_NIO"), "symbol" => 'C$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["NOK"] = array("value" => "NOK", "text" => JText::_("JFORMS_NOK"), "symbol" => 'kr', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["NPR"] = array("value" => "NPR", "text" => JText::_("JFORMS_NPR"), "symbol" => '₨', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["NZD"] = array("value" => "NZD", "text" => JText::_("JFORMS_NZD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["OMR"] = array("value" => "OMR", "text" => JText::_("JFORMS_OMR"), "symbol" => '﷼', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["PAB"] = array("value" => "PAB", "text" => JText::_("JFORMS_PAB"), "symbol" => 'B/.', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["PEN"] = array("value" => "PEN", "text" => JText::_("JFORMS_PEN"), "symbol" => 'S/.', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["PHP"] = array("value" => "PHP", "text" => JText::_("JFORMS_PHP"), "symbol" => '₱', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["PKR"] = array("value" => "PKR", "text" => JText::_("JFORMS_PKR"), "symbol" => '₨', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["PLN"] = array("value" => "PLN", "text" => JText::_("JFORMS_PLN"), "symbol" => 'zł', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["PYG"] = array("value" => "PYG", "text" => JText::_("JFORMS_PYG"), "symbol" => 'Gs', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["QAR"] = array("value" => "QAR", "text" => JText::_("JFORMS_QAR"), "symbol" => '﷼', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["RON"] = array("value" => "RON", "text" => JText::_("JFORMS_RON"), "symbol" => 'lei', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["RSD"] = array("value" => "RSD", "text" => JText::_("JFORMS_RSD"), "symbol" => 'Дин.', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["RUB"] = array("value" => "RUB", "text" => JText::_("JFORMS_RUB"), "symbol" => 'руб', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SAR"] = array("value" => "SAR", "text" => JText::_("JFORMS_SAR"), "symbol" => '﷼', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SBD"] = array("value" => "SBD", "text" => JText::_("JFORMS_SBD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SCR"] = array("value" => "SCR", "text" => JText::_("JFORMS_SCR"), "symbol" => '₨', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SEK"] = array("value" => "SEK", "text" => JText::_("JFORMS_SEK"), "symbol" => 'kr', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SGD"] = array("value" => "SGD", "text" => JText::_("JFORMS_SGD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SHP"] = array("value" => "SHP", "text" => JText::_("JFORMS_SHP"), "symbol" => '£', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SOS"] = array("value" => "SOS", "text" => JText::_("JFORMS_SOS"), "symbol" => 'S', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SRD"] = array("value" => "SRD", "text" => JText::_("JFORMS_SRD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SVC"] = array("value" => "SVC", "text" => JText::_("JFORMS_SVC"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["SYP"] = array("value" => "SYP", "text" => JText::_("JFORMS_SYP"), "symbol" => '£', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["THB"] = array("value" => "THB", "text" => JText::_("JFORMS_THB"), "symbol" => '฿', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["TRY"] = array("value" => "TRY", "text" => JText::_("JFORMS_TRY"), "symbol" => '₤', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["TTD"] = array("value" => "TTD", "text" => JText::_("JFORMS_TTD"), "symbol" => 'TT$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["TWD"] = array("value" => "TWD", "text" => JText::_("JFORMS_TWD"), "symbol" => 'NT$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["UAH"] = array("value" => "UAH", "text" => JText::_("JFORMS_UAH"), "symbol" => '₴', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["USD"] = array("value" => "USD", "text" => JText::_("JFORMS_USD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["UYU"] = array("value" => "UYU", "text" => JText::_("JFORMS_UYU"), "symbol" => '$U', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["UZS"] = array("value" => "UZS", "text" => JText::_("JFORMS_UZS"), "symbol" => 'лв', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["VEF"] = array("value" => "VEF", "text" => JText::_("JFORMS_VEF"), "symbol" => 'Bs', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["VND"] = array("value" => "VND", "text" => JText::_("JFORMS_VND"), "symbol" => '₫', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["XCD"] = array("value" => "XCD", "text" => JText::_("JFORMS_XCD"), "symbol" => '$', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["YER"] = array("value" => "YER", "text" => JText::_("JFORMS_YER"), "symbol" => '﷼', "format" => "{s} {n}");
		$lists["submissions"]["currency"]["ZAR"] = array("value" => "ZAR", "text" => JText::_("JFORMS_ZAR"), "symbol" => 'R', "format" => "{s} {n}");

		if($ctrl AND $fieldName AND isset($lists[$ctrl][$fieldName])){
			return $lists[$ctrl][$fieldName];
		} else if($ctrl AND isset($lists[$ctrl])){
			return $lists[$ctrl];
		} else if(!$ctrl AND !$fieldName) {
			return $lists;
		} else {
			return array();
		}
	}

	/**
	* Gets a list of the actions that can be performed.
	*
	* @access	public static
	*
	* @return	JObject	An ACL object containing authorizations
	*
	* @deprecated	Cook 2.0
	*/
	public static function getAcl($itemId = 0, $section = 'component' ,$user = null)
	{
		return self::getActions($itemId,$section,$user);
	}

	/**
	* Gets a list of the actions that can be performed.
	*
	* @access	public static
	* @param	integer	$itemId	The item ID.
	*
	* @return	JObject	An ACL object containing authorizations
	*
	* @since	1.6
	*/
	public static function getActions($itemId = 0, $section = 'component' ,$user = null)
	{
		static $sections;
		static $actionsByName;
		
		if(!($user instanceof JUser)){
			$user	= JFactory::getUser();
		}
		
		if (empty(self::$acl[$user->id])){
			self::$acl[$user->id] = array();
		}
		
		if(empty($sections)){
			$allActions = ByGiroHelper::getActions('com_jforms','*');
			$sections = ByGiroHelper::groupArrayByValue($allActions, 'section');
			$actionsByName = ByGiroHelper::groupArrayByValue($allActions, 'name',false);
		}
		
		if (!empty(self::$acl[$user->id][$section .'.'. $itemId]))
			return self::$acl[$user->id][$section .'.'. $itemId];
		
		$itemLevelTables = array();
		$asset = COM_JFORMS;
		if($itemId > 0 AND in_array($section,$itemLevelTables)){
			$asset = COM_JFORMS .'.'. $section .'.'. $itemId;
		}
		
		$result	= new JObject;
		foreach($sections as $secName => $sec){
			foreach($sec as $act){
				$action = $act->name;
				// override the component actions with the section actions
				if($section != 'component' AND $secName == 'component' AND !empty($sections[$section])){
					$actionOverrider = str_replace('core.',$section .'.',$act->name);
					if(isset($actionsByName[$actionOverrider])){
						$action = $actionOverrider;
					}
				}
				
				$result->set($act->name, $user->authorise($action, $asset));
			}
		}

		self::$acl[$user->id][$section .'.'. $itemId] = $result;

		return $result;
	}

	/**
	* Return the directories aliases full paths
	*
	* @access	public static
	*
	* @return	array	Arrays of aliases relative path from site root.
	*
	* @since	2.6.4
	*/
	public static function getDirectories()
	{
		if (!empty(self::$directories))
			return self::$directories;

		$comAlias = "com_jforms";
		$configMedias = JComponentHelper::getParams('com_media');
		$config = JComponentHelper::getParams($comAlias);

		$directories = array(
			'DIR_FILES' => "[COM_SITE]" .DS. "files",
			'DIR_FORMS_LANGUAGE_FILE' => $config->get("upload_dir_forms_language_file", "[COM_SITE]" .DS. "files" .DS. "forms_language_file"),
			'DIR_FORMS_FIELDSETS' => $config->get("upload_dir_forms_fieldsets", "[COM_SITE]" .DS. "files" .DS. "forms_fieldsets"),
			'DIR_FORMS_EMAILS' => $config->get("upload_dir_forms_emails", "[COM_SITE]" .DS. "files" .DS. "forms_emails"),
			'DIR_FORMS_EVENTS' => $config->get("upload_dir_forms_events", "[COM_SITE]" .DS. "files" .DS. "forms_events"),
			'DIR_SUBMISSIONS_PDF' => $config->get("upload_dir_submissions_pdf", "[COM_SITE]" .DS. "files" .DS. "submissions_pdf"),
			'DIR_SUBMISSIONS_ATTACHED_FILES' => $config->get("upload_dir_submissions_attached_files", "[COM_SITE]" .DS. "files" .DS. "submissions_attached_files"),
			'DIR_TRASH' => $config->get("trash_dir", 'images' . DS . "trash"),
			'COM_ADMIN' => "administrator" .DS. 'components' .DS. $comAlias,
			'ADMIN' => "administrator",
			'COM_SITE' => 'components' .DS. $comAlias,
			'IMAGES' => $config->get('image_path', 'images'),
			'MEDIAS' => $configMedias->get('file_path', 'images')
		);

		$bases = array(
			'COM_ADMIN' => "administrator" .DS. 'components' .DS. $comAlias,
			'ADMIN' => "administrator",
			'COM_SITE' => 'components' .DS. $comAlias,
			'IMAGES' => $config->get('image_path', 'images'),
			'MEDIAS' => $configMedias->get('file_path', 'images'),
		//	'ROOT' => '',

		);



		// Parse the directory aliases
		foreach($directories as $alias => $directory)
		{
			// Parse the component base folders
			foreach($bases as $aliasBase => $directoryBase)
				$directories[$alias] = preg_replace("/\[" . $aliasBase . "\]/", $directoryBase, $directories[$alias]);
	
			// Clean tags if remains
			$directories[$alias] = preg_replace("/\[.+\]/", "", $directories[$alias]);
		}

		self::$directories = $directories;
		return self::$directories;

	}

	/**
	* Get a file path or url depending of the method
	*
	* @access	public static
	* @param	string	$path	File path. Can contain directories aliases.
	* @param	string	$indirect	Method to access the file : [direct,indirect,physical]
	* @param	array	$options	File parameters.
	*
	* @return	string	File path or url
	*
	* @since	Cook 2.6.1
	*/
	public static function getFile($path, $indirect = 'physical', $options = null)
	{
		switch ($indirect)
		{
			case 'physical':	// Physical file on the drive (url is a path here)
				return JformsClassFile::getPhysical($path, $options);
	
			case 'direct':		// Direct url
				return JformsClassFile::getUrl($path, $options);
	
			case 'indirect':	// Indirect file access (through controller)
			default:
				return JformsClassFile::getIndirectUrl($path, $options);
		}
	}

	/**
	* Extract usefull informations from the thumb creator.
	*
	* @access	public static
	* @param	string	$path	File path. Can contain directories aliases.
	* @param	array	$options	File parameters.
	*
	* @return	mixed	Array of various informations
	*
	* @since	Cook 2.6.1
	*/
	public static function getImageInfos($path, $options = null)
	{
		include_once(JPATH_ADMIN_JFORMS .DS. 'classes' .DS. 'images.php');

		$filename = self::getFile($path, 'physical', null);

		$mime = JformsClassFile::getMime($filename);
		$thumb = new JformsClassImage($filename, $mime);

		$attrs = isset($options['attrs'])?$options['attrs']:null;
		$w = isset($options['width'])?(int)$options['width']:0;
		$h = isset($options['height'])?(int)$options['height']:0;

		if ($attrs)
			$thumb->attrs($attrs);

		$thumb->width($w);
		$thumb->height($h);
		$info = $thumb->info();
		
		return $info;
	}

	
	public static function getImgSizes(){
		$file = JPATH_ADMIN_JFORMS .DS. "classes" .DS. 'file' .DS. 'file.php';
		if(file_exists($file) AND !class_exists('JformsCkClassFile')){
			require_once($file);
		}

		$comAlias = 'jforms';
		
		$comAlias = strtoupper($comAlias);
		return (object)array(
			'minWidth' => (constant($comAlias .'_IMAGES_MIN_WIDTH') ?: 50),
			'maxWidth' => (constant($comAlias .'_IMAGES_MAX_WIDTH') ?: 1000),
			'minHeight' => (constant($comAlias .'_IMAGES_MIN_HEIGHT') ?: 50),
			'maxHeight' => (constant($comAlias .'_IMAGES_MAX_HEIGHT') ?: 1000)
		);
	}	
	
	/**
	* Get an indirect url to find image through model restrictions.
	*
	* @access	public static
	* @param	string	$view	List model name
	* @param	string	$key	Field name where is stored the filename
	* @param	string	$id	Item id
	* @param	array	$options	File parameters.
	*
	* @return	string	Indirect url
	*
	* @since	Cook 2.6.1
	*/
	public static function getIndexedFile($view, $key, $id, $options = null)
	{
		return JformsClassFile::getIndexUrl($view, $key, $id, $options);
	}

	/**
	* Load all menu items.
	*
	* @access	public static
	* @return	void
	*
	* @since	Cook 2.0
	*/
	public static function getMenuItems()
	{
		// Will be handled in XML in future (or/and with the Joomla native menus)
		// -> give your opinion on j-cook.pro/forum

		$items = array();

		$items['admin.forms.default'] = array(
			'label' => 'JFORMS_LAYOUT_FORMS',
			'view' => 'forms',
			'layout' => 'default',
			'icon' => 'jforms_forms'
		);

		$items['admin.submissions.default'] = array(
			'label' => 'JFORMS_LAYOUT_SUBMISSIONS',
			'view' => 'submissions',
			'layout' => 'default',
			'icon' => 'jforms_submissions'
		);

		$items['admin.cpanel'] = array(
			'label' => 'JFORMS_LAYOUT_JFORMS',
			'view' => 'cpanel',
			'icon' => 'jforms_cpanel'
		);

		$items['site.forms'] = array(
			'label' => 'JFORMS_LAYOUT_FORMS',
			'view' => 'forms',
			'icon' => 'jforms_forms'
		);

		$items['site.submissions'] = array(
			'label' => 'JFORMS_LAYOUT_MY_SUBMISSIONS',
			'view' => 'submissions',
			'icon' => 'jforms_submissions'
		);

		$items['site.cpanel'] = array(
			'label' => 'JFORMS_LAYOUT_JFORMS',
			'view' => 'cpanel',
			'icon' => 'jforms_cpanel'
		);

		return $items;
	}

	/**
	* Defines the headers of your template.
	*
	* @access	public static
	*
	* @return	void	
	* @return	void
	*/
	public static function headerDeclarations()
	{
		static $loaded;

		if (isset($loaded)){
			return;
		}
		
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$version = new JVersion();
		
		$siteUrl = JURI::root(true);

		$baseSite = 'components' .DS. COM_JFORMS;
		$baseAdmin = 'administrator' .DS. 'components' .DS. COM_JFORMS;

		$componentUrl = $siteUrl . '/' . str_replace(DS, '/', $baseSite);
		$componentUrlAdmin = $siteUrl . '/' . str_replace(DS, '/', $baseAdmin);

		//Required libraries
		//jQuery Loading : Abstraction to handle cross versions of Joomla
		JDom::_('framework.jquery');
		JDom::_('framework.jquery.noconflict');
		JDom::_('framework.bootstrap');
		
		JHtml::_('behavior.modal');
		JHtml::_('behavior.tooltip');
		
		JDom::_('framework.jquery.ajax');
		JDom::_('framework.hook');
		JDom::_('html.icon.glyphicon');
		JDom::_('html.icon.icomoon'); 
		JDom::_('framework.bootstrap.wizard');
		JDom::_('framework.jquery.msg');
		JDom::_('framework.jquery.extrajs');
		JDom::_('framework.jquery.condrules');
		JDom::_('framework.bootstrap.radiotobtn');

		//Load the jQuery-Validation-Engine (MIT License, Copyright(c) 2011 Cedric Dugas http://www.position-absolute.com)
		JDom::_('framework.jquery.validationengine');
		JdomHtmlValidator::loadLanguageScript();

		$base = JURI::root(true) .'/libraries/jdom/jform/assets/css/';			
		$doc->addStyleSheet($base .'jform.css');
			
		//CSS
		if ($app->isAdmin())
		{
			self::addStyleSheet($doc, $baseAdmin, 'css' .DS. 'jforms.css');
			self::addStyleSheet($doc, $baseAdmin, 'css' .DS. 'toolbar.css');
			
			$side = 'admin';
		}
		else if ($app->isSite())
		{
			self::addStyleSheet($doc, $baseSite, 'css' .DS. 'jforms.css');
			self::addStyleSheet($doc, $baseSite, 'css' .DS. 'toolbar.css');
			
			$side = 'site';
		}
		
		// js
		$doc->addScript($componentUrl . '/js/jforms.js');
			
		// backward compatibility		
		$extraFiles = array(
			'admin' => array(),
			'site' => array()
		);
		if (version_compare($version->RELEASE, '1.7', '<=')){
			$extraFiles['admin'] = array(
				'css' => JURI::root(true) .'/administrator/components/com_jforms/css/jforms_16.css',
				'js' => JURI::root(true) .'/administrator/components/com_jforms/js/jforms_16.js'
			);
			
			$extraFiles['site'] = array(
				'css' => JURI::root(true) .'/components/com_jforms/css/jforms_16.css'
			);

			JDom::_('framework.jquery.migrate');
		} else if(version_compare($version->RELEASE, '2.5', '<=')){
			$extraFiles['admin'] = array(
				'css' => JURI::root(true) .'/administrator/components/com_jforms/css/jforms_25.css'
			);
			
		} else if(version_compare($version->RELEASE, '3', '<=')){
		
		}

		unset($res);
		foreach($extraFiles[$side] as $k => $res){		
			if($k == 'js'){
				$doc->addScript($res);
			} else {
				$doc->addStyleSheet($res);
			}
		}


		$loaded = true;
	}

	/**
	* Load the fork file. (Cook Self Service concept)
	*
	* @access	public static
	* @param	string	$file	Current file to fork.
	* @return	void
	*
	* @since	2.6.3
	*/
	public static function loadFork($file)
	{
		//Transform the file path to reach the fork directory
		$file = preg_replace("#com_jforms#", 'com_jforms' .DS. 'fork', $file);

		// Load the fork file.
		if (!empty($file) && file_exists($file))
			include_once($file);
	}

	/**
	* Recreate the URL with a redirect in order to : -> keep an good SEF ->
	* always kill the post -> precisely control the request
	*
	* @access	public static
	* @param	array	$vars	The array to override the current request.
	*
	* @return	string	Routed URL.
	*/
	public static function urlRequest($vars = array())
	{
		$parts = array();

		// Authorisated followers
		$authorizedInUrl = array(
					'option' => null, 
					'view' => null, 
					'layout' => null, 
					'Itemid' => null, 
					'tmpl' => null,
					'object' => null,
					'lang' => null);

		$jinput = JFactory::getApplication()->input;

		$request = $jinput->getArray($authorizedInUrl);

		foreach($request as $key => $value)
			if (!empty($value))
				$parts[] = $key . '=' . $value;

		$cid = $jinput->get('cid', array(), 'ARRAY');
		if (!empty($cid))
		{
			$cidVals = implode(",", $cid);
			if ($cidVals != '0')
				$parts[] = 'cid[]=' . $cidVals;
		}

		if (count($vars))
		foreach($vars as $key => $value)
			$parts[] = $key . '=' . $value;

		return JRoute::_("index.php?" . implode("&", $parts), false);
	}


	public static function getMultilangTables(){		
		$tables = array();		
		$tables['forms'] = array(
			'name',
			'description',
			'message_after_submit',
			'language_file'
		);

		return $tables;
	}
	
	public static function getLabels($data){
		$labels = array();
		if(is_object($data)){
			$data = clone $data;
			$data = (array)$data;
		}
		
		@$jForm = $data['jforms_snapshot'];
		
		if(empty($jForm->fieldsets)){
			return array();
		}
		
		foreach($jForm->fieldsets as $mainFieldset){			
			if($mainFieldset->form instanceof JForm){
				$fieldsets = $mainFieldset->form->getFieldsets();
				
				foreach($fieldsets as $fset){
					$fields = $mainFieldset->form->getFieldset($fset->name);
					
					foreach($fields as $fi){
						if(method_exists($fi,'getOutput')){
							$arr = $fi->getAllLabels();
							if(count($arr) > 0){
								$labels[$fi->fieldname] = $arr;
							}
						}
					}
				}
			}
		}
	
		return $labels;
	}
	
	public static function replacer($str, $data, $getLabels = true, $callback = null, $options = null){
		static $labels;
		
		if(empty($labels)){			
			$labels = self::getLabels($data);
		}
		
		if(!$getLabels){
			$labels = array();
		}
		
		foreach($data as $key => $details){
			if(is_string($details)){

				// extra little things
				switch($key){
					case 'creation_date':
						$regex_simple = '[['. $key .':';
						$regex = '#\[\['. $key .'\:([^\]]+)\]\]#i';
					
						if (!(strpos($str, $regex_simple) === false)) {
							if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0){
								foreach ($matches as $match) {
									$str = str_replace($match['0'], date ($match['1'],$details), $str);
								}
							}
						}
						break;
						
					case 'pdf':
						$regex_simple = '[['. $key .':';
						$regex = '#\[\['. $key .'\:([^\]]+)\]\]#i';
					
						if (!(strpos($str, $regex_simple) === false)) {
							if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0){
								foreach ($matches as $match) {
									if(is_callable($callback)){
										$str = call_user_func($callback, $key, $match, $details, $str, $data, $labels, $options);
									} else {
										$str = str_replace($match['0'], $details, $str);
									}
								}
							}
						}
						break;						
						
					default:					
						break;
				}
				
				$regex_simple = '[['. $key .']]';
				$regex = '#[['. $key .']]#i';
				if(is_callable($callback)){
					if (!(strpos($str, $regex_simple) === false)){
						if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0){
							foreach ($matches as $match) {
								if(is_callable($callback)){
									$str = call_user_func($callback, $key, $match, $details, $str, $data, $labels, $options);
								} else {
									$str = str_replace($match['0'], $details, $str);
								}
							}
						}
					}
				} else {
					// simple replace
					$str = str_replace($regex_simple,$details,$str);
				}
			} else {
				$regex_simple = '[['. $key .':';
				$regex = '#\[\['. $key .'\:([^\]]+)\]\]#i';

				if (strpos($str, $regex_simple) !== false){
					if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0) {
						foreach ($matches as $match) {
							if(is_callable($callback)){
								$str = call_user_func($callback, $key, $match, $details, $str, $data, $labels, $options);
							} else {
								$str = self::replacerHelper($key, $match, $details, $str, $labels);
							}							
						}
					}
				}
			}	
		}

		if (preg_match_all('#\{([^\}]+)\}#', $str, $matches, PREG_SET_ORDER) > 0) {	
			foreach ($matches as $match) {				
				$str = str_replace($match['0'], JText::_(strtoupper($match['1'])), $str);
			}
		}
		
		return $str;
	}	

	public function replacerHelper($key, $match, $details, $str, $labels = array()){
			$value = ByGiroHelper::array_path_value($details, $match[1], $default = '', $delimiter = ':');
			
			if(is_array($value) OR is_object($value)){
				$html = '<ul>';
				foreach($value as $v){
					$v = (string)$v;
					if(isset($labels[$var])){
						$v = ctype_upper($labels[$var][$v]) ? JText::_($labels[$var][$v]) : $labels[$var][$v];
					}
				
					$html .= '<li>'. $v .'</li>';
				}
				$html .= '</ul>';							
				$value = $html;
			} else {
				if(isset($labels[$var])){
					$value = ctype_upper($labels[$var][$value]) ? JText::_($labels[$var][$value]) : $labels[$var][$value];
				}
			}						
			
			$str = str_replace($match['0'], $value, $str);
		return $str;
	}

	/**
	* Parse the directory aliases.
	*
	* @access	public static
	* @param	string	$path	File path. Can contain directories aliases.
	*
	* @return	string	Litteral unaliased file path or url.
	*
	* @since	Cook 2.6.1
	*/
	public static function getDirectory($path)
	{
		// TO DO: generalize this variable
		$comAlias = 'com_jforms';
		$markers = JformsHelper::getDirectories();

		// Parse the folders aliases
		if(strpos($path, '[') !== false){
			foreach($markers as $marker => $pathStr){
				$path = preg_replace("/\[" . $marker . "\]/", $pathStr, $path);
			}
		}

		// Protect against back folder
		$path = preg_replace("/\.\.+/", "", $path);
		
		// test for empty PATH
		$path_test = trim(str_replace('/','',$path));
		if($path_test == ''){
			// fallback folder
			$path = 'components' .DS. $comAlias .DS. 'files' .DS. 'orphans';
		}
		
		return $path;
	}	
	
	// get all fieldset data and single forms instances
	public static function getjFieldsets($jForm, $live = true, $load = true){
		if(is_array($jForm)){
			$jForm = (object)$jForm;
		} else if(!is_object($jForm)){
			return;
		}
	
		$app = JFactory::getApplication();
		$config	= JComponentHelper::getParams( 'com_jforms' );
		$writeFullXmlInLog = $config->get('writeFullXmlInLog',false);
		$emptyLogOnScriptExecution = $config->get('emptyXmlLogOnScriptExecution',true);
		$jForm_fsets_dir = $config->get('upload_dir_forms_fieldsets', '[COM_SITE]' .DS. 'files' .DS. 'forms_fieldsets');
		$jForm_fsets_dir = self::getDirectory($jForm_fsets_dir);

		static $cleaned;
		if($emptyLogOnScriptExecution){
			$logFile = JFactory::getConfig()->get('log_path') . DS . 'com_jforms.fieldsets.errors.php';
			if(empty($cleaned) AND is_file($logFile)){
				unlink($logFile);
			}
			$cleaned = true;
		}
		
		$jFieldsets = (!empty($jForm->fieldsets)) ? $jForm->fieldsets : array();
		if($live){
			// sorting fieldsets
			$jFieldsets = ByGiroHelper::sort_on_field($jFieldsets, 'ordering', 'ASC');	
		}		

		$jForm->fields = (array)$jForm->fields;
		$fset_fields = ByGiroHelper::groupArrayByValue($jForm->fields, 'fieldset');
		
		if($load){
			unset($fields);
			foreach($fset_fields as $fset_key => $fields){
				$fields = ByGiroHelper::groupArrayByValue($fields, 'enabled');
				
				// enabled fields
				if(!empty($fields[1])){
					$fields = ByGiroHelper::sort_on_field($fields[1], 'ordering', 'ASC');
				} else {
					$fields = array();
				}

				$fset_fields[$fset_key] = $fields;
			}
		}

		$k = 0;		
		foreach($jFieldsets as &$fset){
			$k++;

			if(strpos($fset->form_file,'[') !== false){
				$formFile = JPATH_SITE .DS. self::getDirectory($fset->form_file);
			} else {
				$formFile = JPATH_SITE .DS. $jForm_fsets_dir .DS. $fset->form_file;				
			}
			
			if($live AND $fset->form_file != '' AND is_file($formFile)){
				$xmlString = file_get_contents($formFile);
				$fset->form_file_content = $xmlString;
			}
			
			if(!$load OR (empty($fset->form_file_content) AND empty($fset_fields[$fset->id]))){
				continue;
			}

			// create FSET form
			$fset_form = false;
			if(!empty($fset->form_file_content)){
				// test the XML
				$msg = JText::sprintf('JFORMS_ERROR_INCORRECT_XML_FORM', $jForm->name .' - ID: '. $jForm->id, $fset->name .' - ID:'. $fset->id);		
				if(!ByGiroHelper::testXML($fset->form_file_content, $msg, $writeFullXmlInLog)) continue;
				
				$fset_form = JForm::getInstance('com_jforms.'. $jForm->id .'fset'. $k, $fset->form_file_content,array('control'=>'jform'));	
			}
			
			if($fset_form instanceof JForm){
				$fset->form = $fset_form;
			}			
		}

		$jForm->fieldsets = $jFieldsets;
		
		// add the fields
		if($load){
			$jForm->fields = ByGiroHelper::groupArrayByValue($jForm->fields, 'id', false);	
			$fitofo = new fitofo($fset_fields,$jForm);
			$jForm = $fitofo->getForm();	
		}

		return $jForm;
	}

	public static function getMainForm($jForm){
		if(is_array($jForm)){
			$jForm = (object)$jForm;
		} else if(!is_object($jForm)){
			return;
		}

		$form = null;
		foreach($jForm->fieldsets as $fset){
			if($fset->enabled === 'false' OR !$fset->enabled OR !($fset->form instanceof JForm)){
				continue;
			}
	
			// integrate forms
			if(!($form instanceof JForm)){
				$form = JForm::getInstance('com_jforms.main', $fset->form_file_content, array('control'=>'jform'));
				$form->addFieldPath(JPATH_SITE .DS. 'libraries/jdom/jform/fields');
				$form->addRulePath(JPATH_SITE .DS. 'libraries/jdom/jform/rules');				
			} else {
				// merge this form with the main form
				$form->load($fset->form_file_content, true);
			}
		}
			
		$jForm->form = $form;
		
		return $jForm;
	}
	
	// get the language files and load the current in memory
	public static function getjFormLanguageFiles($jForm, $live = true, $load = true){
		if(is_array($jForm)){
			$jForm = (object)$jForm;
		} else if(!is_object($jForm)){
			return;
		}
		
		$config	= JComponentHelper::getParams( 'com_jforms' );
		$jForm_lang_dir = $config->get('upload_dir_forms_language_file', 'components' .DS. 'com_jforms' .DS. 'files' .DS. 'forms_language_file');
		$jForm_lang_dir = self::getDirectory($jForm_lang_dir);
		$lang = JFactory::getLanguage();

		if($live){
			$installedLanguages = ByGiroHelper::getInstalledLanguages();
			$lang_prefix = array('default'=>'');
			foreach($installedLanguages as $lg){
				$lang_prefix[$lg->lang_code] = '_'. strtolower(str_replace('-','', $lg->lang_code));
			}
	
			foreach($lang_prefix as $key => $pref){
				$lang_var = 'language_file'. $pref;
				$lang_var_content = 'language_file_content'. $pref;				
		
				if(!empty($jForm->$lang_var)){
					$file = JPATH_SITE .DS. $jForm_lang_dir .DS. $jForm->$lang_var;
					if(strpos($jForm->$lang_var,'[') !== false){
						$file = JPATH_SITE .DS. self::getDirectory($jForm->$lang_var);
					}
					
					if(is_file($file)){
						$jForm->$lang_var_content = file_get_contents($file);
					}
				}
			}
		}

		if($load){
			$languages_to_load = array(
				'default' => '',
				$lang->getTag() => '_'. strtolower(str_replace('-','', $lang->getTag()))
			);
				
			$temp_files = array();
			foreach($languages_to_load as $joomlaTag => $jFormsTag){
				$langFileVar = 'language_file'. $jFormsTag;
				$lang_var_content = 'language_file_content'. $jFormsTag;
			
				if(!$live){
					if(empty($jForm->$lang_var_content)){
						continue;
					}
					
					// create a temp filename
					do{
						$filename = 'language/'. $joomlaTag .'/'. $joomlaTag .'.'. ByGiroHelper::generateRandomString(15) .'.ini';
					} while(file_exists($jForm_lang_dir .DS. $filename));
					
					$jForm->$langFileVar = $filename;
					
					$filePath = JPATH_SITE .DS. $jForm_lang_dir .DS. $jForm->$langFileVar;					
					file_put_contents($filePath, $jForm->$lang_var_content);
					
					$temp_files[] = $filePath;
				}
			
				if(!empty($jForm->$langFileVar)){
					self::loadCustomLangFile($jForm->$langFileVar,JPATH_SITE .DS. $jForm_lang_dir,$joomlaTag);
				}
			}
		}
		
		// remove the temp language files
		foreach($temp_files as $tmpFile){
			unlink($tmpFile);
		}
		
		return $jForm;
	}

	public static function loadCustomLangFile($filename,$dir,$language){
		if($filename == ''){
			return;
		}
		
		$lang = JFactory::getLanguage();

		$fake_extension = basename($filename);
		$fake_extension = explode('.',$fake_extension);
		$fake_extension = $fake_extension[1];
			
		$lang->load($fake_extension , JPath::clean($dir), $language, true);	
	
	}

	
	public static function triggerEvents($type,&$data){
		static $version;
		$errors = array();

		$config	= JComponentHelper::getParams( 'com_jforms' );
		$jForm_events_dir = $config->get('upload_dir_forms_events', 'components' .DS. 'com_jforms' .DS. 'files' .DS. 'forms_events');
		$jForm_events_dir = self::getDirectory($jForm_events_dir);
		
		if(empty($version)){
			$version = new JVersion();
		}
		
		if(!is_array($data)){
			$data = (array)$data;
		}
		
		@$snapshot = (!empty($data['jforms_snapshot'])) ? (object)$data['jforms_snapshot'] : new stdClass;

		@$events = (!empty($snapshot->events)) ? $snapshot->events : array();
		foreach($events as $event){
			$condition = $event->event != $type;
			if(is_array($type)){
				$condition = !in_array($event->event,$type);
			}
			if(!$event->enabled OR $condition){
				continue;
			}
			
			$filePath = '';
			if(!empty($event->file)){
				$fileURL = $jForm_events_dir .'/'. $event->file;
				if(strpos($event->file,'[') !== false){
					$fileURL = self::getDirectory($event->file);
				}
				$filePath = JPath::clean(JPATH_SITE .DS. $fileURL);
			}
			
			if(is_file($filePath)){
				// detect the type of file
				switch(JFile::getExt($file)){
					case 'js':
						$url = preg_replace( '/\\\\+/', '/', JURI::root(true) . $fileURL);
						echo '<script src="'. $url .'">';
						break;
						
					case 'css':
						$url = preg_replace( '/\\\\+/', '/', JURI::root(true) . $fileURL);
						echo '<link rel="stylesheet" type="text/css" href="'. $url .'">';					
						break;
						
					default:
						// include the file
						try {
							include $filePath;
						} catch (Exception $e) {
							$errors[] = $e->getMessage();
						}
						
						break;
				}
			}

			if($event->script != ''){
				$script = $event->script;
				try {
					eval("?> $script <?php ");
				} catch (Exception $e) {
					$errors[] = $e->getMessage();
				}
			}
			
		}

		// trigger joomla jForms plugins
		JPluginHelper::importPlugin( 'jForms' );
		
		// Joomla! 1.6 - 1.7 - 2.5
		if (version_compare($version->RELEASE, '2.5', '<='))
		{	
			$dispatcher = JDispatcher::getInstance();
		} else {
			$dispatcher = JEventDispatcher::getInstance();
		}
		
		$dispatcher->trigger( $type, $data );
	}
	
	public static function addMirrorElements(&$item){
		static $allForms;
		static $processedForms;
		
		if(!isset($allForms)){
			// get all forms (fieldsets,events,emails,layouts)
			$db = JFactory::getDBO();
			$query = "SELECT id,name,fieldsets,events,emails,layouts FROM #__jforms_forms WHERE 1 ORDER BY ordering";
			$db->setQuery($query);
			$allForms = $db->loadAssocList('id');
		}
		
		if(!isset($processedForms)){
			$processedForms = array();
		}
		
		$elements = array('fieldsets','events','emails','layouts');
		
		foreach($elements as $f){
			if(empty($item->$f)){
				continue;
			}
			
			foreach($item->$f as &$ele){
				if(empty($ele->mirror)){
					continue;
				}
				
				$mirror_data = (array)json_decode($ele->mirror);				
				$mirror_form = $allForms[$mirror_data['mainId']];
				
				if(empty($mirror_form)){
					continue;
				}
				
				if(empty($processedForms[$mirror_data['mainId']])){
					$mirror_form = ByGiroHelper::stringToJsonFields($mirror_form);
					
					foreach($elements as $fs){
						$mirror_form[$fs] = ByGiroHelper::groupArrayByValue($mirror_form[$fs], 'id', false);
					}
					
					$processedForms[$mirror_data['mainId']] = $mirror_form;
				}
	
				$mirror_ele = $processedForms[$mirror_data['mainId']][$f][$mirror_data['subId']];				
				if(!empty($mirror_ele)){
					$ele = ByGiroHelper::array_replace_i($mirror_ele,$ele, true, true);
				}				
			}
		}
	}
	
	public static function buildFormParts($form, $jForm = null, $dataObject = array(), $options = array()){
		static $formsParsed;
	
		if(!isset($formsParsed)){
			$formsParsed = array();
		}
		
		if(empty($jForm)){
			if(!empty($form->jForm)){
				$jForm = $form->jForm;
			} else {
				return array();
			}
		}
		
		$hash = MD5(json_encode($jForm) . json_encode($dataObject) . json_encode($options));
		if(!empty($formsParsed[$hash])){
			return $formsParsed[$hash];
		}

		$jFieldsets = (!empty($jForm->fieldsets)) ? $jForm->fieldsets : array();
	
		$steps = array();
		$fsets = array();
		$fields = array();
		$counter = 1;
		foreach($jFieldsets as $jFset){		
			if(empty($jFset->form) OR !($jFset->form instanceof JForm)){
				continue;
			}

			$fsetForm = $jFset->form;			
			$jFset = ByGiroHelper::getMlFields($jFset,array('name','description'));
			
			$stepName = $jFset->name_ml;
			if(empty($stepName)){
				$stepName = JText::_('JFORMS_STEP') . $counter;
			}

			$counter++;			
			
			@$steps[$jFset->id] = array(
				'id' => $jFset->id,
				'name' => $stepName,
				'description' => $jFset->description_ml,
				'class' => $jFset->class
			);

			$fieldSets = $fsetForm->getFieldsets();

			foreach($fieldSets as &$fset){
				// repeatable fieldset
				if(strpos($fset->name,'_modal') !== false){
					continue;
				}

				if(!empty($form->isCustom) AND !empty($form->preform) AND empty($fset->showinpreform)){
					continue;
				}
				
				// check ACL
				if(!empty($fset->canView) AND class_exists('ByGiroHelper')){
					$fset->canView = ByGiroHelper::canAccess($fset->canView);					
				} else {
					$fset->canView = true;
				}

				if(!$fset->canView){
					continue;
				}

				if(!empty($fset->canEdit) AND class_exists('ByGiroHelper')){
					$fset->canEdit = ByGiroHelper::canAccess($fset->canEdit);
				} else {
					$fset->canEdit = true;
				}

				$jdomOptions = array('comAlias' => 'jforms');
				if(!$fset->canEdit){
					$jdomOptions = array(
						'canEdit' => false
					);
				}
				
				if($fset->label != ''){
					$label = ctype_upper($fset->label) ? JText::_($fset->label) : $fset->label;
				} else {
					$label = $fset->name;
				}
				
				if(!empty($fset->description)){
					$description = ctype_upper($fset->description) ? JText::_($fset->description) : $fset->description;
				}	

				// get the fields				
				$fset_fields = JDom::_('html.form.fieldset', array(
								'fieldsetName' => $fset->name,
								'form' => $form,
								'jdomOptions' => $jdomOptions,
								'dataObject' => $dataObject,
								'format' => 'array',
								'layout' => @$options['layout']
							));
	
				$fields = array_merge($fields,$fset_fields);
				
				@$overrideData = array(
					'label' => $label,
					'description' => $description,
					'stepId' => $jFset->id
				);
				
				$fsets[$fset->name] = ByGiroHelper::array_replace_i(array(),$fset,$overrideData);
			}
		}

		$data = array(
			'steps' => $steps,
			'fieldsets' => $fsets,
			'fields' => $fields
		);

		$formsParsed[$hash] = $data;
		
		return $data;
	}

	
	public static function getJformsTags($str, $regex = null){
		if(empty($regex)){
			return array();
		}
		
		if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0){
			return $matches;
		}
		
		return array();
	}

	
	public static function parseOptions($opts){
		$overrides = array();
		if(empty($opts)){
			return $overrides;
		}
		
		$opts = explode('|',$opts);
		
		unset($op);
		foreach($opts as $op){
			@list($key, $val) = explode('=', $op, 2);
			$key = trim($key);
			$val = trim($val);
			
			if($key != ''){
				$overrides[$key] = $val;
			}
		}
		
		natsort($overrides);		
		return $overrides;
	}
	
	public static function getLayoutTags($str, $typeTag = 'step', $regex = array()){
		$steps = array(
			'custom' => array(),
			'simple' => array(),
		);
		
		$config = JComponentHelper::getParams('com_jforms');
		$tag_step_open = $config->get('tag_'. $typeTag .'_open', $typeTag);
		$tag_step_close = $config->get('tag_'. $typeTag .'_close', '/'. $typeTag);
		
		if(empty($regex)){		
			$regex = array();
			$regex['regex'] =
				'(\{' . $tag_step_open
				. '(?:\:([0-9]+)+)?'
				. '((?: |&nbsp;)?'
				. '.*?)?'
				. '\})'
				. '(.*?)?'
				. '\{' . $tag_step_close .'\}';
				
			$regex['regex_simple'] = 
				'\{' . $tag_step_open
				. '(?:\:([0-9]+)+)?'
				. '((?: |&nbsp;)?'
				. '.*?)?'
				. '\}';
		}
		
		self::protect($str);
		
		$matches = self::getJformsTags($str,'#'. $regex['regex'] .'#is');
		foreach($matches as $ma){		
			// check nested jforms tags
			while(strpos($ma[4],'{' . $tag_step_open .':') !== false){
				// remove the first tag found
				$tempStr = str_replace($ma[1],'',$ma[0]);
				$tempMatches = self::getJformsTags($tempStr,'#'. $regex['regex'] .'#is');
				
				if(empty($tempMatches)){
					break;	
				}
				
				$ma = $tempMatches[0];
			}

			$steps['custom'][] = array(
				'match' => $ma[0],
				'headTag' => $ma[1],
				'id' => $ma[2],
				'options' => $ma[3],
				'content' => $ma[4]
			);

			$str = str_replace($ma[0],'',$str);
		}
		
		// manage simple tags
		$matches = self::getJformsTags($str,'#'. $regex['regex_simple']  .'#is');		
		unset($ma);
		foreach ($matches as $ma){

			$steps['simple'][] = array(
				'match' => $ma[0],
				'id' => $ma[1],
				'options' => $ma[2]
			);

			$str = str_replace($ma[0],'',$str);
		}		
		
		return $steps;
	}
	
	/**
	 * replace any protected text to original
	 */
	public static function unprotect(&$str)
	{
		$protect_a = '<!-- >> JFORMS_PROTECTED >> -->';
		$protect_b = '<!-- << JFORMS_PROTECTED << -->';
		
		$regex = '#' . preg_quote($protect_a, '#') . '(.*?)' . preg_quote($protect_b, '#') . '#si';
		while (preg_match_all($regex, $str, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$str = str_replace($match['0'], base64_decode($match['1']), $str);
			}
		}
	}

	public static function protect(&$str){
		self::protectScripts($str);
	}

	public static function protectScripts(&$str)
	{
		if (strpos($str, '</script>') === false)
		{
			return;
		}

		$regex = '#<script[\s>].*?</script>#si';

		self::protectByRegex($str, $regex);
	}
	
	/**
	 * protect all text based form fields
	 */
	public static function protectByRegex(&$str, $regex)
	{
		$protect_a = '<!-- >> JFORMS_PROTECTED >> -->';
		$protect_b = '<!-- << JFORMS_PROTECTED << -->';
		
		if (preg_match_all($regex, $str, $matches) > 0)
		{
			$matches = array_unique($matches['0']);

			foreach ($matches as $match)
			{
				$protected = $protect_a . base64_encode($match) . $protect_b;
				$str = str_replace($match, $protected, $str);
			}
		}
	}
	
	public static function replaceField($key, $match, $details, $str, $data, $labels, $options){
	
		// check we have HTML in the route
		$route = strtolower($match[1]);
		
		$isInput = strpos($route,':html:input') !== false;
		$isOutput = strpos($route,':html:output') !== false;
		if($isInput OR $isOutput){
			$parentRoute = str_replace(
				array(':html:input',':html:output'),
				'',
				$route
			);
			$fi = ByGiroHelper::array_path_value($details, $parentRoute, $default = '', $delimiter = ':');
			if(!empty($fi['html']) AND !empty($fi['field'])){
				$htmlType = ($isInput) ? 'input' : 'output';
				
				if($fi['field']->type == 'ckfile' AND isset($options['jdomOptions'])){
					$fi['field']->jdomOptions = array_merge((array)$fi['field']->jdomOptions,(array)$options['jdomOptions']);
				}
				$value = ByGiroHelper::getHtmlField($fi['html'],$fi['field'],$htmlType,$options['form']);
			}
		}

		$route = explode(':',$route);
		@$last = $route[(count($route)-1)];

		if(empty($value)){
			switch($last){
				case 'html':
					array_pop($route);
					$fi = ByGiroHelper::array_path_value($details, $route, $default = '', $delimiter = ':');
					$value = ByGiroHelper::getHtmlField($fi['html'],$fi['field'],$options['type'],$options['form']);
					break;
				
				case 'input':
				case 'output':
				case 'label':
					$fi = ByGiroHelper::array_path_value($details, $route, $default = '', $delimiter = ':');
					$value = ByGiroHelper::renderField($fi['field'],$last,$options['form']);			
					break;

				
				case 'filename':
				case 'dir':
				case 'directlink':
				case 'indirectlink':
					array_pop($route);
					$fi = ByGiroHelper::array_path_value($details, $route, $default = '', $delimiter = ':');
					$value = $fi['field']->value;
					
					if(@$fi['xml']['type'] == 'ckfile'){
						switch($last){
							case 'filename':
								$value = basename($value);
								break;
								
							case 'dir':
								$value = self::getDirectory(dirname($value));
								break;
								
							case 'directlink':
								$value = JURI::root() . '/' . self::getDirectory($value);
								break;
								
							case 'indirectlink':
								$value = JURI::root() . '/' . 'index.php?option=com_jforms&task=file&path='. $value .'&action=download';
								break;
						}
					}
					break;
				
				case 'safe_url_to_file':
					$shortRoute = str_replace('safe_url_to_file','',strtolower($match[1]));
					$value = ByGiroHelper::array_path_value($details, $shortRoute, (string)$details, $delimiter = ':');	
					if(!empty($value)){
						$value = JURI::root() ."/index.php?option=com_jforms&task=file&path=". $value;
					} else {
						unset($value);
					}					
					break;
					
				case 'direct_url_to_file':
					$shortRoute = str_replace('direct_url_to_file','',strtolower($match[1]));
					$value = ByGiroHelper::array_path_value($details, $shortRoute, $details, $delimiter = ':');
					
					if(!empty($value)){
						$value = preg_replace( '/\\\\+/', '/', JURI::root() . self::getDirectory($value));
					} else {
						unset($value);
					}
					break;
			}
		}
		
		if(empty($value)){
			return self::replacerHelper($key, $match, $details, $str, $labels);
		}
		
		$str = str_replace($match['0'], $value, $str);		
		return $str;
	}
	
	public static function replaceLayoutSteps($jForm,$form,&$layout,$dataObject = array(),$options = array(),&$wizard_steps_lists = array()){
		$config = JComponentHelper::getParams('com_jforms');
		$tag_step_open = $config->get('tag_step_open', 'step');
		$tag_step_close = $config->get('tag_step_close', '/step');

		$formParts = self::buildFormParts($form, $jForm, $dataObject, $options);
		$layout_type = (!empty($options['layout_type'])) ? $options['layout_type'] : $jForm->layout_type;
		$layoutSteps = self::getLayoutTags($layout);

		if((count($layoutSteps['custom']) + count($layoutSteps['simple'])) <= 1){
			$layout_type = 'single_form';
		}

		// replace custom steps
		foreach($layoutSteps['custom'] as $layStep){
			$step = self::parseOptions($layStep['options']);		
			if(!empty($layStep['id']) AND !empty($formParts['steps'][$layStep['id']])){
				$jStep = $formParts['steps'][$layStep['id']];
				$step = ByGiroHelper::array_replace_i($jStep,$step);
			}
					
			$step['step_ordering'] = strpos($layout,$layStep['match']);
			$step['hash'] = MD5(json_encode($step) . $step['step_ordering']);
			$wizard_steps_lists[] = $step;
			
			$replacement = $layStep['match'];
			
			// replace head
			$extra_class = '';
			$extra_selectors = '';
			@$extra_content = $step['description'];
			if($layout_type == 'wizard'){
				$extra_class = ' step-pane';
				$extra_selectors = 'id="'. $jForm->formHash .'Step'. $step['hash'] .'"';
			} else {
				@$extra_content = '<legend>'. $step['name'] .'</legend>' . $extra_content;
			}

			@$head = '<div class="fieldsform '. $step['class'] . $extra_class .'" '. $extra_selectors .'>'
				. $extra_content;
			
			$replacement = str_replace($layStep['headTag'],$head,$replacement);
			
			// replace foot
			$foot = '</div>';		
			$replacement = str_replace('{'. $tag_step_close .'}',$foot,$replacement);
		
			$layout = str_replace($layStep['match'],$replacement,$layout);
		}
		
		// replace SIMPLE
		$formParts_fieldsets = ByGiroHelper::groupArrayByValue($formParts['fieldsets'], 'stepId');
		$formParts_fields = ByGiroHelper::groupArrayByValue($formParts['fields'], 'fieldsetName');

		unset($layStep);
		foreach($layoutSteps['simple'] as $layStep){	
			$step = self::parseOptions($layStep['options']);
			if(!empty($layStep['id']) AND !empty($formParts['steps'][$layStep['id']])){
				$jStep = $formParts['steps'][$layStep['id']];
				$step = ByGiroHelper::array_replace_i($jStep,$step);
			} else {
				$layout = str_replace($layStep['match'],'',$layout);
				continue;
			}

			$step['step_ordering'] = strpos($layout,$layStep['match']);
			$step['hash'] = MD5(serialize($step) . $step['step_ordering']);
			$wizard_steps_lists[] = $step;		
		
			// replace head
			$extra_class = '';
			$extra_selectors = '';
			@$extra_content = $step['description'];
			if($layout_type == 'wizard'){
				$extra_class = ' step-pane';
				$extra_selectors = 'id="'. $jForm->formHash .'Step'. $step['hash'] .'"';
			} else {
				@$extra_content = '<legend>'. $step['name'] .'</legend>' . $extra_content;
			}

			@$head = '<div class="'. $step['class'] . $extra_class .'" '. $extra_selectors .'>'
				. $extra_content;
			
			// replace foot
			$foot = '</div>';

			$fieldsets = (!empty($formParts_fieldsets[$step['id']])) ? $formParts_fieldsets[$step['id']] : array();
			unset($fset);
			$replacement = '';
			foreach($fieldsets as $key => $fset){		
				@$replacement .= '<fieldset class="fieldsform '. $fset['class'] .'">
					<legend>'. $fset['label'] .'</legend>				
					'. $fset['description'];			

				$fields = (!empty($formParts_fields[$key])) ? $formParts_fields[$key] : array();
				unset($fi);
				foreach($fields as $fi){
					$replacement .= ByGiroHelper::getHtmlField($fi['html'],$fi['field'],'input',$form);
				}

				$replacement .= '</fieldset>';
			}
		
			$replacement = $head . $replacement . $foot;
			
			$layout = str_replace($layStep['match'],$replacement,$layout);
		}
		
		return $layout_type;
	}
	
	public static function replaceLayoutFieldsets($jForm,$form,&$layout,$dataObject = array(),$options = array()){
		$config = JComponentHelper::getParams('com_jforms');
		$tag_fset_open = $config->get('tag_fset_open', 'fset');
		$tag_fset_close = $config->get('tag_fset_close', '/fset');

		$formParts = self::buildFormParts($form, $jForm, $dataObject, $options);
		$formParts_fields = ByGiroHelper::groupArrayByValue($formParts['fields'], 'fieldsetName');
		
		$regex = array();
		$regex['regex'] =
			'(\{' . $tag_fset_open
			. '(?:\:([a-zA-Z0-9_]+)+)?'
			. '((?: |&nbsp;)?'
			. '.*?)?'
			. '\})'
			. '(.*?)?'
			. '\{' . $tag_fset_close .'\}';
			
		$regex['regex_simple'] = 
			'\{' . $tag_fset_open
			. '(?:\:([a-zA-Z0-9_]+)+)?'
			. '((?: |&nbsp;)?'
			. '.*?)?'
			. '\}';		
		
		$layoutFsets = self::getLayoutTags($layout,'fset', $regex);
		foreach($layoutFsets['simple'] as $key => $layFset){	
			$fset = self::parseOptions($layFset['options']);
			if(!empty($layFset['id']) AND !empty($formParts['fieldsets'][$layFset['id']])){
				$jFset = $formParts['fieldsets'][$layFset['id']];
				$fset = ByGiroHelper::array_replace_i($jFset,$fset);
			} else {
				$layout = str_replace($layFset['match'],'',$layout);
				continue;
			}
		
			$fset['html'] = (!empty($fset['html']) AND strtolower($fset['html']) == 'input') ? 'input' : 'output';

			
			@$replacement = '<div class="'. $fset['class'] .'">
				<h3 class="legend">'. $fset['label'] .'</h3>				
				'. $fset['description'];			

			$fields = (!empty($formParts_fields[$fset['name']])) ? $formParts_fields[$fset['name']] : array();
			unset($fi);
			foreach($fields as $fi){
				$replacement .= ByGiroHelper::getHtmlField($fi['html'],$fi['field'],$fset['html'],$form);
			}

			$replacement .= '</div>';		
			$layout = str_replace($layFset['match'],$replacement,$layout);
		}
	}
	
	/**
	 * Gets a list of payment plugins and their titles
	 */
	public static function getPaymentPlugins()
	{
		JLoader::import('joomla.plugin.helper');
		JPluginHelper::importPlugin('jformspayments');
		$app = JFactory::getApplication();
		$jResponse = $app->triggerEvent('onPaymentGetType');

		$ret = array();

		foreach ($jResponse as $item)
		{
			if (is_object($item))
			{
				$ret[$item->name] = $item;
			}
			elseif (is_array($item))
			{
				if (array_key_exists('name', $item))
				{
					$ret[$item['name']] = (object)$item;
				}
				else
				{
					foreach ($item as $anItem)
					{
						if (is_object($anItem))
						{
							$ret[$anItem->name] = $anItem;
						}
						else
						{
							$ret[$anItem['name']] = (object)$anItem;
						}
					}
				}
			}
		}

		return $ret;
	}
	
	public static function formatPrice($price = 0,$currency_code = null){
		$config = JComponentHelper::getParams('com_jforms');
		
		if(empty($currency_code)){
			$currency_code = $config->get("currency", "USD");
		}
		
		$currencies = self::enumList('submissions', 'currency');
		
		if(empty($currency_code) OR !in_array($currency_code,array_keys($currencies))){
			return $price;
		}
		$currency = $currencies[$currency_code];

		$format = $config->get("currency_display_format", "{s} {n}");		
		$type = $config->get("currency_display_type", "symbol");
		$result = str_replace(array('{s}','{n}'),array($currency[$type],$price),$format);
		
		return $result;
	}
	
	public static function calculateSubTotal($item){
		$jForms = $item->jforms_snapshot;
		$basePrice = $jForms->options->price;
		$result = array(
			'details' => array(),
			'subTotal' => $basePrice
		);

		if(!($jForms->form instanceof JForm)){
			return $result;
		}
		
		// get priceRules
		$rules = array();
		
		//  fieldsets
		foreach($jForms->form->getFieldsets() as $fset){
			
			foreach($jForms->form->getFieldset($fset->name) as $fi){
				$class = $jForms->form->getFieldAttribute($fi->fieldname,'class',null,$fi->group);
				
				if(strpos($class,'condRule[priceRule') === false) continue;
				
				if(empty($item->form_data->{$fi->fieldname})) continue;
				
				// we have a valid price rule
				$value = $item->form_data->{$fi->fieldname};
				
				$fiLabel = $fi->label;
				// if radio or checkBox -> getOption label
				if(in_array($fi->type,array('ckradio','ckcheckboxes','cklist'))){
					$opts = $fi->getOpts();
					$opts = ByGiroHelper::groupArrayByValue($opts, 'value', false);	
				}
				
				$classes = explode(' ',$class);
				foreach($classes as $cl){
					$valid = false;
					if(strpos($cl,'condRule[priceRule') === false) continue;
					
					$ruleParts = array_map('trim', explode(',',substr($cl,9,(strlen($cl)-10))));
					$task = $ruleParts[0];
					$target = $ruleParts[1];
					$ruValue = explode('|',$ruleParts[2]);
					
					if($target != 'this') continue;
					
					
					$optLab = '';
					if(in_array('*',$ruValue)){
						$valid = true;
					} else {
						if(is_array($value)){
							$intersect = array_intersect($value,$ruValue);
							if(!empty($intersect)){
								$valid = true;
								foreach($intersect as $in){
									if(!empty($opts[$in]->text)){
										$optLab .= ' <span class="">'. $opts[$in]->text .'</span>';
									}
								}
							}
						} else if(in_array($value,$ruValue)){
							$valid = true;
							if(!empty($opts[$value]->text)){
								$optLab .= ' <span class="">'. $opts[$value]->text .'</span>';
							}
						}
					}
					if(!$valid) continue;
					
					$task = array_map('trim', explode('|',substr($task,10,(strlen($task)-11))));
					$label = $fiLabel;
					if(in_array($fi->type,array('ckradio','ckcheckboxes','cklist'))){
						$label .= $optLab;
					}
					
					$rules[] = (object)array(
						'variation' => $task[0],
						'price' => $task[1],
						'type' => $task[2],
						'label' => $label,
						'inputVal' => $value
					);
				}
				
			}
		}
		
		// perform rules
		$partial = floatval($basePrice);

		unset($r);
		foreach($rules as $r){
			$price = $r->price;
			if(empty($r->price) OR $r->price == 'this'){
				if(is_array($r->inputVal)) {
					$price = $r->inputVal[0];
				} else {
					$price = $r->inputVal;
				}
			}
			
			if(empty($price)) continue;
			
			$type = '';
			$info = '';
			$priceVal = $price;
			$variation = !empty($r->variation) ? $r->variation : '+';
			if($r->type == '%'){
				$priceVal = $price * $partial / 100;
				$type = '%';
			}
			$info = ' <span class="small">('. $variation .' '. $price .''. $type .')</span>';

			$e = false;
			try{
				$tpartial = eval('return '. $partial .' '. $variation .' '. $priceVal .';');
			} catch(Exception $e){
				
			}
			
			if($e) continue;

			$amount_variation = $tpartial - $partial;
			$priceVar = ($amount_variation < 0) ? '-' : '+';
			$amount_variation = abs($amount_variation);
			
			$partial = $tpartial;
			
			// details
			$result['details'][] = (object)array(
				'variation' => $priceVar,
				'amount_variation' => round($amount_variation,2),
				'info' => $r->label . $info
			);			
		}
		$result['subTotal'] = round($partial,2);
		
		return (object)$result;
	}
		
	public static function checkMaxSubmissions($form_id = null, $aclConfig = array(),$user = null){
		if(empty($form_id)) return true;
		if(empty($aclConfig)) return true;

		if(empty($user)){
			$user = JFactory::getUser();
		}
		
		$userConfig = ByGiroHelper::getUserAclConfig($aclConfig, $user);

		if(empty($userConfig['max_submissions'])) return true;
		
		$userId = $user->id ?: 0;
		
		// count submissions for this user and this form
		$db = JFactory::getDBO();		
		//Get all items
		$query = "SELECT COUNT(*) FROM `#__jforms_submissions` WHERE created_by =". $userId ." AND form_id =". $form_id;
		$db->setQuery($query);
		
		$count = $db->loadResult();
		
		return $userConfig['max_submissions'] > $count;
	}

}

// Load the fork
JformsCkHelper::loadFork(__FILE__);

// Fallback if no fork has been found
if (!class_exists('JformsHelper')){ class JformsHelper extends JformsCkHelper{} }

