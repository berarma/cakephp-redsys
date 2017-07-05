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
			'DS_MERCHANT_TERMINAL' => '956',
			'DS_MERCHANT_CURRENCY' => '978',
			'DS_MERCHANT_MERCHANTCODE' => '000000083',
			'DS_MERCHANT_MERCHANTURL' => 'http://example.com/notification',
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
		'Ds_Signature' => 'IVHvFC2_y-cyq45xbB9NIhCRUm7fYZLT0uNXcNEdefQ=',
		'Ds_MerchantParameters' => '',
	);

	public function setUp() {
		parent::setUp();
		$this->response['Ds_MerchantParameters'] = strtr(base64_encode(json_encode($this->responseParams)), '+/', '-_');
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
		$this->assertEquals('eyJEU19NRVJDSEFOVF9BTU9VTlQiOiIxMDAiLCJEU19NRVJDSEFOVF9PUkRFUiI6IjM0OTgxMiIsIkRTX01FUkNIQU5UX1RFUk1JTkFMIjoiOTU2IiwiRFNfTUVSQ0hBTlRfQ1VSUkVOQ1kiOiI5NzgiLCJEU19NRVJDSEFOVF9NRVJDSEFOVENPREUiOiIwMDAwMDAwODMiLCJEU19NRVJDSEFOVF9NRVJDSEFOVFVSTCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tXC9ub3RpZmljYXRpb24ifQ==', $this->Controller->request->params['RedsysParameters']);
		$this->assertEquals('NfsLlcmorHgEQhqQxXr3N7QJcXftpIGFiEXCYHQGTLw=', $this->Controller->params['RedsysSignature']);
		$this->assertEquals('HMAC_SHA256_V1', $this->Controller->params['RedsysSignatureVersion']);
	}

	public function testResponse() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->Controller->request->data = $this->response;
		$response = $this->RedsysComponent->response();
		$this->assertEquals($this->responseParams['Ds_Date'], $response->get('DS_DATE'));
		$this->assertEquals($this->responseParams['Ds_Hour'], $response->get('DS_HOUR'));
		$this->assertEquals($this->responseParams['Ds_Amount'], $response->get('DS_AMOUNT'));
		$this->assertEquals($this->responseParams['Ds_Currency'], $response->get('DS_CURRENCY'));
		$this->assertEquals($this->responseParams['Ds_Order'], $response->get('DS_ORDER'));
		$this->assertEquals($this->responseParams['Ds_MerchantCode'], $response->get('DS_MERCHANTCODE'));
		$this->assertEquals($this->responseParams['Ds_Terminal'], $response->get('DS_TERMINAL'));
		$this->assertEquals($this->responseParams['Ds_Response'], $response->get('DS_RESPONSE'));
		$this->assertEquals($this->responseParams['Ds_MerchantData'], $response->get('DS_MERCHANTDATA'));
		$this->assertEquals($this->responseParams['Ds_SecurePayment'], $response->get('DS_SECUREPAYMENT'));
		$this->assertEquals($this->responseParams['Ds_TransactionType'], $response->get('DS_TRANSACTIONTYPE'));
		$this->assertEquals($this->responseParams['Ds_Card_Country'], $response->get('DS_CARD_COUNTRY'));
		$this->assertEquals($this->responseParams['Ds_AuthorisationCode'], $response->get('DS_AUTHORISATIONCODE'));
		$this->assertEquals($this->responseParams['Ds_ConsumerLanguage'], $response->get('DS_CONSUMERLANGUAGE'));
		$this->assertEquals($this->responseParams['Ds_Card_Type'], $response->get('DS_CARD_TYPE'));
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

