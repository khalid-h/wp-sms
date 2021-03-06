<?php
	class adpdigital extends WP_SMS {
		private $wsdl_link = "http://ws.adpdigital.com/url/";
		public $tariff = "http://adpdigital.com/services/";
		public $unitrial = true;
		public $unit;
		public $flash = "disable";
		public $isflash = false;

		public function __construct() {
			parent::__construct();
			$this->validateNumber = "09xxxxxxxx";
			
			ini_set("soap.wsdl_cache_enabled", "0");
		}

		public function SendSMS() {
			// Check credit for the gateway
			if(!$this->GetCredit()) return;
			
			/**
			 * Modify sender number
			 *
			 * @since 3.4
			 * @param string $this->from sender number.
			 */
			$this->from = apply_filters('wp_sms_from', $this->from);
			
			/**
			 * Modify Receiver number
			 *
			 * @since 3.4
			 * @param array $this->to receiver number
			 */
			$this->to = apply_filters('wp_sms_to', $this->to);
			
			/**
			 * Modify text message
			 *
			 * @since 3.4
			 * @param string $this->msg text message.
			 */
			$this->msg = apply_filters('wp_sms_msg', $this->msg);
			
			$to = str_replace("09", "989", implode($this->to, ","));
			$msg = urlencode($this->msg);
			
			$result = file_get_contents("{$this->wsdl_link}send?username={$this->username}&password={$this->password}&dstaddress={$to}&body={$msg}&clientid={$this->from}&type=text&unicode=1");
			
			if( strstr($result, 'ERR') ) {
				return 0;
			} else {
				$this->InsertToDB($this->from, $this->msg, $this->to);
				
				/**
				 * Run hook after send sms.
				 *
				 * @since 2.4
				 * @param string $result result output.
				 */
				do_action('wp_sms_send', $result);
				
				return preg_replace('/[^0-9]/', '', $result);
			}
		}

		public function GetCredit() {
			$result = file_get_contents("{$this->wsdl_link}balance?username={$this->username}&password={$this->password}&facility=send");
			
			if( strstr($result, 'ERR') ) {
				return 0;
			} else {
				return preg_replace('/[^0-9]/', '', $result);
			}
		}
	}
?>