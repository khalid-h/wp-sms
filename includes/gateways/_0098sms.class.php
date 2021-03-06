<?php
	class _0098sms extends WP_SMS {
		private $wsdl_link = "http://www.0098sms.com/";
		public $tariff = "http://www.0098sms.com/";
		public $unitrial = false;
		public $unit;
		public $flash = "enable";
		public $isflash = false;
		
		public function __construct() {
			parent::__construct();
			$this->validateNumber = "09xxxxxxxxx";
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
			
			$msg = urlencode($this->msg);
			
			foreach($this->to as $to) {
				$result = file_get_contents($this->wsdl_link."sendsmslink.aspx?FROM=".$this->from."&TO=".$to."&TEXT=".$msg."&USERNAME=".$this->username."&PASSWORD=".$this->password."&DOMAIN=0098");
			}
			
			if($result->Code == 0) {
				$this->InsertToDB($this->from, $this->msg, $this->to);
				
				/**
				 * Run hook after send sms.
				 *
				 * @since 2.4
				 * @param string $result result output.
				 */
				do_action('wp_sms_send', $result);
				
				return $result;
			}
		}
		
		public function GetCredit() {
			if(!$this->username && !$this->password) return;
			
			return true;
		}
	}