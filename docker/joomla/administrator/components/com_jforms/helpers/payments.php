<?php
/**
* @version		0.4.4
* @package		jForms
* @subpackage	
* @copyright	G. Tomaselli
* @author		G. Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		GNU GPL v3 or later
 */

defined('_JEXEC') or die();

JLoader::import('joomla.plugin.plugin');
JLoader::import('joomla.html.parameter');

/**
 * jForms payment plugin abstract class
 */
abstract class plgJformspaymentsAbstract extends JPlugin
{
	/** @var string Name of the plugin, returned to the component */
	protected $ppName = 'abstract';

	/** @var string Translation key of the plugin's title, returned to the component */
	protected $ppKey = 'PLG_JFORMSPAYMENTS_ABSTRACT_TITLE';

	/** @var string Image path, returned to the component */
	protected $ppImage = '';
	
	protected $_order = null;
	
	protected $_vendor = null;
	
	public function __construct(&$subject, $config = array())
	{
		if(!is_object($config['params'])) {
			JLoader::import('joomla.registry.registry');
			$config['params'] = new JRegistry($config['params']);
		}

		parent::__construct($subject, $config);

		if(array_key_exists('ppName', $config)) {
			$this->ppName = $config['ppName'];
		}
		
		if(array_key_exists('ppCurrencies', $config)) {
			$this->ppCurrencies = $config['ppCurrencies'];
		}

		if(array_key_exists('ppImage', $config)) {
			$this->ppImage = $config['ppImage'];
		}

		$name = $this->ppName;

		if(array_key_exists('ppKey', $config)) {
			$this->ppKey = $config['ppKey'];
		} else {
			$this->ppKey = "PLG_JFORMSPAYMENTS_{$name}_TITLE";
		}

		// Load the language files
		$jlang = JFactory::getLanguage();
		$jlang->load('plg_jformspayments_'.$name, JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_jformspayments_'.$name, JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('plg_jformspayments_'.$name, JPATH_ADMINISTRATOR, null, true);
		
		$this->initialized = false;
	}

	public final function onPaymentGetType()
	{
		$title = $this->params->get('title','');
		if(empty($title)) $title = JText::_($this->ppKey);

		$image = trim($this->params->get('ppimage',''));
		if(empty($image)) {
			$image = $this->ppImage;
		}

		$ret = array(
			(object)array(
				'name'		=> $this->ppName,
				'title'		=> $title,
				'image'		=> $image,
				'client_desc' => $this->params->get('client_desc',''),
				'currencies'=> $this->ppCurrencies,
				'currency' => $this->params->get('currency',$this->ppCurrencies[0])
			)
		);

		return $ret;
	}

	/**
	 * Returns the payment form to be submitted by the user's browser. The form must have an ID of
	 * "paymentForm" and a visible submit button.
	 *
	 * @param string $paymentmethod Check it against $this->ppName
	 * @param JUser $user
	 * @param AkeebasubsTableLevel $level
	 * @param AkeebasubsTableSubscription $subscription
	 * @return string
	 */
	abstract public function onNewPayment($paymentmethod, $vendor, $order, $client, $params);

	/**
	 * Processes a callback from the payment processor
	 *
	 * @param string $paymentmethod Check it against $this->ppName
	 * @param array $data Input data
	 */
	abstract public function onPaymentCallback($paymentmethod, $data_sent, $data_received, $params);

	
	abstract public function getOrderId($paymentmethod, $data_received);
	
	public function fixDates(){}

	/**
	 * Logs the received IPN information to file
	 *
	 * @param array $data
	 * @param bool $isValid
	 */
	protected final function logIPN($data, $isValid)
	{
		$config = JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$logpath = $config->get('log_path');
		} else {
			$logpath = $config->getValue('log_path');
		}

		$logFilenameBase = $logpath.'/payment_'.strtolower($this->ppName).'_ipn';

		$logFile = $logFilenameBase.'.php';
		JLoader::import('joomla.filesystem.file');
		if(!JFile::exists($logFile)) {
			$dummy = "<?php die(); ?>\n";
			JFile::write($logFile, $dummy);
		} else {
			if(@filesize($logFile) > 1048756) {
				$altLog = $logFilenameBase.'-1.php';
				if(JFile::exists($altLog)) {
					JFile::delete($altLog);
				}
				JFile::copy($logFile, $altLog);
				JFile::delete($logFile);
				$dummy = "<?php die(); ?>\n";
				JFile::write($logFile, $dummy);
			}
		}
		$logData = JFile::read($logFile);
		if($logData === false) $logData = '';
		$logData .= "\n" . str_repeat('-', 80);
		$pluginName = strtoupper($this->ppName);
		$logData .= $isValid ? 'VALID '.$pluginName.' IPN' : 'INVALID '.$pluginName.' IPN *** FRAUD ATTEMPT OR INVALID NOTIFICATION ***';
		$logData .= "\nDate/time : ".gmdate('Y-m-d H:i:s')." GMT\n\n";
		foreach($data as $key => $value) {
			$logData .= '  ' . str_pad($key, 30, ' ') . $value . "\n";
		}
		$logData .= "\n";
		JFile::write($logFile, $logData);
	}
	
