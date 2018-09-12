<?php
/**
* @version		0.0.1
* @package		jForms
* @subpackage	Orders
* @copyright	G. Tomaselli
* @author		G. Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @license		Proprietary software
**/

defined('_JEXEC') or die();

$paymentclass = include_once JPATH_ADMINISTRATOR.'/components/com_jforms/helpers/payments.php';
if(!$paymentclass) { unset($paymentclass); return; } else { unset($paymentclass); }

class plgJformspaymentsOffline extends plgJformspaymentsAbstract
{
	public function __construct(&$subject, $config = array())
	{
		$config = array_merge($config, array(
			'ppName'		=> 'offline',
			'ppKey'			=> 'PLG_JFORMSPAYMENTS_OFFLINE_TITLE',
			'ppCurrencies'	=> array("AED","AFN","ALL","ANG","ARS","AUD","AWG",
				"AZN","BAM","BBD","BGN","BMD","BND","BOB","BRL","BSD","BWP","BYR",
				"BZD","CAD","CHF","CLP","CNY","COP","CRC","CUP","CZK","DKK","DOP",
				"EGP","EUR","FJD","FKP","GBP","GIP","GTQ","GYD","HKD","HNL","HRK",
				"HUF","IDR","ILS","INR","IRR","ISK","JMD","JPY","KGS","KHR","KPW",
				"KRW","KYD","KZT","LAK","LBP","LKR","LRD","LTL","LVL","MKD","MNT",
				"MUR","MXN","MYR","MZN","NAD","NGN","NIO","NOK","NPR","NZD","OMR",
				"PAB","PEN","PHP","PKR","PLN","PYG","QAR","RON","RSD","RUB","SAR",
				"SBD","SCR","SEK","SGD","SHP","SOS","SRD","SVC","SYP","THB","TRY",
				"TTD","TWD","UAH","USD","UYU","UZS","VEF","VND","XCD","YER","ZAR")
		));

		$language = JFactory::getLanguage();
		$language->load('plg_jformspayments_offline', JPATH_ADMINISTRATOR);
		$language->load('com_jforms');
		
		parent::__construct($subject, $config);
	}

	/**
	 * Returns the payment form to be submitted by the user's browser. The form must have an ID of
	 * "paymentForm" and a visible submit button.
	 *
	 * @param string $paymentmethod
	 * @param JUser $user
	 * @param AkeebasubsTableLevel $level
	 * @param AkeebasubsTableSubscription $subscription
	 * @return string
	 */
	public function onNewPayment($paymentmethod,$vendor, $order, $client, $params = array())
	{
		if($paymentmethod != $this->ppName AND $paymentmethod !== true) return false;
		
		parent::initializePayment($vendor, $order,$client,$params);
		if(!$this->initialized) return false;
		
		$pg_data = $this->pg_data;

		$html = $this->params->get('instructions','');

		return array(
			'form' => $html,
			'data_to_send' => $pg_data
		);
	}

	public function onPaymentCallback($paymentmethod, $data_sent, $data_received, $params = array())
	{
		return false;
	}
	
	public function getOrderId($paymentmethod, $data = array())
	{
		return -1;
	}
}