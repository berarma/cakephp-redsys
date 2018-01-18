<?php
/**
 *
 * RedsysHelper Test Case
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
namespace Berarma\Redsys\Test\TestCase\View\Helper;

use Berarma\Redsys\View\Helper\RedsysHelper;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class RedsysHelperTest extends TestCase
{
    public $helper = null;

    public $data = [
        'url' =>  'https://sis-t.redsys.es:25443/sis/realizarPago',
        'parameters' => 'eyJEU19NRVJDSEFOVF9BTU9VTlQiOiIxMDAiLCJEU19NRVJDSEFOVF9PUkRFUiI6IjM0OTgxMiIsIkRzX01lcmNoYW50X1Rlcm1pbmFsIjoiOTU2IiwiRHNfTWVyY2hhbnRfQ3VycmVuY3kiOiI5NzgiLCJEc19NZXJjaGFudF9NZXJjaGFudENvZGUiOiIwMDAwMDAwODMiLCJEc19NZXJjaGFudF9NZXJjaGFudFVSTCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tXC9ub3RpZmljYXRpb24ifQ',
        'signature' => 'R+7sDHIakXvroq31yg8dPAPLqg0WbaGS3tA2dT7iVcc=',
        'signatureVersion' => 'HMAC_SHA256_V1',
    ];

    public function setUp()
    {
        parent::setUp();
        $request = new ServerRequest();
        $request = $request->withData('Redsys', $this->data);
        $response = new Response();
        $view = new View($request, $response);
        $this->helper = new RedsysHelper($view);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->helper);
    }

    public function testRenderForm()
    {
        $expected = [
            ['form' => ['action' => $this->data['url'], 'method' => 'post', 'accept-charset']],
            ['input' => ['type' => 'hidden', 'name' => 'Ds_MerchantParameters', 'value' => 'eyJEU19NRVJDSEFOVF9BTU9VTlQiOiIxMDAiLCJEU19NRVJDSEFOVF9PUkRFUiI6IjM0OTgxMiIsIkRzX01lcmNoYW50X1Rlcm1pbmFsIjoiOTU2IiwiRHNfTWVyY2hhbnRfQ3VycmVuY3kiOiI5NzgiLCJEc19NZXJjaGFudF9NZXJjaGFudENvZGUiOiIwMDAwMDAwODMiLCJEc19NZXJjaGFudF9NZXJjaGFudFVSTCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tXC9ub3RpZmljYXRpb24ifQ']],
            ['input' => ['type' => 'hidden', 'name' => 'Ds_Signature', 'value' => 'R+7sDHIakXvroq31yg8dPAPLqg0WbaGS3tA2dT7iVcc=']],
            ['input' => ['type' => 'hidden', 'name' => 'Ds_SignatureVersion', 'value' => 'HMAC_SHA256_V1']],
            '/form',
        ];
        $result = $this->helper->renderForm();
        $this->assertHtml($expected, $result);
    }
}