	/**
	 * Translates the given 2-digit country code into the 3-digit country code.
	 *
	 * @param string $country
	 */
	protected function translateCountry($country)
	{
		$countryMap = array(
			'AX' => 'ALA', 'AF' => 'AFG', 'AL' => 'ALB', 'DZ' => 'DZA', 'AS' => 'ASM',
			'AD' => 'AND', 'AO' => 'AGO', 'AI' => 'AIA', 'AQ' => 'ATA', 'AG' => 'ATG',
			'AR' => 'ARG', 'AM' => 'ARM', 'AW' => 'ABW', 'AU' => 'AUS', 'AT' => 'AUT',
			'AZ' => 'AZE', 'BS' => 'BHS', 'BH' => 'BHR', 'BD' => 'BGD', 'BB' => 'BRB',
			'BY' => 'BLR', 'BE' => 'BEL', 'BZ' => 'BLZ', 'BJ' => 'BEN', 'BM' => 'BMU',
			'BT' => 'BTN', 'BO' => 'BOL', 'BA' => 'BIH', 'BW' => 'BWA', 'BV' => 'BVT',
			'BR' => 'BRA', 'IO' => 'IOT', 'BN' => 'BRN', 'BG' => 'BGR', 'BF' => 'BFA',
			'BI' => 'BDI', 'KH' => 'KHM', 'CM' => 'CMR', 'CA' => 'CAN', 'CV' => 'CPV',
			'KY' => 'CYM', 'CF' => 'CAF', 'TD' => 'TCD', 'CL' => 'CHL', 'CN' => 'CHN',
			'CX' => 'CXR', 'CC' => 'CCK', 'CO' => 'COL', 'KM' => 'COM', 'CD' => 'COD',
			'CG' => 'COG', 'CK' => 'COK', 'CR' => 'CRI', 'CI' => 'CIV', 'HR' => 'HRV',
			'CU' => 'CUB', 'CY' => 'CYP', 'CZ' => 'CZE', 'DK' => 'DNK', 'DJ' => 'DJI',
			'DM' => 'DMA', 'DO' => 'DOM', 'EC' => 'ECU', 'EG' => 'EGY', 'SV' => 'SLV',
			'GQ' => 'GNQ', 'ER' => 'ERI', 'EE' => 'EST', 'ET' => 'ETH', 'FK' => 'FLK',
			'FO' => 'FRO', 'FJ' => 'FJI', 'FI' => 'FIN', 'FR' => 'FRA', 'GF' => 'GUF',
			'PF' => 'PYF', 'TF' => 'ATF', 'GA' => 'GAB', 'GM' => 'GMB', 'GE' => 'GEO',
			'DE' => 'DEU', 'GH' => 'GHA', 'GI' => 'GIB', 'GR' => 'GRC', 'GL' => 'GRL',
			'GD' => 'GRD', 'GP' => 'GLP', 'GU' => 'GUM', 'GT' => 'GTM', 'GN' => 'GIN',
			'GW' => 'GNB', 'GY' => 'GUY', 'HT' => 'HTI', 'HM' => 'HMD', 'HN' => 'HND',
			'HK' => 'HKG', 'HU' => 'HUN', 'IS' => 'ISL', 'IN' => 'IND', 'ID' => 'IDN',
			'IR' => 'IRN', 'IQ' => 'IRQ', 'IE' => 'IRL', 'IL' => 'ISR', 'IT' => 'ITA',
			'JM' => 'JAM', 'JP' => 'JPN', 'JO' => 'JOR', 'KZ' => 'KAZ', 'KE' => 'KEN',
			'KI' => 'KIR', 'KP' => 'PRK', 'KR' => 'KOR', 'KW' => 'KWT', 'KG' => 'KGZ',
			'LA' => 'LAO', 'LV' => 'LVA', 'LB' => 'LBN', 'LS' => 'LSO', 'LR' => 'LBR',
			'LY' => 'LBY', 'LI' => 'LIE', 'LT' => 'LTU', 'LU' => 'LUX', 'MO' => 'MAC',
			'MK' => 'MKD', 'MG' => 'MDG', 'MW' => 'MWI', 'MY' => 'MYS', 'MV' => 'MDV',
			'ML' => 'MLI', 'MT' => 'MLT', 'MH' => 'MHL', 'MQ' => 'MTQ', 'MR' => 'MRT',
			'MU' => 'MUS', 'YT' => 'MYT', 'MX' => 'MEX', 'FM' => 'FSM', 'MD' => 'MDA',
			'MC' => 'MCO', 'MN' => 'MNG', 'MS' => 'MSR', 'MA' => 'MAR',	'MZ' => 'MOZ',
			'MM' => 'MMR', 'NA' => 'NAM', 'NR' => 'NRU', 'NP' => 'NPL', 'NL' => 'NLD',
			'AN' => 'ANT', 'NC' => 'NCL', 'NZ' => 'NZL', 'NI' => 'NIC',	'NE' => 'NER',
			'NG' => 'NGA', 'NU' => 'NIU','NF' => 'NFK',	'MP' => 'MNP',	'NO' => 'NOR',
			'OM' => 'OMN','PK' => 'PAK','PW' => 'PLW',	'PS' => 'PSE',	'PA' => 'PAN',
			'PG' => 'PNG','PY' => 'PRY','PE' => 'PER','PH' => 'PHL','PN' => 'PCN',
			'PL' => 'POL','PT' => 'PRT','PR' => 'PRI','QA' => 'QAT','RE' => 'REU',
			'RO' => 'ROU','RU' => 'RUS','RW' => 'RWA','SH' => 'SHN','KN' => 'KNA',
			'LC' => 'LCA','PM' => 'SPM','VC' => 'VCT','WS' => 'WSM','SM' => 'SMR',
			'ST' => 'STP','SA' => 'SAU','SN' => 'SEN','CS' => 'SCG','SC' => 'SYC',
			'SL' => 'SLE','SG' => 'SGP','SK' => 'SVK','SI' => 'SVN','SB' => 'SLB',
			'SO' => 'SOM','ZA' => 'ZAF','GS' => 'SGS','ES' => 'ESP','LK' => 'LKA',
			'SD' => 'SDN','SR' => 'SUR','SJ' => 'SJM','SZ' => 'SWZ','SE' => 'SWE',
			'CH' => 'CHE','SY' => 'SYR','TW' => 'TWN','TJ' => 'TJK','TZ' => 'TZA',
			'TH' => 'THA','TL' => 'TLS','TG' => 'TGO','TK' => 'TKL','TO' => 'TON',
			'TT' => 'TTO','TN' => 'TUN','TR' => 'TUR','TM' => 'TKM','TC' => 'TCA',
			'TV' => 'TUV','UG' => 'UGA','UA' => 'UKR','AE' => 'ARE','GB' => 'GBR',
			'US' => 'USA','UM' => 'UMI','UY' => 'URY','UZ' => 'UZB','VU' => 'VUT',
			'VA' => 'VAT','VE' => 'VEN','VN' => 'VNM','VG' => 'VGB','VI' => 'VIR',
			'WF' => 'WLF','EH' => 'ESH','YE' => 'YEM','ZM' => 'ZMB','ZW' => 'ZWE'
		);
		
		if(array_key_exists($country, $countryMap)) {
			return $countryMap[$country];
		} else {
			return '';
		}
	}
	
