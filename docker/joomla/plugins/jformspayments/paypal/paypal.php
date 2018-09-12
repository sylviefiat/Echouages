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

class plgJformspaymentsPaypal extends plgJformspaymentsAbstract
{
	
	public function __construct(&$subject, $config = array())
	{
		$config = array_merge($config, array(
			'ppName'		=> 'paypal',
			'ppKey'			=> 'PLG_JFORMSPAYMENTS_PAYPAL_TITLE',
			'ppImage'		=> 'https://www.paypal.com/en_US/i/bnr/horizontal_solution_PPeCheck.gif',
			'ppCurrencies'	=> array(
					'EUR','USD','GBP','AUD','CAD','CKZ','DKK','HKD','ILS','MXN','NOK',
					'NZD','PHP','PLN','SGD','SEK','CHF','THB'
				)
		));

		$language = JFactory::getLanguage();
		$language->load('plg_jformspayments_paypal', JPATH_ADMINISTRATOR);
		$language->load('com_jforms');
		
		parent::__construct($subject, $config);
	}

	public function initializePayment($vendor,$order,$client,$params = array()){
		parent::initializePayment($vendor,$order,$client,$params);
		if(!$this->initialized) return false;
		
		$pg_data = $this->pg_data;
		
		// extra data for this payment gateway
		$pg_data['vendor']['merchant']				= $this->getMerchantID();
		$pg_data['system']['paymentURL']			= $this->getPaymentURL();
		$pg_data['system']['cmd'] 					= '_xclick';
		$pg_data['system']['cbt'] 					= $this->params->get('cbt',null);
		$pg_data['system']['cpp_header_image'] 	= $this->params->get('cpp_header_image',null);
		$pg_data['system']['cpp_headerback_color'] = $this->params->get('cpp_headerback_color',null);
		$pg_data['system']['cpp_headerborder_color'] = $this->params->get('cpp_headerborder_color',null);
	
		$this->pg_data = $pg_data;
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
	public function onNewPayment($paymentmethod, $vendor, $order, $client, $params = array())
	{
		if($paymentmethod != $this->ppName) return false;
		
		$this->initializePayment($vendor,$order,$client,$params);
		if(!$this->initialized) return false;
		
		$pg_data = $this->pg_data;
		
		@ob_start();
		include dirname(__FILE__).'/form/form.php';
		$html = @ob_get_clean();

		return array(
			'form' => $html,
			'data_to_send' => $pg_data
		);
	}

	public function onPaymentCallback($paymentmethod, $data_sent, $data_received, $params = array())
	{
		JLoader::import('joomla.utilities.date');

		// Check if we're supposed to handle this
		if($paymentmethod != $this->ppName) return false;
			
		$errors = array();
		
		// Check IPN data for validity (i.e. protect against fraud attempt)
		$isValid = $this->isValidIPN($data_received);
		if(!$isValid) $errors['PAYPAL_REPORTS_INVALID'] = 'PayPal reports transaction as invalid';

		// Check txn_type; we only accept web_accept transactions with this plugin
		if($isValid) {
			$validTypes = array('web_accept','recurring_payment','subscr_payment');
			$isValid = in_array($data_received['txn_type'], $validTypes);
			if(!$isValid) {
				$errors['TRANSACTION_TYPE_NOT_SUPPORTED'] = "Transaction type ". $data_received['txn_type']." can't be processed by this payment plugin.";
			} else {
				$recurring = ($data_received['txn_type'] != 'web_accept');
			}
		}

		// Load the relevant order data
		if($isValid) {
			$id = !empty($data_received['custom']) ? (int)$data_received['custom'] : -1;
			$payment = null;
			if($id > 0) {
				if( $data_sent['order']['id'] <= 0 || $data_sent['order']['id'] != $id ) {
					$isValid = false;
				}
			} else {
				$isValid = false;
			}
			if(!$isValid) $errors['ORDER_ID_INVALID'] = 'The referenced order ID ("custom" field) is invalid';
		}

		// Check that receiver_email / receiver_id is what the site owner has configured
		if($isValid) {
			$receiver_email = $data_received['receiver_email'];
			$receiver_id = $data_received['receiver_id'];
			$valid_id = $data_sent['vendor']['merchant'];
			$isValid =
				($receiver_email == $valid_id)
				|| (strtolower($receiver_email) == strtolower($receiver_email))
				|| ($receiver_id == $valid_id)
				|| (strtolower($receiver_id) == strtolower($receiver_id))
			;
			if(!$isValid) $errors['MERCHANT_ID_NOT_MATCHING'] = 'Merchant ID does not match receiver_email or receiver_id';
		}

		// Check that mc_gross is correct
		$isPartialRefund = false;
		if($isValid && !empty($payment)) {
			$mc_gross = floatval($data_received['mc_gross']);

			$gross = $data_sent['order']['subtotal'] + $data_sent['order']['tax'];

			if($mc_gross > 0) {
				// A positive value means "payment". The prices MUST match!
				// Important: NEVER, EVER compare two floating point values for equality.
				$isValid = ($gross - $mc_gross) < 0.01;
			} else {
				$paidTOOMUCH = false;
				$temp_mc_gross = -1 * $mc_gross;
				$paidTOOMUCH = ($gross - $temp_mc_gross) > 0.01;
			}
			if(!$isValid) $errors['ORDER_TOTAL_NOT_MATCHING'] = 'Paid amount does not match the subscription amount';
		}

		// Check that txn_id has not been previously processed
		if($isValid && !empty($payment) && !$paidTOOMUCH) {
			if($data_sent['system']['previous_callback'] == $data_received['ipn_track_id']) {
				$isValid = false;
				$errors['CALLBACK_ALREADY_PROCESSED'] = "I will not process the same callback twice";
			}
		}

		// Check that mc_currency is correct
		if($isValid && !empty($payment)) {
			$mc_currency = strtoupper($data_received['mc_currency']);
			$currency = strtoupper($data_sent['order']['currency']);
			if($mc_currency != $currency) {
				$isValid = false;
				$errors['CURRENCY_NOT_MATCHING'] = "Invalid currency; expected $currency, got $mc_currency";
			}
		}

		// Log the IPN data
		$this->logIPN($data_received, $isValid);

		// Fraud attempt? Do nothing more!
		if(!$isValid OR !empty($errors)) return array(
			'result' => false,
			'errors' => $errors
		);

		// Check the payment_status
		switch($data_received['payment_status'])
		{
			case 'Canceled_Reversal':
			case 'Completed':
				$status = 'completed';
				break;

			case 'Created':
			case 'Pending':
			case 'Processed':
				$status = 'pending';
				break;

			case 'Refunded':
			case 'Reversed':
			case 'Returned':
				$status = 'refunded';
				break;
				
			
			case 'Denied':
			case 'Expired':
			case 'Failed':
			case 'Voided':
				$status = 'failed';
				break;
				
			default:
				// Partial refunds can only by issued by the merchant. In that case,
				// we don't want the subscription to be cancelled. We have to let the
				// merchant adjust its parameters if needed.
				if($paidTOOMUCH) {
					$status = 'completed';
				} else {
					$status = 'to_be_checked';
				}
				break;
		}
		
		// everythings OK
		
		// return payment details
		$payment_details = array(
			'result' => true,
			'payment_status' => $status
		);
		
		return $payment_details;
	}

	public function getOrderId($paymentmethod, $data_received = array()){
		if($paymentmethod != $this->ppName) return -1;
		return !empty($data_received['custom']) ? (int)$data_received['custom'] : -1;
	}
	
	/**
	 * Gets the form action URL for the payment
	 */
	private function getPaymentURL()
	{
		$sandbox = $this->params->get('sandbox',0);
		if($sandbox) {
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		} else {
			return 'https://www.paypal.com/cgi-bin/webscr';
		}
	}

	/**
	 * Gets the PayPal Merchant ID (usually the email address)
	 */
	private function getMerchantID()
	{
		$merchantID = $this->params->get('merchant','');
		
		$sandbox = $this->params->get('sandbox',0);
		if($sandbox) {
			$merchantID = $this->params->get('sandbox_merchant','');
		}
		
		return $merchantID;
	}

	/**
	 * Validates the incoming data against PayPal's IPN to make sure this is not a
	 * fraudelent request.
	 */
	private function isValidIPN($data)
	{
		$sandbox = $this->params->get('sandbox',0);
		$hostname = $sandbox ? 'www.sandbox.paypal.com' : 'www.paypal.com';

		$url = 'ssl://'.$hostname;
		$port = 443;

		$req = 'cmd=_notify-validate';
		foreach($data as $key => $value) {
			$value = urlencode($value);
			$req .= "&$key=$value";
		}
		$header = '';
		$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
		$header .= "Host: $hostname:$port\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n";
		$header .= "Connection: Close\r\n\r\n";


		$fp = fsockopen ($url, $port, $errno, $errstr, 30);

		if (!$fp) {
			// HTTP ERROR
			return false;
		} else {
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if (stristr($res, "VERIFIED")) {
					return true;
				} else if (stristr($res, "INVALID")) {
					return false;
				}
			}
			fclose ($fp);
		}
	}
}
