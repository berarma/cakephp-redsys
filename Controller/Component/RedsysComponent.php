<?php
/**
 *
 * CakePHP Component to interact with the Redsys TPV service
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

App::uses('Component', 'Controller');
App::uses('Redsys', 'Redsys.Lib');

class RedsysComponent extends Component {

	public $Controller;

/**
 * {@inheritDoc}
 */
	public function startup(Controller $controller) {
		$this->Controller = $controller;
	}

/**
 * Starts new transaction
 *
 * @param string $order Order reference
 * @param string $amount Transaction amount
 * @param string $transactionType Type of transaction
 * @return null
 */
	public function request($params) {
		$Redsys = new Redsys($this->settings, $params);
		$this->Controller->request->params['RedsysUrl'] = $Redsys->getUrl();
		$this->Controller->request->params['RedsysParameters'] = $Redsys->getMessage();
		$this->Controller->request->params['RedsysSignature'] = $Redsys->getSignature();
		$this->Controller->request->params['RedsysSignatureVersion'] = $Redsys->getVersion();
	}

/**
 * Get notification data
 *
 * @return stdClass Notification data
 * @throws CakeException
 */
	public function response() {
		if (!$this->Controller->request->is('post')) {
			throw new CakeException("Redsys notification not using POST.");
		}
		$Redsys = new Redsys($this->settings, $this->Controller->request->data);
		return $Redsys->getData();
	}
}

