<?php
/**
 *
 * CakePHP Library Object to interact with the Sermepa TPV service
 *
 * Copyright 2014 Bernat Arlandis i Mañó
 *
 * This package is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This package is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @copyright Copyright 2014 Bernat Arlandis i Mañó
 * @link http://bernatarlandis.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

class Sermepa extends Object {

	protected $settings;

	protected $notification;

	public function __construct($settings) {
		$this->settings = $settings;
	}

	public function getPostUrl() {
		return $this->settings->serviceUrl;
	}

	public function getPostData($order, $amount, $transactionType = 0) {
		$order = str_pad($order, 12, '0', STR_PAD_LEFT);
		if ($this->settings->extendedSignature) {
			$signature = sha1($amount . $order . $this->settings->merchantCode . $this->settings->currency . $transactionType . $this->settings->merchantUrl . $this->settings->secretKey);
		} else {
			$signature = sha1($amount . $order . $this->settings->merchantCode . $this->settings->currency . $this->settings->secretKey);
		}
		return array(
			'Ds_Merchant_Amount' => $amount,
			'Ds_Merchant_Currency' => $this->settings->currency,
			'Ds_Merchant_Order' => $order,
			'Ds_Merchant_MerchantCode' => $this->settings->merchantCode,
			'Ds_Merchant_MerchantURL' => $this->settings->merchantUrl,
			'Ds_Merchant_UrlOK' => $this->settings->urlOk,
			'Ds_Merchant_UrlKO' => $this->settings->urlKo,
			'Ds_Merchant_MerchantName' => $this->settings->merchantName,
			'Ds_Merchant_ConsumerLanguage' => $this->settings->consumerLanguage,
			'Ds_Merchant_MerchantSignature' => $signature,
			'Ds_Merchant_Terminal' => $this->settings->terminal,
			'Ds_Merchant_TransactionType' => $transactionType,
		);
	}

	public function getNotificationData($data) {
		$this->notification = new stdClass();
		$this->notification->date = $data['Ds_Date'];
		$this->notification->hour = $data['Ds_Hour'];
		$this->notification->amount = $data['Ds_Amount'];
		$this->notification->currency = $data['Ds_Currency'];
		$this->notification->order = $data['Ds_Order'];
		$this->notification->merchantCode = $data['Ds_MerchantCode'];
		$this->notification->terminal = $data['Ds_Terminal'];
		$this->notification->signature = $data['Ds_Signature'];
		$this->notification->response = $data['Ds_Response'];
		$this->notification->merchantData = $data['Ds_MerchantData'];
		$this->notification->securePayment = $data['Ds_SecurePayment'];
		$this->notification->transactionType = $data['Ds_TransactionType'];
		$this->notification->cardCountry = $data['Ds_Card_Country'];
		$this->notification->authorisationCode = $data['Ds_AuthorisationCode'];
		$this->notification->consumerLanguage = $data['Ds_ConsumerLanguage'];
		$this->notification->cardType = $data['Ds_Card_Type'];
		$signature = sha1($this->notification->amount . $this->notification->order . $this->notification->merchantCode . $this->notification->currency . $this->notification->response . $this->settings->secretKey);
		if (strtolower($this->notification->signature) !== $signature) {
			throw new CakeException("Invalid signature in Sermepa notification.");
		}
		return $this->notification;
	}

}
