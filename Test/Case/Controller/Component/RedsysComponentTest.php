<?php
/**
 *
 * RedsysComponent Test Case
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

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('RedsysComponent', 'Redsys.Controller/Component');

class TestRedsysController extends Controller {
}

class RedsysComponentTest extends CakeTestCase {

	public $Controller = null;

	public $RedsysComponent = null;

	public $settings = array(
		'url' => 'https://sis-t.redsys.es:25443/sis/realizarPago',
		//'url' => 'https://sis.redsys.es/sis/realizarPago',
		'secretKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
		'defaults' => [
			'Ds_Merchant_Terminal' => '956',
			'Ds_Merchant_Currency' => '978',
			'Ds_Merchant_MerchantCode' => '000000083',
			'Ds_Merchant_MerchantURL' => 'http://example.com/notification',
		],
	);

	public $responseParams = array(
		'Ds_Date' => '01/01/2000',
		'Ds_Hour' => '08:00',
		'Ds_Amount' => '349812',
		'Ds_Currency' => '978',
		'Ds_Order' => '000000000100',
		'Ds_MerchantCode' => '000000083',
		'Ds_Terminal' => '956',
		'Ds_Response' => '0',
		'Ds_MerchantData' => '',
		'Ds_SecurePayment' => '1',
		'Ds_TransactionType' => '0',
		'Ds_Card_Country' => '',
		'Ds_AuthorisationCode' => '11111',
		'Ds_ConsumerLanguage' => '1',
		'Ds_Card_Type' => 'C',
	);

	public $response = array(
		'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
		'Ds_Signature' => 'CZmqB/GmzfwvpvvJVbx/auo6zrFAx8pPd1+0pzc0bvQ=',
		'Ds_MerchantParameters' => '',
	);

	public function setUp() {
		parent::setUp();
		$this->response['Ds_MerchantParameters'] = base64_encode(json_encode($this->responseParams));
		$Collection = new ComponentCollection();
		$this->RedsysComponent = new RedsysComponent($Collection, $this->settings);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestRedsysController($CakeRequest, $CakeResponse);
		$this->RedsysComponent->startup($this->Controller);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->RedsysComponent);
		unset($this->Controller);
	}

	public function testRequest() {
		$this->RedsysComponent->request([
			'DS_MERCHANT_AMOUNT' => '100',
			'DS_MERCHANT_ORDER' => '349812',
		]);
		$this->assertEquals($this->settings['url'], $this->Controller->params['RedsysUrl']);
		$this->assertEquals('eyJEU19NRVJDSEFOVF9BTU9VTlQiOiIxMDAiLCJEU19NRVJDSEFOVF9PUkRFUiI6IjM0OTgxMiIsIkRzX01lcmNoYW50X1Rlcm1pbmFsIjoiOTU2IiwiRHNfTWVyY2hhbnRfQ3VycmVuY3kiOiI5NzgiLCJEc19NZXJjaGFudF9NZXJjaGFudENvZGUiOiIwMDAwMDAwODMiLCJEc19NZXJjaGFudF9NZXJjaGFudFVSTCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tXC9ub3RpZmljYXRpb24ifQ==', $this->Controller->request->params['RedsysParameters']);
		$this->assertEquals('R+7sDHIakXvroq31yg8dPAPLqg0WbaGS3tA2dT7iVcc=', $this->Controller->params['RedsysSignature']);
		$this->assertEquals('HMAC_SHA256_V1', $this->Controller->params['RedsysSignatureVersion']);
	}

	public function testResponse() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->Controller->request->data = $this->response;
		$response = $this->RedsysComponent->response();
		$this->assertEquals($this->responseParams['Ds_Date'], $response['Ds_Date']);
		$this->assertEquals($this->responseParams['Ds_Hour'], $response['Ds_Hour']);
		$this->assertEquals($this->responseParams['Ds_Amount'], $response['Ds_Amount']);
		$this->assertEquals($this->responseParams['Ds_Currency'], $response['Ds_Currency']);
		$this->assertEquals($this->responseParams['Ds_Order'], $response['Ds_Order']);
		$this->assertEquals($this->responseParams['Ds_MerchantCode'], $response['Ds_MerchantCode']);
		$this->assertEquals($this->responseParams['Ds_Terminal'], $response['Ds_Terminal']);
		$this->assertEquals($this->responseParams['Ds_Response'], $response['Ds_Response']);
		$this->assertEquals($this->responseParams['Ds_MerchantData'], $response['Ds_MerchantData']);
		$this->assertEquals($this->responseParams['Ds_SecurePayment'], $response['Ds_SecurePayment']);
		$this->assertEquals($this->responseParams['Ds_TransactionType'], $response['Ds_TransactionType']);
		$this->assertEquals($this->responseParams['Ds_Card_Country'], $response['Ds_Card_Country']);
		$this->assertEquals($this->responseParams['Ds_AuthorisationCode'], $response['Ds_AuthorisationCode']);
		$this->assertEquals($this->responseParams['Ds_ConsumerLanguage'], $response['Ds_ConsumerLanguage']);
		$this->assertEquals($this->responseParams['Ds_Card_Type'], $response['Ds_Card_Type']);
	}

	public function testPostException() {
		$this->expectException('CakeException');
		$this->Controller->request->data = $this->response;
		$response = $this->RedsysComponent->response();
	}

	public function testSignatureException() {
		$this->expectException('Exception');
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->Controller->request->data = $this->response;
		$this->Controller->request->data['Ds_Signature'] = '';
		$response = $this->RedsysComponent->response();
	}
}