	protected function initializePayment($vendor,$order,$client,$params = array()){
		
		foreach($params as $key => $param){
			$this->params->set($key,$param);
		}
		
		$this->_vendor = (array)$vendor;
		$this->_order = (array)$order;
		$this->_client = (array)$client;
		
		/*
		$rootURL = rtrim(JURI::base(),'/');
		$subpathURL = JURI::base(true);
		if(!empty($subpathURL) && ($subpathURL != '/')) {
			$rootURL = substr($rootURL, 0, -1 * strlen($subpathURL));
		}
		*/
		$system_data = array(
			'rootURL'	=> JURI::root(),
			'postback'	=> JURI::root() .'index.php?option=com_jforms&task=callback&pg=paypal',
			'success'	=> JURI::root() .'index.php?option=com_jforms&view=submission&layout=finish&cid[0]='.$order['id'] .'&result=1',
			'cancel'		=> JURI::root() .'index.php?option=com_jforms&view=submission&layout=finish&cid[0]='.$order['id'] .'&result=0',
		);
		
		$vendor_data = array(
			'merchant'		=> $this->getMerchantID(),
		);
		
		@$order_data = array(
			'id'			=> $order['id'],
			'title'			=> $order['title'],
			'subtotal'		=> $order['subtotal'],
			'tax'			=> $order['tax'],
			'currency'		=> $this->params->get('currency',$this->ppCurrencies[0]),
		);
		
		@$client_data = array(
			'name'			=> $client['name'],
			'lastname'		=> $client['lastname'],
			'address'		=> $client['address'],
			'zip'			=> $client['zip'],
			'city'			=> $client['city'],
			'state'			=> $client['state'],
			'country'		=> $client['country'],
			'phone_number'	=> $client['phone_number'],
			'mobile_number'	=> $client['mobile_number'],
			'email'			=> $client['email']
		);
		
		$this->pg_data = array(
			'system' => $system_data,
			'vendor' => $vendor_data,
			'order' => $order_data,
			'client' => $client_data
		);
		$this->initialized = true;
		
		return true;
	}

	private function getMerchantID()
	{
		return null;
	}
	
	private function getPaymentURL()
	{
		return null;
	}
}