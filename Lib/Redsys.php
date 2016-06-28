<?php
/**
 *
 * CakePHP Library Object to interact with the Redsys TPV service
 *
 * Copyright 2016 Bernat Arlandis
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
 * @copyright Copyright 2016 Bernat Arlandis
 * @link http://bernatarlandis.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

class Redsys extends Object {

	protected $settings;

	protected $params;

	protected $message;

	public function __construct($settings, $params)
	{
		$this->settings = $settings;
		if (isset($params['Ds_SignatureVersion']) && isset($params['Ds_MerchantParameters']) && isset($params['Ds_Signature'])) {
			if ($params['Ds_SignatureVersion'] !== $this->getVersion()) {
				throw new Exception("Redsys: invalid signature version.");
			}
			$this->message = $params['Ds_MerchantParameters'];
			if ($this->hash($this->message) !== $params['Ds_Signature']) {
				throw new Exception("Redsys: invalid signature.");
			}
			$this->params = json_decode(base64_decode($this->message), true);
		} else {
			if (isset($this->settings['defaults'])) {
				$params += $this->settings['defaults'];
			}
			$this->params = $params;
			$this->message = base64_encode(json_encode($this->params));
		}
	}

	protected function hash($message, $key = null)
	{
		if ($key === null) {
			$key = $this->settings['secretKey'];
		}
		$iv = str_repeat("\0", 8);
		$key = mcrypt_encrypt(MCRYPT_3DES, base64_decode($key), $this->getOrder(), MCRYPT_MODE_CBC, $iv);
		return base64_encode(hash_hmac('sha256', $this->message, $key, true));
	}

	protected function getOrder()
	{
		if (!empty($this->params['DS_MERCHANT_ORDER'])){
			return $this->params['DS_MERCHANT_ORDER'];
		} elseif (!empty($this->params['Ds_Merchant_Order'])) {
			return $this->params['Ds_Merchant_Order'];
		} elseif (!empty($this->params['DS_ORDER'])) {
			return $this->params['DS_ORDER'];
		} else {
			return $this->params['Ds_Order'];
		}
	}

	public function getUrl()
	{
		return $this->settings['url'];
	}

	public function getVersion()
	{
		return 'HMAC_SHA256_V1';
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getSignature()
	{
		return $this->hash($this->message);
	}

	public function getData()
	{
		return $this->params;
	}
}
