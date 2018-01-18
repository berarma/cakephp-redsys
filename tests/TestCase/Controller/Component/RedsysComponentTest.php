<?php
/**
 *
 * RedsysComponent Test Case
 *
 * Copyright (c) Bernat Arlandis
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
 * @copyright Copyright (c) Bernat Arlandis
 * @link http://bernatarlandis.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */
namespace Berarma\Redsys\Test\TestCase\Controller\Component;

use Berarma\Redsys\Controller\Component\RedsysComponent;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\TestSuite\TestCase;

class RedsysComponentTest extends TestCase
{
    public $controller;

    public $component;

    public $settings = [
        'url' => 'https://sis-t.redsys.es:25443/sis/realizarPago',
        //'url' => 'https://sis.redsys.es/sis/realizarPago',
        'secretKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
        'defaults' => [
            'DS_MERCHANT_TERMINAL' => '956',
            'DS_MERCHANT_CURRENCY' => '978',
            'DS_MERCHANT_MERCHANTCODE' => '000000083',
            'DS_MERCHANT_MERCHANTURL' => 'http://example.com/notification',
        ],
    ];

    public $responseParams = [
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
    ];

    public $response = [
        'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
        'Ds_Signature' => 'IVHvFC2_y-cyq45xbB9NIhCRUm7fYZLT0uNXcNEdefQ=',
        'Ds_MerchantParameters' => '',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->response['Ds_MerchantParameters'] = strtr(base64_encode(json_encode($this->responseParams)), '+/', '-_');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGet()
    {
        $this->buildObjects();
        $this->assertEquals($this->settings['defaults']['DS_MERCHANT_TERMINAL'], $this->component->get('DS_MERCHANT_TERMINAL'));
    }

    public function testRequest()
    {
        $this->buildObjects();
        $this->component->request([
            'DS_MERCHANT_AMOUNT' => '100',
            'DS_MERCHANT_ORDER' => '349812',
        ]);
        $this->assertEquals($this->settings['url'], $this->controller->request->getData('Redsys.url'));
        $this->assertEquals('eyJEU19NRVJDSEFOVF9BTU9VTlQiOiIxMDAiLCJEU19NRVJDSEFOVF9PUkRFUiI6IjM0OTgxMiIsIkRTX01FUkNIQU5UX1RFUk1JTkFMIjoiOTU2IiwiRFNfTUVSQ0hBTlRfQ1VSUkVOQ1kiOiI5NzgiLCJEU19NRVJDSEFOVF9NRVJDSEFOVENPREUiOiIwMDAwMDAwODMiLCJEU19NRVJDSEFOVF9NRVJDSEFOVFVSTCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tXC9ub3RpZmljYXRpb24ifQ==', $this->controller->request->getData('Redsys.parameters'));
        $this->assertEquals('NfsLlcmorHgEQhqQxXr3N7QJcXftpIGFiEXCYHQGTLw=', $this->controller->request->getData('Redsys.signature'));
        $this->assertEquals('HMAC_SHA256_V1', $this->controller->request->getData('Redsys.signatureVersion'));
        $this->assertEquals($this->settings['defaults']['DS_MERCHANT_TERMINAL'], $this->component->get('DS_MERCHANT_TERMINAL'));
        $this->assertEquals(100, $this->component->get('DS_MERCHANT_AMOUNT'));
    }

    public function testResponse()
    {
        $request = new ServerRequest(['post' => $this->response]);
        $request = $request->withMethod('POST');
        $this->buildObjects($request);
        $response = $this->component->response();
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

    public function testPostException()
    {
        $request = new ServerRequest(['post' => $this->response]);
        $this->buildObjects($request);
        $this->expectException('\Cake\Network\Exception\MethodNotAllowedException');
        $response = $this->component->response();
    }

    public function testSignatureException()
    {
        $data = $this->response;
        $data['Ds_Signature'] = '';
        $request = new ServerRequest(['post' => $data]);
        $request = $request->withMethod('POST');
        $this->buildObjects($request);
        $this->expectException('Exception');
        $response = $this->component->response();
    }

    protected function buildObjects(ServerRequest $request = null, Response $response = null)
    {
        if ($request === null) {
            $request = new ServerRequest();
        }
        if ($response === null) {
            $response = new Response();
        }
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->component = new RedsysComponent($registry, $this->settings);
        $event = new Event('Controller.startup', $this->controller);
        $this->component->startup($event);
    }
}

