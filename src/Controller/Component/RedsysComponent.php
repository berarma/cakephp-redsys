<?php
/**
 *
 * CakePHP Component to interact with the Redsys TPV service
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
namespace Berarma\Redsys\Controller\Component;

use Berarma\Redsys\Lib\Redsys;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;

class RedsysComponent extends Component
{
    private $controller = null;

    private $redsys = null;

    /**
     * {@inheritDoc}
     */
    public function startup(Event $event)
    {
        $this->controller = $event->getSubject();
        $this->redsys = new Redsys($this->getConfig());
    }

    /**
     * Starts new transaction
     *
     * @param string $order Order reference
     * @param string $amount Transaction amount
     * @param string $transactionType Type of transaction
     * @return null
     */
    public function request(array $params = [])
    {
        $this->redsys = new Redsys($this->getConfig(), $params);
        $data = [
            'url' => $this->redsys->getUrl(),
            'parameters' => $this->redsys->getMessage(),
            'signature' => $this->redsys->getSignature(),
            'signatureVersion' => $this->redsys->getVersion(),
        ];
        $this->controller->request = $this->controller->request->withData('Redsys', $data);
    }

    /**
     * Get notification data
     *
     * @return Redsys Redsys object
     * @throws CakeException
     */
    public function response()
    {
        if (!$this->controller->request->is('post')) {
            throw new \Cake\Network\Exception\MethodNotAllowedException("Redsys notification not using POST.");
        }
        $Redsys = new Redsys($this->getConfig(), $this->controller->request->getData());
        return $Redsys;
    }

    /**
     * Get request param
     *
     * @param string $name Param name
     * @return string Param value
     */
    public function get($name)
    {
        return $this->redsys->get($name);
    }
}

