<?php
/**
 *
 * SermepaComponent Test Case
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

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('SermepaComponent', 'Sermepa.Controller/Component');

class TestSermepaController extends Controller {
}

class SermepaComponentTest extends CakeTestCase {

	public $Controller = null;

	public $SermepaComponent = null;

	public $settings = array(
		'serviceUrl' => 'https://sis-t.redsys.es:25443/sis/realizarPago',
		//'serviceUrl' => 'https://sis.redsys.es/sis/realizarPago',
		'extendedSignature' => false,
		'merchantName' => 'Merchant Name',
		'merchantCode' => '000000083',
		'secretKey' => 'QWERTYASDF0123456789',
		'terminal' => '956',
		'currency' => '978',
		'consumerLanguage' => '1',
		'merchantUrl' => 'http://example.com/notification',
	);

	public $notification = array(
		'Ds_Date' => '01/01/2000',
		'Ds_Hour' => '08:00',
		'Ds_Amount' => '349812',
		'Ds_Currency' => '978',
		'Ds_Order' => '000000000100',
		'Ds_MerchantCode' => '000000083',
		'Ds_Terminal' => '956',
		'Ds_Signature' => '6a517f4bbebf2a042689c6e47cb436eeaf920410',
		'Ds_Response' => '0',
		'Ds_MerchantData' => '',
		'Ds_SecurePayment' => '1',
		'Ds_TransactionType' => '0',
		'Ds_Card_Country' => '',
		'Ds_AuthorisationCode' => '11111',
		'Ds_ConsumerLanguage' => '1',
		'Ds_Card_Type' => 'C',
	);

	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->SermepaComponent = new SermepaComponent($Collection, $this->settings);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestSermepaController($CakeRequest, $CakeResponse);
		$this->SermepaComponent->startup($this->Controller);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->SermepaComponent);
		unset($this->Controller);
	}

	public function testCreateTransactionWithStaticSettings() {

		$this->SermepaComponent->createTransaction('100', '349812');
		$this->assertEquals('0', $this->Controller->request->params['sermepaData']['Ds_Merchant_TransactionType']);
		$this->assertEquals('000000000100', $this->Controller->request->params['sermepaData']['Ds_Merchant_Order']);
		$this->assertEquals('349812', $this->Controller->request->params['sermepaData']['Ds_Merchant_Amount']);
		$this->assertEquals('978', $this->Controller->request->params['sermepaData']['Ds_Merchant_Currency']);
		$this->assertEquals('1', $this->Controller->request->params['sermepaData']['Ds_Merchant_ConsumerLanguage']);
		$this->assertEquals('3f8daabbdd6efe028ea209ac992d0a4f34aea275', $this->Controller->params['sermepaData']['Ds_Merchant_MerchantSignature']);
		$this->assertEquals('https://sis-t.redsys.es:25443/sis/realizarPago', $this->Controller->params['sermepaUrl']);

	}

	public function testCreateTransactionWithDynamicSettings() {

		$this->SermepaComponent->serviceUrl = 'https://sis.redsys.es/sis/realizarPago';
		$this->SermepaComponent->extendedSignature = true;
		$this->SermepaComponent->consumerLanguage = '2';
		$this->SermepaComponent->currency = '840';
		$this->SermepaComponent->createTransaction('0100', '349812', '1');
		$this->assertEquals('1', $this->Controller->request->params['sermepaData']['Ds_Merchant_TransactionType']);
		$this->assertEquals('000000000100', $this->Controller->request->params['sermepaData']['Ds_Merchant_Order']);
		$this->assertEquals('349812', $this->Controller->request->params['sermepaData']['Ds_Merchant_Amount']);
		$this->assertEquals('840', $this->Controller->request->params['sermepaData']['Ds_Merchant_Currency']);
		$this->assertEquals('2', $this->Controller->request->params['sermepaData']['Ds_Merchant_ConsumerLanguage']);
		$this->assertEquals('https://sis.redsys.es/sis/realizarPago', $this->Controller->params['sermepaUrl']);
		$this->assertEquals('59954c2bde3a39e484c6b640110b99f182802323', $this->Controller->params['sermepaData']['Ds_Merchant_MerchantSignature']);

	}

	public function testGetNotification() {

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->Controller->request->data = $this->notification;
		$notification = $this->SermepaComponent->getNotification();
		$this->assertEquals($this->notification['Ds_Date'], $notification->date);
		$this->assertEquals($this->notification['Ds_Hour'], $notification->hour);
		$this->assertEquals($this->notification['Ds_Amount'], $notification->amount);
		$this->assertEquals($this->notification['Ds_Currency'], $notification->currency);
		$this->assertEquals($this->notification['Ds_Order'], $notification->order);
		$this->assertEquals($this->notification['Ds_MerchantCode'], $notification->merchantCode);
		$this->assertEquals($this->notification['Ds_Terminal'], $notification->terminal);
		$this->assertEquals($this->notification['Ds_Signature'], $notification->signature);
		$this->assertEquals($this->notification['Ds_Response'], $notification->response);
		$this->assertEquals($this->notification['Ds_MerchantData'], $notification->merchantData);
		$this->assertEquals($this->notification['Ds_SecurePayment'], $notification->securePayment);
		$this->assertEquals($this->notification['Ds_TransactionType'], $notification->transactionType);
		$this->assertEquals($this->notification['Ds_Card_Country'], $notification->cardCountry);
		$this->assertEquals($this->notification['Ds_AuthorisationCode'], $notification->authorisationCode);
		$this->assertEquals($this->notification['Ds_ConsumerLanguage'], $notification->consumerLanguage);
		$this->assertEquals($this->notification['Ds_Card_Type'], $notification->cardType);

	}

	public function testGetNotificationPostException() {

		$this->setExpectedException('CakeException', 'POST');
		$this->Controller->request->data = $this->notification;
		$notification = $this->SermepaComponent->getNotification();

	}

	public function testGetNotificationSignatureException() {

		$this->setExpectedException('CakeException', 'signature');
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->Controller->request->data = $this->notification;
		$this->Controller->request->data['Ds_Signature'] = '';
		$notification = $this->SermepaComponent->getNotification();

	}

}
