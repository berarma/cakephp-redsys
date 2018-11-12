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
            $this->params = array_change_key_case(json_decode($this->decodeBase64url($this->message), true), CASE_UPPER);
            if ($this->hash($this->message) !== $this->decodeBase64url($params['Ds_Signature'])) {
                throw new Exception("Redsys: invalid signature.");
            }
        } else {
            if (isset($this->settings['defaults'])) {
                $params += $this->settings['defaults'];
            }
            $this->params = array_change_key_case($params, CASE_UPPER);
            $this->message = base64_encode(json_encode($this->params));
        }
        
        if (! function_exists('mcrypt_encrypt') && version_compare(phpversion(), '7.1', '<')) {
            throw new Exception("Redsys: php_mcrypt extension is missing.");
        } elseif (! function_exists('openssl_encrypt') && version_compare(phpversion(), '7.1', '>=')) {
            throw new Exception("Redsys: php_openssl extension is missing.");
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
        return base64_encode($this->hash($this->message));
    }

    public function get($param)
    {
        $param = strtoupper($param);
        if (isset($this->params[$param])) {
            return $this->params[$param];
        }
        return null;
    }

    public function getData()
    {
        return $this->params;
    }

    protected function hash($message, $key = null)
    {
        if ($key === null) {
            $key = base64_decode($this->settings['secretKey']);
        }
        
        $order = $this->getOrder();
        $iv = str_repeat("\0", 8);

        if (function_exists('mcrypt_encrypt') && version_compare(phpversion(), '7.1', '<')) {
            $key = mcrypt_encrypt(MCRYPT_3DES, $key, $order, MCRYPT_MODE_CBC, $iv);
        } elseif (function_exists('openssl_encrypt') && version_compare(phpversion(), '7.1', '>=')) {
            $padded_order_length = ceil(strlen($order) / 8) * 8;
            $padding =  str_repeat("\0", $padded_order_length - strlen($order));
            $key = openssl_encrypt($order . $padding, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, $iv);
            $key = substr($key, 0, 16);
        }
        
        return hash_hmac('sha256', $message, $key, true);
    }

    protected function getOrder()
    {
        $order = $this->get('DS_MERCHANT_ORDER');
        if ($order !== null) {
            return $order;
        }
        $order = $this->get('DS_ORDER');
        if ($order !== null){
            return $order;
        }
        return null;
    }

    protected function encodeBase64url($data)
    {
        return strtr(base64_encode($data), '+/', '-_');
    }

    protected function decodeBase64url($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

