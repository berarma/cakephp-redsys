<?php
/**
 *
 * RedsysHelper Test Case
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
App::uses('View', 'View');
App::uses('RedsysHelper', 'Redsys.View/Helper');

class RedsysHelperTest extends CakeTestCase {

	public $Redsys = null;

	public $redsysUrl = 'https://sis-t.redsys.es:25443/sis/realizarPago';

	public function setUp() {
		parent::setUp();
		$Controller = new Controller();
		$Controller->request = new CakeRequest();
		$Controller->request->params = array(
			'RedsysUrl' => $this->redsysUrl,
			'RedsysParameters' => 'eyJEU19NRVJDSEFOVF9BTU9VTlQiOiIxMDAiLCJEU19NRVJDSEFOVF9PUkRFUiI6IjM0OTgxMiIsIkRzX01lcmNoYW50X1Rlcm1pbmFsIjoiOTU2IiwiRHNfTWVyY2hhbnRfQ3VycmVuY3kiOiI5NzgiLCJEc19NZXJjaGFudF9Db2RlIjoiMDAwMDAwMDgzIiwiRHNfTWVyY2hhbnRfVXJsIjoiaHR0cDpcL1wvZXhhbXBsZS5jb21cL25vdGlmaWNhdGlvbiJ9',
			'RedsysSignature' => 'U23a2yuDScMgPkkcN7VGP8t+nDJhHPt/UtRed3ADQEM=',
			'RedsysSignatureVersion' => 'HMAC_SHA256_V1',
		);
		$View = new View($Controller);
		$this->Redsys = new RedsysHelper($View);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Redsys);
	}

	public function testRenderForm() {
		$result = $this->Redsys->renderForm();
		$this->assertTags($result, array(
			array('form' => array('action' => $this->redsysUrl, 'method' => 'post', 'id', 'accept-charset')),
			array('input' => array('type' => 'hidden', 'id', 'name' => 'Ds_MerchantParameters', 'value' => 'eyJEU19NRVJDSEFOVF9BTU9VTlQiOiIxMDAiLCJEU19NRVJDSEFOVF9PUkRFUiI6IjM0OTgxMiIsIkRzX01lcmNoYW50X1Rlcm1pbmFsIjoiOTU2IiwiRHNfTWVyY2hhbnRfQ3VycmVuY3kiOiI5NzgiLCJEc19NZXJjaGFudF9Db2RlIjoiMDAwMDAwMDgzIiwiRHNfTWVyY2hhbnRfVXJsIjoiaHR0cDpcL1wvZXhhbXBsZS5jb21cL25vdGlmaWNhdGlvbiJ9')),
			array('input' => array('type' => 'hidden', 'id', 'name' => 'Ds_Signature', 'value' => 'U23a2yuDScMgPkkcN7VGP8t+nDJhHPt/UtRed3ADQEM=')),
			array('input' => array('type' => 'hidden', 'id', 'name' => 'Ds_SignatureVersion', 'value' => 'HMAC_SHA256_V1')),
			'/form',
		));
	}
}

