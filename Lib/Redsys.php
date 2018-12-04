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

class Redsys extends CakeObject {

	protected $_settings;

	protected $_params;

	protected $_message;

/**
 * Constructor
 *
 * @param array $settings Default settings
 * @param array $params Specific parameters
 * @throws Exception
 */
	public function __construct($settings, $params) {
		$this->_settings = $settings;
		if (isset($params['Ds_SignatureVersion']) && isset($params['Ds_MerchantParameters']) && isset($params['Ds_Signature'])) {
			if ($params['Ds_SignatureVersion'] !== $this->getVersion()) {
				throw new Exception("Redsys: invalid signature version.");
			}
			$this->_message = $params['Ds_MerchantParameters'];
			$this->_params = array_change_key_case(json_decode($this->_decodeBase64url($this->_message), true), CASE_UPPER);
			if ($this->_hash($this->_message) !== $this->_decodeBase64url($params['Ds_Signature'])) {
				throw new Exception("Redsys: invalid signature.");
			}
		} else {
			if (isset($this->_settings['defaults'])) {
				$params += $this->_settings['defaults'];
			}
			$this->_params = array_change_key_case($params, CASE_UPPER);
			$this->_message = base64_encode(json_encode($this->_params));
		}
	}

/**
 * Get configured URL
 *
 * @return string URL
 */
	public function getUrl() {
		return $this->_settings['url'];
	}

/**
 * Get version
 *
 * @return string Version string
 */
	public function getVersion() {
		return 'HMAC_SHA256_V1';
	}

/**
 * Get the message to be sent
 *
 * @return string Message
 */
	public function getMessage() {
		return $this->_message;
	}

/**
 * Get the signature string
 *
 * @return string Signature
 */
	public function getSignature() {
		return base64_encode($this->_hash($this->_message));
	}

/**
 * Get parameter value
 *
 * @param string $param Param name
 * @return string Param value
 */
	public function get($param) {
		$param = strtoupper($param);
		if (isset($this->_params[$param])) {
			return $this->_params[$param];
		}
		return null;
	}

/**
 * Get all parameters
 *
 * @return array All the parameters
 */
	public function getData() {
		return $this->_params;
	}

/**
 * Calculate hash for a message string
 *
 * @param string $message Message
 * @param string $key Key
 * @return string Hash
 */
	protected function _hash($message, $key = null) {
		if ($key === null) {
			$key = base64_decode($this->_settings['secretKey']);
		}
		$order = $this->_getOrder();
		$iv = str_repeat("\0", 8);
		if (function_exists('openssl_encrypt')) {
			$paddedLength = ceil(strlen($order) / 8) * 8;
			$key = substr(openssl_encrypt(str_pad($order, $paddedLength, "\0"), 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, $iv), 0, $paddedLength);
		} else {
			$key = mcrypt_encrypt(MCRYPT_3DES, $key, $order, MCRYPT_MODE_CBC, $iv);
		}
		return hash_hmac('sha256', $message, $key, true);
	}

/**
 * Get Order Id
 *
 * @return string Order Id
 */
	protected function _getOrder() {
		$order = $this->get('DS_MERCHANT_ORDER');
		if ($order !== null) {
			return $order;
		}
		$order = $this->get('DS_ORDER');
		if ($order !== null) {
			return $order;
		}
		return null;
	}

/**
 * URL encode to Base64
 *
 * @param string $data Text to encode
 * @return string Encoded text
 */
	protected function _encodeBase64url($data) {
		return strtr(base64_encode($data), '+/', '-_');
	}

/**
 * URL decode from Base64
 *
 * @param string $data Text to decode
 * @return string Decoded text
 */
	protected function _decodeBase64url($data) {
		return base64_decode(strtr($data, '-_', '+/'));
	}
}

