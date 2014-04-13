<?php
/**
 *
 * SermepaHelper Test Case
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
App::uses('View', 'View');
App::uses('SermepaHelper', 'Sermepa.View/Helper');

class SermepaHelperTest extends CakeTestCase {

	public $Sermepa = null;

	public $sermepaUrl = 'https://sis-t.redsys.es:25443/sis/realizarPago';

	public $sermepaData = array(
		'Ds_MerchantName' => 'Merchant Name',
		'Ds_MerchantCode' => '000000001',
		'Ds_SecretKey' => 'QWERTYASDF0123456789',
		'Ds_Terminal' => '956',
		'Ds_Currency' => '978',
		'Ds_ConsumerLanguage' => '1',
		'Ds_MerchantUrl' => 'http://example.com/notification',
	);

	public function setUp() {
		parent::setUp();
		$Controller = new Controller();
		$View = new View($Controller);
		$this->Sermepa = new SermepaHelper($View);
		$this->Sermepa->request->addParams(array(
			'sermepaUrl' => $this->sermepaUrl,
			'sermepaData' => $this->sermepaData,
		));
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Sermepa);
	}

	public function testRenderForm() {

		$result = $this->Sermepa->renderForm();
		$expected = array(
			'form' => array('action' => $this->sermepaUrl, 'method' => 'post', 'id', 'accept-charset'),
			'div' => array('style'),
			'input' => array('type', 'name', 'value'),
			'/div',
		);
		foreach ($this->sermepaData as $key => $value) {
			$expected[] = array('input' => array('name' => $key, 'value' => $value, 'id', 'type'));
		}
		$expected[] = '/form';
		$this->assertTags($result, $expected, true);
	}
}
